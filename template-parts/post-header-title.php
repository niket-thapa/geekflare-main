<?php
/**
 * Template part for displaying post title
 *
 * @package Main
 * @since 1.0.0
 */

if ( is_singular() ) {
	the_title( '<h1 class="entry-title text-4xl md:text-5xl font-bold text-gray-800 leading-none md:leading-[1.2]">', '</h1>' );
} else {
	the_title( '<h2 class="entry-title text-4xl md:text-5xl font-bold text-gray-800 leading-none md:leading-[1.2]"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
}

