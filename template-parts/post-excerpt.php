<?php
/**
 * Template part for displaying post excerpt
 *
 * @package Main
 * @since 1.0.0
 */

if ( has_excerpt() ) :
	?>
	<div class="top-text text-base md:text-lg lg:text-xl xl:text-[1.375rem] lg:leading-[1.75] font-medium tracking-2p py-3 md:py-4 border-l-4 ps-4 mb-6 md:mb-8 border-s-[#ffa405]">
		<?php echo wp_kses_post( get_the_excerpt() ); ?>
	</div>
	<?php
endif;

