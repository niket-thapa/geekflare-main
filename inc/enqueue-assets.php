<?php
/**
 * Enqueue Frontend Assets
 *
 * Handles the enqueuing of all frontend scripts and stylesheets.
 * Ensures proper loading order and priority to override WordPress core styles.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue theme scripts and styles
 *
 * Enqueues all frontend assets including fonts, stylesheets, and JavaScript.
 * Uses priority 999 to ensure styles load after WordPress core styles.
 *
 * @since 1.0.0
 * @return void
 */
function main_scripts() {
	// Enqueue fonts CSS first (before main styles)
	wp_enqueue_style(
		'main-fonts',
		get_stylesheet_directory_uri() . '/src/css/fonts.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// Enqueue Flickity CSS (required for carousels)
	// Load before main-style so main-style can override if needed
	wp_enqueue_style(
		'main-flickity',
		get_stylesheet_directory_uri() . '/dist/flickity.css',
		array( 'main-fonts' ),
		wp_get_theme()->get( 'Version' ),
		'all'
	);

	// Enqueue compiled Tailwind CSS (includes fonts via @import)
	// Load with high priority to override WordPress core styles and Flickity
	wp_enqueue_style(
		'main-style',
		get_stylesheet_directory_uri() . '/dist/style.css',
		array( 'main-fonts', 'main-flickity' ),
		wp_get_theme()->get( 'Version' ),
		'all'
	);

	// Enqueue theme JavaScript
	wp_enqueue_script(
		'main-script',
		get_stylesheet_directory_uri() . '/dist/script.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	// Enqueue comment reply script on singular posts with comments enabled
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Enqueue archive-specific scripts only on archive pages
	if ( is_archive() ) {
		// Flatpickr CSS for date range picker
		$flatpickr_css_path = get_stylesheet_directory() . '/dist/flatpickr.min.css';
		if ( file_exists( $flatpickr_css_path ) ) {
			wp_enqueue_style(
				'flatpickr',
				get_stylesheet_directory_uri() . '/dist/flatpickr.min.css',
				array(),
				filemtime( $flatpickr_css_path )
			);
		}

		// Flatpickr JS for date range picker
		$flatpickr_js_path = get_stylesheet_directory() . '/dist/flatpickr.min.js';
		$archive_datepicker_deps = array();
		if ( file_exists( $flatpickr_js_path ) ) {
			wp_enqueue_script(
				'flatpickr',
				get_stylesheet_directory_uri() . '/dist/flatpickr.min.js',
				array(),
				filemtime( $flatpickr_js_path ),
				true
			);
			$archive_datepicker_deps[] = 'flatpickr';
		}

		// Archive tags filter script
		$archive_tags_filter_path = get_stylesheet_directory() . '/src/js/archive-tags-filter.js';
		if ( file_exists( $archive_tags_filter_path ) ) {
			wp_enqueue_script(
				'main-archive-tags-filter',
				get_stylesheet_directory_uri() . '/src/js/archive-tags-filter.js',
				array(),
				filemtime( $archive_tags_filter_path ),
				true
			);
		}

		// Archive search script with REST API dependency (load BEFORE datepicker)
		$archive_search_path = get_stylesheet_directory() . '/src/js/archive-search.js';
		$archive_search_deps = array();
		if ( file_exists( $archive_search_path ) ) {
			wp_enqueue_script(
				'main-archive-search',
				get_stylesheet_directory_uri() . '/src/js/archive-search.js',
				$archive_search_deps,
				filemtime( $archive_search_path ),
				true
			);

			// Localize script with REST API settings
			wp_localize_script(
				'main-archive-search',
				'wpApiSettings',
				array(
					'root'  => esc_url_raw( rest_url() ),
					'nonce' => wp_create_nonce( 'wp_rest' ),
				)
			);

			// Add search script as dependency for datepicker
			$archive_datepicker_deps[] = 'main-archive-search';
		}

		// Archive date picker script (depends on Flatpickr AND archive-search)
		$archive_datepicker_path = get_stylesheet_directory() . '/src/js/archive-datepicker.js';
		if ( file_exists( $archive_datepicker_path ) ) {
			wp_enqueue_script(
				'main-archive-datepicker',
				get_stylesheet_directory_uri() . '/src/js/archive-datepicker.js',
				$archive_datepicker_deps,
				filemtime( $archive_datepicker_path ),
				true
			);
		}
	}

	// Enqueue product filter script on single posts
	if ( is_singular() ) {
		$product_filter_path = get_stylesheet_directory() . '/src/js/product-filter.js';
		if ( file_exists( $product_filter_path ) ) {
			wp_enqueue_script(
				'main-product-filter',
				get_stylesheet_directory_uri() . '/src/js/product-filter.js',
				array(),
				filemtime( $product_filter_path ),
				true
			);
		}
	}
}
// Priority 999 ensures our styles load AFTER WordPress core styles (which load at priority 10)
add_action( 'wp_enqueue_scripts', 'main_scripts', 999 );

/**
 * Modify style loader tag
 *
 * Adds custom data attribute to main stylesheet for identification
 * and potential JavaScript manipulation.
 *
 * @since 1.0.0
 *
 * @param string $html   The link tag for the enqueued style.
 * @param string $handle The style's registered handle.
 * @param string $href   The stylesheet's source URL.
 * @param string $media  The stylesheet's media attribute.
 * @return string Modified link tag.
 */
function main_style_loader_tag( $html, $handle, $href, $media ) {
	// Only modify our main stylesheet
	if ( 'main-style' === $handle ) {
		// Add inline style attribute to ensure it loads with highest priority
		// The order in the DOM (last) combined with high priority ensures override
		$html = str_replace(
			"rel='stylesheet'",
			"rel='stylesheet' data-theme-style='main'",
			$html
		);
	}
	return $html;
}
add_filter( 'style_loader_tag', 'main_style_loader_tag', 999, 4 );

/**
 * Ensure theme styles print last
 *
 * Reorders the styles queue to ensure main-style is printed last,
 * allowing it to override WordPress core styles.
 *
 * @since 1.0.0
 * @return void
 */
function main_print_styles_last() {
	global $wp_styles;

	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	// Ensure our styles are queued last by reordering
	if ( isset( $wp_styles->queue ) && is_array( $wp_styles->queue ) ) {
		$queue = $wp_styles->queue;
		// Remove our style from queue if it exists
		$queue = array_values(
			array_filter(
				$queue,
				function( $handle ) {
					return 'main-style' !== $handle;
				}
			)
		);
		// Add our style at the end
		$queue[] = 'main-style';
		$wp_styles->queue = $queue;
	}
}
// Run at priority 999 to ensure it happens after all other enqueues
add_action( 'wp_print_styles', 'main_print_styles_last', 999 );
