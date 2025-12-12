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
					<?php
					if ( is_singular() ) {
						the_title( '<h1 class="entry-title text-4xl md:text-5xl font-bold text-gray-800 leading-none md:leading-[1.2]">', '</h1>' );
					} else {
						the_title( '<h2 class="entry-title text-4xl md:text-5xl font-bold text-gray-800 leading-none md:leading-[1.2]"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
					}

					if ( 'post' === get_post_type() ) {
						?>
						<div class="post-meta-bar relative">
							<div class="post-meta-info">
								<span class="post-meta-updated">
									Last Updated :
									<time datetime="<?php echo esc_attr( get_the_modified_date( 'Y-m-d' ) ); ?>">
										<?php echo esc_html( get_the_modified_date( 'j M, Y' ) ); ?>
									</time>
								</span>
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
										href="<?php echo esc_url( main_get_social_share_url( 'twitter' ) ); ?>" 
										class="share-icon share-icon--twitter" 
										aria-label="Share on X"
										target="_blank"
										rel="noopener noreferrer"
									>
										<img src="<?php echo esc_url( main_get_image_url( 'x.svg' ) ); ?>" alt="X" width="20" height="20" />
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
							$author_bio = get_the_author_meta( 'description', $author_id );
							$author_archive_url = get_author_posts_url( $author_id );
							
							// Get social media URLs from WordPress contact methods
							$linkedin_url = get_the_author_meta( 'linkedin', $author_id );
							$x_username = get_the_author_meta( 'twitter', $author_id );
							// Construct X URL from username (handle both full URLs and usernames)
							$x_url = '';
							if ( ! empty( $x_username ) ) {
								if ( strpos( $x_username, 'http' ) === 0 || strpos( $x_username, 'x.com' ) !== false || strpos( $x_username, 'twitter.com' ) !== false ) {
									// Already a full URL
									$x_url = $x_username;
								} else {
									// Just username, construct URL
									$x_username = ltrim( $x_username, '@' ); // Remove @ if present
									$x_url = 'https://x.com/' . $x_username;
								}
							}
							$facebook_url = get_the_author_meta( 'facebook', $author_id );
							$instagram_url = get_the_author_meta( 'instagram', $author_id );
							
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
								class="flex items-center gap-3 w-full max-w-[231px] not-italic relative post-author-wrap"
								itemscope
								itemtype="https://schema.org/Person">
								<div
									class="rounded-full overflow-hidden w-12 md:w-[3.25rem] [&_img]:w-full [&_img]:h-auto [&_img]:object-cover relative z-10">
									<?php echo $author_avatar; ?>
								</div>
								<div class="flex flex-col justify-center gap-0.5 md:gap-0 relative z-10">
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
								<div class="author-info-drop absolute -start-4 -top-4 pt-[5.5rem] rounded-2xl bg-white px-5 pb-5 w-[22.75rem] md:w-[25rem] border z-1 border-[#E9EAEB] shadow-[0px_56px_23px_rgba(191,191,191,0.01),0px_32px_19px_rgba(191,191,191,0.05),0px_14px_14px_rgba(191,191,191,0.09),0px_4px_8px_rgba(191,191,191,0.1)]">
									<div class="author-drop-body">
										<?php if ( ! empty( $author_bio ) ) : ?>
											<div class="author-bio text-sm text-gray-800 tracking-2p border-y border-gray-200 py-4 [&_p]:m-0">
												<?php echo wpautop( wp_kses_post( $author_bio ) ); ?>
											</div>
										<?php endif; ?>
										<div class="flex items-center justify-between pt-4">
											<?php if ( ! empty( $linkedin_url ) || ! empty( $x_url ) || ! empty( $facebook_url ) || ! empty( $instagram_url ) ) : ?>
												<div class="post-meta-share__icons flex items-center gap-2.5 lg:[&_img]:w-7 lg:[&_svg]:w-7">
													<?php if ( ! empty( $linkedin_url ) ) : ?>
														<a
															href="<?php echo esc_url( $linkedin_url ); ?>"
															class="share-icon share-icon--linkedin"
															aria-label="Visit LinkedIn Profile"
															target="_blank"
															rel="noopener noreferrer"
														>
															<img src="<?php echo esc_url( main_get_image_url( 'linkedin.svg' ) ); ?>" alt="LinkedIn" width="24" height="24" />
														</a>
													<?php endif; ?>
													<?php if ( ! empty( $x_url ) ) : ?>
														<a
															href="<?php echo esc_url( $x_url ); ?>"
															class="share-icon share-icon--x"
															aria-label="Visit X Profile"
															target="_blank"
															rel="noopener noreferrer"
														>
															<img src="<?php echo esc_url( main_get_image_url( 'x.svg' ) ); ?>" alt="X" width="20" height="20" />
														</a>
													<?php endif; ?>
													<?php if ( ! empty( $instagram_url ) ) : ?>
														<a
															href="<?php echo esc_url( $instagram_url ); ?>"
															class="share-icon share-icon--instagram"
															aria-label="Visit Instagram Profile"
															target="_blank"
															rel="noopener noreferrer"
														>
															<img src="<?php echo esc_url( main_get_image_url( 'instagram.svg' ) ); ?>" alt="Instagram" width="24" height="24" />
														</a>
													<?php endif; ?>
													<?php if ( ! empty( $facebook_url ) ) : ?>
														<a
															href="<?php echo esc_url( $facebook_url ); ?>"
															class="share-icon share-icon--facebook"
															aria-label="Visit Facebook Profile"
															target="_blank"
															rel="noopener noreferrer"
														>
															<img src="<?php echo esc_url( main_get_image_url( 'facebook.svg' ) ); ?>" alt="Facebook" width="24" height="24" />
														</a>
													<?php endif; ?>
												</div>
											<?php endif; ?>
											<a href="<?php echo esc_url( $author_archive_url ); ?>" class="text-sm font-semibold text-primary btn-read-guide md:py-1">Read Full Bio</a>
										</div>
									</div>
								</div>
							</address>
							<div class="post-author-ai flex flex-col md:flex-row gap-3 md:items-center">
								<span class="post-author-ai__label text-sm font-medium text-gray-500 tracking-2p">
									Summarise on:
								</span>
								<div class="post-author-ai__chips grid grid-cols-2 gap-2 md:flex items-center">
									<?php
									$current_url = esc_url( get_permalink() );
									$chatgpt_url = 'https://chat.openai.com/?q=summarize+this+guide+at+' . urlencode( $current_url );
									$gemini_url = 'https://www.google.com/search?udm=50&aep=11&q=summarize+this+article+' . urlencode( $current_url );
									?>
									<a
										href="<?php echo esc_url( $chatgpt_url ); ?>"
										data-tooltip="Summarize on ChatGPT"
										class="post-author-ai-chip tooltip-trigger bg-gray-200 rounded-lg flex items-center text-sm text-gray-800 font-semibold gap-1.5 justify-center p-2.5 [&_img]:w-4 [&_img]:h-auto md:py-2 md:px-3.5"
										target="_blank"
										rel="noopener noreferrer">
										<span class="post-author-ai-chip__icon">
											<img src="<?php echo esc_url( main_get_image_url( 'ChatGPT-logo.png' ) ); ?>" alt="ChatGPT" width="16" height="16" />
										</span>
										<span>ChatGPT</span>
									</a>
									<a
										href="<?php echo esc_url( $gemini_url ); ?>"
										data-tooltip="Summarize on Google AI"
										class="post-author-ai-chip tooltip-trigger bg-gray-200 rounded-lg flex items-center text-sm text-gray-800 font-semibold gap-1.5 justify-center p-2.5 [&_img]:w-4 [&_img]:h-auto md:py-2 md:px-3.5"
										target="_blank"
										rel="noopener noreferrer">
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

				<div class="mobile_sidebar_wrap lg:row-start-1 lg:row-span-2 lg:col-start-2 py-6 lg:py-0 sticky start-0 top-0 lg:static max-lg:bg-white lg:bg-none max-lg:z-50">
            <?php get_template_part( 'template-parts/post-sidebar-info' ); ?>
        </div>

				<div class="lg:row-start-2 lg:col-start-1 min-w-0">
					<?php if ( has_excerpt() ) : ?>
						<div class="top-text text-sm md:text-base lg:text-lg font-medium tracking-2p pt-8 md:pt-7 pb-8">
							<?php echo wp_kses_post( get_the_excerpt() ); ?>
						</div>
					<?php endif; ?>
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
		if ( get_theme_mod( 'partners_show_on_single', true ) ) {
			$partners_logos = main_get_partners_logos();
			$partners_title = get_theme_mod( 'partners_title', __( 'Thanks to Our Partners', 'main' ) );
			
			if ( ! empty( $partners_logos ) ) {
				?>
				<section class="partners-section bg-gray-50 py-16 md:py-24">
					<div class="container-1056">
						<div class="flex flex-col items-center gap-12 md:gap-14">
							<?php // Title ?>
							<h2 class="text-3xl md:text-4xl font-bold leading-none text-gray-800 text-center">
								<?php echo esc_html( $partners_title ); ?>
							</h2>
							<?php // Partners Grid ?>
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

		// Comments
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}

	endwhile;
	?>
</main>

<?php
get_footer();

