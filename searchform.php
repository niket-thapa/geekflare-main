<?php
/**
 * Template for displaying search form
 *
 * @package Main
 * @since 1.0.0
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only">
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'main' ); ?></span>
		<input type="search" class="search-field px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="<?php esc_attr_e( 'Search...', 'main' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	</label>
	<button type="submit" class="search-submit btn btn--primary rounded-full inline-flex min-w-40 justify-center transition-colors">
		<?php esc_html_e( 'Search', 'main' ); ?>
	</button>
</form>

