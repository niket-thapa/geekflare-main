<?php
/**
 * Widget Areas Registration
 *
 * Registers all sidebar and widget areas for the theme,
 * including the main sidebar and footer menu areas.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register widget areas
 *
 * Registers the main sidebar and footer widget areas.
 * Footer areas are dynamically generated for multiple menu sections.
 *
 * @since 1.0.0
 * @return void
 */
function main_widgets_init() {
	// Register main sidebar
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'main' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'main' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	// Footer Widget Areas
	$footer_menus = array(
		'company'  => __( 'Company Menu', 'main' ),
		'legal'    => __( 'Legal Menu', 'main' ),
		'general'  => __( 'General Menu', 'main' ),
		'products' => __( 'Products Menu', 'main' ),
	);

	// Register footer widget areas
	foreach ( $footer_menus as $id => $name ) {
		register_sidebar(
			array(
				'name'          => sprintf( esc_html__( 'Footer - %s', 'main' ), $name ),
				'id'            => 'footer-' . $id,
				'description'   => sprintf( esc_html__( 'Add %s widgets here.', 'main' ), $name ),
				'before_widget' => '<div id="%1$s" class="footer-menu-widget widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="footer-menu-title widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}
add_action( 'widgets_init', 'main_widgets_init' );
