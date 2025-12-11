<?php
/**
 * Disable Comments Functionality
 *
 * Completely disables comments for posts using WordPress hooks and filters.
 * This removes comment support, closes comments, and hides all comment-related UI.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove comment support from post types
 *
 * @since 1.0.0
 * @return void
 */
function main_disable_comments_post_types_support() {
	$post_types = get_post_types();
	foreach ( $post_types as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
}
add_action( 'admin_init', 'main_disable_comments_post_types_support' );

/**
 * Close comments on the front-end
 *
 * @since 1.0.0
 * @param bool $open Whether comments are open.
 * @param int  $post_id The post ID.
 * @return bool Always returns false to close comments.
 */
function main_disable_comments_status( $open, $post_id ) {
	return false;
}
add_filter( 'comments_open', 'main_disable_comments_status', 20, 2 );
add_filter( 'pings_open', 'main_disable_comments_status', 20, 2 );

/**
 * Hide existing comments
 *
 * @since 1.0.0
 * @param array $comments Array of comment objects.
 * @return array Empty array to hide all comments.
 */
function main_disable_comments_hide_existing_comments( $comments ) {
	return array();
}
add_filter( 'comments_array', 'main_disable_comments_hide_existing_comments', 10, 2 );

/**
 * Remove comments page from menu
 *
 * @since 1.0.0
 * @return void
 */
function main_disable_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'main_disable_comments_admin_menu' );

/**
 * Redirect any user trying to access comments page
 *
 * @since 1.0.0
 * @return void
 */
function main_disable_comments_admin_menu_redirect() {
	global $pagenow;
	if ( $pagenow === 'edit-comments.php' ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'main_disable_comments_admin_menu_redirect' );

/**
 * Remove comments metabox from dashboard
 *
 * @since 1.0.0
 * @return void
 */
function main_disable_comments_dashboard() {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'admin_init', 'main_disable_comments_dashboard' );

/**
 * Remove comments links from admin bar
 *
 * @since 1.0.0
 * @param WP_Admin_Bar $wp_admin_bar The admin bar object.
 * @return void
 */
function main_disable_comments_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_menu( 'comments' );
}
add_action( 'admin_bar_menu', 'main_disable_comments_admin_bar', 999 );

/**
 * Remove comments column from posts/pages list
 *
 * @since 1.0.0
 * @param array $columns Array of column names.
 * @return array Modified array without comments column.
 */
function main_disable_comments_column( $columns ) {
	unset( $columns['comments'] );
	return $columns;
}
add_filter( 'manage_posts_columns', 'main_disable_comments_column' );
add_filter( 'manage_pages_columns', 'main_disable_comments_column' );

/**
 * Remove comments metabox from post edit screen
 *
 * @since 1.0.0
 * @return void
 */
function main_disable_comments_metabox() {
	$post_types = get_post_types();
	foreach ( $post_types as $post_type ) {
		remove_meta_box( 'commentstatusdiv', $post_type, 'normal' );
		remove_meta_box( 'commentsdiv', $post_type, 'normal' );
	}
}
add_action( 'admin_init', 'main_disable_comments_metabox' );

/**
 * Remove comment count from admin dashboard widgets
 *
 * @since 1.0.0
 * @return void
 */
function main_disable_comments_dashboard_widgets() {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'main_disable_comments_dashboard_widgets' );

/**
 * Disable comment feed
 *
 * @since 1.0.0
 * @return void
 */
function main_disable_comments_feed() {
	wp_die( __( 'Comments are disabled.', 'main' ) );
}
add_action( 'do_feed_rss2_comments', 'main_disable_comments_feed', 1 );
add_action( 'do_feed_atom_comments', 'main_disable_comments_feed', 1 );

/**
 * Remove comment-reply script enqueue
 *
 * @since 1.0.0
 * @return void
 */
function main_disable_comments_script() {
	wp_deregister_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'main_disable_comments_script', 100 );

/**
 * Remove comment form from frontend
 *
 * @since 1.0.0
 * @return void
 */
function main_disable_comments_form() {
	return false;
}
add_filter( 'comment_form_default_fields', '__return_empty_array' );
add_filter( 'comment_form_logged_in_ui', '__return_empty_string' );

/**
 * Override comments_template to return empty
 *
 * @since 1.0.0
 * @param string $template Path to comments template.
 * @return string Empty string to prevent loading comments template.
 */
function main_disable_comments_template( $template ) {
	return '';
}
add_filter( 'comments_template', 'main_disable_comments_template', 20 );

/**
 * Remove comment count from REST API
 *
 * @since 1.0.0
 * @param array  $response Response data.
 * @param object $post Post object.
 * @return array Modified response without comment count.
 */
function main_disable_comments_rest_api( $response, $post ) {
	if ( isset( $response->data['comment_status'] ) ) {
		unset( $response->data['comment_status'] );
	}
	if ( isset( $response->data['ping_status'] ) ) {
		unset( $response->data['ping_status'] );
	}
	return $response;
}
add_filter( 'rest_prepare_post', 'main_disable_comments_rest_api', 10, 2 );
add_filter( 'rest_prepare_page', 'main_disable_comments_rest_api', 10, 2 );

