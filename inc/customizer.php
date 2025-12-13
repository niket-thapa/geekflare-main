<?php
/**
 * Theme Customizer Settings
 *
 * Registers all theme customizer options including:
 * - Header settings (search, language menu, products button)
 * - Footer settings (logo, email, copyright, social icons)
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme customizer options
 *
 * Adds all customizer sections, settings, and controls for
 * header and footer customization.
 *
 * @since 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function main_customize_register( $wp_customize ) {
	// Include custom repeater control class (only in customizer context)
	if ( ! class_exists( 'Main_Customize_Repeater_Control' ) ) {
		$repeater_control_file = get_template_directory() . '/inc/class-customize-repeater-control.php';
		if ( file_exists( $repeater_control_file ) ) {
			require_once $repeater_control_file;
		}
	}

	// ============================================
	// Header Section
	// ============================================
	$wp_customize->add_section(
		'header_settings',
		array(
			'title'    => __( 'Header Settings', 'main' ),
			'priority' => 30,
		)
	);

	// Show Search Toggle
	$wp_customize->add_setting(
		'header_show_search',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);
	$wp_customize->add_control(
		'header_show_search',
		array(
			'label'   => __( 'Show Search', 'main' ),
			'section' => 'header_settings',
			'type'    => 'checkbox',
		)
	);

	// Show Language Menu Toggle
	$wp_customize->add_setting(
		'header_show_lang_menu',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);
	$wp_customize->add_control(
		'header_show_lang_menu',
		array(
			'label'   => __( 'Show Language Menu', 'main' ),
			'section' => 'header_settings',
			'type'    => 'checkbox',
		)
	);

	// Products Button URL
	$wp_customize->add_setting(
		'header_products_url',
		array(
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'header_products_url',
		array(
			'label'   => __( 'Products Button URL', 'main' ),
			'section' => 'header_settings',
			'type'    => 'url',
		)
	);

	// Products Button Text
	$wp_customize->add_setting(
		'header_products_text',
		array(
			'default'           => __( 'Products', 'main' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'header_products_text',
		array(
			'label'   => __( 'Products Button Text', 'main' ),
			'section' => 'header_settings',
			'type'    => 'text',
		)
	);

	// Products Button Icon
	$wp_customize->add_setting(
		'header_products_icon',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'header_products_icon',
			array(
				'label'   => __( 'Products Button Icon', 'main' ),
				'section' => 'header_settings',
			)
		)
	);

	// ============================================
	// Footer Section
	// ============================================
	$wp_customize->add_section(
		'footer_settings',
		array(
			'title'    => __( 'Footer Settings', 'main' ),
			'priority' => 160,
		)
	);

	// Footer Logo
	$wp_customize->add_setting(
		'footer_logo',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'footer_logo',
			array(
				'label'   => __( 'Footer Logo', 'main' ),
				'section' => 'footer_settings',
			)
		)
	);

	// Footer Email Address
	$wp_customize->add_setting(
		'footer_email',
		array(
			'default'           => 'info@example.com',
			'sanitize_callback' => 'sanitize_email',
		)
	);
	$wp_customize->add_control(
		'footer_email',
		array(
			'label'   => __( 'Footer Email Address', 'main' ),
			'section' => 'footer_settings',
			'type'    => 'email',
		)
	);

	// Footer Copyright Text
	$wp_customize->add_setting(
		'footer_copyright_text',
		array(
			'default'           => '© CURRENT_YEAR Geekflare. All rights reserved. Geekflare&reg; is a registered trademark.',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
	$wp_customize->add_control(
		'footer_copyright_text',
		array(
			'label'   => __( 'Footer Copyright Text', 'main' ),
			'section' => 'footer_settings',
			'type'    => 'textarea',
		)
	);

	// Footer Social Icons
	$social_icons = array(
		'twitter'  => 'Twitter',
		'linkedin' => 'LinkedIn',
		'youtube'  => 'YouTube',
	);

	// Register social icon settings and controls
	foreach ( $social_icons as $key => $label ) {
		// Social Icon URL
		$wp_customize->add_setting(
			"footer_social_{$key}_url",
			array(
				'default'           => '#',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			"footer_social_{$key}_url",
			array(
				'label'   => sprintf( __( '%s URL', 'main' ), $label ),
				'section' => 'footer_settings',
				'type'    => 'url',
			)
		);

		// Social Icon Image
		$wp_customize->add_setting(
			"footer_social_{$key}_icon",
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				"footer_social_{$key}_icon",
				array(
					'label'   => sprintf( __( '%s Icon Image', 'main' ), $label ),
					'section' => 'footer_settings',
				)
			)
		);
	}

	// ============================================
	// Partners Section
	// ============================================
	$wp_customize->add_section(
		'partners_settings',
		array(
			'title'    => __( 'Partners Section', 'main' ),
			'priority' => 150,
		)
	);

	// Show Partners Section on Single Posts
	$wp_customize->add_setting(
		'partners_show_on_single',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);
	$wp_customize->add_control(
		'partners_show_on_single',
		array(
			'label'       => __( 'Show Partners Section on Single Post Pages', 'main' ),
			'description' => __( 'Display the partners section before related articles on single post pages.', 'main' ),
			'section'     => 'partners_settings',
			'type'        => 'checkbox',
		)
	);

	// Partners Section Title
	$wp_customize->add_setting(
		'partners_title',
		array(
			'default'           => __( 'Thanks to Our Partners', 'main' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'partners_title',
		array(
			'label'   => __( 'Partners Section Title', 'main' ),
			'section' => 'partners_settings',
			'type'    => 'text',
		)
	);

	// Partner Logos Repeater
	$wp_customize->add_setting(
		'partners_logos',
		array(
			'default'           => '[]',
			'sanitize_callback' => 'main_sanitize_partners_logos',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new Main_Customize_Repeater_Control(
			$wp_customize,
			'partners_logos',
			array(
				'label'       => __( 'Partner Logos', 'main' ),
				'description' => __( 'Add, remove, and reorder partner logos. Each partner needs an image, alt text, and optionally a link URL.', 'main' ),
				'section'     => 'partners_settings',
				'button_label' => __( 'Add Partner', 'main' ),
			)
		)
	);
}
add_action( 'customize_register', 'main_customize_register' );

/**
 * Get partners logos from customizer
 *
 * Retrieves and returns the partners logos array from customizer settings.
 *
 * @since 1.0.0
 *
 * @return array Array of partner logos with 'image' and 'alt' keys.
 */
