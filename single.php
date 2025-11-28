<?php
/**
 * The template for displaying single posts
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
		<div class="container-1184 py-8 md:py-10 lg:pt-12 lg:pb-20">
			<?php main_breadcrumbs(); ?>
			<div
				class="lg:grid grid-cols-1 lg:grid-cols-[23.315%_1fr] lg:grid-rows-[auto,1fr] lg:gap-x-11 pt-5.5">
				<div class="pb-6 mt-[4.6875rem] lg:mt-0">
					<aside class="table-of-contents w-full border border-gray-200 rounded-xl overflow-hidden flex flex-col">
						<!-- Table of Content Header -->
						<btn
							type="btn"
							class="toc-header flex items-center justify-between px-4 py-[0.8125rem] md:py-3.5 gap-3 w-full bg-gray-50 border-b border-gray-200 transition-colors"
							aria-expanded="false"
							aria-controls="toc-content">
							<span class="text-sm font-bold text-gray-800">Table of Content</span>
							<svg
								class="toc-chevron w-3.5 h-2.5 p-0.5 transition-transform duration-200"
								width="10"
								height="6"
								viewBox="0 0 10 6"
								fill="none"
								xmlns="http://www.w3.org/2000/svg"
								aria-hidden="true">
								<path
									d="M0.75 0.75L4.75 4.75L8.75 0.75"
									stroke="#1D2939"
									stroke-width="1.5"
									stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
						</btn>

						<!-- Table of Content Content -->
						<nav
							id="toc-content"
							class="toc-content accordion-panel flex flex-col"
							role="region"
							aria-live="polite">
							<div class="accordion-panel__inner flex flex-col gap-2 p-4">
								<a
									href="#summary"
									class="toc-item toc-item--active flex items-center justify-between px-3 py-2.5 gap-2 rounded-lg bg-eva-prime-50 text-sm font-semibold text-gray-800 transition-colors">
									<span>Summary</span>
									<svg
										class="w-5 h-5 opacity-0"
										width="20"
										height="20"
										viewBox="0 0 20 20"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
										aria-hidden="true">
										<path
											d="M7.5 4.167L13.333 10L7.5 15.833"
											stroke="#252B37"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</a>

								<a
									href="#why-trust"
									class="toc-item flex items-center justify-between px-3 py-2.5 gap-2 rounded-lg bg-gray-50 text-sm font-medium text-gray-800 transition-colors">
									<span>Why Trust our Guide</span>
									<svg
										class="w-5 h-5 opacity-0"
										width="20"
										height="20"
										viewBox="0 0 20 20"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
										aria-hidden="true">
										<path
											d="M7.5 4.167L13.333 10L7.5 15.833"
											stroke="#252B37"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</a>

								<a
									href="#top-picks"
									class="toc-item flex items-center justify-between px-3 py-2.5 gap-2 rounded-lg bg-gray-50 text-sm font-medium text-gray-800 transition-colors">
									<span>Top Picks</span>
									<svg
										class="w-5 h-5 opacity-0"
										width="20"
										height="20"
										viewBox="0 0 20 20"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
										aria-hidden="true">
										<path
											d="M7.5 4.167L13.333 10L7.5 15.833"
											stroke="#252B37"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</a>

								<!-- Nested items for Top Picks -->
								<div class="toc-nested ms-6 border-l border-gray-200 flex flex-col">
									<a
										href="#monday-com"
										class="toc-nested-item flex items-center gap-1 py-2.5 px-0 text-sm font-medium text-eva-prime-600 transition-colors">
										<span class="w-2 text-gray-200 -ms-0.5">—</span>
										<span>Monday.com</span>
									</a>
									<a
										href="#trello"
										class="toc-nested-item flex items-center gap-1 py-2.5 px-0 text-sm font-medium text-gray-800 transition-colors">
										<span class="w-2 text-gray-200 -ms-0.5">—</span>
										<span>Trello</span>
									</a>
									<a
										href="#asana"
										class="toc-nested-item flex items-center gap-1 py-2.5 px-0 text-sm font-medium text-gray-800 transition-colors">
										<span class="w-2 text-gray-200 -ms-0.5">—</span>
										<span>Asana</span>
									</a>
								</div>

								<a
									href="#products"
									class="toc-item flex items-center justify-between px-3 py-2.5 gap-2 rounded-lg bg-gray-50 text-sm font-medium text-gray-800 transition-colors">
									<span>Products</span>
									<svg
										class="w-5 h-5 opacity-0"
										width="20"
										height="20"
										viewBox="0 0 20 20"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
										aria-hidden="true">
										<path
											d="M7.5 4.167L13.333 10L7.5 15.833"
											stroke="#252B37"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</a>

								<a
									href="#honourable-mentions"
									class="toc-item flex items-center justify-between px-3 py-2.5 gap-2 rounded-lg bg-gray-50 text-sm font-medium text-gray-800 transition-colors">
									<span>Honourable Mentions</span>
									<svg
										class="w-5 h-5 opacity-0"
										width="20"
										height="20"
										viewBox="0 0 20 20"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
										aria-hidden="true">
										<path
											d="M7.5 4.167L13.333 10L7.5 15.833"
											stroke="#252B37"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</a>

								<a
									href="#faq"
									class="toc-item flex items-center justify-between px-3 py-2.5 gap-2 rounded-lg bg-gray-50 text-sm font-medium text-gray-800 transition-colors">
									<span>FAQ</span>
									<svg
										class="w-5 h-5 opacity-0"
										width="20"
										height="20"
										viewBox="0 0 20 20"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
										aria-hidden="true">
										<path
											d="M7.5 4.167L13.333 10L7.5 15.833"
											stroke="#252B37"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</a>
							</div>
						</nav>

						<!-- Filter Header -->
						<btn
							type="btn"
							class="filter-header flex items-center justify-between px-4 py-[0.8125rem] md:py-3.5 gap-3 w-full bg-gray-50 border-t border-gray-200 transition-colors"
							aria-expanded="false"
							aria-controls="filter-content">
							<span class="text-sm font-bold text-gray-800">Filter</span>
							<svg
								class="filter-chevron w-3.5 h-2.5 p-0.5 transition-transform duration-200"
								width="10"
								height="6"
								viewBox="0 0 10 6"
								fill="none"
								xmlns="http://www.w3.org/2000/svg"
								aria-hidden="true">
								<path
									d="M0.75 0.75L4.75 4.75L8.75 0.75"
									stroke="#1D2939"
									stroke-width="1.5"
									stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
						</btn>

						<!-- Filter Content -->
						<form id="filter-content" class="filter-content accordion-panel block" role="region" aria-live="polite">
							<div class="accordion-panel__inner flex flex-col gap-6 p-4">
								<!-- Tools Section -->
								<fieldset class="filter-group flex flex-col gap-3">
									<legend
										class="filter-group__label text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-500">
										Tools
									</legend>
									<div class="flex flex-col gap-3">
										<label class="filter-option">
											<input type="checkbox" name="tools" value="all" checked />
											<span>All</span>
										</label>
										<label class="filter-option">
											<input type="checkbox" name="tools" value="ai" />
											<span>AI Tool</span>
										</label>
										<label class="filter-option">
											<input type="checkbox" name="tools" value="software" />
											<span>Software</span>
										</label>
										<label class="filter-option">
											<input type="checkbox" name="tools" value="gaming" />
											<span>Gaming</span>
										</label>
									</div>
								</fieldset>

								<!-- Subscription Type Section -->
								<fieldset class="filter-group flex flex-col gap-3 mt-6">
									<legend
										class="filter-group__label text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-500">
										Subscription Type
									</legend>
									<div class="flex flex-col gap-3">
										<label class="filter-option">
											<input type="checkbox" name="subscription" value="all" checked />
											<span>All</span>
										</label>
										<label class="filter-option">
											<input type="checkbox" name="subscription" value="free" />
											<span>Free Only</span>
										</label>
										<label class="filter-option">
											<input type="checkbox" name="subscription" value="paid" />
											<span>Paid</span>
										</label>
									</div>
								</fieldset>
							</div>
						</form>
					</aside>
				</div>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'min-w-0' ); ?>>
					<header class="entry-header mb-8">
						<?php
						if ( is_singular() ) {
							the_title( '<h1 class="entry-title text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 leading-none md:leading-none lg:leading-none">', '</h1>' );
						} else {
							the_title( '<h2 class="entry-title text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 leading-none md:leading-none lg:leading-none"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
						}

						if ( 'post' === get_post_type() ) {
							?>
							<div class="post-meta-bar">
								<div class="post-meta-info">
									<span class="post-meta-updated">
										Last Update :
										<time datetime="<?php echo esc_attr( get_the_modified_date( 'Y-m-d' ) ); ?>">
											<?php echo esc_html( get_the_modified_date( 'j M, Y' ) ); ?>
										</time>
									</span>
									<?php
									$show_affiliate_disclosure = get_post_meta( get_the_ID(), 'show_affiliate_disclosure', true );
									if ( $show_affiliate_disclosure ) :
										?>
										<button type="button" class="post-meta-disclosure bg-gray-100 rounded-xl">
											<span class="post-meta-disclosure__icon" aria-hidden="true">
												<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 18 18">
													<g stroke="#252b37" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
														<path d="M9 15.75a6.75 6.75 0 1 0 0-13.5 6.75 6.75 0 0 0 0 13.5M9 6h.008" />
														<path d="M8.25 9H9v3h.75" />
													</g>
												</svg>
											</span>
											Affiliate Disclosure
										</button>
										<?php
									endif;
									?>
								</div>
								<div class="post-meta-share">
									<span class="post-meta-share__label">Share:</span>
									<div class="post-meta-share__icons">
										<a 
											href="<?php echo esc_url( main_get_social_share_url( 'linkedin' ) ); ?>" 
											class="share-icon share-icon--linkedin" 
											aria-label="Share on LinkedIn"
											target="_blank"
											rel="noopener noreferrer"
										>
											<img src="<?php echo esc_url( main_get_image_url( 'linkedin.svg' ) ); ?>" alt="LinkedIn" width="20" height="20" />
										</a>
										<a 
											href="<?php echo esc_url( main_get_social_share_url( 'instagram' ) ); ?>" 
											class="share-icon share-icon--instagram" 
											aria-label="Share on Instagram"
											target="_blank"
											rel="noopener noreferrer"
										>
											<img src="<?php echo esc_url( main_get_image_url( 'instagram.svg' ) ); ?>" alt="Instagram" width="20" height="20" />
										</a>
										<a 
											href="<?php echo esc_url( main_get_social_share_url( 'facebook' ) ); ?>" 
											class="share-icon share-icon--facebook" 
											aria-label="Share on Facebook"
											target="_blank"
											rel="noopener noreferrer"
										>
											<img src="<?php echo esc_url( main_get_image_url( 'facebook.svg' ) ); ?>" alt="Facebook" width="20" height="20" />
										</a>
									</div>
								</div>
							</div>
							<div class="post-author-bar lg:items-center gap-6 border border-x-0 border-gray-200 py-8 md:py-7">
								<?php
								$author_id = get_the_author_meta( 'ID' );
								$author_name = get_the_author();
								$author_job_title = main_get_author_job_title( $author_id );
								$author_avatar = get_avatar(
									$author_id,
									52,
									'',
									$author_name,
									array(
										'class' => 'w-full h-auto object-cover',
										'loading' => 'eager',
									)
								);
								// Add itemprop="image" to avatar for schema.org
								$author_avatar = str_replace( '<img', '<img itemprop="image"', $author_avatar );
								?>
								<address
									class="flex items-center gap-3 w-full max-w-[231px] not-italic"
									itemscope
									itemtype="https://schema.org/Person">
									<div
										class="rounded-full overflow-hidden w-12 md:w-[3.25rem] [&_img]:w-full [&_img]:h-auto [&_img]:object-cover">
										<?php echo $author_avatar; ?>
									</div>
									<div class="flex flex-col justify-center gap-0.5 md:gap-0">
										<span
											class="text-base md:text-lg font-semibold leading-none md:leading-7 text-gray-800"
											itemprop="name">
											<?php echo esc_html( $author_name ); ?>
										</span>
										<?php if ( ! empty( $author_job_title ) ) : ?>
											<span
												class="text-xs md:text-sm font-medium leading-4 md:leading-5 tracking-2p text-gray-500"
												itemprop="jobTitle">
												<?php echo esc_html( $author_job_title ); ?>
											</span>
										<?php endif; ?>
									</div>
								</address>
								<div class="post-author-ai flex flex-col md:flex-row gap-3 md:items-center">
									<span class="post-author-ai__label text-sm font-medium text-gray-500 tracking-2p">
										Summarise on:
									</span>
									<div class="post-author-ai__chips grid grid-cols-2 gap-2 md:flex items-center">
										<a
											href="#"
											class="post-author-ai-chip bg-gray-200 rounded-lg flex items-center text-sm text-gray-800 font-semibold gap-1.5 justify-center p-2.5 [&_img]:w-4 [&_img]:h-auto md:py-2 md:px-3.5">
											<span class="post-author-ai-chip__icon">
												<img src="<?php echo esc_url( main_get_image_url( 'ChatGPT-logo.png' ) ); ?>" alt="ChatGPT" width="16" height="16" />
											</span>
											<span>ChatGPT</span>
										</a>
										<a
											href="#"
											class="post-author-ai-chip bg-gray-200 rounded-lg flex items-center text-sm text-gray-800 font-semibold gap-1.5 justify-center p-2.5 [&_img]:w-4 [&_img]:h-auto md:py-2 md:px-3.5">
											<span class="post-author-ai-chip__icon">
												<img src="<?php echo esc_url( main_get_image_url( 'gemini-logo.png' ) ); ?>" alt="Gemini" width="16" height="16" />
											</span>
											<span>Gemini</span>
										</a>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</header>

					<div class="top-text text-sm md:text-base font-medium tracking-2p text-gray-700 pt-8 md:pt-7 pb-8">
						We've tested and compared the top project management tools to help you find the perfect solution for
						your team. Our comprehensive guide covers features, pricing, and real-world use cases.
					</div>

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="entry-thumbnail mb-8">
							<?php the_post_thumbnail( 'large', array( 'class' => 'w-full rounded-lg' ) ); ?>
						</div>
					<?php endif; ?>

					<div class="entry-content prose prose-lg max-w-none">
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
				</article>
			</div>
		</div>

		<?php
		// Partners Section
		if ( get_theme_mod( 'partners_show_on_single', true ) ) {
			$partners_logos = main_get_partners_logos();
			$partners_title = get_theme_mod( 'partners_title', __( 'Thanks to Our Partners', 'main' ) );
			
			if ( ! empty( $partners_logos ) ) {
				?>
				<section class="partners-section bg-gray-50 py-16 md:py-24">
					<div class="container-1056">
						<div class="flex flex-col items-center gap-12 md:gap-14">
							<!-- Title -->
							<h2 class="text-3xl md:text-4xl font-bold leading-none text-gray-800 text-center">
								<?php echo esc_html( $partners_title ); ?>
							</h2>
							<!-- Partners Grid -->
							<div class="flex flex-col gap-3 md:gap-6 w-full">
								<div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6">
									<?php foreach ( $partners_logos as $partner ) : ?>
										<div class="flex justify-center items-center overflow-hidden bg-white border border-gray-200 rounded-2xl">
											<div class="[&_img]:w-full [&_img]:h-auto [&_img]:max-w-full">
												<img src="<?php echo esc_url( $partner['image'] ); ?>" alt="<?php echo esc_attr( $partner['alt'] ); ?>" />
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</section>
				<?php
			}
		}

		// Related posts
		$related = get_posts( array(
			'category__in'   => wp_get_post_categories( $post->ID ),
			'numberposts'    => 3,
			'post__not_in'   => array( $post->ID ),
		) );

		if ( $related ) {
			?>
			<section class="related-articles-section bg-gray-50 py-16 md:py-24">
				<div class="container-1056 flex flex-col items-center gap-12 md:gap-14">
					<h2 class="text-3xl md:text-4xl font-bold leading-none text-gray-800 text-center"><?php esc_html_e( 'Related Guides', 'main' ); ?></h2>

					<div class="w-full flex flex-col gap-14 md:grid md:grid-cols-2 md:gap-14 lg:grid-cols-3 xl:gap-12">
						<?php
						foreach ( $related as $post ) {
							setup_postdata( $post );
							get_template_part( 'template-parts/content', 'card' );
						}
						wp_reset_postdata();
						?>
					</div>

					<div class="flex justify-center">
						<a href="#" class="btn btn--primary rounded-full">
							Read More Articles
							<svg
								xmlns="http://www.w3.org/2000/svg"
								class="button-icon"
								width="16"
								height="16"
								fill="none"
								viewBox="0 0 16 16">
								<path
									stroke="#fff"
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="1.5"
									d="M6 3.333 10.667 8 6 12.666" />
							</svg>
						</a>
					</div>
				</div>
			</section>
			<?php
		}

		// Comments
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}

	endwhile;
	?>
</main>

<?php
get_footer();

