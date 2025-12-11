<?php
/**
 * Insights Section Block Template
 *
 * @var array $attributes Block attributes.
 * @var string $content Block default content.
 * @var WP_Block $block Block instance.
 *
 * @package Main
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$heading = $attributes['heading'] ?? 'Fresh insights to help you make smarter software decisions';
$category_ids = $attributes['categories'] ?? array();
$sticky_limit = isset( $attributes['stickyLimit'] ) ? intval( $attributes['stickyLimit'] ) : 3;
$regular_limit = isset( $attributes['regularLimit'] ) ? intval( $attributes['regularLimit'] ) : 3;
$move_sticky_to_right = isset( $attributes['moveStickyToRight'] ) && $attributes['moveStickyToRight'];
$heading_align = isset( $attributes['headingAlign'] ) ? $attributes['headingAlign'] : 'left';
$show_button = isset( $attributes['showButton'] ) && $attributes['showButton'];
$button_text = $attributes['buttonText'] ?? 'Read More Articles';
$button_link_type = $attributes['buttonLinkType'] ?? 'custom';
$button_link_id = $attributes['buttonLinkId'] ?? '';
$button_custom_url = $attributes['buttonCustomUrl'] ?? '#';

// Ensure category_ids is an array
if ( ! is_array( $category_ids ) ) {
	$category_ids = array();
}

// Filter out invalid IDs
$category_ids = array_filter( array_map( 'intval', $category_ids ) );

if ( empty( $category_ids ) ) {
	return;
}

// Get all category IDs including subcategories
$all_category_ids = array();
foreach ( $category_ids as $cat_id ) {
	// Add the parent category
	$all_category_ids[] = $cat_id;
	
	// Get all child categories recursively
	$child_cats = get_term_children( $cat_id, 'category' );
	if ( ! is_wp_error( $child_cats ) && ! empty( $child_cats ) ) {
		$all_category_ids = array_merge( $all_category_ids, $child_cats );
	}
}

// Remove duplicates
$all_category_ids = array_unique( $all_category_ids );

// Get sticky posts from selected categories
$sticky_post_ids = get_option( 'sticky_posts' );
$sticky_posts = array();

if ( ! empty( $sticky_post_ids ) && is_array( $sticky_post_ids ) ) {
	// Get sticky posts that belong to selected categories (including subcategories)
	$sticky_posts = get_posts( array(
		'post_type'      => 'post',
		'posts_per_page' => $sticky_limit * 2, // Get more to filter by category
		'post__in'        => $sticky_post_ids,
		'category__in'    => $all_category_ids, // Use all category IDs including children
		'orderby'         => 'date',
		'order'           => 'DESC',
		'ignore_sticky_posts' => false,
	) );
	
	// Limit sticky posts
	$sticky_posts = array_slice( $sticky_posts, 0, $sticky_limit );
}

// Get regular (non-sticky) posts from selected categories
$sticky_post_ids = ! empty( $sticky_posts ) ? array_column( $sticky_posts, 'ID' ) : array();
$regular_posts = get_posts( array(
	'post_type'      => 'post',
	'posts_per_page' => $regular_limit,
	'category__in'    => $all_category_ids, // Use all category IDs including children
	'post__not_in'    => $sticky_post_ids,
	'orderby'         => 'date',
	'order'           => 'DESC',
) );

// Get sticky posts class
$sticky_posts_class = 'min-w-0 sticky_posts';
if ( $move_sticky_to_right ) {
	$sticky_posts_class .= ' lg:order-2';
}

// Get heading alignment class
$heading_align_class = 'text-' . ( $heading_align === 'center' ? 'center' : ( $heading_align === 'right' ? 'right' : 'left' ) );

// Build button URL based on link type
$button_url = '#';
if ( $button_link_type === 'category' && ! empty( $button_link_id ) ) {
	$button_url = get_category_link( intval( $button_link_id ) );
} elseif ( $button_link_type === 'tag' && ! empty( $button_link_id ) ) {
	$button_url = get_tag_link( intval( $button_link_id ) );
} elseif ( $button_link_type === 'custom' ) {
	$button_url = $button_custom_url;
}
?>

<section class="insights-section bg-gray-25 py-16 md:py-24">
	<div class="container-1056 flex flex-col gap-12 lg:gap-16">
		<div class="flex flex-col gap-4 <?php echo esc_attr( $heading_align_class ); ?>">
			<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="text-3xl md:text-5xl font-bold font-gilroy text-gray-900 leading-none md:leading-none max-w-[66rem]">
					<?php echo wp_kses_post( nl2br( $heading ) ); ?>
				</h2>
			<?php endif; ?>
		</div>
		
		<div class="flex flex-col gap-20 lg:grid lg:grid-cols-2 lg:gap-16 xl:gap-24">
			<div class="<?php echo esc_attr( $sticky_posts_class ); ?>">
				<?php if ( ! empty( $sticky_posts ) ) : ?>
					<div
						data-flickity='<?php echo esc_attr( wp_json_encode( array(
							'cellAlign'       => 'left',
							'wrapAround'      => true,
							'pageDots'        => true,
							'imagesLoaded'    => true,
							'prevNextButtons' => false,
							'autoPlay'        => true,
						) ) ); ?>'
						class="-m-6 feature-articles-carousel"
					>
						<?php foreach ( $sticky_posts as $post ) : ?>
							<?php
							setup_postdata( $post );
							
							// Get primary category
							$categories = get_the_category( $post->ID );
							$primary_category = ! empty( $categories ) ? $categories[0] : null;
							$category_name = $primary_category ? $primary_category->name : '';
							$category_url = $primary_category ? get_category_link( $primary_category->term_id ) : '#';
							
							// Get excerpt
							$excerpt = $post->post_excerpt;
							if ( empty( $excerpt ) ) {
								$excerpt = wp_trim_words( $post->post_content, 20, '...' );
							}
							?>
							<article class="article-item w-full flex flex-col gap-6 p-6">
								<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="article-thumb relative rounded-2xl overflow-hidden">
									<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
										<?php echo get_the_post_thumbnail( $post->ID, 'large', array( 'class' => 'w-full object-cover' ) ); ?>
									<?php else : ?>
										<img
											src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23e5e7eb' width='400' height='300'/%3E%3C/svg%3E"
											alt="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>"
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
									<?php if ( ! empty( $category_name ) ) : ?>
										<a href="<?php echo esc_url( $category_url ); ?>" class="text-xs font-semibold tracking-widest uppercase text-primary">
											<?php echo esc_html( $category_name ); ?>
										</a>
									<?php endif; ?>
									<h3 class="text-xl md:text-2xl font-semibold text-gray-900 md:-tracking-2p">
										<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
											<?php echo esc_html( get_the_title( $post->ID ) ); ?>
										</a>
									</h3>
									<?php if ( ! empty( $excerpt ) ) : ?>
										<div class="text-sm font-medium text-gray-500 leading-5 tracking-1p">
											<?php echo esc_html( $excerpt ); ?>
										</div>
									<?php endif; ?>
									<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="text-sm font-semibold text-primary btn-read-guide py-2 md:py-3">
										Read Guide
									</a>
								</div>
							</article>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				<?php endif; ?>
			</div>
			
			<div class="flex flex-col gap-8 min-w-0 other_posts">
				<?php if ( ! empty( $regular_posts ) ) : ?>
					<?php foreach ( $regular_posts as $index => $post ) : ?>
						<?php
						setup_postdata( $post );
						
						// Get primary category
						$categories = get_the_category( $post->ID );
						$primary_category = ! empty( $categories ) ? $categories[0] : null;
						$category_name = $primary_category ? $primary_category->name : '';
						$category_url = $primary_category ? get_category_link( $primary_category->term_id ) : '#';
						
						// Get excerpt
						$excerpt = $post->post_excerpt;
						if ( empty( $excerpt ) ) {
							$excerpt = wp_trim_words( $post->post_content, 15, '...' );
						}
						
						$is_last = ( $index === count( $regular_posts ) - 1 );
						?>
						<article class="flex flex-col gap-4<?php echo $is_last ? '' : ' border-b border-gray-200 pb-8'; ?>">
							<?php if ( ! empty( $category_name ) ) : ?>
								<a href="<?php echo esc_url( $category_url ); ?>" class="text-xs font-semibold tracking-widest uppercase text-primary">
									<?php echo esc_html( $category_name ); ?>
								</a>
							<?php endif; ?>
							<div class="flex flex-col gap-2">
								<h3 class="text-lg md:text-xl md:-tracking-2p font-semibold text-gray-900">
									<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
										<?php echo esc_html( get_the_title( $post->ID ) ); ?>
									</a>
								</h3>
								<?php if ( ! empty( $excerpt ) ) : ?>
									<div class="text-sm font-medium text-gray-500 leading-5 tracking-2p md:tracking-1p line-clamp-1">
										<?php echo esc_html( $excerpt ); ?>
									</div>
								<?php endif; ?>
							</div>
							<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="text-sm font-semibold text-primary btn-read-guide md:py-2 md:mt-1">
								Read Guide
							</a>
						</article>
					<?php endforeach; ?>
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( $show_button ) : ?>
			<div class="flex justify-center">
				<a href="<?php echo esc_url( $button_url ); ?>" class="btn btn--primary rounded-full">
					<?php echo esc_html( $button_text ); ?>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16">
						<path
							stroke="#fff"
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="1.5"
							d="M6 3.333 10.667 8 6 12.666" />
					</svg>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>