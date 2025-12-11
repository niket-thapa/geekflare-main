<?php
/**
 * Hero Banner Block Template
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

$heading = $attributes['heading'] ?? 'The Definitive Software Buying Guides';
$description = $attributes['description'] ?? 'Cut through the noise with expert-written guides to choose the perfect tools for your business.';
$hero_image = $attributes['heroImage'] ?? null;
$hero_video_webm = $attributes['heroVideoWebm'] ?? 'https://cdn.geekflare.com/general/gia-standby.webm';
$hero_video_mp4 = $attributes['heroVideoMp4'] ?? 'https://cdn.geekflare.com/general/gia-standby.mp4';
$search_suggestions = $attributes['searchSuggestions'] ?? array();
$search_placeholder = $attributes['searchPlaceholder'] ?? 'Enter keyword';
$search_button_text = $attributes['searchButtonText'] ?? 'Search Guides';

// Get hero image URL
$hero_image_url = '';
if ( $hero_image && isset( $hero_image['id'] ) ) {
	$hero_image_url = wp_get_attachment_image_url( $hero_image['id'], 'full' );
} elseif ( $hero_image && isset( $hero_image['url'] ) ) {
	$hero_image_url = $hero_image['url'];
}
?>

<div class="hero-banner">
	<div class="container-1056 relative z-1 flex flex-col gap-12 md:gap-12 lg:gap-14">
		<div class="hero-banner__content lg:mb-0.25">
			<h1 class="text-center font-bold text-5xl leading-none lg:text-6xl xl:text-7.5xl">
				<?php echo wp_kses_post( nl2br( $heading ) ); ?>
			</h1>
			<div class="hero-banner__description text-sm leading-[1.25rem] font-medium text-center text-gray-500 tracking-2p md:text-base md:leading-normal md:tracking-1p mx-auto max-w-[31rem]">
				<?php echo esc_html( $description ); ?>
			</div>
		</div>
		<div class="hero-banner__form hero-banner-form flex flex-col p-5 gap-6 md:p-7 lg:p-8 lg:flex-row">
			<?php if ( ( $hero_video_webm || $hero_video_mp4 ) || $hero_image_url ) : ?>
				<div class="hero-banner__image mx-auto w-[8.75rem] md:w-40 lg:w-[11.25rem] lg:-my-0.25">
					<?php if ( $hero_video_webm || $hero_video_mp4 ) : ?>
						<video class="max-w-[80%] max-h-[80%] object-contain mx-auto" autoplay muted loop playsinline>
							<?php if ( $hero_video_webm ) : ?>
								<source src="<?php echo esc_url( $hero_video_webm ); ?>" type="video/webm"/>
							<?php endif; ?>
							<?php if ( $hero_video_mp4 ) : ?>
								<source src="<?php echo esc_url( $hero_video_mp4 ); ?>" type="video/mp4"/>
							<?php endif; ?>
							Your browser does not support the video tag.
						</video>
					<?php else : ?>
						<img src="<?php echo esc_url( $hero_image_url ); ?>" alt="<?php echo esc_attr( $heading ); ?>" loading="eager">
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="hero-banner__form-content hero-form-content flex flex-col gap-5 lg:flex-1 lg:py-1">
				<form action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" method="get" aria-label="<?php esc_attr_e( 'Search buying guides', 'main' ); ?>">
					<div class="relative flex flex-col gap-3 md:flex-row md:gap-2">
						<div class="relative flex-1">
							<div class="absolute inset-y-0 start-0 flex items-center ps-3 md:ps-4 md:ms-0.5 pointer-events-none [&_svg]:w-5 [&_svg]:h-5 md:[&_svg]:w-6 md:[&_svg]:h-6" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" role="presentation">
									<path d="M9.17 15c1.54 0 3.03-.62 4.12-1.71A5.85 5.85 0 0 0 15 9.17c0-1.55-.62-3.03-1.71-4.13a5.85 5.85 0 0 0-4.12-1.71c-1.55 0-3.03.62-4.13 1.71a5.87 5.87 0 0 0-1.71 4.13c0 1.54.62 3.03 1.71 4.12A5.87 5.87 0 0 0 9.17 15m7.5 1.67-3.34-3.34" style="fill:none;stroke:#717680;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.5"/>
								</svg>
							</div>
							<label for="bannerSearch" class="sr-only"><?php esc_html_e( 'Search for buying guides', 'main' ); ?></label>
							<input 
								type="text" 
								id="bannerSearch" 
								name="s"
								class="form-input ps-11 pe-4 md-large md:ps-14 md:pe-16" 
								placeholder="<?php echo esc_attr( $search_placeholder ); ?>" 
								value="<?php echo get_search_query(); ?>"
								aria-describedby="search-description"
							>
							<span id="search-description" class="sr-only"><?php esc_html_e( 'Search our collection of expert-written software buying guides', 'main' ); ?></span>
						</div>
						<div class="hidden md:absolute md:inset-y-0 md:end-3 md:flex md:items-center">
							<button class="btn btn--primary rounded-full text-sm leading-5 py-3 px-4 w-full md:w-auto" type="submit">
								<?php echo esc_html( $search_button_text ); ?>
							</button>
						</div>
					</div>
				</form>
				<?php if ( ! empty( $search_suggestions ) ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Popular search suggestions', 'main' ); ?>">
						<ul class="search-suggestions flex flex-wrap gap-3 list-none p-0 m-0">
							<?php foreach ( $search_suggestions as $index => $suggestion ) : ?>
								<?php if ( ! empty( $suggestion ) ) : ?>
									<li>
										<button 
											type="button" 
											class="search-suggestions-item <?php echo $index === 0 ? 'active' : ''; ?>" 
											data-value="<?php echo esc_attr( $suggestion ); ?>" 
											aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>"
										>
											<?php echo esc_html( $suggestion ); ?>
										</button>
									</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

