<?php
/**
 * The footer template
 *
 * @package Main
 * @since 1.0.0
 */
?>

<footer id="site-footer" class="site-footer bg-gray-100 border-t border-gray-200 pt-12 pb-6 lg:pt-20">
	<div class="container-1056">
		<div class="footer-content flex flex-col gap-12">
			<!-- Logo and Menu Section -->
			<div class="footer-top flex flex-col gap-14 md:grid md:grid-cols-[40%_1fr] lg:grid-cols-[34.7%_1fr] xl:gap-16">
				<!-- Logo -->
				<div class="flex items-start">
					<?php
					$footer_logo = get_theme_mod( 'footer_logo', '' );
					if ( $footer_logo ) {
						echo '<img src="' . esc_url( $footer_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="h-7 md:h-10">';
					} elseif ( has_custom_logo() ) {
						$logo_id = get_theme_mod( 'custom_logo' );
						$logo = wp_get_attachment_image_src( $logo_id, 'full' );
						if ( $logo ) {
							echo '<img src="' . esc_url( $logo[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="h-7 md:h-10">';
						}
					} else {
						echo '<span class="text-xl font-bold text-gray-900">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
					}
					?>
				</div>

				<!-- Menu Sections -->
				<div class="footer-menus flex flex-col md:row-span-2">
					<!-- First Row: COMPANY, LEGAL, GENERAL, PRODUCTS -->
					<div class="footer-menu-row flex flex-row gap-y-14 gap-x-[4.5rem] flex-wrap lg:gap-x-10 lg:justify-between">
						<?php
						// Company Menu Widget
						if ( is_active_sidebar( 'footer-company' ) ) {
							dynamic_sidebar( 'footer-company' );
						}

						// Legal Menu Widget
						if ( is_active_sidebar( 'footer-legal' ) ) {
							dynamic_sidebar( 'footer-legal' );
						}

						// General Menu Widget
						if ( is_active_sidebar( 'footer-general' ) ) {
							dynamic_sidebar( 'footer-general' );
						}

						// Products Menu Widget
						if ( is_active_sidebar( 'footer-products' ) ) {
							dynamic_sidebar( 'footer-products' );
						}
						?>
					</div>
				</div>

				<!-- Email Button -->
				<div class="footer-email -mt-2.5 md:mt-auto flex">
					<?php
					$footer_email = get_theme_mod( 'footer_email', 'info@example.com' );
					if ( $footer_email ) {
						?>
						<a href="mailto:<?php echo esc_attr( $footer_email ); ?>" class="footer-email-button btn btn--primary rounded-full">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1.33301 4.66699L6.77629 8.47729C7.21707 8.78583 7.43746 8.94011 7.67718 8.99986C7.88894 9.05265 8.11041 9.05265 8.32217 8.99986C8.56189 8.94011 8.78228 8.78583 9.22306 8.47729L14.6663 4.66699M4.53301 13.3337H11.4663C12.5864 13.3337 13.1465 13.3337 13.5743 13.1157C13.9506 12.9239 14.2566 12.618 14.4484 12.2416C14.6663 11.8138 14.6663 11.2538 14.6663 10.1337V5.86699C14.6663 4.74689 14.6663 4.18683 14.4484 3.75901C14.2566 3.38269 13.9506 3.07673 13.5743 2.88498C13.1465 2.66699 12.5864 2.66699 11.4663 2.66699H4.53301C3.4129 2.66699 2.85285 2.66699 2.42503 2.88498C2.0487 3.07673 1.74274 3.38269 1.55099 3.75901C1.33301 4.18683 1.33301 4.74689 1.33301 5.86699V10.1337C1.33301 11.2538 1.33301 11.8138 1.55099 12.2416C1.74274 12.618 2.0487 12.9239 2.42503 13.1157C2.85285 13.3337 3.4129 13.3337 4.53301 13.3337Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<span><?php echo esc_html( $footer_email ); ?></span>
						</a>
						<?php
					}
					?>
				</div>
			</div>

			<!-- Divider and Social Section -->
			<div class="footer-bottom flex flex-col gap-6">
				<!-- Divider -->
				<div class="footer-divider border-t border-[#D5D7DA]"></div>

				<!-- Social Icons and Copyright -->
				<div class="footer-social-copyright flex flex-col gap-6 lg:flex-row-reverse lg:items-center lg:justify-between">
					<!-- Social Icons -->
					<div class="flex flex-row items-center gap-0">
						<?php
						$social_networks = array( 'twitter', 'linkedin', 'youtube' );
						foreach ( $social_networks as $network ) {
							$social_url = get_theme_mod( "footer_social_{$network}_url", '#' );
							$social_icon = get_theme_mod( "footer_social_{$network}_icon", '' );
							
							if ( $social_url ) {
								?>
								<a href="<?php echo esc_url( $social_url ); ?>" class="w-10 h-10 flex items-center justify-center no-underline transition-opacity hover:opacity-70" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $network ) ); ?>">
									<?php
									if ( $social_icon ) {
										echo '<img src="' . esc_url( $social_icon ) . '" alt="' . esc_attr( ucfirst( $network ) ) . '" class="h-4 w-auto">';
									} else {
										// Fallback SVG icons
										switch ( $network ) {
											case 'twitter':
												?>
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-auto">
													<path d="M12.1562 1.5H14.375L9.54688 7L15.2188 14.5H10.7812L7.29688 9.95312L3.32812 14.5H1.125L6.26562 8.60938L0.84375 1.5H5.39062L8.53125 5.65625L12.1562 1.5ZM11.3906 13.1875H12.6094L4.71875 2.75H3.40625L11.3906 13.1875Z" fill="#212121"/>
												</svg>
												<?php
												break;
											case 'linkedin':
												?>
												<svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-auto">
													<path d="M3.14062 14H0.234375V4.65625H3.14062V14ZM1.6875 3.375C1.21875 3.375 0.820312 3.20833 0.492188 2.875C0.164062 2.54167 0 2.14583 0 1.6875C0 1.21875 0.164062 0.820312 0.492188 0.492188C0.820312 0.164062 1.21875 0 1.6875 0C2.14583 0 2.53906 0.164062 2.86719 0.492188C3.19531 0.820312 3.35938 1.21875 3.35938 1.6875C3.35938 2.14583 3.19531 2.54167 2.86719 2.875C2.53906 3.20833 2.14583 3.375 1.6875 3.375ZM14 14H11.0938V9.45312C11.0938 8.91146 11.0286 8.36198 10.8984 7.80469C10.7682 7.2474 10.3333 6.96875 9.59375 6.96875C8.83333 6.96875 8.35156 7.21615 8.14844 7.71094C7.94531 8.20573 7.84375 8.76042 7.84375 9.375V14H4.95312V4.65625H7.73438V5.92188H7.78125C7.96875 5.55729 8.29688 5.21615 8.76562 4.89844C9.23438 4.58073 9.81771 4.42188 10.5156 4.42188C11.2552 4.42188 11.8542 4.53646 12.3125 4.76562C12.7604 4.99479 13.1094 5.30729 13.3594 5.70312C13.6094 6.09896 13.7812 6.56771 13.875 7.10938C13.9583 7.65104 14 8.23438 14 8.85938V14Z" fill="#212121"/>
												</svg>
												<?php
												break;
											case 'youtube':
												?>
												<svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-auto">
													<path d="M17.1719 3.875C17.0781 3.51042 16.8958 3.1901 16.625 2.91406C16.3542 2.63802 16.0365 2.45312 15.6719 2.35938C15.3385 2.26562 14.8021 2.19792 14.0625 2.15625C13.3229 2.10417 12.5781 2.06771 11.8281 2.04688C11.0781 2.02604 10.4219 2.01042 9.85938 2C9.28646 2 9 2 9 2C9 2 8.71354 2 8.14062 2C7.57812 2.01042 6.92188 2.02604 6.17188 2.04688C5.42188 2.06771 4.67708 2.10417 3.9375 2.15625C3.19792 2.19792 2.66146 2.26562 2.32812 2.35938C1.96354 2.45312 1.64583 2.63802 1.375 2.91406C1.10417 3.1901 0.921875 3.51042 0.828125 3.875C0.734375 4.20833 0.661458 4.61458 0.609375 5.09375C0.567708 5.5625 0.536458 6.01302 0.515625 6.44531C0.494792 6.8776 0.479167 7.24479 0.46875 7.54688C0.46875 7.85938 0.46875 8.01562 0.46875 8.01562C0.46875 8.01562 0.46875 8.16667 0.46875 8.46875C0.479167 8.77083 0.494792 9.14062 0.515625 9.57812C0.536458 10.0156 0.567708 10.4688 0.609375 10.9375C0.661458 11.4062 0.734375 11.8073 0.828125 12.1406C0.921875 12.5156 1.10417 12.8359 1.375 13.1016C1.64583 13.3672 1.96354 13.5469 2.32812 13.6406C2.66146 13.7344 3.19792 13.8021 3.9375 13.8438C4.67708 13.8958 5.42188 13.9323 6.17188 13.9531C6.92188 13.974 7.57812 13.9896 8.14062 14C8.71354 14 9 14 9 14C9 14 9.28646 14 9.85938 14C10.4219 13.9896 11.0781 13.974 11.8281 13.9531C12.5781 13.9323 13.3229 13.8958 14.0625 13.8438C14.8021 13.8021 15.3385 13.7344 15.6719 13.6406C16.0365 13.5469 16.3542 13.3672 16.625 13.1016C16.8958 12.8359 17.0781 12.5156 17.1719 12.1406C17.2656 11.8073 17.3385 11.4062 17.3906 10.9375C17.4323 10.4688 17.4635 10.0156 17.4844 9.57812C17.5052 9.14062 17.5208 8.77083 17.5312 8.46875C17.5312 8.16667 17.5312 8.01562 17.5312 8.01562C17.5312 8.01562 17.5312 7.85938 17.5312 7.54688C17.5208 7.24479 17.5052 6.8776 17.4844 6.44531C17.4635 6.01302 17.4323 5.5625 17.3906 5.09375C17.3385 4.61458 17.2656 4.20833 17.1719 3.875ZM7.25 10.5469V5.46875L11.7188 8.01562L7.25 10.5469Z" fill="#212121"/>
												</svg>
												<?php
												break;
										}
									}
									?>
								</a>
								<?php
							}
						}
						?>
					</div>

					<!-- Copyright -->
					<div class="text-sm font-medium text-gray-500 leading-5 tracking-2p lg:tracking-1p">
						<?php
						$copyright = get_theme_mod( 'footer_copyright_text', '© ' . date( 'Y' ) . ' Geekflare. All rights reserved. Geekflare&reg; is a registered trademark.' );
						// Replace CURRENT_YEAR placeholder with actual year if present
						$copyright = str_replace( 'CURRENT_YEAR', date( 'Y' ), $copyright );
						// Also ensure year is always current if not present
						if ( strpos( $copyright, date( 'Y' ) ) === false && strpos( $copyright, 'CURRENT_YEAR' ) === false ) {
							// If no year is present, prepend current year
							$copyright = '© ' . date( 'Y' ) . ' ' . $copyright;
						}
						echo wp_kses_post( $copyright );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
