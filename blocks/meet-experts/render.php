<?php
/**
 * Meet Experts Block Template
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

$heading = $attributes['heading'] ?? 'Meet our experts';
$subheading = $attributes['subheading'] ?? 'Discover in-depth insights, reviews, and guides crafted by our team of industry experts.';
$author_ids = $attributes['authors'] ?? array();

// Ensure author_ids is an array
if ( ! is_array( $author_ids ) ) {
	$author_ids = array();
}

// Filter out invalid IDs
$author_ids = array_filter( array_map( 'intval', $author_ids ) );

if ( empty( $author_ids ) ) {
	return;
}

// Group authors by 4
if ( ! function_exists( 'main_group_authors' ) ) {
	function main_group_authors( $authors, $group_size = 4 ) {
		$groups = array();
		$count = count( $authors );
		for ( $i = 0; $i < $count; $i += $group_size ) {
			$groups[] = array_slice( $authors, $i, $group_size );
		}
		return $groups;
	}
}

// Get author data
$authors_data = array();
foreach ( $author_ids as $author_id ) {
	$author = get_userdata( $author_id );
	if ( ! $author ) {
		continue;
	}

	// Get author job title
	$job_title = main_get_author_job_title( $author_id );

	// Get author bio
	$bio = get_user_meta( $author_id, 'description', true );
	if ( empty( $bio ) ) {
		$bio = get_the_author_meta( 'description', $author_id );
	}

	// Avatar will be handled by get_avatar() function

	// Author archive URL
	$archive_url = get_author_posts_url( $author_id );

	// Author display name
	$display_name = $author->display_name ?: $author->user_nicename;

	$authors_data[] = array(
		'id'            => $author_id,
		'name'          => $display_name,
		'bio'           => $bio,
		'job_title'     => $job_title,
		'archive_url'   => $archive_url,
	);
}

if ( empty( $authors_data ) ) {
	return;
}

$author_groups = main_group_authors( $authors_data, 4 );
$show_carousel = count( $authors_data ) > 4;
?>

<?php
// Get texture image URLs
$texture_mobile_url = main_get_image_url( 'texture-mobile.svg' );
$texture_desktop_url = main_get_image_url( 'texture-desktop.svg' );
?>
<section class="meet-experts-section relative isolate overflow-hidden bg-[#FFF8F5] py-8 md:py-24">
	<span 
		class="absolute inset-0 pointer-events-none opacity-70 bg-cover meet-experts-texture"
		style="--texture-mobile: url('<?php echo esc_url( $texture_mobile_url ); ?>'); --texture-desktop: url('<?php echo esc_url( $texture_desktop_url ); ?>'); background-image: var(--texture-mobile);"
	></span>
	<style>
		@media (min-width: 1024px) {
			.meet-experts-texture {
				background-image: var(--texture-desktop) !important;
			}
		}
	</style>
	
	<div class="container-1056 relative flex flex-col items-center gap-8 md:gap-14">
		<div class="flex flex-col items-center text-center gap-3 md:gap-3 max-w-[22.75rem] md:max-w-4xl pt-8 pb-4 md:py-0">
			<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="text-3xl md:text-5xl font-bold font-gilroy text-gray-900 leading-none md:leading-none">
					<?php echo wp_kses_post( nl2br( $heading ) ); ?>
				</h2>
			<?php endif; ?>
			
			<?php if ( ! empty( $subheading ) ) : ?>
				<div class="text-sm md:text-base font-medium text-gray-500 tracking-2p md:tracking-1p">
					<?php echo esc_html( $subheading ); ?>
				</div>
			<?php endif; ?>
		</div>
		
		<div class="w-full">
			<?php if ( $show_carousel ) : ?>
				<div
					data-flickity='<?php echo esc_attr( wp_json_encode( array(
						'cellAlign'       => 'left',
						'wrapAround'      => true,
						'pageDots'        => true,
						'imagesLoaded'    => true,
						'prevNextButtons' => false,
						'autoPlay'        => 3000,
					) ) ); ?>'
					class="-mx-6 -my-12 md:-m-12 meet-experts-carousel"
				>
			<?php else : ?>
				<div>
			<?php endif; ?>
				<?php foreach ( $author_groups as $group ) : ?>
					<div class="w-full flex flex-col gap-4 px-6 py-12 md:p-12 md:max-w-none md:grid md:grid-cols-2 md:gap-8">
						<?php foreach ( $group as $author ) : ?>
							<article class="flex flex-col gap-4 md:gap-4 rounded-2xl bg-white p-6 md:p-6 shadow-[0px_56px_23px_rgba(191,191,191,0.01),0px_32px_19px_rgba(191,191,191,0.05),0px_14px_14px_rgba(191,191,191,0.09),0px_4px_8px_rgba(191,191,191,0.1)]">
								<div class="flex items-center gap-4 pb-2">
									<?php echo get_avatar( $author['id'], 52, '', $author['name'], array( 'class' => 'h-[3.25rem] w-[3.25rem] rounded-full object-cover' ) ); ?>
									
									<div class="flex flex-col gap-0.5">
										<h3 class="text-base md:text-lg font-semibold text-gray-900 md:-tracking-2p">
											<?php echo esc_html( $author['name'] ); ?>
										</h3>
										<div class="text-xs md:text-sm font-medium text-gray-500 tracking-2p md:tracking-1p">
											<?php echo esc_html( $author['job_title'] ); ?>
										</div>
									</div>
								</div>
								
								<?php if ( ! empty( $author['bio'] ) ) : ?>
									<div class="text-sm font-medium text-gray-500 leading-5 tracking-2p md:tracking-1p">
										<?php echo esc_html( $author['bio'] ); ?>
									</div>
								<?php endif; ?>
								
								<a
									href="<?php echo esc_url( $author['archive_url'] ); ?>"
									class="text-sm font-semibold text-primary btn-read-guide md:py-3"
								>
									<?php esc_html_e( 'Read Guide', 'main' ); ?>
								</a>
							</article>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>

