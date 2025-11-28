<?php
/**
 * Custom Blocks Registration and Editor Assets
 *
 * Handles the registration of custom Gutenberg blocks and ensures
 * proper styling and functionality in the block editor.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom block category
 *
 * Adds a custom "Main" category to the block inserter for better organization.
 *
 * @since 1.0.0
 *
 * @param array                   $categories Array of block categories.
 * @param WP_Block_Editor_Context $editor_context The current block editor context.
 * @return array Modified array of block categories.
 */
function main_block_categories( $categories, $editor_context ) {
	// Add category in all block editor contexts
	array_unshift(
		$categories,
		array(
			'slug'  => 'main',
			'title' => __( 'Main', 'main' ),
			'icon'  => null,
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'main_block_categories', 10, 2 );

/**
 * Register custom blocks
 *
 * Automatically discovers and registers all custom blocks in the blocks/ directory.
 * Each block folder must contain a block.json file.
 *
 * @since 1.0.0
 * @return void
 */
function main_register_blocks() {
	// Get all block folders in the blocks/ directory
	$blocks_dir    = get_stylesheet_directory() . '/blocks';
	$block_folders = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

	if ( empty( $block_folders ) ) {
		return;
	}

	// Register each block folder
	foreach ( $block_folders as $block_folder ) {
		$block_json = $block_folder . '/block.json';
		$block_name = basename( $block_folder );

		// Verify block.json exists
		if ( ! file_exists( $block_json ) ) {
			continue;
		}

		// Read block.json to get block name
		$json_content = file_get_contents( $block_json );
		$json_data    = json_decode( $json_content, true );

		if ( ! $json_data || ! isset( $json_data['name'] ) ) {
			continue;
		}

		$full_block_name = $json_data['name'];
		$script_handle   = 'main-' . str_replace( '/', '-', $full_block_name );

		// Register editor script (plain JS, no ES6 imports)
		$editor_script = $block_folder . '/editor.js';

		if ( file_exists( $editor_script ) ) {
			// Determine dependencies based on block
			$editor_deps = array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' );

			// Add api-fetch for blocks that need it
			$blocks_needing_api = array(
				'explore-categories',
				'meet-experts',
				'insights-section',
				'articles-section',
				'article-carousel',
				'summary-table',
				'product-list',
				'product-item',
				'honorable-mentions',
				'awards',
			);
			if ( in_array( $block_name, $blocks_needing_api, true ) ) {
				$editor_deps[] = 'wp-api-fetch';
			}

			// Add wp-data for blocks that need it
			$blocks_needing_data = array(
				'product-item',
			);
			if ( in_array( $block_name, $blocks_needing_data, true ) ) {
				$editor_deps[] = 'wp-data';
			}

			wp_register_script(
				$script_handle . '-editor',
				get_stylesheet_directory_uri() . '/blocks/' . $block_name . '/' . basename( $editor_script ),
				$editor_deps,
				filemtime( $editor_script ),
				false
			);

			// Localize theme URL for meet-experts block editor script
			if ( 'meet-experts' === $block_name ) {
				wp_localize_script(
					$script_handle . '-editor',
					'mainTheme',
					array(
						'imageUrl' => get_stylesheet_directory_uri() . '/assets/images/',
					)
				);
			}
		}

		// Register editor style if editor.css exists
		$editor_style = $block_folder . '/editor.css';
		if ( file_exists( $editor_style ) ) {
			wp_register_style(
				$script_handle . '-editor',
				get_stylesheet_directory_uri() . '/blocks/' . $block_name . '/editor.css',
				array( 'wp-edit-blocks', 'main-style-editor' ),
				filemtime( $editor_style ),
				'all'
			);
		}

		// Register frontend style if style.css exists
		$frontend_style = $block_folder . '/style.css';
		if ( file_exists( $frontend_style ) ) {
			wp_register_style(
				$script_handle . '-style',
				get_stylesheet_directory_uri() . '/blocks/' . $block_name . '/style.css',
				array(),
				filemtime( $frontend_style )
			);
		}

		// Register view script if view.js exists
		$view_script = $block_folder . '/view.js';
		if ( file_exists( $view_script ) ) {
			wp_register_script(
				$script_handle . '-view',
				get_stylesheet_directory_uri() . '/blocks/' . $block_name . '/view.js',
				array(),
				filemtime( $view_script ),
				true
			);
		}

		// Register the block with manually registered assets
		$block_args = array();
		if ( file_exists( $editor_script ) ) {
			$block_args['editor_script'] = $script_handle . '-editor';
		}
		if ( file_exists( $editor_style ) ) {
			$block_args['editor_style'] = $script_handle . '-editor';
		}
		if ( file_exists( $frontend_style ) ) {
			$block_args['style'] = $script_handle . '-style';
		}
		if ( file_exists( $view_script ) ) {
			$block_args['view_script'] = $script_handle . '-view';
		}

		// Register the block (block.json will be read automatically)
		register_block_type( $block_folder, $block_args );
	}
}
add_action( 'init', 'main_register_blocks', 10 );

/**
 * Enqueue block editor assets
 *
 * Makes main theme styles available in the block editor so Tailwind classes work.
 * Also initializes Flickity carousels in the editor iframe.
 * Priority 999 ensures our styles load AFTER WordPress core editor styles.
 *
 * @since 1.0.0
 * @return void
 */
function main_enqueue_block_editor_assets() {
	// Enqueue fonts CSS first in editor
	wp_enqueue_style(
		'main-fonts-editor',
		get_stylesheet_directory_uri() . '/src/css/fonts.css',
		array( 'wp-edit-blocks' ),
		wp_get_theme()->get( 'Version' ),
		'all'
	);

	// Enqueue Flickity CSS in editor (before main-style so it can be overridden)
	wp_enqueue_style(
		'main-flickity-editor',
		get_stylesheet_directory_uri() . '/dist/flickity.css',
		array( 'wp-edit-blocks', 'main-fonts-editor' ),
		wp_get_theme()->get( 'Version' ),
		'all'
	);

	// Enqueue main theme styles in editor so blocks can use Tailwind classes
	// This ensures styles are available in the editor iframe
	// Use 'all' media to ensure responsive classes work in editor
	// Load with high priority to override WordPress core styles and Flickity
	wp_enqueue_style(
		'main-style-editor',
		get_stylesheet_directory_uri() . '/dist/style.css',
		array( 'wp-edit-blocks', 'main-fonts-editor', 'main-flickity-editor' ),
		wp_get_theme()->get( 'Version' ),
		'all'
	);

	// Enqueue main theme JavaScript in editor (includes custom-flickity.js)
	// This enables Flickity carousels to work in the editor iframe
	wp_enqueue_script(
		'main-script-editor',
		get_stylesheet_directory_uri() . '/dist/script.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);


	// Add inline style to ensure theme-main class has highest specificity
	wp_add_inline_style(
		'main-style-editor',
		'
		/* Higher specificity for editor previews */
		.editor-styles-wrapper .theme-main,
		.block-editor-block-preview .theme-main,
		.block-editor-block-list__layout .theme-main {
			/* All theme styles apply with highest priority */
		}
		
		/* Pullquote block wrapper styles for editor - matching frontend styles */
		.editor-styles-wrapper .site-main .wp-block-pullquote,
		.block-editor-block-preview .site-main .wp-block-pullquote,
		.editor-styles-wrapper .wp-block-pullquote,
		.block-editor-block-preview .wp-block-pullquote {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: flex-start;
			padding: 1.25rem;
			gap: 0.5rem;
			background-color: rgb(255 255 255);
			border-width: 1px;
			border-style: solid;
			border-color: rgb(233 234 235);
			border-radius: 1rem;
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper .site-main .wp-block-pullquote,
			.block-editor-block-preview .site-main .wp-block-pullquote,
			.editor-styles-wrapper .wp-block-pullquote,
			.block-editor-block-preview .wp-block-pullquote {
				gap: 0.75rem;
			}
		}
		
		/* Pullquote blockquote wrapper styles for editor */
		.editor-styles-wrapper .site-main .wp-block-pullquote blockquote,
		.block-editor-block-preview .site-main .wp-block-pullquote blockquote,
		.editor-styles-wrapper .wp-block-pullquote blockquote,
		.block-editor-block-preview .wp-block-pullquote blockquote {
			border: none;
			padding: 0;
			margin: 0;
			font-size: 0.875rem;
			font-weight: 500;
			line-height: 1.25rem;
			letter-spacing: 0.02em;
			color: rgb(37 43 55);
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper .site-main .wp-block-pullquote blockquote,
			.block-editor-block-preview .site-main .wp-block-pullquote blockquote,
			.editor-styles-wrapper .wp-block-pullquote blockquote,
			.block-editor-block-preview .wp-block-pullquote blockquote {
				font-size: 1rem;
			}
		}
		
		/* Pullquote block styles for editor - matching frontend styles */
		.editor-styles-wrapper .wp-block-pullquote blockquote p,
		.block-editor-block-preview .wp-block-pullquote blockquote p,
		.editor-styles-wrapper .site-main .wp-block-pullquote blockquote p,
		.block-editor-block-preview .site-main .wp-block-pullquote blockquote p {
			margin: 0;
			font-style: italic;
		}
		
		.editor-styles-wrapper .wp-block-pullquote blockquote cite,
		.block-editor-block-preview .wp-block-pullquote blockquote cite,
		.editor-styles-wrapper .site-main .wp-block-pullquote blockquote cite,
		.block-editor-block-preview .site-main .wp-block-pullquote blockquote cite {
			font-style: normal;
			font-size: 0.75rem;
			text-transform: none;
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper .wp-block-pullquote blockquote cite,
			.block-editor-block-preview .wp-block-pullquote blockquote cite,
			.editor-styles-wrapper .site-main .wp-block-pullquote blockquote cite,
			.block-editor-block-preview .site-main .wp-block-pullquote blockquote cite {
				font-size: 0.875rem;
			}
		}
		
		.editor-styles-wrapper .wp-block-pullquote blockquote cite:before,
		.block-editor-block-preview .wp-block-pullquote blockquote cite:before,
		.editor-styles-wrapper .site-main .wp-block-pullquote blockquote cite:before,
		.block-editor-block-preview .site-main .wp-block-pullquote blockquote cite:before {
			content: "—";
		}
	'
	);

	// Add inline script to ensure responsive breakpoints work in editor iframe
	// Also initialize Flickity carousels inside the editor iframe
	// Check if wp-blocks is registered before adding inline script
	if ( wp_script_is( 'wp-blocks', 'registered' ) || wp_script_is( 'wp-blocks', 'enqueued' ) ) {
		wp_add_inline_script(
			'wp-blocks',
			'
		(function() {
			// Wait for editor to be ready
			if (typeof wp !== "undefined" && wp.domReady) {
				wp.domReady(function() {
					var flickityLibUrl = "' . esc_js( get_stylesheet_directory_uri() . '/dist/flickity.pkgd.min.js' ) . '";
					
					// Function to find and access editor iframe
					function getEditorIframe() {
						return document.querySelector("iframe[name=\'editor-canvas\']") || 
						       document.querySelector("iframe.editor-canvas__iframe") ||
						       document.querySelector(".block-editor-iframe__container iframe") ||
						       document.querySelector("iframe.iso-editor-canvas");
					}
					
					// Function to ensure viewport meta exists in editor iframe
					function ensureEditorViewport(iframeDoc) {
						if (!iframeDoc || !iframeDoc.head) return;
						
						var iframeHead = iframeDoc.head;
						var existingViewport = iframeHead.querySelector("meta[name=\'viewport\']");
						
						if (!existingViewport) {
							var viewport = iframeDoc.createElement("meta");
							viewport.name = "viewport";
							viewport.content = "width=device-width, initial-scale=1";
							iframeHead.appendChild(viewport);
						} else {
							existingViewport.content = "width=device-width, initial-scale=1";
						}
					}
					
					// Function to initialize Flickity manually in iframe
					function initFlickityInIframe(iframeDoc) {
						if (!iframeDoc || !iframeDoc.body) return;
						
						try {
							var iframeWindow = iframeDoc.defaultView || iframeDoc.parentWindow;
							if (!iframeWindow) return;
							
							// Find all carousels with data-flickity attribute
							var carousels = iframeDoc.querySelectorAll("[data-flickity]");
							if (carousels.length === 0) return;
							
							// Check if Flickity is available
							if (!iframeWindow.Flickity) {
								// Wait for Flickity to load
								setTimeout(function() {
									initFlickityInIframe(iframeDoc);
								}, 100);
								return;
							}
							
							// Initialize each carousel
							carousels.forEach(function(carousel) {
								// Skip if already initialized
								if (carousel.flickity) return;
								
								try {
									var optionsAttr = carousel.getAttribute("data-flickity");
									if (!optionsAttr) return;
									
									var options = {};
									try {
										options = JSON.parse(optionsAttr);
									} catch (e) {
										return;
									}
									
									// Initialize Flickity
									var flkty = new iframeWindow.Flickity(carousel, options);
									
									// Handle pagination if enabled
									if (carousel.getAttribute("data-pagination") === "true") {
										var counter = iframeDoc.createElement("div");
										counter.className = "flickity-pagination";
										counter.style.pointerEvents = "none";
										carousel.appendChild(counter);
										
										var updatePagination = function() {
											var current = flkty.selectedIndex + 1;
											var total = flkty.slides.length;
											counter.textContent = current + "/" + total;
										};
										
										flkty.on("select", updatePagination);
										updatePagination();
									}
								} catch (error) {
									// Silently fail if Flickity initialization fails
								}
							});
							
						} catch (e) {
							// Silently fail if Flickity initialization fails
						}
					}
					
					// Function to load script in iframe and initialize
					function setupEditorIframe() {
						var editorFrame = getEditorIframe();
						
						if (!editorFrame) {
							setTimeout(setupEditorIframe, 100);
							return;
						}
						
						try {
							var iframeDoc = editorFrame.contentDocument || editorFrame.contentWindow.document;
							if (!iframeDoc || !iframeDoc.readyState) {
								setTimeout(setupEditorIframe, 100);
								return;
							}
							
							// Ensure viewport meta exists
							ensureEditorViewport(iframeDoc);
							
							// Load Flickity and script in iframe
							var iframeHead = iframeDoc.head;
							var iframeWindow = iframeDoc.defaultView || iframeDoc.parentWindow;
							
							// Check if Flickity library is already loaded
							var flickityScript = iframeHead.querySelector("script[data-flickity-lib]");
							
							// Load Flickity library
							if (!flickityScript && flickityLibUrl) {
								var flickityLibScript = iframeDoc.createElement("script");
								flickityLibScript.src = flickityLibUrl;
								flickityLibScript.setAttribute("data-flickity-lib", "true");
								flickityLibScript.onload = function() {
									// Initialize after Flickity loads
									setTimeout(function() {
										initFlickityInIframe(iframeDoc);
									}, 100);
								};
								iframeHead.appendChild(flickityLibScript);
							} else {
								// Flickity already loaded, just initialize
								setTimeout(function() {
									initFlickityInIframe(iframeDoc);
								}, 100);
							}
							
							// Re-initialize when content changes (for dynamically added blocks)
							var observer = new MutationObserver(function() {
								setTimeout(function() {
									initFlickityInIframe(iframeDoc);
								}, 200);
							});
							
							if (iframeDoc.body) {
								observer.observe(iframeDoc.body, {
									childList: true,
									subtree: true
								});
							}
							
						} catch (e) {
							// Cross-origin or not ready yet
							setTimeout(setupEditorIframe, 100);
						}
					}
					
					// Initialize when iframe loads
					function handleIframeLoad() {
						var editorFrame = getEditorIframe();
						if (editorFrame) {
							try {
								var iframeDoc = editorFrame.contentDocument || editorFrame.contentWindow.document;
								if (iframeDoc.readyState === "complete") {
									setupEditorIframe();
								} else {
									editorFrame.addEventListener("load", setupEditorIframe, { once: true });
								}
							} catch (e) {
								setTimeout(handleIframeLoad, 100);
							}
						}
					}
					
					// Try immediately
					setTimeout(setupEditorIframe, 100);
					
					// Listen for iframe load events
					var iframes = document.querySelectorAll("iframe");
					iframes.forEach(function(iframe) {
						iframe.addEventListener("load", handleIframeLoad);
					});
					
					// Watch for new iframes
					var observer = new MutationObserver(function(mutations) {
						mutations.forEach(function(mutation) {
							mutation.addedNodes.forEach(function(node) {
								if (node.nodeType === 1 && node.tagName === "IFRAME") {
									node.addEventListener("load", handleIframeLoad);
									setTimeout(setupEditorIframe, 200);
								}
							});
						});
					});
					
					observer.observe(document.body, {
						childList: true,
						subtree: true
					});
				});
			}
		})();
	'
		);
	}
}
// Priority 999 ensures our editor styles load AFTER WordPress core editor styles
add_action( 'enqueue_block_editor_assets', 'main_enqueue_block_editor_assets', 999 );

/**
 * Ensure editor styles print last
 *
 * Reorders the editor styles queue to ensure main-style-editor is printed last,
 * allowing it to override WordPress core editor styles.
 *
 * @since 1.0.0
 * @return void
 */
function main_print_editor_styles_last() {
	global $wp_styles;

	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	// Ensure our editor styles are queued last by reordering
	if ( isset( $wp_styles->queue ) && is_array( $wp_styles->queue ) ) {
		$queue = $wp_styles->queue;
		// Remove our editor styles from queue if they exist
		$queue = array_values(
			array_filter(
				$queue,
				function( $handle ) {
					return 'main-style-editor' !== $handle && 'main-fonts-editor' !== $handle;
				}
			)
		);
		// Add our editor styles at the end
		$queue[] = 'main-fonts-editor';
		$queue[] = 'main-style-editor';
		$wp_styles->queue = $queue;
	}
}
// Run at priority 999 to ensure it happens after all other editor enqueues
add_action( 'admin_print_styles', 'main_print_editor_styles_last', 999 );
