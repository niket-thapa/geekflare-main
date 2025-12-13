<?php
/**
 * Template for displaying search form
 *
 * @package Main
 * @since 1.0.0
 */
?>

<form role="search" method="get" class="main-search-form mx-auto max-w-xl w-full" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only">
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'main' ); ?></span>
	</label>
	<input type="search" class="form-input header-search__input search-field" placeholder="<?php esc_attr_e( 'Search...', 'main' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="btn btn--primary header-search__submit hidden lg:flex" aria-label="<?php esc_attr_e( 'Submit search', 'main' ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
			<path d="M11 18a7.001 7.001 0 1 0 0-14.002A7.001 7.001 0 0 0 11 18m9 2-4-4" style="fill:none;stroke:#FFFFFF;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8"/>
		</svg>
	</button>
</form>

