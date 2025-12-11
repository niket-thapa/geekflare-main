<?php
/**
 * Article Carousel Block Template
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

$heading = $attributes['heading'] ?? 'Protect your digital assets.';
$category_ids = $attributes['categories'] ?? array();
$post_limit = isset( $attributes['postLimit'] ) ? intval( $attributes['postLimit'] ) : 6;
$button_text = $attributes['buttonText'] ?? 'Read More Articles';
$button_link_type = $attributes['buttonLinkType'] ?? 'custom';
$button_link_id = $attributes['buttonLinkId'] ?? '';
$button_custom_url = $attributes['buttonCustomUrl'] ?? '#';

// Generate unique carousel ID for this block instance
// Use static counter combined with microtime hash to ensure each block gets a truly unique ID
static $carousel_counter = 0;
$carousel_counter++;
// Generate unique suffix from microtime and counter to ensure uniqueness
$unique_suffix = substr( md5( uniqid( (string) $carousel_counter, true ) ), 0, 8 );
$carousel_id = 'articles-carousel-' . $carousel_counter . '-' . $unique_suffix;

// Build button URL based on link type
$button_url = '#';
if ( $button_link_type === 'category' && ! empty( $button_link_id ) ) {
	$button_url = get_category_link( intval( $button_link_id ) );
} elseif ( $button_link_type === 'tag' && ! empty( $button_link_id ) ) {
	$button_url = get_tag_link( intval( $button_link_id ) );
} elseif ( $button_link_type === 'custom' ) {
	$button_url = $button_custom_url;
}

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

// Get posts from selected categories (including subcategories)
$posts = get_posts( array(
	'post_type'      => 'post',
	'posts_per_page' => $post_limit,
	'category__in'   => $all_category_ids,
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
?>

<section class="security-guides-section bg-gray-25 py-16 md:py-24 overflow-hidden">
	<div class="container-1056 flex flex-col gap-12 md:gap-14">
		<div class="flex items-end md:justify-between gap-6">
			<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="text-3xl md:text-5xl font-bold font-gilroy text-gray-900 leading-none md:leading-none">
					<?php echo wp_kses_post( nl2br( $heading ) ); ?>
				</h2>
			<?php endif; ?>
			<div class="flex items-center gap-2 md:gap-3">
				<button
					type="button"
					aria-label="Previous article"
					class="btn btn--outline rounded-full max-sm:py-2 max-sm:px-3"
					data-flickity-control="prev"
					data-flickity-target="#<?php echo esc_attr( $carousel_id ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 100 100">
						<style>
							.s0 {
								fill: none;
								stroke: #252b37;
								stroke-linecap: round;
								stroke-linejoin: round;
								stroke-width: 9.4;
							}
						</style>
						<path d="M79.17 50H20.83M45.83 75l-25-25M45.83 25l-25 25" class="s0" />
					</svg>
				</button>
				<button
					type="button"
					aria-label="Next article"
					class="btn btn--outline rounded-full max-sm:py-2 max-sm:px-3"
					data-flickity-control="next"
					data-flickity-target="#<?php echo esc_attr( $carousel_id ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
						<style>
							.se0 {
								fill: none;
								stroke: #252b37;
								stroke-linecap: round;
								stroke-linejoin: round;
								stroke-width: 1.5;
							}
						</style>
						<path d="M3.33 8h9.34M8.67 12l4-4M8.67 4l4 4" class="se0" />
					</svg>
				</button>
			</div>
		</div>

		<?php if ( ! empty( $posts ) ) : ?>
			<div
				id="<?php echo esc_attr( $carousel_id ); ?>"
				class="articles-carousel -mx-6"
				data-flickity='<?php echo esc_attr( wp_json_encode( array(
					'cellAlign'       => 'left',
					'pageDots'        => false,
					'wrapAround'      => false,
					'imagesLoaded'    => true,
					'prevNextButtons' => false,
					'contain'         => true,
				) ) ); ?>'>
				<?php foreach ( $posts as $post ) : ?>
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
					<div class="w-[23rem] px-6">
						<article class="article-item flex flex-col gap-5.5 md:gap-6">
							<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="article-thumb relative rounded-2xl overflow-hidden">
								<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
									<?php echo get_the_post_thumbnail( $post->ID, 'large', array( 'class' => 'w-full object-cover' ) ); ?>
								<?php else : ?>
									<img
										src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23e5e7eb' width='400' height='300'/%3E%3C/svg%3E"
										alt="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>"
										class="w-full object-cover" />
								<?php endif; ?>
							</a>
							<div class="flex flex-col gap-4">
								<?php if ( ! empty( $category_name ) ) : ?>
									<a href="<?php echo esc_url( $category_url ); ?>" class="text-xs font-semibold tracking-widest uppercase text-primary">
										<?php echo esc_html( $category_name ); ?>
									</a>
								<?php endif; ?>
								<div class="flex flex-col gap-2">
									<h3 class="text-lg font-semibold text-gray-900 md:-tracking-2p">
										<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
											<?php echo esc_html( get_the_title( $post->ID ) ); ?>
										</a>
									</h3>
									<?php if ( ! empty( $excerpt ) ) : ?>
										<div class="text-sm font-medium text-gray-500 tracking-2p leading-5 line-clamp-1 md:tracking-1p">
											<?php echo esc_html( $excerpt ); ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</article>
					</div>
				<?php endforeach; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		<?php endif; ?>

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
	</div>
</section>