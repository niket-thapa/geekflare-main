<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package Main
 * @since 1.0.0
 */
?>

<section class="no-results not-found text-center py-12">
	<header class="page-header mb-4">
		<h1 class="page-title text-3xl md:text-4xl font-bold mb-4"><?php esc_html_e( 'Nothing Found', 'main' ); ?></h1>
	</header>

	<div class="page-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
			<p class="text-lg text-gray-600 mb-4">
				<?php
				printf(
					wp_kses(
						/* translators: 1: link to WP admin new post page. */
						__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'main' ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					esc_url( admin_url( 'post-new.php' ) )
				);
				?>
			</p>
		<?php elseif ( is_search() ) : ?>
			<p class="text-lg text-gray-600 mb-4"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'main' ); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p class="text-lg text-gray-600 mb-4"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'main' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>

