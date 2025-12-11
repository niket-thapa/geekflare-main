<?php
/**
 * Template Helper Functions
 *
 * Contains utility functions for templates including:
 * - Image URL helpers
 * - Excerpt customization
 * - Body class modifications
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get theme image URL
 *
 * Helper function to get image URLs that work in both frontend and admin.
 * Returns the full URL path to an image in the theme's assets/images directory.
 *
 * @since 1.0.0
 *
 * @param string $image_path Image path relative to assets/images directory (e.g., 'texture-mobile.svg').
 * @return string Full URL to the image.
 */
function main_get_image_url( $image_path ) {
	return get_stylesheet_directory_uri() . '/assets/images/' . ltrim( $image_path, '/' );
}

/**
 * Custom excerpt length
 *
 * Limits the number of words in post excerpts.
 *
 * @since 1.0.0
 *
 * @param int $length The default excerpt length.
 * @return int Modified excerpt length.
 */
function main_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'main_excerpt_length' );

/**
 * Custom excerpt more
 *
 * Replaces the default "[...]" with a custom read more indicator.
 *
 * @since 1.0.0
 *
 * @param string $more The default "read more" text.
 * @return string Modified "read more" text.
 */
function main_excerpt_more( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'main_excerpt_more' );

/**
 * Add custom body classes
 *
 * Adds theme-specific body classes for enhanced styling control.
 *
 * @since 1.0.0
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function main_body_classes( $classes ) {
	// Add class for no sidebar
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Add theme identifier class for higher CSS specificity
	$classes[] = 'theme-main';

	return $classes;
}
add_filter( 'body_class', 'main_body_classes' );

/**
 * Get post count for current archive/search page
 *
 * Returns the appropriate post count based on the current page context.
 * Works with category, tag, taxonomy, author, post type archive, date, and search pages.
 *
 * @since 1.0.0
 *
 * @return int Post count for the current archive/search context.
 */
function main_get_archive_post_count() {
	global $wp_query;
	
	$post_count = 0;
	
	if ( is_category() || is_tag() || is_tax() ) {
		$queried_object = get_queried_object();
		if ( $queried_object && isset( $queried_object->count ) ) {
			// For categories, get count including child categories
			if ( is_category() ) {
				$category_id = $queried_object->term_id;
				
				// Get all child category IDs
				$child_categories = get_term_children( $category_id, 'category' );
				
				if ( ! is_wp_error( $child_categories ) && ! empty( $child_categories ) ) {
					// Add parent category to the array
					$all_categories = array_merge( array( $category_id ), $child_categories );
					
					// Count posts in parent and all child categories
					$args = array(
						'category__in' => $all_categories,
						'posts_per_page' => -1,
						'fields' => 'ids',
						'post_status' => 'publish'
					);
					
					$query = new WP_Query( $args );
					$post_count = $query->found_posts;
					wp_reset_postdata();
				} else {
					// No child categories, use the default count
					$post_count = $queried_object->count;
				}
			} else {
				// For tags and custom taxonomies, use default count
				$post_count = $queried_object->count;
			}
		}
	} elseif ( is_author() || is_post_type_archive() || is_date() || is_search() ) {
		$post_count = $wp_query->found_posts;
	}
	
	return $post_count;
}

/**
 * Get social sharing URL
 *
 * Generates the appropriate sharing URL for various social media platforms.
 *
 * @since 1.0.0
 *
 * @param string $platform Social platform: 'linkedin', 'facebook', 'instagram', 'twitter'.
 * @param string $url URL to share (optional, defaults to current post URL).
 * @param string $title Title/description to share (optional).
 * @return string Sharing URL for the specified platform.
 */
function main_get_social_share_url( $platform, $url = '', $title = '' ) {
	if ( empty( $url ) ) {
		$url = get_permalink();
	}

	if ( empty( $title ) ) {
		$title = get_the_title();
	}

	$encoded_url   = rawurlencode( $url );
	$encoded_title = rawurlencode( $title );

	switch ( strtolower( $platform ) ) {
		case 'linkedin':
			return 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url;
		
		case 'facebook':
			return 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
		
		case 'twitter':
		case 'x':
			return 'https://x.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;
		
		case 'instagram':
			// Instagram doesn't support direct web sharing, link to their website
			return 'https://www.instagram.com/';
		
		default:
			return $url;
	}
}
