<?php
/**
 * Template part for displaying related articles section
 *
 * @package Main
 * @since 1.0.0
 */

// Related posts
$related = get_posts( array(
	'category__in'   => wp_get_post_categories( get_the_ID() ),
	'numberposts'    => 3,
	'post__not_in'   => array( get_the_ID() ),
) );

if ( $related ) {
	?>
	<section class="related-articles-section bg-white py-16 md:py-24">
		<div class="container-1056 flex flex-col items-center gap-12 md:gap-14">
			<h4 class="text-xl md:text-2xl font-bold leading-none text-gray-800 text-center"><?php esc_html_e( 'More from Geekflare', 'main' ); ?></h4>

			<div class="w-full flex flex-col gap-14 md:grid md:grid-cols-2 md:gap-14 lg:grid-cols-3 xl:gap-12">
				<?php
				foreach ( $related as $post ) {
					setup_postdata( $post );
					get_template_part( 'template-parts/content', 'related' );
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
}

