<?php
/**
 * Template part for displaying partners section
 *
 * @package Main
 * @since 1.0.0
 */

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
					<h4 class="text-xl md:text-2xl font-bold leading-none text-gray-800 text-center">
						<?php echo esc_html( $partners_title ); ?>
					</h4>
					<?php // Partners Grid ?>
					<div class="flex flex-col gap-3 md:gap-6 w-full">
						<div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-5">
							<?php foreach ( $partners_logos as $partner ) : ?>
								<?php
								$partner_url = isset( $partner['url'] ) && ! empty( $partner['url'] ) ? $partner['url'] : '#';
								$link_attrs = '';
								if ( $partner_url !== '#' ) {
									$link_attrs = 'target="_blank" rel="noopener noreferrer"';
								}
								?>
								<a href="<?php echo esc_url( $partner_url ); ?>" <?php echo $link_attrs; ?> class="flex justify-center items-center overflow-hidden bg-white border-2 border-transparent hover:border-gray-200 transition-colors duration-300 rounded-2xl h-20 md:h-32 px-7 md:px-10 py-4">
									<figure class="[&_img]:w-full [&_img]:h-auto [&_img]:max-w-full">
										<img src="<?php echo esc_url( $partner['image'] ); ?>" alt="<?php echo esc_attr( $partner['alt'] ); ?>" />
									</figure>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

