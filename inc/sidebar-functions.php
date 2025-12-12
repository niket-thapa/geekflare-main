<?php
/**
 * Sidebar Functions (Final Fix)
 *
 * Functions for generating dynamic sidebars with auto TOC.
 * Fixed: Skip H3s inside #products, prevent duplicates.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Sidebar Meta Fields
 *
 * @since 1.0.0
 */
function main_register_sidebar_meta() {
	// Show TOC toggle
	register_post_meta(
		'post',
		'show_sidebar_toc',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	// Show Filters toggle (Buying Guide only)
	register_post_meta(
		'post',
		'show_sidebar_filters',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	// Custom HTML for Info template
	register_post_meta(
		'post',
		'sidebar_custom_html',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'main_register_sidebar_meta' );

/**
 * Extract Headings from Post Content (Final Fix)
 *
 * Parses post content and extracts H2 and H3 headings for TOC.
 * Also extracts custom blocks and product names for nested structure.
 * MAINTAINS DOCUMENT ORDER by tracking position of each element.
 *
 * For Buying Guide template: includes products with nested product items.
 * For Info template: excludes products section entirely.
 *
 * @since 1.0.0
 * @param int $post_id Post ID.
 * @return array Array of headings with ID, level, text, and optional children.
 */
function main_get_post_headings( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return array();
	}

	// Get raw content
	$content = $post->post_content;

	// Remove shortcodes
	$content = strip_shortcodes( $content );

	// Apply content filters to get rendered HTML
	$content = apply_filters( 'the_content', $content );

	// Parse HTML
	libxml_use_internal_errors( true );
	$dom = new DOMDocument();
	$dom->loadHTML( 
		'<?xml encoding="UTF-8">' . $content,
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD 
	);
	libxml_clear_errors();

	$xpath = new DOMXPath( $dom );
	
	$toc_items = array(); // Will store items with position for sorting
	$used_ids = array();
	$position_counter = 0;

	// Helper function to get node position in document
	$get_node_position = function( $node ) use ( $xpath ) {
		// Get all preceding nodes to determine position
		$preceding = $xpath->query( 'preceding::*', $node );
		return $preceding->length;
	};

	// Check if this is a buying guide template
	$is_buying_guide = function_exists( 'main_is_buying_guide_template' ) && main_is_buying_guide_template( $post_id );

	// Define custom blocks to look for
	$custom_block_selectors = array(
		'why_trust_us'       => array( 'type' => 'class', 'default_text' => 'Why Trust Us' ),
		'final_verdict'      => array( 'type' => 'class', 'default_text' => 'Final Verdict' ),
		'honorable-mentions' => array( 'type' => 'id', 'default_text' => 'Honorable Mentions' ),
	);

	// Only add products with nested items for buying guide template
	if ( $is_buying_guide ) {
		$custom_block_selectors['products'] = array( 'type' => 'id', 'default_text' => 'Products', 'has_children' => true );
	}

	// Collect all custom blocks with their positions
	$custom_block_nodes = array();
	foreach ( $custom_block_selectors as $selector => $config ) {
		$block = null;
		
		if ( $config['type'] === 'class' ) {
			$query = "//*[contains(concat(' ', normalize-space(@class), ' '), ' {$selector} ')]";
			$elements = $xpath->query( $query );
			if ( $elements->length > 0 ) {
				$block = $elements->item( 0 );
			}
		} else {
			$elements = $xpath->query( "//*[@id='{$selector}']" );
			if ( $elements->length > 0 ) {
				$block = $elements->item( 0 );
			}
		}

		if ( $block ) {
			$custom_block_nodes[ $selector ] = array(
				'node'     => $block,
				'config'   => $config,
				'position' => $get_node_position( $block ),
			);
		}
	}

	// Get all H2 and H3 elements
	$h_tags = $xpath->query( '//h2 | //h3' );

	foreach ( $h_tags as $heading ) {
		$text = trim( $heading->textContent );
		
		if ( empty( $text ) ) {
			continue;
		}

		// Check if this heading is inside a custom block we handle separately
		$parent = $heading->parentNode;
		$should_skip = false;
		
		while ( $parent ) {
			if ( $parent->nodeType === XML_ELEMENT_NODE ) {
				$parent_class = $parent->getAttribute( 'class' );
				$parent_id = $parent->getAttribute( 'id' );
				
				// Always skip headings inside #products div (regardless of template type)
				if ( $parent_id === 'products' ) {
					$should_skip = true;
					break;
				}
				
				// Skip headings inside custom blocks
				foreach ( $custom_block_selectors as $selector => $config ) {
					if ( $config['type'] === 'class' && strpos( $parent_class, $selector ) !== false ) {
						$should_skip = true;
						break 2;
					}
					if ( $config['type'] === 'id' && $parent_id === $selector ) {
						$should_skip = true;
						break 2;
					}
				}
			}
			$parent = $parent->parentNode;
		}

		// Skip if inside custom block or products div
		if ( $should_skip ) {
			continue;
		}

		// Get or create ID
		$id = $heading->getAttribute( 'id' );
		if ( empty( $id ) ) {
			$id = sanitize_title( $text );
		}

		// Ensure unique ID
		$base_id = $id;
		$counter = 1;
		while ( in_array( $id, $used_ids ) ) {
			$id = $base_id . '-' . $counter;
			$counter++;
		}
		$used_ids[] = $id;

		$level = (int) $heading->tagName[1]; // h2 = 2, h3 = 3

		$toc_items[] = array(
			'id'       => $id,
			'text'     => $text,
			'level'    => $level,
			'children' => array(),
			'position' => $get_node_position( $heading ),
		);
	}

	// Add custom blocks to TOC items
	foreach ( $custom_block_nodes as $selector => $data ) {
		$block = $data['node'];
		$config = $data['config'];
		$position = $data['position'];

		// Find first H2 inside this block for the title
		$h2 = $xpath->query( './/h2', $block );
		
		if ( $h2->length > 0 ) {
			$heading_text = trim( $h2->item( 0 )->textContent );
		} else {
			$heading_text = $config['default_text'];
		}

		// Get or create ID for the block
		$block_id = $block->getAttribute( 'id' );
		if ( empty( $block_id ) ) {
			$block_id = $selector;
		}

		// Ensure unique ID
		$base_id = $block_id;
		$counter = 1;
		while ( in_array( $block_id, $used_ids ) ) {
			$block_id = $base_id . '-' . $counter;
			$counter++;
		}
		$used_ids[] = $block_id;

		// Get children for products
		$children = array();
		if ( ! empty( $config['has_children'] ) && $selector === 'products' ) {
			$children = main_get_product_items( $dom, $xpath );
		}

		$toc_items[] = array(
			'id'       => $block_id,
			'text'     => $heading_text,
			'level'    => 2,
			'children' => $children,
			'position' => $position,
		);
	}

	// Sort by position to maintain document order
	usort( $toc_items, function( $a, $b ) {
		return $a['position'] - $b['position'];
	} );

	// Remove position key from final output
	$headings = array_map( function( $item ) {
		unset( $item['position'] );
		return $item;
	}, $toc_items );

	return $headings;
}

/**
 * Get Product Items for Nested TOC
 *
 * Extracts product names ONLY from .buying_guide_item elements inside #products.
 *
 * @since 1.0.0
 * @param DOMDocument $dom DOM document.
 * @param DOMXPath    $xpath XPath object.
 * @return array Array of product items.
 */
function main_get_product_items( $dom, $xpath ) {
	$products = array();
	
	// Find all .buying_guide_item elements ONLY inside #products
	$query = "//*[@id='products']//*[contains(concat(' ', normalize-space(@class), ' '), ' buying_guide_item ')]";
	$items = $xpath->query( $query );

	foreach ( $items as $item ) {
		// Find h3 inside .product-name-wrap within this specific item
		$h3_query = ".//*[contains(concat(' ', normalize-space(@class), ' '), ' product-name-wrap ')]//h3";
		$h3_elements = $xpath->query( $h3_query, $item );

		if ( $h3_elements->length > 0 ) {
			$h3 = $h3_elements->item( 0 );
			$product_name = trim( $h3->textContent );

			// Get or create ID
			$product_id = $h3->getAttribute( 'id' );
			if ( empty( $product_id ) ) {
				$product_id = sanitize_title( $product_name );
			}

			$products[] = array(
				'id'   => $product_id,
				'text' => $product_name,
			);
		}
	}

	return $products;
}

/**
 * Add IDs to Headings and Custom Blocks in Content
 *
 * Ensures all H2, H3 tags and custom blocks have IDs for anchor links.
 *
 * @since 1.0.0
 * @param string $content Post content.
 * @return string Modified content with IDs.
 */
function main_add_heading_ids( $content ) {
	if ( ! is_singular( 'post' ) ) {
		return $content;
	}

	// Parse HTML
	libxml_use_internal_errors( true );
	$dom = new DOMDocument();
	$dom->loadHTML(
		'<?xml encoding="UTF-8">' . $content,
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);
	libxml_clear_errors();

	$xpath = new DOMXPath( $dom );
	$used_ids = array();

	// Add IDs to H2 and H3
	$h_tags = $xpath->query( '//h2 | //h3' );
	foreach ( $h_tags as $heading ) {
		if ( ! $heading->hasAttribute( 'id' ) ) {
			$text = trim( $heading->textContent );
			if ( ! empty( $text ) ) {
				$id = sanitize_title( $text );
				$base_id = $id;
				$counter = 1;
				
				while ( in_array( $id, $used_ids ) ) {
					$id = $base_id . '-' . $counter;
					$counter++;
				}

				$heading->setAttribute( 'id', $id );
				$used_ids[] = $id;
			}
		} else {
			$used_ids[] = $heading->getAttribute( 'id' );
		}
	}

	// Add IDs to custom blocks
	$custom_blocks = array(
		'why_trust_us',
		'final_verdict'
	);

	foreach ( $custom_blocks as $class_name ) {
		$query = "//*[contains(concat(' ', normalize-space(@class), ' '), ' {$class_name} ')]";
		$elements = $xpath->query( $query );

		foreach ( $elements as $element ) {
			if ( ! $element->hasAttribute( 'id' ) ) {
				$id = $class_name;
				$base_id = $id;
				$counter = 1;
				
				while ( in_array( $id, $used_ids ) ) {
					$id = $base_id . '-' . $counter;
					$counter++;
				}

				$element->setAttribute( 'id', $id );
				$used_ids[] = $id;
			} else {
				$used_ids[] = $element->getAttribute( 'id' );
			}
		}
	}

	// Add IDs to product H3s inside #products
	$product_h3s = $xpath->query( "//*[@id='products']//*[contains(concat(' ', normalize-space(@class), ' '), ' product-name-wrap ')]//h3" );
	foreach ( $product_h3s as $h3 ) {
		if ( ! $h3->hasAttribute( 'id' ) ) {
			$text = trim( $h3->textContent );
			if ( ! empty( $text ) ) {
				$id = sanitize_title( $text );
				$base_id = $id;
				$counter = 1;
				
				while ( in_array( $id, $used_ids ) ) {
					$id = $base_id . '-' . $counter;
					$counter++;
				}

				$h3->setAttribute( 'id', $id );
				$used_ids[] = $id;
			}
		} else {
			$used_ids[] = $h3->getAttribute( 'id' );
		}
	}

	// Save modified HTML
	$content = $dom->saveHTML();
	
	// Remove XML declaration
	$content = preg_replace( '/^<\?xml[^>]+\?>/', '', $content );
	
	return $content;
}
add_filter( 'the_content', 'main_add_heading_ids', 10 );

/**
 * Check if Sidebar Should Show TOC
 *
 * @since 1.0.0
 * @param int $post_id Post ID.
 * @return bool
 */
function main_sidebar_show_toc( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$show_toc = get_post_meta( $post_id, 'show_sidebar_toc', true );
	
	// Default to true if not set
	return ( $show_toc !== false ) ? (bool) $show_toc : true;
}

/**
 * Check if Sidebar Should Show Filters
 *
 * @since 1.0.0
 * @param int $post_id Post ID.
 * @return bool
 */
function main_sidebar_show_filters( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$show_filters = get_post_meta( $post_id, 'show_sidebar_filters', true );
	
	// Default to true if not set
	return ( $show_filters !== false ) ? (bool) $show_filters : true;
}

/**
 * Get Sidebar Custom HTML
 *
 * @since 1.0.0
 * @param int $post_id Post ID.
 * @return string
 */
function main_get_sidebar_custom_html( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return get_post_meta( $post_id, 'sidebar_custom_html', true );
}

/**
 * Enqueue TOC Generator JavaScript
 *
 * @since 1.0.0
 */
function main_enqueue_toc_generator() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$script_path = MAIN_THEME_DIR . '/assets/js/toc-generator.js';
	
	if ( file_exists( $script_path ) ) {
		wp_enqueue_script(
			'main-toc-generator',
			MAIN_THEME_URI . '/assets/js/toc-generator.js',
			array(),
			filemtime( $script_path ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'main_enqueue_toc_generator' );

/**
 * Enqueue Sidebar Settings in Editor
 *
 * @since 1.0.0
 */
function main_enqueue_sidebar_settings() {
	$screen = get_current_screen();
	if ( ! $screen || ! $screen->is_block_editor() || 'post' !== $screen->post_type ) {
		return;
	}

	$script_path = MAIN_THEME_DIR . '/dist/sidebar-settings.js';
	if ( ! file_exists( $script_path ) ) {
		return;
	}

	wp_enqueue_script(
		'main-sidebar-settings',
		MAIN_THEME_URI . '/dist/sidebar-settings.js',
		array(
			'wp-plugins',
			'wp-edit-post',
			'wp-element',
			'wp-components',
			'wp-data',
			'wp-i18n',
			'wp-core-data',
		),
		filemtime( $script_path ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'main_enqueue_sidebar_settings' );