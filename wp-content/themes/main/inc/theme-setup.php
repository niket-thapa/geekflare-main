<?php
/**
 * Theme Setup and Configuration
 *
 * Handles theme support features, navigation menu registration,
 * and initial theme configuration.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @since 1.0.0
 * @return void
 */
function main_setup() {
	// Add theme support for automatic feed links
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add custom logo support
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Switch default core markup for search form, comment form, and comments
	// to output valid HTML5
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		)
	);

	// Add theme support for selective refresh for widgets
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for editor styles
	add_theme_support( 'editor-styles' );
	add_editor_style( 'dist/style.css' );

	// Add support for Block Styles
	add_theme_support( 'wp-block-styles' );

	// Add support for wide and full alignments
	add_theme_support( 'align-wide' );

	// Add support for responsive embeds
	add_theme_support( 'responsive-embeds' );

	// Add support for custom color palette
	add_theme_support( 'editor-color-palette' );

	// Add support for custom font sizes
	add_theme_support( 'editor-font-sizes' );

	// Register navigation menus
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'main' ),
			'footer'  => esc_html__( 'Footer Menu', 'main' ),
		)
	);

	// Set content width to match theme design
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'main_setup' );

/**
 * Register custom block styles for WordPress core blocks
 *
 * @since 1.0.0
 * @return void
 */
function main_register_block_styles() {
	// Register table block styles
	register_block_style(
		'core/table',
		array(
			'name'         => 'highlight-f-column',
			'label'        => __( 'Highlight First Column', 'main' ),
		)
	);

	register_block_style(
		'core/table',
		array(
			'name'         => 'highlight-f-row',
			'label'        => __( 'Highlight First Row', 'main' ),
		)
	);
}
add_action( 'init', 'main_register_block_styles' );
