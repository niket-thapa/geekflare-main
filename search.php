<?php
/**
 * The template for displaying search results
 *
 * @package Main
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main">
	<?php if ( have_posts() ) : ?>
		<div class="archive-banner pt-8 pb-12 md:py-14 lg:py-16 xl:py-20">
			<div class="container-1056">
				<div class="flex flex-col gap-12 lg:grid lg:grid-cols-1 lg:gap-14 xl:gap-20">
					<div class="flex flex-col gap-6 min-w-0 lg:self-center">
						<?php main_breadcrumbs(); ?>
						<h1 class="page-title text-4xl md:text-5xl xl:text-6xl md:leading-none xl:leading-none font-bold text-gray-800">
						<?php
						/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'main' ), '<span>' . get_search_query() . '</span>' );
						?>
						</h1>
						<?php
						// Get post count for current search
						$post_count = main_get_archive_post_count();
						?>
						<div class="flex gap-3 items-center pt-6 md:pt-4 xl:pt-6">
							<div
								class="flex items-center justify-center bg-success-50 border border-success-200 w-11 h-11 rounded-xl">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#05603a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-newspaper-icon lucide-newspaper"><path d="M15 18h-5"/><path d="M18 14h-8"/><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="10" y="6" rx="1"/></svg>
							</div>
							<div class="flex-1 flex flex-col gap-0.5">
								<div class="text-lg leading-5 text-gray-800 font-bold"><?php echo esc_html( number_format_i18n( $post_count ) ); ?></div>
								<div class="font-medium text-xs md:text-sm tracking-2p text-gray-500">Expert guides</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container-1056 flex flex-col gap-y-12 md:gap-y-14 pb-14 md:pb-20 xl:pb-24">

			<div id="archive-posts-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-14 md:gap-x-8 xl:gap-x-12">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'search' );
				endwhile;
				?>
			</div>

			<?php main_pagination(); ?>
		</div>
	<?php else : ?>
		<div class="no-results not-found text-center py-12">
			<h1 class="text-4xl font-bold mb-4"><?php esc_html_e( 'Nothing Found', 'main' ); ?></h1>
			<p class="text-lg text-gray-600 mb-8">
				<?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'main' ); ?>
			</p>
			<?php get_search_form(); ?>
		</div>
	<?php endif; ?>
</main>

<?php
get_footer();

