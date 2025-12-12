<?php
/**
 * Sidebar Widgets
 *
 * Registers widget areas that display below the hardcoded sidebars.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Widget Areas
 *
 * @since 1.0.0
 */
function main_register_sidebar_widgets() {
	// Buying Guide Sidebar Widget Area
	register_sidebar(
		array(
			'name'          => __( 'Buying Guide Sidebar Widgets', 'main' ),
			'id'            => 'buying-guide-sidebar',
			'description'   => __( 'Widgets appear below the TOC and filters in buying guide posts', 'main' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	// Info Article Sidebar Widget Area
	register_sidebar(
		array(
			'name'          => __( 'Info Article Sidebar Widgets', 'main' ),
			'id'            => 'info-article-sidebar',
			'description'   => __( 'Widgets appear below the TOC in info article posts', 'main' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'main_register_sidebar_widgets' );