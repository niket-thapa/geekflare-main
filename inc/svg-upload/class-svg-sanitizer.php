<?php
/**
 * SVG Sanitizer Class
 *
 * Custom implementation for sanitizing SVG files to prevent security vulnerabilities.
 * Removes dangerous elements, attributes, and scripts from SVG files.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SVG_Sanitizer
 *
 * Handles sanitization of SVG files to ensure security.
 */
class SVG_Sanitizer {

	/**
	 * Allowed SVG tags
	 *
	 * @var array
	 */
	private $allowed_tags = array(
		'svg',
		'g',
		'path',
		'circle',
		'rect',
		'ellipse',
		'line',
		'polyline',
		'polygon',
		'text',
		'tspan',
		'defs',
		'linearGradient',
		'radialGradient',
		'stop',
		'clipPath',
		'mask',
		'pattern',
		'image',
		'use',
		'symbol',
		'title',
		'desc',
		'metadata',
		'style',
		'foreignObject',
	);

	/**
	 * Allowed SVG attributes
	 *
	 * @var array
	 */
	private $allowed_attributes = array(
		'class',
		'id',
		'fill',
		'stroke',
		'stroke-width',
		'stroke-linecap',
		'stroke-linejoin',
		'stroke-dasharray',
		'stroke-dashoffset',
		'stroke-miterlimit',
		'opacity',
		'fill-opacity',
		'stroke-opacity',
		'transform',
		'd',
		'cx',
		'cy',
		'r',
		'rx',
		'ry',
		'x',
		'y',
		'x1',
		'y1',
		'x2',
		'y2',
		'width',
		'height',
		'viewBox',
		'xmlns',
		'xmlns:xlink',
		'points',
		'offset',
		'stop-color',
		'stop-opacity',
		'gradientTransform',
		'gradientUnits',
		'spreadMethod',
		'xlink:href',
		'href',
		'preserveAspectRatio',
		'version',
		'xml:space',
		'font-family',
		'font-size',
		'font-weight',
		'text-anchor',
		'dominant-baseline',
		'letter-spacing',
		'word-spacing',
		'text-decoration',
		'clip-path',
		'mask',
		'filter',
		'style',
		'role',
		'aria-label',
		'aria-labelledby',
		'aria-hidden',
	);

	/**
	 * Sanitize SVG content
	 *
	 * @param string $svg_content The SVG content to sanitize.
	 * @return string|false Sanitized SVG content or false on failure.
	 */
	public function sanitize( $svg_content ) {
		if ( empty( $svg_content ) ) {
			return false;
		}

		// Check if content is gzipped
		$is_gzipped = $this->is_gzipped( $svg_content );
		if ( $is_gzipped ) {
			$svg_content = gzdecode( $svg_content );
			if ( false === $svg_content ) {
				return false;
			}
		}

		// Remove dangerous content using regex (first pass)
		$svg_content = $this->remove_dangerous_content( $svg_content );

		// Use DOMDocument for more thorough sanitization if available
		if ( class_exists( 'DOMDocument' ) && function_exists( 'libxml_use_internal_errors' ) ) {
			$svg_content = $this->sanitize_with_dom( $svg_content );
		}

		// Final cleanup
		$svg_content = $this->final_cleanup( $svg_content );

		// Re-zip if it was originally gzipped
		if ( $is_gzipped ) {
			$svg_content = gzencode( $svg_content );
		}

		return $svg_content;
	}

	/**
	 * Remove dangerous content using regex patterns
	 *
	 * @param string $content SVG content.
	 * @return string Cleaned content.
	 */
	private function remove_dangerous_content( $content ) {
		// Remove script tags and their content
		$content = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $content );

