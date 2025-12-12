<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Main
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main">
	<div class="container-1056 mx-auto px-4 py-12">
		<div class="text-center">
			<h1 class="text-6xl font-bold mb-4">404</h1>
			<h2 class="text-3xl font-semibold mb-4"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'main' ); ?></h2>
			<p class="text-lg text-gray-600 mb-8">
				<?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'main' ); ?>
			</p>
			<?php get_search_form(); ?>
		</div>
	</div>
</main>

<?php
get_footer();

