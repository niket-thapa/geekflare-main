<?php
/**
 * The template for displaying all pages
 *
 * @package Main
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'container-1056 mx-auto px-4 py-8' ); ?>>
			<header class="entry-header mb-8">
				<?php the_title( '<h1 class="entry-title text-4xl font-bold mb-4">', '</h1>' ); ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="entry-thumbnail mb-8">
					<?php the_post_thumbnail( 'large', array( 'class' => 'w-full rounded-lg' ) ); ?>
				</div>
			<?php endif; ?>

			<div class="entry-content prose max-w-none">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'main' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<?php if ( get_edit_post_link() ) : ?>
				<footer class="entry-footer mt-8">
					<?php
					edit_post_link(
						sprintf(
							wp_kses(
								/* translators: %s: Name of current post. Only visible to screen readers */
								__( 'Edit <span class="screen-reader-text">%s</span>', 'main' ),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							),
							get_the_title()
						),
						'<span class="edit-link">',
						'</span>'
					);
					?>
				</footer>
			<?php endif; ?>
		</article>

		<?php
		// Comments
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}

	endwhile;
	?>
</main>

<?php
get_footer();

