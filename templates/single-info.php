<?php
/**
 * Template for Info/Tutorial Articles
 * 
 * Features:
 * - AI summarization buttons (ChatGPT & Gemini)
 * - Simplified sidebar (TOC only, no filters)
 * - Always-visible sidebar
 * - NO affiliate disclosure
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
		<div class="container py-8 md:py-10 lg:pt-12 lg:pb-20">
			<?php main_breadcrumbs(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'mt-5.5 lg:grid grid-cols-1 lg:grid-cols-[1fr_18.75rem] lg:grid-rows-[auto,1fr] lg:gap-x-11 main-post-wrapper relative' ); ?>>
				<header class="entry-header mb-2 lg:mb-8 lg:row-start-1 lg:col-start-1">
					<?php get_template_part( 'template-parts/post-header-title' ); ?>
					<?php if ( 'post' === get_post_type() ) : ?>
						<?php get_template_part( 'template-parts/post-meta-bar' ); ?>
						<?php get_template_part( 'template-parts/post-author-bar' ); ?>
					<?php endif; ?>
				</header>

				<div class="mobile_sidebar_wrap lg:row-start-1 lg:row-span-2 lg:col-start-2 py-6 lg:py-0 sticky start-0 top-0 lg:static max-lg:bg-white lg:bg-none max-lg:z-50">
            <?php get_template_part( 'template-parts/post-sidebar-info' ); ?>
        </div>

				<div class="lg:row-start-2 lg:col-start-1 min-w-0">
					<?php get_template_part( 'template-parts/post-excerpt' ); ?>
					<div class="entry-content prose max-w-none">
						<?php
						the_content(
							sprintf(
								wp_kses(
									/* translators: %s: Name of current post. Only visible to screen readers */
									__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'main' ),
									array(
										'span' => array(
											'class' => array(),
										),
									)
								),
								wp_kses_post( get_the_title() )
							)
						);
						wp_link_pages(
							array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'main' ),
								'after'  => '</div>',
							)
						);
						?>
					</div>
				</div>
			</article>
		</div>

		<?php
		// Partners Section
		get_template_part( 'template-parts/partners-section' );

		// Related Articles Section
		get_template_part( 'template-parts/related-articles' );

		// Comments
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}

	endwhile;
	?>
</main>

<?php
get_footer();

