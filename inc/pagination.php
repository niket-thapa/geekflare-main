<?php
/**
 * Custom Pagination Function
 * 
 * Displays a professional, accessible pagination with first/last and prev/next controls
 * 
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Pagination Function
 * 
 * @since 1.0.0
 * @return void
 */
function main_pagination() {
	global $wp_query;
	
	// Get pagination data
	$total_pages = $wp_query->max_num_pages;
	$current_page = max( 1, get_query_var( 'paged' ) );
	
	// Exit early if pagination not needed
	if ( $total_pages <= 1 ) {
		return;
	}
	
	// Generate page number links
	$page_numbers = paginate_links( array(
		'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
		'format'    => '?paged=%#%',
		'current'   => $current_page,
		'total'     => $total_pages,
		'mid_size'  => 1,
		'end_size'  => 1,
		'type'      => 'array',
		'prev_next' => false,
		'add_args'  => false,
	) );
	
	// Safety check
	if ( ! $page_numbers || ! is_array( $page_numbers ) ) {
		return;
	}
	
	// Check if we're on first or last page
	$is_first_page = ( $current_page === 1 );
	$is_last_page = ( $current_page === $total_pages );
    
	?>
	<nav class="main-pagination" role="navigation" aria-label="<?php esc_attr_e( 'Posts pagination', 'main' ); ?>">
		<div class="nav-links">
			
			<?php
			/**
			 * First Page Button
			 */
			if ( !$is_first_page ) : ?>
				<a class="page-numbers" 
				   href="<?php echo esc_url( get_pagenum_link( 1 ) ); ?>"
				   aria-label="<?php esc_attr_e( 'Go to first page', 'main' ); ?>"
				   title="<?php esc_attr_e( 'First page', 'main' ); ?>">
					<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<polyline points="11 17 6 12 11 7"></polyline>
						<polyline points="18 17 13 12 18 7"></polyline>
					</svg>
					<span class="sr-only"><?php esc_html_e( 'First', 'main' ); ?></span>
				</a>
			<?php endif; ?>
			
			<?php
			/**
			 * Previous Page Button
			 */
			if ( !$is_first_page ) : ?>
				<a class="page-numbers" 
				   href="<?php echo esc_url( get_pagenum_link( $current_page - 1 ) ); ?>"
				   aria-label="<?php esc_attr_e( 'Go to previous page', 'main' ); ?>"
				   title="<?php esc_attr_e( 'Previous page', 'main' ); ?>">
					<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<polyline points="15 18 9 12 15 6"></polyline>
					</svg>
					<span class="sr-only"><?php esc_html_e( 'Previous', 'main' ); ?></span>
				</a>
			<?php endif; ?>
			
			<?php
			/**
			 * Page Number Links
			 */
			foreach ( $page_numbers as $page_link ) :
				// Detect if this is the current page
				$is_current = ( strpos( $page_link, 'current' ) !== false );
				
				// Detect if this is dots separator
				$is_dots = ( strpos( $page_link, 'dots' ) !== false );
				
				if ( $is_current ) {
					// Current page already has proper class from WordPress
					$page_link = str_replace( '<span', '<span aria-current="page"', $page_link );
				} elseif ( $is_dots ) {
					// Dots separator - add disabled styling
					$page_link = str_replace(
						'page-numbers dots',
						'page-numbers dots pointer-events-none',
						$page_link
					);
				}
				
				// Output the link as-is (WordPress already added page-numbers class)
				echo wp_kses_post( $page_link );
			endforeach;
			?>
			
			<?php
			/**
			 * Next Page Button
			 */
			if ( !$is_last_page ) : ?>
				<a class="page-numbers" 
				   href="<?php echo esc_url( get_pagenum_link( $current_page + 1 ) ); ?>"
				   aria-label="<?php esc_attr_e( 'Go to next page', 'main' ); ?>"
				   title="<?php esc_attr_e( 'Next page', 'main' ); ?>">
					<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<polyline points="9 18 15 12 9 6"></polyline>
					</svg>
					<span class="sr-only"><?php esc_html_e( 'Next', 'main' ); ?></span>
				</a>
			<?php endif; ?>
			
			<?php
			/**
			 * Last Page Button
			 */
			if ( !$is_last_page ) : ?>
				<a class="page-numbers" 
				   href="<?php echo esc_url( get_pagenum_link( $total_pages ) ); ?>"
				   aria-label="<?php /* translators: %s: total number of pages */ printf( esc_attr__( 'Go to last page (page %s)', 'main' ), $total_pages ); ?>"
				   title="<?php esc_attr_e( 'Last page', 'main' ); ?>">
					<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<polyline points="13 17 18 12 13 7"></polyline>
						<polyline points="6 17 11 12 6 7"></polyline>
					</svg>
					<span class="sr-only"><?php esc_html_e( 'Last', 'main' ); ?></span>
				</a>
			<?php endif; ?>
			
		</div>
		
	</nav>
	<?php
}
