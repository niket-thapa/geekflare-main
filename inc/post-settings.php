<?php
/**
 * Post Settings and Meta Fields
 *
 * Registers custom meta fields for posts, including affiliate disclosure toggle.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register affiliate disclosure meta field for posts.
 */
function main_register_post_meta_fields() {
	// Register affiliate disclosure toggle for posts
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
 * Add meta box for affiliate disclosure setting.
 */
function main_add_post_settings_meta_box() {
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
 * @param WP_Post $post Current post object.
 */
function main_render_post_settings_meta_box( $post ) {
	wp_nonce_field( 'main_post_settings_meta', 'main_post_settings_meta_nonce' );

	$show_affiliate_disclosure = get_post_meta( $post->ID, 'show_affiliate_disclosure', true );
	?>
	<div class="main-post-settings">
		<p>
			<label for="show_affiliate_disclosure" style="display: flex; align-items: center; gap: 8px;">
				<input 
					type="checkbox" 
					id="show_affiliate_disclosure" 
					name="show_affiliate_disclosure" 
					value="1" 
					<?php checked( $show_affiliate_disclosure, true ); ?>
				>
				<strong><?php esc_html_e( 'Show Affiliate Disclosure', 'main' ); ?></strong>
			</label>
		</p>
		<p class="description" style="margin-top: 8px;">
			<?php esc_html_e( 'Enable to show the affiliate disclosure button in the post meta bar.', 'main' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Save post settings meta data.
 *
 * @param int $post_id Post ID.
 */
function main_save_post_settings_meta( $post_id ) {
	// Verify nonce
	if ( ! isset( $_POST['main_post_settings_meta_nonce'] ) || 
		 ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['main_post_settings_meta_nonce'] ) ), 'main_post_settings_meta' ) ) {
		return;
	}

	// Check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Save affiliate disclosure setting
	$show_affiliate_disclosure = isset( $_POST['show_affiliate_disclosure'] ) ? (bool) $_POST['show_affiliate_disclosure'] : false;
	update_post_meta( $post_id, 'show_affiliate_disclosure', $show_affiliate_disclosure );
}
add_action( 'save_post', 'main_save_post_settings_meta' );

/**
 * Enqueue admin assets for post settings.
 *
 * @param string $hook Current admin page hook.
 */
function main_post_settings_admin_assets( $hook ) {
	// Only load on post edit pages
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
		$version = file_exists( $script_path ) ? filemtime( $script_path ) : time();
		wp_enqueue_script(
			'main-post-settings',
			MAIN_THEME_URI . '/assets/js/admin-post-settings.js',
			array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n' ),
			$version,
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'main_post_settings_admin_assets' );

