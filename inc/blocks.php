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

		// Check if render.php exists and set render callback explicitly
		// WordPress should read "render": "file:./render.php" from block.json automatically,
		// but we explicitly set it here to ensure it works when manually registering assets
		$render_template = $block_folder . '/render.php';
		if ( file_exists( $render_template ) ) {
			// Store the template path in a way that the callback can access it
			$template_path = $render_template;
			$block_args['render_callback'] = function( $attributes, $content, $block ) use ( $template_path ) {
				if ( ! file_exists( $template_path ) ) {
					return '';
				}
				ob_start();
				include $template_path;
				return ob_get_clean();
			};
		}

		// Register the block (block.json will be read automatically)
		// Pass empty array if no block_args to let WordPress read everything from block.json
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

	// Enqueue heading number style extension
	$heading_extension_path = get_stylesheet_directory() . '/src/js/heading-number-style.js';
	if ( file_exists( $heading_extension_path ) ) {
		wp_enqueue_script(
			'main-heading-number-style',
			get_stylesheet_directory_uri() . '/src/js/heading-number-style.js',
			array(
				'wp-blocks',
				'wp-element',
				'wp-block-editor',
				'wp-components',
				'wp-i18n',
				'wp-hooks',
			),
			filemtime( $heading_extension_path ),
			false // Load in header for block editor
		);
	}


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
		
		/* Heading Number Style - Editor */
		.editor-styles-wrapper .has_number_style::before,
		.block-editor-block-preview .has_number_style::before,
		.block-editor-block-list__layout .has_number_style::before {
			content: attr(data-number);
			width: 1.25em;
			aspect-ratio: 1 / 1;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			background-color: #FF4A00;
			color: #ffffff;
			margin-right: 0.625rem;
		}
	'
	);

	// Add comprehensive prose styles for editor to match frontend
	wp_add_inline_style(
		'main-style-editor',
		'
		/* Editor Prose Styles - Match Frontend */
		/* Apply prose base styles to editor content */
		.editor-styles-wrapper {
			--tw-prose-body: #333;
			--tw-prose-headings: #333;
			--tw-prose-links: #ff4a00;
			--tw-prose-bold: #000;
			font-size: 0.875rem;
			font-weight: 500;
			letter-spacing: 0.02em;
			color: #333;
			line-height: 1.75;
		}

		.post-type-post .block-editor-block-list__layout.is-root-container > :where(:not(.alignleft):not(.alignright):not(.alignfull)),
		.post-type-post .editor-visual-editor__post-title-wrapper > :where(:not(.alignleft):not(.alignright):not(.alignfull)) {
			max-width: 780px;
		}
		
		@media (min-width: 1024px) {
			.editor-styles-wrapper {
				font-size: 1rem;
			}
		}

		.schema-faq-section .schema-faq-question {
			display: block !important;
			position: relative;
		}

		.schema-faq-section .schema-faq-question:after {
			position: absolute;
			inset-inline-end: 0;
		}

		.buying_guide_item {
			margin-bottom: 50px;
		}

		.buying_guide_item .product-score-breakdown,
		.buying_guide_item .key-features,
		.buying_guide_item .pros-cons {
			margin-bottom: 30px;
		}
		
		/* Paragraphs */
		.editor-styles-wrapper p {
			margin-top: 1rem;
			margin-bottom: 1rem;
			line-height: 1.75;
		}
		
		/* Headings */
		.editor-styles-wrapper h1 {
			font-size: 1.875rem;
			font-weight: 700;
			line-height: 1.2;
			margin-top: 2rem;
			margin-bottom: 1rem;
		}
		
		.editor-styles-wrapper h1.m-0 {
			margin: 0 !important;
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper h1 {
				font-size: 2.25rem;
			}
		}
		
		.editor-styles-wrapper h2 {
			font-size: 1.5rem;
			font-weight: 700;
			line-height: 1.2;
			margin-top: 2rem;
			margin-bottom: 1rem;
		}
		
		.editor-styles-wrapper h2.m-0 {
			margin: 0 !important;
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper h2 {
				font-size: 1.875rem;
			}
		}
		
		.editor-styles-wrapper h3 {
			font-size: 1.25rem;
			font-weight: 700;
			line-height: 1.2;
			margin-top: 1.5rem;
			margin-bottom: 0.75rem;
		}
		
		.editor-styles-wrapper h3.m-0 {
			margin: 0 !important;
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper h3 {
				font-size: 1.5rem;
			}
		}
		
		.editor-styles-wrapper h4 {
			font-size: 1.125rem;
			font-weight: 700;
			line-height: 1.2;
			margin-top: 1.5rem;
			margin-bottom: 0.75rem;
		}
		
		.editor-styles-wrapper h4.m-0 {
			margin: 0 !important;
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper h4 {
				font-size: 1.25rem;
			}
		}
		
		.editor-styles-wrapper h5 {
			font-size: 1rem;
			font-weight: 700;
			line-height: 1.2;
			margin-top: 1rem;
			margin-bottom: 0.5rem;
		}
		
		.editor-styles-wrapper h5.m-0 {
			margin: 0 !important;
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper h5 {
				font-size: 1.125rem;
			}
		}
		
		.editor-styles-wrapper h6 {
			font-size: 0.875rem;
			font-weight: 700;
			line-height: 1.2;
			margin-top: 1rem;
			margin-bottom: 0.5rem;
		}
		
		.editor-styles-wrapper h6.m-0 {
			margin: 0 !important;
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper h6 {
				font-size: 1rem;
			}
		}

		.buying_guide_item figure.wp-block-table {
			margin-block: 0 !important;
		}

		.buying_guide_item figure.wp-block-table table {
			margin-block: 1px !important;
		}
		
		/* Links */
		.editor-styles-wrapper a:not(.btn) {
			color: #ff4a00;
		}
		
		.editor-styles-wrapper a:not(.btn):hover {
			color: #e84300;
			text-decoration: underline;
		}
		
		/* Strong and Emphasis */
		.editor-styles-wrapper strong {
			font-weight: 700;
		}
		
		.editor-styles-wrapper em {
			font-style: italic;
		}
		
		/* Lists */
		.editor-styles-wrapper ul {
			list-style-type: disc;
			padding-left: 1.5rem;
			margin-top: 1rem;
			margin-bottom: 1rem;
		}
		
		.editor-styles-wrapper ol {
			list-style-type: decimal;
			padding-left: 1.5rem;
			margin-top: 1rem;
			margin-bottom: 1rem;
		}
		
		.editor-styles-wrapper li {
			line-height: 1.75;
		}
		
		.editor-styles-wrapper li > ul,
		.editor-styles-wrapper li > ol {
			margin-top: 0.5rem;
			margin-bottom: 0;
		}
		
		/* Blockquote */
		.editor-styles-wrapper blockquote {
			border-left: 4px solid #d1d5db;
			padding-left: 1.5rem;
			padding-top: 0.5rem;
			padding-bottom: 0.5rem;
			margin-top: 1.5rem;
			margin-bottom: 1.5rem;
			font-style: italic;
		}
		
		/* Horizontal Rule */
		.editor-styles-wrapper hr {
			border-top: 1px solid #e5e7eb;
			margin-top: 2rem;
			margin-bottom: 2rem;
		}
		
		/* Code */
		.editor-styles-wrapper code {
			background-color: #f3f4f6;
			font-size: 0.875em;
			font-weight: 500;
			padding: 0.125rem 0.375rem;
			border-radius: 0.25rem;
			border: 1px solid #e5e7eb;
			font-family: "Monaco", "Menlo", "Ubuntu Mono", "Consolas", "source-code-pro", monospace;
		}
		
		.editor-styles-wrapper code::before,
		.editor-styles-wrapper code::after {
			content: none;
		}
		
		/* Code Blocks */
		.editor-styles-wrapper pre {
			background-color: #1f2937;
			border-radius: 0.75rem;
			padding: 1rem;
			overflow-x: auto;
			font-family: "Monaco", "Menlo", "Ubuntu Mono", "Consolas", "source-code-pro", monospace;
			font-size: 0.875rem;
			line-height: 1.75;
			margin-top: 1.5rem;
			margin-bottom: 1.5rem;
			border: 1px solid #374151;
		}
		
		@media (min-width: 768px) {
			.editor-styles-wrapper pre {
				padding: 1.5rem;
			}
		}
		
		.editor-styles-wrapper pre code {
			background-color: transparent;
			padding: 0;
			border: 0;
			font-size: inherit;
			font-weight: 400;
			color: #e5e7eb;
		}
		
		.editor-styles-wrapper pre code::before,
		.editor-styles-wrapper pre code::after {
			content: none;
		}
		
		/* Images */
		.editor-styles-wrapper img {
			max-width: 100%;
			height: auto;
			margin-top: 1.5rem;
			margin-bottom: 1.5rem;
		}

		.editor-styles-wrapper img.m-0 {
			margin: 0 !important;
		}
		
		/* Figure */
		.editor-styles-wrapper figure {
			margin-top: 1.5rem;
			margin-bottom: 1.5rem;
		}
		
		.editor-styles-wrapper figcaption {
			font-size: 0.875rem;
			text-align: center;
			font-style: italic;
			margin-top: 0.5rem;
		}
		
		/* Keyboard Keys */
		.editor-styles-wrapper kbd {
			background-color: #f3f4f6;
			border: 1px solid #d1d5db;
			border-radius: 0.25rem;
			padding: 0.25rem 0.5rem;
			font-size: 0.875rem;
			font-family: "Monaco", "Menlo", "Ubuntu Mono", "Consolas", "source-code-pro", monospace;
			box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
		}
		
		/* Address */
		.editor-styles-wrapper address {
			font-style: normal;
			line-height: 1.75;
			margin-top: 1rem;
			margin-bottom: 1rem;
		}
		
		/* Details/Summary */
		.editor-styles-wrapper details {
			border: 1px solid #e5e7eb;
			border-radius: 0.75rem;
			overflow: hidden;
			margin-top: 1rem;
			margin-bottom: 1rem;
		}
		
		.editor-styles-wrapper summary {
			padding: 0.75rem 1rem;
			background-color: #f9fafb;
			cursor: pointer;
			font-weight: 600;
		}
		
		.editor-styles-wrapper details[open] summary {
			border-bottom: 1px solid #e5e7eb;
		}
		
		.editor-styles-wrapper details > *:not(summary) {
			padding: 0.75rem 1rem;
		}
		
		/* Mark (Highlighted Text) */
		.editor-styles-wrapper mark {
			background-color: #fef08a;
			padding: 0.125rem 0.25rem;
			border-radius: 0.25rem;
			color: #000;
		}
		
		/* Abbreviation */
		.editor-styles-wrapper abbr {
			text-decoration: underline;
			text-decoration-style: dotted;
			cursor: help;
			text-decoration-skip-ink: none;
		}
		
		/* Superscript and Subscript */
		.editor-styles-wrapper sup {
			font-size: 0.75em;
			line-height: 0;
			vertical-align: super;
		}
		
		.editor-styles-wrapper sub {
			font-size: 0.75em;
			line-height: 0;
			vertical-align: sub;
		}
		
		/* Deleted and Inserted Text */
		.editor-styles-wrapper del {
			text-decoration: line-through;
			color: #6b7280;
		}
		
		.editor-styles-wrapper ins {
			text-decoration: underline;
			text-decoration-thickness: 2px;
			text-decoration-color: #16a34a;
			background-color: #f0fdf4;
			text-decoration-skip-ink: none;
		}
		
		/* Small Text */
		.editor-styles-wrapper small {
			font-size: 0.875em;
		}
		
		/* Underline */
		.editor-styles-wrapper u {
			text-decoration: underline;
			text-decoration-skip-ink: none;
		}
		
		/* Citation */
		.editor-styles-wrapper cite {
			font-style: italic;
			color: #4b5563;
		}
		
		.editor-styles-wrapper blockquote cite {
			display: block;
			margin-top: 0.5rem;
			font-size: 0.875rem;
		}
		
		/* Iframe */
		.editor-styles-wrapper iframe {
			width: 100%;
			border-radius: 0.75rem;
			border: 1px solid #e5e7eb;
			aspect-ratio: 16 / 9;
			max-width: 100%;
			height: auto;
			margin-top: 1.5rem;
			margin-bottom: 1.5rem;
		}
		
		/* Progress Bar */
		.editor-styles-wrapper progress {
			width: 100%;
			height: 0.5rem;
			border-radius: 9999px;
			overflow: hidden;
			appearance: none;
			background-color: #e5e7eb;
			margin-top: 1rem;
			margin-bottom: 1rem;
		}
		
		.editor-styles-wrapper progress::-webkit-progress-bar {
			background-color: #e5e7eb;
			border-radius: 9999px;
		}
		
		.editor-styles-wrapper progress::-webkit-progress-value {
			background-color: #ff4a00;
			border-radius: 9999px;
		}
		
		.editor-styles-wrapper progress::-moz-progress-bar {
			background-color: #ff4a00;
			border-radius: 9999px;
		}
		
		/* Table Styling - Default prose table styles */
		.editor-styles-wrapper table:not([class~="product-compare-table"]) {
			width: 100%;
			border-collapse: collapse;
			border: 1px solid rgb(229 231 235);
			border-radius: 0.75rem;
		}
		
		.editor-styles-wrapper table:not([class*="m-0"]):not([class~="product-compare-table"]) {
			margin-top: 1.5rem;
			margin-bottom: 1.5rem;
		}
		
		.editor-styles-wrapper thead:not(:where([class~="product-compare-table"] *)) {
			background-color: rgb(249 250 251);
		}
		
		.editor-styles-wrapper th:not(:where([class~="product-compare-table"] *)) {
			padding: 0.75rem 1rem;
			text-align: left;
			font-size: 0.875rem;
			font-weight: 600;
			border-bottom: 1px solid rgb(229 231 235);
		}
		
		.editor-styles-wrapper td:not(:where([class~="product-compare-table"] *)) {
			padding: 0.75rem 1rem;
			font-size: 0.875rem;
			border-bottom: 1px solid rgb(229 231 235);
		}
		
		.editor-styles-wrapper tbody tr:last-child td:not(:where([class~="product-compare-table"] *)) {
			border-bottom: 0;
		}
		
		/* Override wp-block-table styles with higher specificity */
		.editor-styles-wrapper .wp-block-table table {
			border: none;
			border-collapse: separate;
			border-spacing: 0;
			border-radius: 1rem;
			overflow: hidden;
			width: 100%;
			margin-left: 1px;
			margin-right: 1px;
			max-width: calc(100% - 2px);
			box-shadow: 0 0 0 1px rgb(229 231 235);
		}
		
		.editor-styles-wrapper .wp-block-table:not([class*="m-0"]) table {
			margin-top: 1.5rem;
			margin-bottom: 1.5rem;
		}
		
		.editor-styles-wrapper .wp-block-table thead {
			background-color: rgb(249 250 251);
			border: none;
		}
		
		.editor-styles-wrapper .wp-block-table th,
		.editor-styles-wrapper .wp-block-table table th {
		  border-color:rgb(229 231 235);
			padding: 0.75rem 1rem;
			text-align: left;
			font-size: 0.875rem;
			font-weight: 600;
			border-bottom: 1px solid rgb(229 231 235);
			border-right: none;
			border-top: none;
		}
		
		.editor-styles-wrapper .wp-block-table th:first-child,
		.editor-styles-wrapper .wp-block-table table th:first-child {
			border-left: none;
		}
		
		.editor-styles-wrapper .wp-block-table td,
		.editor-styles-wrapper .wp-block-table table td {
		  border-color:rgb(229 231 235);
			padding: 0.75rem 1rem;
			font-size: 0.875rem;
			border-bottom: 1px solid rgb(229 231 235);
			border-right: none;
			border-top: none;
		}
		
		.editor-styles-wrapper .wp-block-table td:first-child,
		.editor-styles-wrapper .wp-block-table table td:first-child {
			border-left: none;
		}
		
		.editor-styles-wrapper .wp-block-table tbody tr:last-child td,
		.editor-styles-wrapper .wp-block-table table tbody tr:last-child td {
			border-bottom: 0;
		}
		
		/* Table Block Style: Highlight First Column */
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column thead,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column table thead {
			background-color: transparent;
		}
		
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column th:not(:first-child),
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column td:not(:first-child),
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column table th:not(:first-child),
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column table td:not(:first-child) {
			background-color: transparent;
			font-weight: 500;
		}
		
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column th:first-child,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column td:first-child,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column table th:first-child,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-column table td:first-child {
			background-color: rgb(249 250 251);
			font-weight: 600;
		}
		
		/* Table Block Style: Highlight First Row */
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row thead,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row table thead {
			background-color: transparent;
		}
		
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row tbody tr th,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row tbody tr td,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row table tbody tr th,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row table tbody tr td {
			background-color: transparent;
		}
		
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row thead th,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row table thead th {
			background-color: rgb(249 250 251);
			font-weight: 600;
		}
		
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row table:not(:has(thead)) tbody tr:first-child th,
		.editor-styles-wrapper .wp-block-table.is-style-highlight-f-row table:not(:has(thead)) tbody tr:first-child td {
			background-color: rgb(249 250 251);
			font-weight: 600;
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
						       document.querySelector(".block-editor-iframe__container-1056 iframe") ||
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
