<?php
/**
 * SVG Upload Functionality Initialization
 *
 * Main initialization file for SVG upload functionality.
 * This file loads all necessary classes and initializes the SVG handler.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load required classes
require_once MAIN_THEME_DIR . '/inc/svg-upload/class-svg-sanitizer.php';
require_once MAIN_THEME_DIR . '/inc/svg-upload/class-svg-handler.php';

/**
 * Initialize SVG upload functionality
 *
 * Creates and initializes the SVG handler instance.
 */
function main_theme_init_svg_upload() {
	// Check if classes are available
	if ( ! class_exists( 'SVG_Sanitizer' ) || ! class_exists( 'SVG_Handler' ) ) {
		return;
	}

	// Initialize the handler
	new SVG_Handler();
}

// Initialize on WordPress init
add_action( 'init', 'main_theme_init_svg_upload', 1 );

