<?php
/**
 * Template Selector
 *
 * Registers post template meta field and handles template selection functionality.
 * Allows users to choose between "Buying Guide" and "Info Article" templates.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Template Selection Meta Field
 *
 * Registers a meta field for posts to store the selected template type.
 * This meta field is exposed to the REST API for use in the block editor.
 *
 * @since 1.0.0
 */
function main_register_template_meta() {
	register_post_meta(
		'post',
		'mcb_post_template',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => 'buying_guide',
			'sanitize_callback' => 'main_sanitize_template_choice',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'main_register_template_meta' );

/**
 * Sanitize Template Choice
 *
 * Ensures only valid template values are saved to the database.
 *
 * @since 1.0.0
 * @param string $value The template value to sanitize.
 * @return string The sanitized template value.
 */
function main_sanitize_template_choice( $value ) {
	$allowed = array( 'buying_guide', 'info' );
	return in_array( $value, $allowed, true ) ? $value : 'buying_guide';
}

/**
 * Enqueue Template Selector in Block Editor
 *
 * Loads the template selector JavaScript component in the block editor.
 * Uses the theme's existing build system (esbuild).
 *
 * @since 1.0.0
 */
function main_enqueue_template_selector() {
	// Only load in block editor
	$screen = get_current_screen();
	if ( ! $screen || ! $screen->is_block_editor() ) {
		return;
	}

	// Only for post type 'post'
	if ( 'post' !== $screen->post_type ) {
		return;
	}

	$script_path = get_template_directory() . '/dist/template-selector.js';

	// Check if the build file exists
	if ( ! file_exists( $script_path ) ) {
		return;
	}

	// Enqueue the template selector script
	wp_enqueue_script(
		'main-template-selector',
		get_template_directory_uri() . '/dist/template-selector.js',
		array(
			'wp-plugins',
			'wp-edit-post',
			'wp-element',
			'wp-components',
			'wp-data',
			'wp-i18n',
			'wp-core-data',
		),
		filemtime( $script_path ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'main_enqueue_template_selector' );

/**
 * Get Current Post Template
 *
 * Helper function to get the currently selected template for a post.
 *
 * @since 1.0.0
 * @param int $post_id The post ID. Defaults to current post.
 * @return string The template type ('buying_guide' or 'info').
 */
function main_get_post_template( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$template = get_post_meta( $post_id, 'mcb_post_template', true );

	// Return default if not set
	if ( empty( $template ) ) {
		return 'buying_guide';
	}

	return $template;
}

/**
 * Check if Post Uses Buying Guide Template
 *
 * Helper function to check if a post is using the buying guide template.
 *
 * @since 1.0.0
 * @param int $post_id The post ID. Defaults to current post.
 * @return bool True if using buying guide template, false otherwise.
 */
function main_is_buying_guide_template( $post_id = 0 ) {
	return 'buying_guide' === main_get_post_template( $post_id );
}

/**
 * Check if Post Uses Info Template
 *
 * Helper function to check if a post is using the info article template.
 *
 * @since 1.0.0
 * @param int $post_id The post ID. Defaults to current post.
 * @return bool True if using info template, false otherwise.
 */
function main_is_info_template( $post_id = 0 ) {
	return 'info' === main_get_post_template( $post_id );
}

/**
 * Add Template Class to Post
 *
 * Adds a CSS class to post based on the selected template.
 * Useful for template-specific styling.
 *
 * @since 1.0.0
 * @param array $classes Existing post classes.
 * @return array Modified post classes.
 */
function main_add_template_class( $classes ) {
	if ( is_singular( 'post' ) ) {
		$template = main_get_post_template();
		$classes[] = 'post-template-' . $template;
	}
	return $classes;
}
add_filter( 'post_class', 'main_add_template_class' );

/**
 * Add Template Info to Admin Columns
 *
 * Adds a column in the posts list showing which template is being used.
 *
 * @since 1.0.0
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function main_add_template_column( $columns ) {
	// Insert after title column
	$new_columns = array();
	foreach ( $columns as $key => $value ) {
		$new_columns[ $key ] = $value;
		if ( 'title' === $key ) {
			$new_columns['template'] = __( 'Template', 'main' );
		}
	}
	return $new_columns;
}
add_filter( 'manage_post_posts_columns', 'main_add_template_column' );

/**
 * Display Template in Admin Column
 *
 * Shows the template name in the posts list.
 *
 * @since 1.0.0
 * @param string $column  The column name.
 * @param int    $post_id The post ID.
 */
function main_display_template_column( $column, $post_id ) {
	if ( 'template' === $column ) {
		$template = main_get_post_template( $post_id );
		
		if ( 'buying_guide' === $template ) {
			echo '<span style="display: inline-block; padding: 3px 8px; background: #e7f5fe; color: #007cba; border-radius: 3px; font-size: 11px; font-weight: 600;">📋 Buying Guide</span>';
		} else {
			echo '<span style="display: inline-block; padding: 3px 8px; background: #ecf7ed; color: #46b450; border-radius: 3px; font-size: 11px; font-weight: 600;">📖 Info Article</span>';
		}
	}
}
add_action( 'manage_post_posts_custom_column', 'main_display_template_column', 10, 2 );

/**
 * Make Template Column Sortable
 *
 * Allows sorting posts by template type in admin.
 *
 * @since 1.0.0
 * @param array $columns Sortable columns.
 * @return array Modified sortable columns.
 */
function main_template_column_sortable( $columns ) {
	$columns['template'] = 'template';
	return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'main_template_column_sortable' );

/**
 * Handle Template Column Sorting
 *
 * Modifies the query to sort by template meta value.
 *
 * @since 1.0.0
 * @param WP_Query $query The WordPress query object.
 */
function main_template_column_orderby( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( 'template' === $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', 'mcb_post_template' );
		$query->set( 'orderby', 'meta_value' );
	}
}
add_action( 'pre_get_posts', 'main_template_column_orderby' );
