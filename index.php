<?php
/**
 * The main template file
 *
 * @package Main
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main">
	<?php if ( have_posts() ) : ?>
		<div class="container-1056 mx-auto px-4 py-8">
			<?php if ( is_home() && ! is_front_page() ) : ?>
				<header class="page-header mb-4">
					<h1 class="text-3xl font-bold"><?php single_post_title(); ?></h1>
				</header>
			<?php endif; ?>

			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', get_post_type() );
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => __( 'Previous', 'main' ),
				'next_text' => __( 'Next', 'main' ),
			) );
			?>
		</div>
	<?php else : ?>
		<div class="container-1056 mx-auto px-4 py-8">
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		</div>
	<?php endif; ?>
</main>

<?php
get_footer();
