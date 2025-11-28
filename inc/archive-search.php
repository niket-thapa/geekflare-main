<?php
/**
 * Archive Page Search Functionality
 *
 * Handles search functionality on archive pages including:
 * - Search form submission
 * - Query modification for search results
 * - AJAX search support via REST API
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Modify archive query to include search
 *
 * When a search query is present, modify the main query to filter posts
 * by search terms while maintaining the archive context (category, tag, etc.).
 *
 * @since 1.0.0
 *
 * @param WP_Query $query The WordPress query object.
 * @return void
 */
function main_archive_search_query( $query ) {
	// Only modify main query on frontend archive pages
	if ( is_admin() || ! $query->is_main_query() || ! is_archive() ) {
		return;
	}

	// Check if search query exists (from form submission or AJAX)
	$search_query = isset( $_GET['archive_search'] ) ? sanitize_text_field( wp_unslash( $_GET['archive_search'] ) ) : '';
	$date_start   = main_get_archive_date_start();
	$date_end     = main_get_archive_date_end();

	// Preserve archive context when searching
	// Don't override author/tag/category if already set by the archive
	$current_author = $query->get( 'author' );
	$current_author_name = $query->get( 'author_name' );
	
	// If search query exists, add it but maintain archive context
	if ( ! empty( $search_query ) ) {
		$query->set( 's', $search_query );
		
		// If we're on an author archive, ensure author filter is maintained
		if ( is_author() ) {
			if ( empty( $current_author ) && empty( $current_author_name ) ) {
				$author = get_queried_object();
				if ( $author && isset( $author->ID ) ) {
					$query->set( 'author', $author->ID );
				}
			}
		}
		
		// If we're on a tag archive, ensure tag filter is maintained
		if ( is_tag() ) {
			$current_tag = $query->get( 'tag_id' );
			if ( empty( $current_tag ) ) {
				$tag = get_queried_object();
				if ( $tag && isset( $tag->term_id ) ) {
					$query->set( 'tag_id', $tag->term_id );
				}
			}
		}
		
		// If we're on a category archive, ensure category filter is maintained
		if ( is_category() ) {
			$current_cat = $query->get( 'cat' );
			if ( empty( $current_cat ) ) {
				$category = get_queried_object();
				if ( $category && isset( $category->term_id ) ) {
					$query->set( 'cat', $category->term_id );
				}
			}
		}
	}

	// Add date range filter to main query
	if ( $date_start || $date_end ) {
		$date_query = array(
			'inclusive' => true,
		);

		if ( $date_start ) {
			$date_query['after'] = $date_start;
		}

		if ( $date_end ) {
			$date_query['before'] = $date_end;
		}

		$query->set( 'date_query', array( $date_query ) );
	}
}
add_action( 'pre_get_posts', 'main_archive_search_query' );

/**
 * Get archive search query
 *
 * Helper function to retrieve the current archive search query.
 *
 * @since 1.0.0
 *
 * @return string The search query string.
 */
function main_get_archive_search_query() {
	return isset( $_GET['archive_search'] ) ? sanitize_text_field( wp_unslash( $_GET['archive_search'] ) ) : '';
}

/**
 * Normalize archive date input.
 *
 * Accepts dates in Y-m-d format and returns a sanitized string or empty string.
 *
 * @since 1.0.0
 *
 * @param string $date Date string from request.
 * @return string
 */
function main_archive_normalize_date( $date ) {
	if ( empty( $date ) ) {
		return '';
	}

	$date = sanitize_text_field( wp_unslash( $date ) );
	$parsed_date = DateTime::createFromFormat( 'Y-m-d', $date );

	if ( false === $parsed_date || $parsed_date->format( 'Y-m-d' ) !== $date ) {
		return '';
	}

	return $date;
}

/**
 * Retrieve archive date range start if provided.
 *
 * @since 1.0.0
 *
 * @return string
 */
function main_get_archive_date_start() {
	return main_archive_normalize_date( isset( $_GET['archive_date_start'] ) ? $_GET['archive_date_start'] : '' );
}

