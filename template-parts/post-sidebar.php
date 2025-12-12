<?php
/**
 * Post Sidebar Template (Buying Guide - Fixed Loop)
 *
 * Displays table of contents with all headings properly looped.
 *
 * @package Main
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get post settings
$post_id = get_the_ID();
$show_toc = main_sidebar_show_toc( $post_id );
$show_filters = main_sidebar_show_filters( $post_id );
$headings = main_get_post_headings( $post_id );

// Get all Best Suited For terms (only if filters are enabled)
$best_suited_terms = array();
if ( $show_filters ) {
	$best_suited_terms = get_terms(
		array(
			'taxonomy'   => MAIN_PRODUCTS_BEST_SUITED_TAXONOMY,
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
	if ( is_wp_error( $best_suited_terms ) ) {
		$best_suited_terms = array();
	}
}

// Get all Features terms (only if filters are enabled)
$features_terms = array();
if ( $show_filters ) {
	$features_terms = get_terms(
		array(
			'taxonomy'   => MAIN_PRODUCTS_FEATURES_TAXONOMY,
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
	if ( is_wp_error( $features_terms ) ) {
		$features_terms = array();
	}
}

$features_limit = 10;
$has_more_features = count( $features_terms ) > $features_limit;
?>
<div class="lg:sticky lg:start-0 lg:top-20">
	<aside class="table-of-contents w-full border border-gray-200 rounded-xl overflow-hidden flex flex-col">
	
		<?php if ( $show_toc && ! empty( $headings ) ) : ?>
		<!-- Table of Content Header -->
		<button
			type="button"
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
		</button>
	
		<!-- Table of Content Content -->
		<nav
			id="toc-content"
			class="toc-content accordion-panel flex flex-col"
			role="region"
			aria-live="polite">
			<div class="accordion-panel__inner flex flex-col gap-2 p-4 <?php echo $show_filters ? 'border-b border-gray-200' : ''; ?>">
				<?php foreach ( $headings as $index => $heading ) : ?>
	
					<?php if ( 2 === $heading['level'] ) : ?>
						<!-- H2 Level Items -->
						
						<a href="#<?php echo esc_attr( $heading['id'] ); ?>"
							class="toc-item <?php echo 0 === $index ? 'toc-item--active' : ''; ?> block px-3 py-2.5 gap-2 rounded-lg <?php echo 0 === $index ? 'bg-eva-prime-50' : 'bg-gray-50'; ?> text-sm <?php echo 0 === $index ? 'font-semibold' : 'font-medium'; ?> text-gray-800 transition-colors"
							data-heading-id="<?php echo esc_attr( $heading['id'] ); ?>">
							<span><?php echo esc_html( $heading['text'] ); ?></span>
						</a>
	
						<?php if ( ! empty( $heading['children'] ) ) : ?>
							<!-- Nested Products -->
							<div class="toc-nested ms-6 border-l border-gray-200 flex flex-col">
								<?php foreach ( $heading['children'] as $child_index => $child ) : ?>
									
									<a href="#<?php echo esc_attr( $child['id'] ); ?>"
										class="toc-nested-item flex items-center gap-1 py-2.5 px-0 text-sm font-medium text-gray-800 transition-colors"
										data-heading-id="<?php echo esc_attr( $child['id'] ); ?>">
										<span class="w-2 text-gray-200 -ms-0.5">—</span>
										<span><?php echo esc_html( $child['text'] ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
	
					<?php elseif ( 3 === $heading['level'] ) : ?>
						<!-- H3 Level Items -->
						
						<a href="#<?php echo esc_attr( $heading['id'] ); ?>"
							class="toc-item block px-3 py-2.5 gap-2 rounded-lg bg-gray-50 text-sm font-medium text-gray-800 transition-colors"
							data-heading-id="<?php echo esc_attr( $heading['id'] ); ?>">
							<span><?php echo esc_html( $heading['text'] ); ?></span>
						</a>
					<?php endif; ?>
	
				<?php endforeach; ?>
			</div>
		</nav>
		<?php endif; ?>
	
		<?php if ( $show_filters ) : ?>
		<!-- Filter Header -->
		<button
			type="button"
			class="filter-header flex items-center justify-between px-4 py-[0.8125rem] md:py-3.5 gap-3 w-full bg-gray-50 transition-colors"
			aria-expanded="false"
			aria-controls="filter-content">
			<span class="text-sm font-bold text-gray-800">Filter</span>
			<svg
				class="filter-chevron w-3.5 h-2.5 p-0.5 transition-transform duration-200"
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
		</button>
	
		<!-- Filter Content -->
		<form id="filter-content" class="filter-content accordion-panel block" role="region" aria-live="polite">
			<div class="accordion-panel__inner flex flex-col gap-6 p-4">
	
				<!-- Pricing Section -->
				<div class="filter-group flex flex-col gap-3">
					<div class="filter-group__label text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-500">
						<?php esc_html_e( 'Pricing', 'main' ); ?>
					</div>
					<div class="flex flex-col gap-3">
						<label class="filter-option">
							<input type="radio" name="pricing" value="all" checked />
							<span><?php esc_html_e( 'All', 'main' ); ?></span>
						</label>
						<label class="filter-option">
							<input type="radio" name="pricing" value="free" />
							<span><?php esc_html_e( 'Free', 'main' ); ?></span>
						</label>
						<label class="filter-option">
							<input type="radio" name="pricing" value="0-10" />
							<span><?php esc_html_e( 'Under $10', 'main' ); ?></span>
						</label>
						<label class="filter-option">
							<input type="radio" name="pricing" value="10-20" />
							<span><?php esc_html_e( '$10 - $20', 'main' ); ?></span>
						</label>
						<label class="filter-option">
							<input type="radio" name="pricing" value="20+" />
							<span><?php esc_html_e( '$20+', 'main' ); ?></span>
						</label>
					</div>
				</div>
	
				<!-- Best Suited For Section -->
				<?php if ( ! empty( $best_suited_terms ) ) : ?>
				<div class="filter-group flex flex-col gap-3">
					<div class="filter-group__label text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-500">
						<?php esc_html_e( 'Best Suited For', 'main' ); ?>
					</div>
					<div class="flex flex-col gap-3">
						<?php foreach ( $best_suited_terms as $term ) : ?>
							<label class="filter-option">
								<input type="checkbox" name="best-suited[]" value="<?php echo esc_attr( $term->slug ); ?>" />
								<span><?php echo esc_html( $term->name ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
	
				<!-- Features Section -->
				<?php if ( ! empty( $features_terms ) ) : ?>
				<div class="filter-group flex flex-col gap-3">
					<div class="filter-group__label text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-500">
						<?php esc_html_e( 'Features', 'main' ); ?>
					</div>
					<div class="flex flex-col gap-3" id="features-list">
						<?php 
						$visible_features = array_slice( $features_terms, 0, $features_limit );
						$hidden_features = array_slice( $features_terms, $features_limit );
						?>
						
						<?php foreach ( $visible_features as $term ) : ?>
							<label class="filter-option">
								<input type="checkbox" name="features[]" value="<?php echo esc_attr( $term->slug ); ?>" />
								<span><?php echo esc_html( $term->name ); ?></span>
							</label>
						<?php endforeach; ?>

						<?php if ( $has_more_features ) : ?>
							<div id="hidden-features" style="display: none;">
								<?php foreach ( $hidden_features as $term ) : ?>
									<label class="filter-option" style="margin-top: 12px;">
										<input type="checkbox" name="features[]" value="<?php echo esc_attr( $term->slug ); ?>" />
										<span><?php echo esc_html( $term->name ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
							<button type="button" id="show-more-features" class="text-sm font-semibold text-primary hover:text-hover-primary hover:underline transition-colors mt-2 text-left">
								<?php esc_html_e( 'Show More Features', 'main' ); ?>
							</button>
							<button type="button" id="show-less-features" class="text-sm font-semibold text-primary hover:text-hover-primary hover:underline transition-colors mt-2 text-left" style="display: none;">
								<?php esc_html_e( 'Show Less Features', 'main' ); ?>
							</button>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>

				<!-- Clear Filters Button -->
				<div class="btn_clear_wrap pt-4" style="display: none;">
					<button type="reset" class="btn btn--primary btn--small w-full rounded-full">
						<?php esc_html_e( 'Clear Filters', 'main' ); ?>
					</button>
				</div>

				<!-- Results Count -->
				<div class="filter-results-count text-sm text-gray-600 pt-2 border-t border-gray-200" style="display: none;">
					<span id="results-count">0</span> <?php esc_html_e( 'products found', 'main' ); ?>
				</div>
			</div>
		</form>
		<?php endif; ?>
	
	</aside>
	<?php
		// Widget area below sidebar
		if ( is_active_sidebar( 'buying-guide-sidebar' ) ) {
			echo '<div class="sidebar-widgets mt-6">';
			dynamic_sidebar( 'buying-guide-sidebar' );
			echo '</div>';
		}
	?>
</div>