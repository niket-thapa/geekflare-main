<?php
/**
 * Template Name: Main
 * 
 * A minimal page template with only the main wrapper
 * No extra divs, just the main content area
 *
 * @package Main
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>

<?php
get_footer();

