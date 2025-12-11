<?php
/**
 * Remove only Author prefix from archive titles
 */
add_filter( 'get_the_archive_title', 'remove_author_prefix_archive_title' );
function remove_author_prefix_archive_title( $title ) {
	if ( is_author() ) {
		$title = get_the_author();
	}
	return $title;
}

function add_single_post_body_class($classes) {
    if (is_single()) {
        $classes[] = 'single-post-page';
    }
    return $classes;
}
add_filter('body_class', 'add_single_post_body_class');