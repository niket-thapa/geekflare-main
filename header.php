<?php
/**
 * The header template
 *
 * @package Main
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'main' ); ?></a>

	<header id="site-header" class="site-header py-2.5 md:py-3 lg:py-0 border-b border-gray-200">
		<div class="container-1216 py-1 lg:py-0 flex flex-wrap items-center gap-5 md:gap-3">
			<div class="site-logo max-lg:me-auto">
				<?php
				if ( has_custom_logo() ) {
					// Use WordPress built-in function for custom logo
					the_custom_logo();
				} else {
					?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<span class="text-2xl font-bold text-gray-900"><?php bloginfo( 'name' ); ?></span>
					</a>
					<?php
				}
				?>
			</div>

			<div class="main-menu-wrap">
				<nav class="main-menu max-lg:pb-4" id="main-menu">
					<?php
					$walker = class_exists( 'Main_Walker_Nav_Menu' ) ? new Main_Walker_Nav_Menu() : null;
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'p-0 m-0 lg:flex text-base',
						'fallback_cb'    => false,
						'walker'         => $walker,
					) );
					?>
				</nav>

				<div class="flex items-center gap-3 lg:ms-auto max-lg:px-2.5 max-lg:pt-4">
					<?php
					// Header Search
					$show_search = get_theme_mod( 'header_show_search', true );
					if ( $show_search ) :
						?>
						<div class="header-search">
							<button class="header-search__toggle button p-3 flex max-lg:absolute max-lg:start-0 max-lg:px-2 max-lg:top-1/2 max-lg:-translate-y-1/2" type="button" aria-label="<?php esc_attr_e( 'Open search', 'main' ); ?>" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-auto lg:w-6" width="24" height="24" viewBox="0 0 24 24">
									<path d="M11 18a7.001 7.001 0 1 0 0-14.002A7.001 7.001 0 0 0 11 18m9 2-4-4" style="fill:none;stroke:#252b37;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8"/>
								</svg>
							</button>
							<form class="main-search-form header-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" aria-hidden="true">
								<label for="header-search-input" class="sr-only"><?php esc_html_e( 'Search', 'main' ); ?></label>
								<input 
									id="header-search-input" 
									class="form-input header-search__input" 
									type="search" 
									name="s"
									placeholder="<?php esc_attr_e( 'Search', 'main' ); ?>" 
									value="<?php echo get_search_query(); ?>"
									autocomplete="off" 
								/>
								<button type="submit" class="btn btn--primary header-search__submit hidden lg:flex" aria-label="<?php esc_attr_e( 'Submit search', 'main' ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
										<path d="M11 18a7.001 7.001 0 1 0 0-14.002A7.001 7.001 0 0 0 11 18m9 2-4-4" style="fill:none;stroke:#FFFFFF;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8"/>
									</svg>
								</button>
							</form>
						</div>
						<?php
					endif;

					// Language Menu - Static HTML
					$show_lang_menu = get_theme_mod( 'header_show_lang_menu', true );
					if ( $show_lang_menu ) :
						// Get theme directory URL for flag images
						$flag_base_url = get_template_directory_uri() . '/assets/images/';
						?>
						<div class="header-lang-menu">
							<button type="button" class="header-lang-menu__toggle btn btn--secondary rounded-full px-3 flex" aria-haspopup="true" aria-expanded="false">
								<img src="<?php echo esc_url( $flag_base_url . 'flag-US.svg' ); ?>" class="header-lang-menu__flag-img" width="20" height="20" alt="<?php esc_attr_e( 'English flag', 'main' ); ?>">
								<span class="iso-name">EN</span>
								<svg width="9" height="5.14" viewBox="0 0 7 4" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M3.56771 3.56445C3.44401 3.68815 3.2959 3.75 3.12337 3.75C2.95085 3.75 2.80599 3.68815 2.6888 3.56445L0.188802 1.06445C0.0976563 0.973307 0.0390625 0.865885 0.0130208 0.742188C-0.0130208 0.61849 0 0.498047 0.0520833 0.380859C0.0976562 0.263672 0.174154 0.170898 0.281576 0.102539C0.388997 0.0341797 0.504557 0 0.628255 0H5.62825C5.75195 0 5.86751 0.0341797 5.97493 0.102539C6.08236 0.170898 6.15885 0.263672 6.20443 0.380859C6.25651 0.498047 6.26953 0.61849 6.24349 0.742188C6.21745 0.865885 6.15885 0.973307 6.06771 1.06445L3.56771 3.56445Z" fill="#212121"/>
								</svg>
							</button>
							<ul class="header-lang-menu__list" aria-label="<?php esc_attr_e( 'Select language', 'main' ); ?>">
								<li>
									<button type="button" class="header-lang-menu__option is-active" data-iso="EN" data-label="<?php esc_attr_e( 'English', 'main' ); ?>" data-flag="<?php echo esc_url( $flag_base_url . 'flag-US.svg' ); ?>">
										<img src="<?php echo esc_url( $flag_base_url . 'flag-US.svg' ); ?>" width="20" height="20" alt="<?php esc_attr_e( 'English', 'main' ); ?>" />
										<span><?php esc_html_e( 'English', 'main' ); ?></span>
									</button>
								</li>
								<li>
									<button type="button" class="header-lang-menu__option" data-iso="DE" data-label="<?php esc_attr_e( 'German', 'main' ); ?>" data-flag="<?php echo esc_url( $flag_base_url . 'flag-DE.svg' ); ?>">
										<img src="<?php echo esc_url( $flag_base_url . 'flag-DE.svg' ); ?>" width="20" height="20" alt="<?php esc_attr_e( 'German', 'main' ); ?>" />
										<span><?php esc_html_e( 'German', 'main' ); ?></span>
									</button>
								</li>
								<li>
									<button type="button" class="header-lang-menu__option" data-iso="FR" data-label="<?php esc_attr_e( 'French', 'main' ); ?>" data-flag="<?php echo esc_url( $flag_base_url . 'fleg-FR.svg' ); ?>">
										<img src="<?php echo esc_url( $flag_base_url . 'fleg-FR.svg' ); ?>" width="20" height="20" alt="<?php esc_attr_e( 'French', 'main' ); ?>" />
										<span><?php esc_html_e( 'French', 'main' ); ?></span>
									</button>
								</li>
							</ul>
						</div>
						<?php
					endif;
					?>
				</div>
			</div>
			<?php 
				// Products/CTA Button
				$products_url = get_theme_mod( 'header_products_url', '#' );
				$products_text = get_theme_mod( 'header_products_text', __( 'Products', 'main' ) );
				$products_icon = get_theme_mod( 'header_products_icon', '' );
			?>
			<a href="<?php echo esc_url( $products_url ); ?>" class="btn btn--primary rounded-full">
				<?php if ( $products_icon ) : ?>
					<img src="<?php echo esc_url( $products_icon ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="btn-logo-icon">
				<?php endif; ?>
				<span><?php echo esc_html( $products_text ); ?></span>
			</a>

			<button type="button" class="button py-2 px-0 lg:hidden mobile_menu_toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'main' ); ?>" aria-expanded="false">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M3 12H21M3 6H21M3 18H15" stroke="#252B37" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
		</div>
	</header>
