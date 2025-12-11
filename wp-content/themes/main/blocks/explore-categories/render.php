<?php
/**
 * Explore Categories Block Template
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

$heading = $attributes['heading'] ?? 'Explore guides by category';
$categories = $attributes['categories'] ?? array();

// Ensure categories is an array
if ( ! is_array( $categories ) ) {
	$categories = array();
}

// Get theme directory URL for images
$image_base_url = get_template_directory_uri() . '/assets/images/';
?>

<div class="explore-category-section py-6 md:py-8 lg:py-10">
	<div class="container-1280">
		<div class="home-categories-container-1056 rounded-3xl md:rounded-4xl p-5 md:p-12 lg:p-16 xl:p-24 flex flex-col gap-6 md:gap-11 lg:gap-12 xl:gap-14">
			<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="text-center text-white text-3xl md:text-4xl lg:text-5xl font-bold font-gilroy leading-none pt-3 mt-0.5 pb-0.5 md:p-0 lg:-mb-0.25">
					<?php echo esc_html( $heading ); ?>
				</h2>
			<?php endif; ?>
			
			<?php if ( ! empty( $categories ) ) : ?>
				<div class="home-categories-content pt-1 flex flex-col gap-3 md:flex-row md:flex-wrap md:gap-6 md:justify-center xl:px-6">
					<?php foreach ( $categories as $category ) : ?>
						<?php
						if ( empty( $category['title'] ) ) {
							continue;
						}
						
						// Get the URL - can be category, tag, or custom URL
						$link_url = '#';
						if ( ! empty( $category['linkType'] ) && ! empty( $category['linkId'] ) ) {
							if ( $category['linkType'] === 'category' ) {
								$link_url = get_category_link( $category['linkId'] );
							} elseif ( $category['linkType'] === 'tag' ) {
								$link_url = get_tag_link( $category['linkId'] );
							}
						} elseif ( ! empty( $category['customUrl'] ) ) {
							$link_url = esc_url( $category['customUrl'] );
						}
						
						// Get icon - check if it's a media attachment with ID
						$icon_id = null;
						$icon_url = '';
						if ( ! empty( $category['icon'] ) && ! empty( $category['icon']['id'] ) ) {
							$icon_id = intval( $category['icon']['id'] );
						} elseif ( ! empty( $category['icon'] ) && ! empty( $category['icon']['url'] ) ) {
							$icon_url = $category['icon']['url'];
						} elseif ( ! empty( $category['iconUrl'] ) ) {
							$icon_url = $category['iconUrl'];
						}
						
						$title = $category['title'] ?? '';
						$subtitle = $category['subtitle'] ?? '';
						$icon_alt = ! empty( $title ) ? esc_attr( $title . ' category icon' ) : '';
						?>
						<a href="<?php echo esc_url( $link_url ); ?>" class="home-categories-item flex w-full md:flex-[0_0_calc(50%-0.75rem)] xl:flex-[0_0_calc(33.333%-1rem)] p-3 md:p-4 bg-white rounded-xl gap-4 items-center">
							<?php if ( ! empty( $icon_id ) ) : ?>
								<div class="home-categories-item-icon w-11">
									<?php echo wp_get_attachment_image( $icon_id, 'full', false, array( 'alt' => $icon_alt ) ); ?>
								</div>
							<?php elseif ( ! empty( $icon_url ) ) : ?>
								<div class="home-categories-item-icon w-11">
									<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $icon_alt ); ?>" />
								</div>
							<?php endif; ?>
							
							<div class="home-categories-item-text flex-1">
								<?php if ( ! empty( $title ) ) : ?>
									<h3 class="text-base font-gilroy font-semibold leading-normal md:text-lg">
										<?php echo esc_html( $title ); ?>
									</h3>
								<?php endif; ?>
								
								<?php if ( ! empty( $subtitle ) ) : ?>
									<div class="text-xs text-gray-500 tracking-2p leading-4 md:text-sm md:leading-5 md:tracking-1p">
										<?php echo esc_html( $subtitle ); ?>
									</div>
								<?php endif; ?>
							</div>
							
							<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-auto" width="20" height="20" fill="none" viewBox="0 0 20 20" aria-hidden="true" role="presentation">
								<path stroke="#252b37" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 4.167 13.333 10 7.5 15.833" />
							</svg>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

