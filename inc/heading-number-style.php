<?php
/**
 * Heading Number Style Extension
 *
 * Adds PHP filter to ensure number style attributes are applied
 * to heading blocks on the frontend.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add number style attributes to heading block output
 *
 * @param string   $block_content The block content about to be appended.
 * @param array    $parsed_block  The full block, including name and attributes.
 * @param WP_Block $block_instance The block instance.
 * @return string Modified block content.
 */
function main_heading_number_style_render( $block_content, $parsed_block, $block_instance ) {
	// Only process core/heading blocks
	if ( ! isset( $parsed_block['blockName'] ) || 'core/heading' !== $parsed_block['blockName'] ) {
		return $block_content;
	}

	// Ensure content is a string
	if ( ! is_string( $block_content ) || empty( $block_content ) ) {
		return $block_content;
	}

	$attributes = isset( $parsed_block['attrs'] ) ? $parsed_block['attrs'] : array();
	$has_number_style = isset( $attributes['hasNumberStyle'] ) && $attributes['hasNumberStyle'];
	$number_value     = isset( $attributes['numberValue'] ) ? sanitize_text_field( $attributes['numberValue'] ) : '';

	if ( ! $has_number_style ) {
		return $block_content;
	}

	// Parse the HTML content
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	
	// Wrap content in a container to handle fragments properly
	$html = '<!DOCTYPE html><html><body>' . $block_content . '</body></html>';
	@$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();

	// Find the heading element (h1, h2, h3, etc.)
	$xpath = new DOMXPath( $dom );
	$headings = $xpath->query( '//h1 | //h2 | //h3 | //h4 | //h5 | //h6' );

	if ( $headings->length > 0 ) {
		$heading = $headings->item( 0 );

		// Add class
		$existing_class = $heading->getAttribute( 'class' );
		if ( strpos( $existing_class, 'has_number_style' ) === false ) {
			$new_class = $existing_class
				? $existing_class . ' has_number_style'
				: 'has_number_style';
			$heading->setAttribute( 'class', $new_class );
		}

		// Add data attribute
		if ( ! empty( $number_value ) ) {
			$heading->setAttribute( 'data-number', esc_attr( $number_value ) );
		}

		// Get modified HTML from body
		$body = $dom->getElementsByTagName( 'body' )->item( 0 );
		if ( $body ) {
			$block_content = '';
			foreach ( $body->childNodes as $node ) {
				$block_content .= $dom->saveHTML( $node );
			}
		} else {
			// Fallback: get the heading element directly
			$block_content = $dom->saveHTML( $heading );
		}
	}

	// Ensure we return a string
	return is_string( $block_content ) ? trim( $block_content ) : '';
}
add_filter( 'render_block', 'main_heading_number_style_render', 10, 3 );

