<?php
/**
 * Template part for displaying post cards
 *
 * @package Main
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-item flex flex-col gap-5.5 md:gap-6' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="article-thumb relative rounded-2xl overflow-hidden block">
			<?php the_post_thumbnail( 'large', array( 'class' => 'w-full object-cover' ) ); ?>
		</a>
	<?php endif; ?>
	<div class="flex flex-col gap-4">
		<?php
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			?>
			<div class="flex flex-wrap gap-2">
				<?php
				foreach ( array_slice( $categories, 0, 1 ) as $category ) {
					echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="text-xs font-semibold tracking-widest uppercase text-primary">' . esc_html( $category->name ) . '</a>';
				}
				?>
			</div>
			<?php
		}
		?>
		<div class="flex flex-col gap-2">
			<?php the_title( '<div class="text-lg font-semibold"><a href="' . esc_url( get_permalink() ) . '" class="text-gray-900 hover:text-eva-prime-600">', '</a></div>' ); ?>
			<div class="text-sm font-medium text-gray-500 tracking-2p leading-5 line-clamp-1 md:tracking-1p">
				<?php the_excerpt(); ?>
			</div>
		</div>
	</div>
</article>