		// Remove event handlers (onclick, onload, etc.)
		$content = preg_replace( '/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $content );
		$content = preg_replace( '/\s*on\w+\s*=\s*[^\s>]*/i', '', $content );

		// Remove javascript: protocol in href and xlink:href
		$content = preg_replace( '/\s*(?:xlink:)?href\s*=\s*["\']javascript:[^"\']*["\']/i', '', $content );

		// Remove data URIs that might contain scripts
		$content = preg_replace( '/\s*(?:xlink:)?href\s*=\s*["\']data:text\/html[^"\']*["\']/i', '', $content );

		// Remove DOCTYPE declarations (can be used for XXE attacks)
		$content = preg_replace( '/<!DOCTYPE[^>]*>/i', '', $content );

		// Remove XML entity declarations
		$content = preg_replace( '/<!ENTITY[^>]*>/i', '', $content );

		// Remove XML processing instructions
		$content = preg_replace( '/<\?xml-stylesheet[^>]*\?>/i', '', $content );

		// Remove iframe and embed tags
		$content = preg_replace( '/<(iframe|embed|object|applet)\b[^>]*>(.*?)<\/\1>/is', '', $content );

		// Remove base64 encoded data URIs in style attributes (potential XSS)
		$content = preg_replace( '/style\s*=\s*["\'][^"\']*data:[^"\']*base64[^"\']*["\']/i', '', $content );

		return $content;
	}

	/**
	 * Sanitize using DOMDocument for more thorough cleaning
	 *
	 * @param string $content SVG content.
	 * @return string Sanitized content.
	 */
	private function sanitize_with_dom( $content ) {
		libxml_use_internal_errors( true );
		libxml_clear_errors();

		// Store original XML declaration if present (handle with or without whitespace)
		$xml_declaration = '';
		$content_trimmed = trim( $content );
		if ( preg_match( '/^<\?xml[^>]*\?>/', $content_trimmed, $matches ) ) {
			$xml_declaration = trim( $matches[0] );
		}
		
		// Remove existing XML declaration for processing (handle various formats)
		$content_without_decl = preg_replace( '/^<\?xml[^>]*\?>\s*/i', '', $content_trimmed );

		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$dom->formatOutput = false;
		$dom->preserveWhiteSpace = false;
		$dom->substituteEntities = false;

		// Load SVG - suppress warnings for malformed XML
		$loaded = @$dom->loadXML( $content_without_decl, LIBXML_NOENT | LIBXML_NONET | LIBXML_DTDLOAD | LIBXML_DTDATTR );

		if ( ! $loaded || ! $dom->documentElement ) {
			libxml_clear_errors();
			// Return original content if DOM parsing fails
			return $content;
		}

		// Get filtered allowed tags and attributes
		$allowed_tags = $this->get_allowed_tags();
		$allowed_attrs = $this->get_allowed_attributes();

		// Convert allowed tags and attributes to lowercase for case-insensitive comparison
		$allowed_tags_lower = array_map( 'strtolower', $allowed_tags );
		$allowed_attrs_lower = array_map( 'strtolower', $allowed_attrs );

		// Get all elements
		$xpath = new DOMXPath( $dom );
		$all_elements = $xpath->query( '//*' );

		$elements_to_remove = array();

		foreach ( $all_elements as $element ) {
			$tag_name = strtolower( $element->tagName );

			// Remove disallowed tags (case-insensitive comparison)
			if ( ! in_array( $tag_name, $allowed_tags_lower, true ) ) {
				$elements_to_remove[] = $element;
				continue;
			}

			// Check and remove disallowed attributes
			if ( $element->hasAttributes() ) {
				$attrs_to_remove = array();
				foreach ( $element->attributes as $attr ) {
					$attr_name = strtolower( $attr->name );
					$attr_name_original = $attr->name; // Keep original for removal

					// Remove namespaced attributes except xmlns and xlink
					if ( strpos( $attr_name, ':' ) !== false ) {
						$namespace = explode( ':', $attr_name )[0];
						if ( ! in_array( $namespace, array( 'xmlns', 'xlink' ), true ) ) {
							$attrs_to_remove[] = $attr_name_original;
							continue;
						}
					}

					// Remove disallowed attributes (case-insensitive comparison)
					if ( ! in_array( $attr_name, $allowed_attrs_lower, true ) ) {
						$attrs_to_remove[] = $attr_name_original;
					}

					// Check attribute values for dangerous content
					$attr_value = $attr->value;
					if ( preg_match( '/javascript:/i', $attr_value ) ||
						 preg_match( '/data:text\/html/i', $attr_value ) ||
						 preg_match( '/on\w+\s*=/i', $attr_value ) ) {
						$attrs_to_remove[] = $attr->name;
					}
				}

				foreach ( $attrs_to_remove as $attr_name ) {
					$element->removeAttribute( $attr_name );
				}
			}
		}

		// Remove disallowed elements
		foreach ( $elements_to_remove as $element ) {
			if ( $element->parentNode ) {
				$element->parentNode->removeChild( $element );
			}
		}

		// Extract just the SVG element (without XML declaration)
		$sanitized = $dom->saveXML( $dom->documentElement );

		libxml_clear_errors();

		// Ensure we have a proper SVG structure
		if ( empty( $sanitized ) || strpos( $sanitized, '<svg' ) === false ) {
			return $content; // Return original if sanitization failed
		}

		// Handle XML declaration - normalize it to be properly formatted
		if ( ! empty( $xml_declaration ) ) {
			// Check if declaration has version attribute
			if ( preg_match( '/version\s*=\s*["\']([^"\']+)["\']/', $xml_declaration ) ) {
				// Check if it also has encoding
				$has_encoding = preg_match( '/encoding\s*=\s*["\']([^"\']+)["\']/', $xml_declaration );
				if ( $has_encoding ) {
					// Has both, use as-is but ensure proper format
					$sanitized = trim( $xml_declaration ) . "\n" . $sanitized;
				} else {
					// Has version but no encoding, add encoding
					$sanitized = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $sanitized;
				}
			} else {
				// Original declaration was malformed (missing version), use a proper one
				$sanitized = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $sanitized;
			}
		}
		// If no original declaration, don't add one (SVG works fine without it in browsers)

		return $sanitized;
	}

