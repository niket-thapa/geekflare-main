<?php
/**
 * Template Functions
 *
 * Contains template-related functions including:
 * - Archive title modifications
 * - Breadcrumb navigation
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove prefix from archive titles
 *
 * Strips default WordPress prefixes (like "Category:", "Tag:", etc.)
 * from archive page titles for cleaner display.
 *
 * @since 1.0.0
 *
 * @param string $title The default archive title.
 * @return string Modified archive title without prefix.
 */
function main_archive_title( $title ) {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'main_archive_title' );

/**
 * Display breadcrumb navigation
 *
 * Generates a semantic, accessible breadcrumb trail with Schema.org
 * structured data for SEO purposes.
 *
 * Supports:
 * - Home page
 * - Category archives
 * - Single posts (with category)
 * - Pages (with parent hierarchy)
 * - Search results
 * - 404 pages
 *
 * @since 1.0.0
 * @return void
 */
function main_breadcrumbs() {
	?>
	<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'main' ); ?>">
		<ol class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">
			<?php
			// Position counter
			$position = 1;

			// Home
			?>
			<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<a href="<?php echo esc_url( home_url() ); ?>" itemprop="item">
					<span itemprop="name"><?php esc_html_e( 'Home', 'main' ); ?></span>
				</a>
				<meta itemprop="position" content="<?php echo esc_attr( $position++ ); ?>" />
			</li>

			<?php
			// Category Archive
			if ( is_category() ) {
				$cat = get_queried_object();
				
				// Show parent categories if this is a subcategory
				if ( $cat->parent ) {
					$ancestors = array_reverse( get_ancestors( $cat->term_id, 'category' ) );
					foreach ( $ancestors as $ancestor_id ) {
						$ancestor = get_term( $ancestor_id, 'category' );
						?>
						<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
							<a href="<?php echo esc_url( get_category_link( $ancestor_id ) ); ?>" itemprop="item">
								<span itemprop="name"><?php echo esc_html( $ancestor->name ); ?></span>
							</a>
							<meta itemprop="position" content="<?php echo esc_attr( $position++ ); ?>" />
						</li>
						<?php
					}
				}
				
				// Current category
				?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<span itemprop="name" class="current-item"><?php echo esc_html( $cat->name ); ?></span>
					<meta itemprop="position" content="<?php echo esc_attr( $position++ ); ?>" />
				</li>
				<?php
			}

			// Single Post - Only show category hierarchy, no post title
			if ( is_single() ) {
				$cat = get_the_category();
				if ( isset( $cat[0] ) ) {
					// Show parent categories if exists
					if ( $cat[0]->parent ) {
						$ancestors = array_reverse( get_ancestors( $cat[0]->term_id, 'category' ) );
						foreach ( $ancestors as $ancestor_id ) {
							$ancestor = get_term( $ancestor_id, 'category' );
							?>
							<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
								<a href="<?php echo esc_url( get_category_link( $ancestor_id ) ); ?>" itemprop="item">
									<span itemprop="name"><?php echo esc_html( $ancestor->name ); ?></span>
								</a>
								<meta itemprop="position" content="<?php echo esc_attr( $position++ ); ?>" />
							</li>
							<?php
						}
					}
					
					// Current category
					?>
					<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
						<a href="<?php echo esc_url( get_category_link( $cat[0]->term_id ) ); ?>" itemprop="item">
							<span itemprop="name"><?php echo esc_html( $cat[0]->name ); ?></span>
						</a>
						<meta itemprop="position" content="<?php echo esc_attr( $position++ ); ?>" />
					</li>
					<?php
				}
			}

			// Pages with parents - Keep page breadcrumbs as they are
			if ( is_page() && ! is_front_page() ) {
				global $post;
				$parents = array_reverse( get_post_ancestors( $post->ID ) );

				foreach ( $parents as $parent_id ) {
					?>
					<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
						<a href="<?php echo esc_url( get_permalink( $parent_id ) ); ?>" itemprop="item">
							<span itemprop="name"><?php echo esc_html( get_the_title( $parent_id ) ); ?></span>
						</a>
						<meta itemprop="position" content="<?php echo esc_attr( $position++ ); ?>" />
					</li>
					<?php
				}

				// Current page
				?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<span itemprop="name" class="current-item"><?php echo esc_html( get_the_title() ); ?></span>
					<meta itemprop="position" content="<?php echo esc_attr( $position++ ); ?>" />
				</li>
				<?php
			}

			// Search
			if ( is_search() ) {
				?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<span itemprop="name" class="current-item">
						<?php
						/* translators: %s: Search query */
						printf( esc_html__( 'Search results for: %s', 'main' ), esc_html( get_search_query() ) );
						?>
					</span>
					<meta itemprop="position" content="<?php echo esc_attr( $position++ ); ?>" />
				</li>
				<?php
			}

			// 404
			if ( is_404() ) {
				?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<span itemprop="name" class="current-item"><?php esc_html_e( 'Page not found', 'main' ); ?></span>
					<meta itemprop="position" content="<?php echo esc_attr( $position++ ); ?>" />
				</li>
				<?php
			}
			?>
		</ol>
	</nav>
	<?php
}
