<?php
/**
 * The template for displaying archive pages
 *
 * @package Main
 * @since 1.0.0
 */

// Get current archive context
$current_category = null;
$current_tag = null;
$current_author = null;
$sticky_post_for_banner = null;

// Handle category archives
if ( is_category() ) {
	$current_category = get_queried_object();
	
	// Get sticky post from current category if available
	if ( $current_category && ! empty( $current_category->term_id ) ) {
		$sticky_post_ids = get_option( 'sticky_posts' );
		
		if ( ! empty( $sticky_post_ids ) && is_array( $sticky_post_ids ) ) {
			// Get sticky posts that belong to the current category
			$sticky_posts = get_posts( array(
				'post_type'           => 'post',
				'posts_per_page'      => -1,
				'post__in'            => $sticky_post_ids,
				'category__in'        => array( $current_category->term_id ),
				'ignore_sticky_posts' => false,
			) );
			
			// If multiple sticky posts, pick one randomly
			if ( ! empty( $sticky_posts ) ) {
				if ( count( $sticky_posts ) > 1 ) {
					$sticky_post_for_banner = $sticky_posts[ array_rand( $sticky_posts ) ];
				} else {
					$sticky_post_for_banner = $sticky_posts[0];
				}
			}
		}
	}
}
// Handle tag archives
elseif ( is_tag() ) {
	$current_tag = get_queried_object();
	
	// Get sticky post from current tag if available
	if ( $current_tag && ! empty( $current_tag->term_id ) ) {
		$sticky_post_ids = get_option( 'sticky_posts' );
		
		if ( ! empty( $sticky_post_ids ) && is_array( $sticky_post_ids ) ) {
			// Get sticky posts that have the current tag
			$sticky_posts = get_posts( array(
				'post_type'           => 'post',
				'posts_per_page'      => -1,
				'post__in'            => $sticky_post_ids,
				'tag__in'             => array( $current_tag->term_id ),
				'ignore_sticky_posts' => false,
			) );
			
			// If multiple sticky posts, pick one randomly
			if ( ! empty( $sticky_posts ) ) {
				if ( count( $sticky_posts ) > 1 ) {
					$sticky_post_for_banner = $sticky_posts[ array_rand( $sticky_posts ) ];
				} else {
					$sticky_post_for_banner = $sticky_posts[0];
				}
			}
		}
	}
}
// Handle author archives
elseif ( is_author() ) {
	$current_author = get_queried_object();
	
	// Get sticky post from current author if available
	if ( $current_author && ! empty( $current_author->ID ) ) {
		$sticky_post_ids = get_option( 'sticky_posts' );
		
		if ( ! empty( $sticky_post_ids ) && is_array( $sticky_post_ids ) ) {
			// Get sticky posts by the current author
			$sticky_posts = get_posts( array(
				'post_type'           => 'post',
				'posts_per_page'      => -1,
				'post__in'            => $sticky_post_ids,
				'author'              => $current_author->ID,
				'ignore_sticky_posts' => false,
			) );
			
			// If multiple sticky posts, pick one randomly
			if ( ! empty( $sticky_posts ) ) {
				if ( count( $sticky_posts ) > 1 ) {
					$sticky_post_for_banner = $sticky_posts[ array_rand( $sticky_posts ) ];
				} else {
					$sticky_post_for_banner = $sticky_posts[0];
				}
			}
		}
	}
}

// Exclude the sticky post from the main query to avoid duplication
if ( $sticky_post_for_banner ) {
	$sticky_post_id_to_exclude = $sticky_post_for_banner->ID;
	function main_exclude_sticky_from_archive( $query ) {
		if ( ! is_admin() && $query->is_main_query() && ( is_category() || is_tag() || is_author() ) ) {
			$sticky_id = isset( $GLOBALS['main_sticky_post_id'] ) ? $GLOBALS['main_sticky_post_id'] : null;
			if ( $sticky_id ) {
				$post_not_in = $query->get( 'post__not_in' );
				if ( ! is_array( $post_not_in ) ) {
					$post_not_in = array();
				}
				$post_not_in[] = $sticky_id;
				$query->set( 'post__not_in', $post_not_in );
			}
		}
	}
	$GLOBALS['main_sticky_post_id'] = $sticky_post_id_to_exclude;
	add_action( 'pre_get_posts', 'main_exclude_sticky_from_archive' );
}

get_header();
?>

