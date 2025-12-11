<?php
/**
 * Main Theme Functions File
 *
 * This file serves as the entry point for all theme functionality.
 * It includes modular files from the inc/ directory to keep the codebase
 * clean, organized, and maintainable.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme Directory Path
 *
 * Defines the theme directory path for easier file inclusion.
 */
define( 'MAIN_THEME_DIR', get_template_directory() );
define( 'MAIN_THEME_URI', get_template_directory_uri() );

// ============================================================================
// Core Includes
// ============================================================================

/**
 * Include Custom Walker
 *
 * Custom navigation menu walker for advanced menu functionality.
 */
$walker_file = MAIN_THEME_DIR . '/inc/class-walker-nav-menu.php';
if ( file_exists( $walker_file ) ) {
	require_once $walker_file;
}

// ============================================================================
// Theme Setup & Configuration
// ============================================================================

/**
 * Theme Setup
 *
 * Registers theme support, navigation menus, and initial configuration.
 */
require_once MAIN_THEME_DIR . '/inc/theme-setup.php';

// ============================================================================
// Helper Functions
// ============================================================================

/**
 * Template Helper Functions
 *
 * Utility functions for templates including image URLs, excerpts, and body classes.
 */
require_once MAIN_THEME_DIR . '/inc/template-helpers.php';

// ============================================================================
// Asset Management
// ============================================================================

/**
 * Frontend Asset Enqueuing
 *
 * Handles the enqueuing of all frontend scripts and stylesheets
 * with proper loading order and priority.
 */
require_once MAIN_THEME_DIR . '/inc/enqueue-assets.php';

// ============================================================================
// Widget Areas
// ============================================================================

/**
 * Widget Areas Registration
 *
 * Registers sidebar and footer widget areas.
 */
require_once MAIN_THEME_DIR . '/inc/widgets.php';

// ============================================================================
// Customizer
// ============================================================================

/**
 * Theme Customizer Settings
 *
 * Registers all theme customizer options for header and footer settings.
 */
require_once MAIN_THEME_DIR . '/inc/customizer.php';

// ============================================================================
// Custom Blocks
// ============================================================================

/**
 * Custom Blocks Registration & Editor Assets
 *
 * Registers custom Gutenberg blocks and ensures proper styling
 * and functionality in the block editor.
 */
require_once MAIN_THEME_DIR . '/inc/blocks.php';

// ============================================================================
// Template Functions
// ============================================================================

/**
 * Template Functions
 *
 * Archive title modifications and breadcrumb navigation.
 */
require_once MAIN_THEME_DIR . '/inc/template-functions.php';

// ============================================================================
// Additional Features
// ============================================================================

/**
 * Pagination Functions
 *
 * Custom pagination functionality with first/last and prev/next controls.
 */
require_once MAIN_THEME_DIR . '/inc/pagination.php';

/**
 * Archive Search Functionality
 *
 * Handles search functionality on archive pages with AJAX support.
 */
require_once MAIN_THEME_DIR . '/inc/archive-search.php';

/**
 * Custom Post Types
 *
 * Product data layer for guides.
 */
require_once MAIN_THEME_DIR . '/inc/cpt/products.php';

/**
 * Product Helper Functions
 *
 * Utility functions for product queries and display.
 */
require_once MAIN_THEME_DIR . '/inc/product-helpers.php';

/**
 * Post Settings
 *
 * Custom meta fields and settings for posts.
 */
require_once MAIN_THEME_DIR . '/inc/post-settings.php';

/**
 * User Profile Customizations
 *
 * Custom fields and functions for user profiles.
 */
require_once MAIN_THEME_DIR . '/inc/user-profile.php';

/**
 * SVG Upload Support
 *
 * Enables secure SVG file uploads with sanitization and proper media library display.
 */
require_once MAIN_THEME_DIR . '/inc/svg-upload/svg-upload-init.php';

/**
 * Disable Comments
 *
 * Completely disables comments for posts using WordPress hooks and filters.
 */
require_once MAIN_THEME_DIR . '/inc/disable-comments.php';

/**
 * Template Selector
 *
 * Post template selection functionality for Buying Guide and Info Article templates.
 */
require_once MAIN_THEME_DIR . '/inc/template-selector.php';

/**
 * Sidebar Functions
 *
 * Dynamic sidebar generation with auto TOC and filters.
 */
require_once MAIN_THEME_DIR . '/inc/sidebar-functions.php';

/**
 * Custom Author URL Structure
 *
 * Removes 'author' from author archive URLs.
 */
require_once MAIN_THEME_DIR . '/inc/custom-author-url.php';

/**
 * Sidebar Widgets
 *
 * Widget areas for buying guide and info sidebars.
 */
require_once MAIN_THEME_DIR . '/inc/sidebar-widgets.php';