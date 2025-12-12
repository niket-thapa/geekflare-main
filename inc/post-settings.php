<?php
/**
 * Post Settings
 *
 * Custom meta fields and settings for posts.
 * Registers affiliate disclosure meta field and enqueues block editor panel.
 * The affiliate disclosure setting is managed in the "Affiliate Disclosure" panel in block editor.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom post meta fields.
 * 
 * Registers meta fields for post settings that are accessible
 * via the REST API for use in the block editor.
 *
 * @since 1.0.0
 */
function main_register_post_meta_fields() {
	// Affiliate Disclosure Toggle
	register_post_meta(
		'post',
		'show_affiliate_disclosure',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'main_register_post_meta_fields', 20 );

/**
 * Add meta box for post settings.
 * 
 * Classic editor meta box (hidden in block editor).
 * Note: Affiliate disclosure is managed in the "Affiliate Disclosure" panel in block editor.
 * 
 * @since 1.0.0
 */
function main_add_post_settings_meta_box() {
	// Only show in classic editor, not block editor
	$screen = get_current_screen();
	if ( $screen && method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
		return;
	}
	
	add_meta_box(
		'main-post-settings',
		__( 'Post Settings', 'main' ),
		'main_render_post_settings_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'main_add_post_settings_meta_box' );

/**
 * Render the post settings meta box.
 * 
 * Classic editor only. Block editor uses JavaScript component.
 * Note: Affiliate disclosure setting is managed in the "Affiliate Disclosure" panel in block editor.
 *
 * @since 1.0.0
 * @param WP_Post $post Current post object.
 */
function main_render_post_settings_meta_box( $post ) {
	wp_nonce_field( 'main_post_settings_meta', 'main_post_settings_meta_nonce' );
	?>
	<div class="main-post-settings-wrapper">
		<p class="description">
			<?php esc_html_e( 'Post settings are managed in the block editor sidebar panels.', 'main' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Save post settings meta box data.
 * 
 * Note: Affiliate disclosure setting is now managed via REST API in block editor.
 * This function is kept for backward compatibility but no longer handles affiliate disclosure.
 *
 * @since 1.0.0
 * @param int $post_id Post ID.
 */
function main_save_post_settings_meta_box( $post_id ) {
	// Verify nonce
	if ( ! isset( $_POST['main_post_settings_meta_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['main_post_settings_meta_nonce'], 'main_post_settings_meta' ) ) {
		return;
	}

	// Check if not an autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check user permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Post settings are now managed in block editor via REST API
	// This function is kept for backward compatibility
}
add_action( 'save_post', 'main_save_post_settings_meta_box' );

/**
 * Enqueue admin assets for post settings.
 * 
 * Loads JavaScript for block editor post settings panel.
 * Panel visibility is controlled based on selected template.
 *
 * @since 1.0.0
 * @param string $hook Current admin page hook.
 */
function main_post_settings_admin_assets( $hook ) {
	// Only load on post edit screen
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}

	// Enqueue block editor plugin script
	$script_path = MAIN_THEME_DIR . '/assets/js/admin-post-settings.js';
	if ( file_exists( $script_path ) ) {
		$version = filemtime( $script_path );
		
		wp_enqueue_script(
			'main-post-settings',
			MAIN_THEME_URI . '/assets/js/admin-post-settings.js',
			array(
				'wp-plugins',
				'wp-edit-post',
				'wp-element',
				'wp-components',
				'wp-data',
				'wp-i18n',
			),
			$version,
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'main_post_settings_admin_assets' );