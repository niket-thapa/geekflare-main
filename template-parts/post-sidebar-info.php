<?php
/**
 * Info Article Sidebar Template (Enhanced)
 * 
 * Displays table of contents with custom blocks support.
 * Includes: .why_trust_us, #honorable-mentions, .final_verdict, .schema-faq
 *
 * @package Main
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get post settings
$post_id = get_the_ID();
$show_toc = main_sidebar_show_toc( $post_id );
$custom_html = main_get_sidebar_custom_html( $post_id );
$headings = main_get_post_headings( $post_id );
?>
<div class="lg:sticky lg:start-0 lg:top-20">
	<aside class="table-of-contents info-sidebar w-full border border-gray-200 rounded-xl overflow-hidden flex flex-col lg:sticky lg:start-0 lg:top-20">
	
		<?php if ( $show_toc && ! empty( $headings ) ) : ?>
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
	
		<!-- Table of Content Content (Auto-generated with custom blocks) -->
		<nav
			id="toc-content"
			class="toc-content accordion-panel flex flex-col"
			role="region"
			aria-live="polite">
			<div class="accordion-panel__inner flex flex-col gap-2 p-4">
				<?php foreach ( $headings as $index => $heading ) : ?>
	
					<?php if ( 2 === $heading['level'] ) : ?>
						<!-- H2 Level Items -->
						<a
							href="#<?php echo esc_attr( $heading['id'] ); ?>"
							class="toc-item <?php echo 0 === $index ? 'toc-item--active' : ''; ?> block px-3 py-2.5 gap-2 rounded-lg <?php echo 0 === $index ? 'bg-eva-prime-50' : 'bg-gray-50'; ?> text-sm <?php echo 0 === $index ? 'font-semibold' : 'font-medium'; ?> text-gray-800 transition-colors"
							data-heading-id="<?php echo esc_attr( $heading['id'] ); ?>">
							<span><?php echo esc_html( $heading['text'] ); ?></span>
						</a>
	
					<?php elseif ( 3 === $heading['level'] ) : ?>
						<!-- H3 Level Items (indented) -->
						<a
							href="#<?php echo esc_attr( $heading['id'] ); ?>"
							class="toc-item block px-3 py-2.5 gap-2 rounded-lg bg-gray-50 text-sm font-medium text-gray-800 transition-colors"
							data-heading-id="<?php echo esc_attr( $heading['id'] ); ?>">
							<span><?php echo esc_html( $heading['text'] ); ?></span>
						</a>
					<?php endif; ?>
	
				<?php endforeach; ?>
			</div>
		</nav>
		<?php endif; ?>
	
	</aside>
	
	<?php if ( ! empty( $custom_html ) ) : ?>
	<!-- Custom HTML Section -->
	<div class="custom-html-section mt-5">
		<div class="p-4">
			<div class="custom-html-content text-sm text-gray-800 [&_p]:mb-3 [&_p:last-child]:mb-0 [&_a]:text-blue-600 [&_a]:underline [&_h1]:text-base [&_h1]:font-bold [&_h1]:mb-2 [&_h2]:text-base [&_h2]:font-bold [&_h2]:mb-2 [&_h3]:text-sm [&_h3]:font-bold [&_h3]:mb-2">
				<?php echo do_shortcode( wp_kses_post( $custom_html ) ); ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php
		// Widget area below sidebar
		if ( is_active_sidebar( 'info-article-sidebar' ) ) {
			echo '<div class="sidebar-widgets mt-6">';
			dynamic_sidebar( 'info-article-sidebar' );
			echo '</div>';
		}
	?>
</div>