/**
 * Sanitize partners logos JSON data
 *
 * @since 1.0.0
 *
 * @param string $value JSON string containing partner logos data.
 * @return string Sanitized JSON string.
 */
function main_sanitize_partners_logos( $value ) {
	$decoded = array();
	
	// Handle empty/null values
	if ( empty( $value ) ) {
		return '[]';
	}
	
	// Handle different input formats
	if ( is_array( $value ) ) {
		// Already an array - use directly
		$decoded = $value;
	} elseif ( is_string( $value ) ) {
		// Trim whitespace
		$value = trim( $value );
		
		// If empty string, return empty array
		if ( $value === '' || $value === '[]' ) {
			return '[]';
		}
		
		// Try to decode JSON string
		$decoded = json_decode( $value, true );
		
		// Check for JSON decode errors
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			// If JSON decode failed, try PHP unserialize (WordPress might serialize arrays)
			if ( is_serialized( $value, true ) ) {
				$decoded = maybe_unserialize( $value );
			} else {
				// Invalid format - return empty array
				return '[]';
			}
		}
	}
	
	// Ensure we have an array
	if ( ! is_array( $decoded ) ) {
		return '[]';
	}
	
	$sanitized = array();
	foreach ( $decoded as $partner ) {
		if ( ! is_array( $partner ) ) {
			continue;
		}
		
		$image = isset( $partner['image'] ) ? esc_url_raw( $partner['image'] ) : '';
		$alt   = isset( $partner['alt'] ) ? sanitize_text_field( $partner['alt'] ) : '';
		$url   = isset( $partner['url'] ) ? esc_url_raw( $partner['url'] ) : '';
		
		// Save all items (even without images) so repeater works properly
		$sanitized[] = array(
			'image' => $image,
			'alt'   => $alt,
			'url'   => $url,
		);
	}
	
	// Return as JSON string (consistent format)
	$json = wp_json_encode( $sanitized );
	
	// Ensure valid JSON is returned
	if ( $json === false || json_last_error() !== JSON_ERROR_NONE ) {
		return '[]';
	}
	
	return $json;
}

/**
 * Get partners logos from customizer
 *
 * Retrieves and returns the partners logos array from customizer settings.
 *
 * @since 1.0.0
 *
 * @return array Array of partner logos with 'image' and 'alt' keys.
 */
function main_get_partners_logos() {
	$logos_json = get_theme_mod( 'partners_logos', '[]' );
	$logos      = json_decode( $logos_json, true );
	
	if ( ! is_array( $logos ) ) {
		return array();
	}
	
	// Filter out items without images for display
	$filtered = array();
	foreach ( $logos as $logo ) {
		if ( ! empty( $logo['image'] ) ) {
			$filtered[] = $logo;
		}
	}
	
	return $filtered;
}
