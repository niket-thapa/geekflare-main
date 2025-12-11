<?php
/**
 * Numbers Section Block Template
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

$heading = $attributes['heading'] ?? 'Empowering businesses like yours since 2015';
$description = $attributes['description'] ?? '';
$stats = $attributes['stats'] ?? array();

// Ensure stats is an array
if ( ! is_array( $stats ) ) {
	$stats = array();
}
?>

<div class="home-numbers-section py-16 md:py-24">
	<div class="container-1056 flex flex-col items-center gap-12 md:gap-14">
		<div class="flex flex-col items-center text-center gap-3">
			<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="text-center text-3xl md:text-4xl lg:text-5xl font-bold font-gilroy leading-none md:leading-none lg:leading-none">
					<?php echo wp_kses_post( nl2br( $heading ) ); ?>
				</h2>
			<?php endif; ?>
			
			<?php if ( ! empty( $description ) ) : ?>
				<div class="numbers-section-text md:text-base md:leading-normal text-gray-500 mx-auto text-center max-w-[53.75rem] tracking-2p md:tracking-1p text-sm leading-5">
					<?php echo esc_html( $description ); ?>
				</div>
			<?php endif; ?>
		</div>
		
		<?php if ( ! empty( $stats ) ) : ?>
			<div class="numbers-highlight grid grid-cols-2 gap-4 md:gap-8 lg:grid-cols-3 w-full max-w-[56.5rem]">
				<?php foreach ( $stats as $index => $stat ) : ?>
					<?php
					if ( empty( $stat['number'] ) && empty( $stat['label'] ) ) {
						continue;
					}
					
					// Determine column span and order for responsive layout
					$col_span = 'col-span-1';
					$order = $index + 1;
					
					// Second stat (index 1) spans 2 columns on mobile
					if ( $index === 1 && count( $stats ) === 3 ) {
						$col_span = 'col-span-2';
						$order = 3;
					} elseif ( $index === 2 && count( $stats ) === 3 ) {
						$order = 2;
					}
					?>
					<div class="stats-card <?php echo esc_attr( $col_span ); ?> lg:col-span-1 order-<?php echo esc_attr( $order ); ?> lg:order-none flex flex-col items-center justify-center text-center p-6 md:p-10 gap-2 md:gap-3 bg-white rounded-2xl shadow-[0px_56px_23px_rgba(191,191,191,0.01),0px_32px_19px_rgba(191,191,191,0.05),0px_14px_14px_rgba(191,191,191,0.09),0px_4px_8px_rgba(191,191,191,0.1)]">
						<?php if ( ! empty( $stat['number'] ) ) : ?>
							<span class="text-3xl md:text-5xl font-bold font-gilroy leading-none md:leading-none bg-gradient-to-r from-[#FFC33D] via-[#FF7E29] to-[#FF4A00] bg-clip-text text-transparent">
								<?php echo esc_html( $stat['number'] ); ?>
							</span>
						<?php endif; ?>
						
						<?php if ( ! empty( $stat['label'] ) ) : ?>
							<span class="text-sm md:text-base font-medium font-gilroy leading-5 md:leading-6 tracking-2p text-gray-500">
								<?php echo esc_html( $stat['label'] ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