<main id="main-content" class="site-main">
	<?php if ( have_posts() ) : ?>
		<div class="archive-banner pt-8 pb-12 md:py-14 lg:py-16 xl:py-20">
			<div class="container-1056">
				<div class="flex flex-col gap-12 lg:grid lg:grid-cols-[1fr_41.675%] lg:gap-14 xl:gap-20">
					<div class="flex flex-col gap-6 min-w-0 lg:self-center">
						<?php main_breadcrumbs(); ?>
						<?php
						the_archive_title( '<h1 class="page-title text-4xl md:text-5xl xl:text-6xl md:leading-none xl:leading-none font-bold text-gray-800">', '</h1>' );
						the_archive_description( '<div class="archive-description text-sm md:text-base font-medium text-gray-500 tracking-2p">', '</div>' );
						?>
						<?php
						// Get post count for current archive
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
					<?php
					// Display sticky post if found
					if ( $sticky_post_for_banner ) :
						$sticky_post = $sticky_post_for_banner;
						setup_postdata( $sticky_post );
						
						// Get context-appropriate taxonomy term for display
						$display_term_name = '';
						$display_term_url = '#';
						
						if ( is_tag() && $current_tag ) {
							// For tag archives, show the current tag
							$display_term_name = $current_tag->name;
							$display_term_url = get_tag_link( $current_tag->term_id );
						} elseif ( is_author() && $current_author ) {
							// For author archives, show primary category (or author name if no category)
							$categories = get_the_category( $sticky_post->ID );
							$primary_category = ! empty( $categories ) ? $categories[0] : null;
							if ( $primary_category ) {
								$display_term_name = $primary_category->name;
								$display_term_url = get_category_link( $primary_category->term_id );
							} else {
								$display_term_name = $current_author->display_name;
								$display_term_url = get_author_posts_url( $current_author->ID );
							}
						} else {
							// For category archives, show primary category
							$categories = get_the_category( $sticky_post->ID );
							$primary_category = ! empty( $categories ) ? $categories[0] : null;
							if ( $primary_category ) {
								$display_term_name = $primary_category->name;
								$display_term_url = get_category_link( $primary_category->term_id );
							}
						}
						
						// Get excerpt
						$excerpt = $sticky_post->post_excerpt;
						if ( empty( $excerpt ) ) {
							$excerpt = wp_trim_words( $sticky_post->post_content, 20, '...' );
						}
						?>
						<div class="min-w-0">
							<article class="flex flex-col gap-6 article-item">
								<a href="<?php echo esc_url( get_permalink( $sticky_post->ID ) ); ?>" class="article-thumb relative rounded-2xl overflow-hidden">
									<?php if ( has_post_thumbnail( $sticky_post->ID ) ) : ?>
										<?php echo get_the_post_thumbnail( $sticky_post->ID, 'large', array( 'class' => 'w-full object-cover' ) ); ?>
									<?php else : ?>
										<img
											src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23e5e7eb' width='400' height='300'/%3E%3C/svg%3E"
											alt="<?php echo esc_attr( get_the_title( $sticky_post->ID ) ); ?>"
											class="w-full object-cover" />
									<?php endif; ?>
									<div class="absolute top-3 start-3 m-0.25 inline-flex items-center gap-1 px-2.5 py-2 rounded-full bg-white text-xs leading-[1.25] font-semibold text-primary shadow-sm">
										<span class="inline-flex h-4 w-4 items-center justify-center" aria-hidden="true">
											<svg
												xmlns="http://www.w3.org/2000/svg"
												width="17"
												height="17"
												viewBox="0 0 17 17"
												aria-hidden="true"
												role="presentation">
												<path
													d="M9.11 1.14q-.09-.08-.21-.11t-.24.01q-.12.03-.22.11-.09.09-.13.2l-1.4 3.84L5.38 3.7q-.08-.07-.18-.11-.11-.04-.22-.03-.1.01-.2.06t-.16.14Q2.55 6.48 2.54 9.15c0 1.49.59 2.91 1.64 3.96a5.592 5.592 0 0 0 9.55-3.96c0-3.78-3.23-6.86-4.62-8.02zm2.58 8.62q-.1.55-.36 1.05-.26.49-.66.89-.39.4-.89.66t-1.05.35q-.21.04-.38-.08-.17-.13-.21-.33-.03-.21.09-.38t.33-.21c1.05-.17 1.95-1.07 2.12-2.12q.05-.2.22-.32.17-.11.37-.08t.32.2q.12.16.1.37"
													style="fill: #e84300" />
											</svg>
										</span>
										<span>Spotlight</span>
									</div>
								</a>
								<div class="flex flex-col gap-4">
									<?php if ( ! empty( $display_term_name ) ) : ?>
										<a href="<?php echo esc_url( $display_term_url ); ?>" class="text-xs font-semibold tracking-widest uppercase text-primary">
											<?php echo esc_html( $display_term_name ); ?>
										</a>
									<?php endif; ?>
									<div class="flex flex-col gap-4 md:gap-2">
										<h3 class="text-xl md:text-2xl md:leading-8 font-semibold">
											<a class="text-gray-800" href="<?php echo esc_url( get_permalink( $sticky_post->ID ) ); ?>">
												<?php echo esc_html( get_the_title( $sticky_post->ID ) ); ?>
											</a>
										</h3>
										<?php if ( ! empty( $excerpt ) ) : ?>
											<div class="text-sm font-medium text-gray-500 leading-5 tracking-1p">
												<?php echo esc_html( $excerpt ); ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<a href="<?php echo esc_url( get_permalink( $sticky_post->ID ) ); ?>" class="text-sm font-semibold text-primary btn-read-guide lg:hidden">
									Read Guide
								</a>
							</article>
						</div>
						<?php
						wp_reset_postdata();
					endif;
					?>
				</div>
			</div>
		</div>
		<div class="container-1056 flex flex-col gap-y-12 md:gap-y-14 py-14 md:py-20 xl:py-24">
			<div class="archive-filter flex flex-col lg:flex-row lg:justify-between gap-6 lg:items-center">
				<h2 class="text-3xl lg:text-4xl leading-none lg:leading-none font-bold text-gray-800"><?php esc_html_e('Filter by Topic', 'main'); ?></h2>
				<?php
				// Get current archive context for search form
				$current_author_id = null;
				$current_tag_id = null;
				if ( is_author() ) {
					$current_author = get_queried_object();
					if ( $current_author && isset( $current_author->ID ) ) {
						$current_author_id = $current_author->ID;
					}
				} elseif ( is_tag() ) {
					$current_tag_id = get_queried_object_id();
				}
				?>
				<form 
					role="search" 
					method="get" 
					class="flex flex-col md:flex-row gap-2 archive-search-form -mt-1 md:mt-0"
					action="<?php echo esc_url( get_pagenum_link( 1 ) ); ?>"
					data-category-id="<?php echo is_category() && $current_category ? esc_attr( $current_category->term_id ) : ''; ?>"
					data-tag-id="<?php echo $current_tag_id ? esc_attr( $current_tag_id ) : ''; ?>"
					data-author-id="<?php echo $current_author_id ? esc_attr( $current_author_id ) : ''; ?>">
						<div class="relative w-full md:max-w-[21.25rem] md:w-[21.25rem]">
							<div
								class="absolute inset-y-0 start-0 start-4 flex items-center pointer-events-none [&_svg]:w-5 [&_svg]:h-5 md:[&_svg]:w-6 md:[&_svg]:h-6 z-10">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
									<path
										stroke="#717680"
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="1.5"
										d="M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14M20 20l-4-4" />
								</svg>
							</div>
							<input
								type="search"
								id="articlesSearch"
								name="archive_search"
								class="archive-search-input form-input h-12 md:h-14 ps-11 pe-4 md:ps-14 tracking-2p"
								placeholder="<?php esc_attr_e( 'Search articles..', 'main' ); ?>"
								value="<?php echo esc_attr( main_get_archive_search_query() ); ?>"
								autocomplete="off" />
						</div>
						<div class="relative w-[12.5625rem] md:w-[14.5625rem]">
							<div
								class="absolute inset-y-0 start-0 start-4 flex items-center pointer-events-none [&_svg]:w-5 [&_svg]:h-5 md:[&_svg]:w-6 md:[&_svg]:h-6">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
									<path
										stroke="#535862"
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="1.5"
										d="M18 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2M16 3v4M8 3v4M4 11h16M10 16h4" />
								</svg>
							</div>
							<input
								type="search"
								id="dateRangeSearch"
								name="archive_date_range"
								class="archive-datepicker-input form-input h-12 md:h-14 ps-11 pe-4 md:ps-14 tracking-2p"
								placeholder="Filter by Date posted"
								value="<?php echo esc_attr( main_get_archive_date_range_value() ); ?>" />
							<input type="hidden" id="archiveDateStart" name="archive_date_start" value="<?php echo esc_attr( main_get_archive_date_start() ); ?>" />
							<input type="hidden" id="archiveDateEnd" name="archive_date_end" value="<?php echo esc_attr( main_get_archive_date_end() ); ?>" />
						</div>
				</form>
			</div>
			<?php
			// Get subcategories from the current category
			$subcategories = array();
			$current_category_id = null;

			if ( is_category() ) {
				$current_category = get_queried_object();
				$current_category_id = $current_category->term_id;
				
				// Get child categories
				$child_categories = get_terms( array(
					'taxonomy'   => 'category',
					'parent'     => $current_category_id,
					'hide_empty' => true,
					'orderby'    => 'name',
					'order'      => 'ASC',
				) );
				
				if ( ! empty( $child_categories ) && ! is_wp_error( $child_categories ) ) {
					foreach ( $child_categories as $child_cat ) {
						$subcategories[] = array(
							'term'  => $child_cat,
							'count' => $child_cat->count,
						);
					}
				}
			}

			// Check if viewing a subcategory
			$is_subcategory = false;
			$parent_category_id = null;
			if ( is_category() ) {
				$current_category = get_queried_object();
				if ( $current_category->parent > 0 ) {
					$is_subcategory = true;
					$parent_category_id = $current_category->parent;
				}
			}

			// Always show the filter (at minimum the "All Articles" button will show)
			$current_category_url = is_category() ? get_category_link( $current_category_id ) : '';
			$is_parent_category = is_category() && ! $is_subcategory;
			?>
			<div
				class="relative after:content-[''] after:absolute after:inset-y-0 after:end-0 md:after:end-[3.75rem] after:w-24 after:bg-[linear-gradient(90deg,rgba(252,252,255,0)_0%,#FCFCFF_100%)] after:pointer-events-none md:pe-[3.75rem]">
				<div class="tags-filter flex gap-2.5 overflow-x-auto scrollbar-hide">
					<?php if ( is_category() ) : ?>
						<a href="<?php echo esc_url( $current_category_url ); ?>" class="tag-item flex items-center text-sm whitespace-nowrap font-semibold text-gray-500 gap-1.5 rounded-xl md:rounded-2xl border border-gray-200 py-3 px-3.5 md:text-base md:leading-5.5 md:px-4 <?php echo $is_parent_category ? 'active' : ''; ?> [&.active]:bg-primary [&.active]:text-white [&.active]:border-primary hover:text-primary hover:bg-success-50 hover:border-primary transition-colors duration-300">
							<span class="tags-filter-item-name">All Articles</span>
							<span class="tags-filter-item-count text-xs tracking-2p md:text-sm">
								(<?php echo esc_html( number_format_i18n( $post_count ) ); ?>)
							</span>
						</a>
					<?php endif; ?>
					
					<?php foreach ( $subcategories as $subcat_data ) : 
						$subcat = $subcat_data['term'];
						$subcat_count = $subcat_data['count'];
						$subcat_url = get_category_link( $subcat->term_id );
						$is_active = is_category() && $current_category_id === $subcat->term_id;
						?>
						<a href="<?php echo esc_url( $subcat_url ); ?>" class="tag-item flex items-center text-sm whitespace-nowrap font-semibold text-gray-500 gap-1.5 rounded-xl md:rounded-2xl border border-gray-200 py-3 px-3.5 md:text-base md:leading-5.5 md:px-4 <?php echo $is_active ? 'active' : ''; ?> [&.active]:bg-primary [&.active]:text-white [&.active]:border-primary hover:text-primary hover:bg-success-50 hover:border-primary transition-colors duration-300">
							<span class="tags-filter-item-name"><?php echo esc_html( $subcat->name ); ?></span>
							<span class="tags-filter-item-count text-xs tracking-2p md:text-sm">
								(<?php echo esc_html( number_format_i18n( $subcat_count ) ); ?>)
							</span>
						</a>
					<?php endforeach; ?>
				</div>
				<?php if ( ! empty( $subcategories ) ) : ?>
					<button
						type="button"
						id="tagsFilterScrollBtn"
						class="hidden md:flex items-center justify-center bg-success-50 border border-eva-prime-200 w-12 h-12 rounded-2xl absolute z-1 end-0 top-0 opacity-0 pointer-events-none">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
							<path
								stroke="#e84300"
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="1.5"
								d="m9 6 6 6-6 6" />
						</svg>
					</button>
				<?php endif; ?>
			</div>

			<div id="archive-posts-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-14 md:gap-x-8 xl:gap-x-12">
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', get_post_type() );
					endwhile;
				} else {
					// Show message if no posts found (including search)
					$search_query = main_get_archive_search_query();
					if ( ! empty( $search_query ) ) {
						?>
						<div class="col-span-full text-center py-12">
							<p class="text-gray-500 text-lg font-medium">
								<?php
								/* translators: %s: search query */
								printf( esc_html__( 'No articles found matching "%s".', 'main' ), esc_html( $search_query ) );
								?>
							</p>
							<p class="text-gray-400 text-sm mt-2">
								<?php esc_html_e( 'Try different keywords or clear your search.', 'main' ); ?>
							</p>
						</div>
						<?php
					} else {
						get_template_part( 'template-parts/content', 'none' );
					}
				}
				?>
			</div>

				<?php main_pagination(); ?>
		</div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</main>

<?php
get_footer();

