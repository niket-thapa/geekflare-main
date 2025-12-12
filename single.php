<?php
/**
 * The template for displaying single posts
 * 
 * This file acts as a router that loads the appropriate template
 * based on the user's selection in the post editor.
 *
 * @package Main
 * @since 1.0.0
 */

// Get the selected template from meta
$selected_template = get_post_meta( get_the_ID(), 'mcb_post_template', true );

// Default to buying_guide if not set
if ( empty( $selected_template ) ) {
	$selected_template = 'buying_guide';
}

// Load the appropriate template
if ( 'info' === $selected_template && file_exists( get_template_directory() . '/templates/single-info.php' ) ) {
	include get_template_directory() . '/templates/single-info.php';
} else {
	// Default to buying guide template
	include get_template_directory() . '/templates/single-buying-guide.php';
}