/**
 * Retrieve archive date range end if provided.
 *
 * @since 1.0.0
 *
 * @return string
 */
function main_get_archive_date_end() {
	return main_archive_normalize_date( isset( $_GET['archive_date_end'] ) ? $_GET['archive_date_end'] : '' );
}

/**
 * Get display-ready value for the date range input.
 *
 * @since 1.0.0
 *
 * @return string
 */
function main_get_archive_date_range_value() {
	$start = main_get_archive_date_start();
	$end   = main_get_archive_date_end();

	if ( $start && $end ) {
		return sprintf( '%1$s to %2$s', $start, $end );
	}

	if ( $start ) {
		return $start;
	}

	if ( $end ) {
		return $end;
	}

	return '';
}

/**
 * Register REST API endpoint for archive search
 *
 * Provides an endpoint for AJAX search functionality on archive pages.
 *
 * @since 1.0.0
 * @return void
 */
function main_register_archive_search_endpoint() {
	register_rest_route(
		'main/v1',
		'/archive-search',
		array(
			'methods'             => 'GET',
			'callback'            => 'main_handle_archive_search',
			'permission_callback' => '__return_true',
			'args'                => array(
				'search'   => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'category' => array(
					'required' => false,
					'type'     => 'integer',
				),
				'tag'      => array(
					'required' => false,
					'type'     => 'integer',
				),
				'author'   => array(
					'required' => false,
					'type'     => 'integer',
				),
				'date_start' => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'date_end'   => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'paged'    => array(
					'required' => false,
					'type'     => 'integer',
					'default'  => 1,
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'main_register_archive_search_endpoint' );

/**
 * Handle archive search REST API request
 *
 * Processes search requests and returns formatted HTML for posts.
 *
 * @since 1.0.0
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response|WP_Error Response object or error.
 */
function main_handle_archive_search( $request ) {
	$search_query = $request->get_param( 'search' );
	$category_id  = $request->get_param( 'category' );
	$tag_id       = $request->get_param( 'tag' );
	$author_id    = $request->get_param( 'author' );
	$date_start   = main_archive_normalize_date( $request->get_param( 'date_start' ) );
	$date_end     = main_archive_normalize_date( $request->get_param( 'date_end' ) );
	$paged        = $request->get_param( 'paged' );

	// Build query args
	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => get_option( 'posts_per_page', 12 ),
		'paged'          => $paged,
		'post_status'    => 'publish',
	);

	// Add search query
	if ( ! empty( $search_query ) ) {
		$args['s'] = $search_query;
	}

	// Add category filter
	if ( ! empty( $category_id ) ) {
		$args['cat'] = $category_id;
	}

	// Add tag filter
	if ( ! empty( $tag_id ) ) {
		$args['tag_id'] = $tag_id;
	}

	// Add author filter
	if ( ! empty( $author_id ) ) {
		$args['author'] = absint( $author_id );
	}

	// Add date range filter
	if ( $date_start || $date_end ) {
		$date_query = array(
			'inclusive' => true,
		);

		if ( $date_start ) {
			$date_query['after'] = $date_start;
		}

		if ( $date_end ) {
			$date_query['before'] = $date_end;
		}

		$args['date_query'] = array( $date_query );
	}

	$search_query_obj = new WP_Query( $args );

	ob_start();
	if ( $search_query_obj->have_posts() ) {
		while ( $search_query_obj->have_posts() ) {
			$search_query_obj->the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		}
		wp_reset_postdata();
	} else {
		?>
		<div class="col-span-full text-center py-12">
			<p class="text-gray-500 text-lg">
				<?php esc_html_e( 'No articles found matching your search.', 'main' ); ?>
			</p>
		</div>
		<?php
	}

	$posts_html = ob_get_clean();

	$response = array(
		'success'    => true,
		'posts_html' => $posts_html,
		'found'      => $search_query_obj->found_posts,
		'max_pages'  => $search_query_obj->max_num_pages,
		'current'    => $paged,
	);

	return rest_ensure_response( $response );
}