	/**
	 * Final cleanup pass
	 *
	 * @param string $content SVG content.
	 * @return string Cleaned content.
	 */
	private function final_cleanup( $content ) {
		// Remove any remaining script-like content
		$content = preg_replace( '/<script[^>]*>/i', '', $content );
		$content = preg_replace( '/<\/script>/i', '', $content );

		// Remove any remaining event handlers
		$content = preg_replace( '/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $content );

		// Ensure proper SVG structure
		if ( strpos( $content, '<svg' ) === false ) {
			return false;
		}

		// Minify whitespace (optional)
		$content = preg_replace( '/\s+/', ' ', $content );
		$content = preg_replace( '/>\s+</', '><', $content );

		return trim( $content );
	}

	/**
	 * Check if content is gzipped
	 *
	 * @param string $content Content to check.
	 * @return bool True if gzipped.
	 */
	private function is_gzipped( $content ) {
		if ( function_exists( 'mb_strpos' ) ) {
			return 0 === mb_strpos( $content, "\x1f\x8b\x08" );
		}
		return 0 === strpos( $content, "\x1f\x8b\x08" );
	}

	/**
	 * Get allowed tags (for filtering)
	 *
	 * @return array Allowed tags.
	 */
	public function get_allowed_tags() {
		/**
		 * Filter allowed SVG tags
		 *
		 * @param array $tags Array of allowed SVG tag names.
		 * @return array
		 */
		return apply_filters( 'main_theme_svg_allowed_tags', $this->allowed_tags );
	}

	/**
	 * Get allowed attributes (for filtering)
	 *
	 * @return array Allowed attributes.
	 */
	public function get_allowed_attributes() {
		/**
		 * Filter allowed SVG attributes
		 *
		 * @param array $attributes Array of allowed SVG attribute names.
		 * @return array
		 */
		return apply_filters( 'main_theme_svg_allowed_attributes', $this->allowed_attributes );
	}
}

