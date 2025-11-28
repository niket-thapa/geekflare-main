<?php
/**
 * Products Custom Post Type Registration.
 *
 * @package Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAIN_PRODUCTS_CPT', 'products' );
define( 'MAIN_PRODUCTS_TAXONOMY', 'product-category' );
define( 'MAIN_PRODUCTS_FEATURES_TAXONOMY', 'product-features' );
define( 'MAIN_PRODUCTS_BEST_SUITED_TAXONOMY', 'product-best-suited-for' );

/**
 * Register the Products custom post type.
 */
function main_register_products_cpt() {
	$labels = array(
		'name'                  => _x( 'Products', 'Post Type General Name', 'main' ),
		'singular_name'         => _x( 'Product', 'Post Type Singular Name', 'main' ),
		'menu_name'             => __( 'Products', 'main' ),
		'name_admin_bar'        => __( 'Product', 'main' ),
		'archives'              => __( 'Product Archives', 'main' ),
		'attributes'            => __( 'Product Attributes', 'main' ),
		'all_items'             => __( 'All Products', 'main' ),
		'add_new_item'          => __( 'Add New Product', 'main' ),
		'add_new'               => __( 'Add New', 'main' ),
		'new_item'              => __( 'New Product', 'main' ),
		'edit_item'             => __( 'Edit Product', 'main' ),
		'update_item'           => __( 'Update Product', 'main' ),
		'view_item'             => __( 'View Product', 'main' ),
		'view_items'            => __( 'View Products', 'main' ),
		'search_items'          => __( 'Search Product', 'main' ),
		'not_found'             => __( 'Not found', 'main' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'main' ),
		'featured_image'        => __( 'Product Image', 'main' ),
		'set_featured_image'    => __( 'Set product image', 'main' ),
		'remove_featured_image' => __( 'Remove product image', 'main' ),
		'use_featured_image'    => __( 'Use as product image', 'main' ),
		'insert_into_item'      => __( 'Insert into product', 'main' ),
		'uploaded_to_this_item' => __( 'Uploaded to this product', 'main' ),
		'items_list'            => __( 'Products list', 'main' ),
		'items_list_navigation' => __( 'Products list navigation', 'main' ),
		'filter_items_list'     => __( 'Filter products list', 'main' ),
	);

	$args = array(
		'label'               => __( 'Product', 'main' ),
		'description'         => __( 'Reusable product data for guides.', 'main' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields' ),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 22,
		'menu_icon'           => 'dashicons-screenoptions',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_in_rest'        => true,
		'rewrite'             => array(
			'slug'       => 'guides/products',
			'with_front' => false,
		),
	);

	register_post_type( MAIN_PRODUCTS_CPT, $args );
}
add_action( 'init', 'main_register_products_cpt' );

/**
 * Remove editor support for Products CPT after registration.
 * This ensures the editor is completely disabled even if other plugins try to add it.
 */
function main_remove_products_editor_support() {
	remove_post_type_support( MAIN_PRODUCTS_CPT, 'editor' );
}
add_action( 'init', 'main_remove_products_editor_support', 20 );

/**
 * Register taxonomy for product grouping.
 */
function main_register_products_taxonomy() {
	$labels = array(
		'name'              => _x( 'Product Categories', 'taxonomy general name', 'main' ),
		'singular_name'     => _x( 'Product Category', 'taxonomy singular name', 'main' ),
		'search_items'      => __( 'Search Product Categories', 'main' ),
		'all_items'         => __( 'All Product Categories', 'main' ),
		'parent_item'       => __( 'Parent Product Category', 'main' ),
		'parent_item_colon' => __( 'Parent Product Category:', 'main' ),
		'edit_item'         => __( 'Edit Product Category', 'main' ),
		'update_item'       => __( 'Update Product Category', 'main' ),
		'add_new_item'      => __( 'Add New Product Category', 'main' ),
		'new_item_name'     => __( 'New Product Category', 'main' ),
		'menu_name'         => __( 'Product Categories', 'main' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array(
			'slug'         => 'guides/product-category',
			'hierarchical' => true,
		),
	);

	register_taxonomy( MAIN_PRODUCTS_TAXONOMY, array( MAIN_PRODUCTS_CPT ), $args );
}
add_action( 'init', 'main_register_products_taxonomy' );

/**
 * Register Features taxonomy for products.
 */
function main_register_product_features_taxonomy() {
	$labels = array(
		'name'              => _x( 'Features', 'taxonomy general name', 'main' ),
		'singular_name'     => _x( 'Feature', 'taxonomy singular name', 'main' ),
		'search_items'      => __( 'Search Features', 'main' ),
		'all_items'         => __( 'All Features', 'main' ),
		'parent_item'       => __( 'Parent Feature', 'main' ),
		'parent_item_colon' => __( 'Parent Feature:', 'main' ),
		'edit_item'         => __( 'Edit Feature', 'main' ),
		'update_item'       => __( 'Update Feature', 'main' ),
		'add_new_item'      => __( 'Add New Feature', 'main' ),
		'new_item_name'     => __( 'New Feature Name', 'main' ),
		'menu_name'         => __( 'Features', 'main' ),
		'not_found'         => __( 'No features found', 'main' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array(
			'slug'         => 'guides/product-features',
			'hierarchical' => true,
		),
	);

	register_taxonomy( MAIN_PRODUCTS_FEATURES_TAXONOMY, array( MAIN_PRODUCTS_CPT ), $args );
}
add_action( 'init', 'main_register_product_features_taxonomy' );

/**
 * Register Best Suited For taxonomy for products.
 */
function main_register_product_best_suited_taxonomy() {
	$labels = array(
		'name'              => _x( 'Best Suited For', 'taxonomy general name', 'main' ),
		'singular_name'     => _x( 'Best Suited For', 'taxonomy singular name', 'main' ),
		'search_items'      => __( 'Search Best Suited For', 'main' ),
		'all_items'         => __( 'All Best Suited For', 'main' ),
		'edit_item'         => __( 'Edit Best Suited For', 'main' ),
		'update_item'       => __( 'Update Best Suited For', 'main' ),
		'add_new_item'      => __( 'Add New Best Suited For', 'main' ),
		'new_item_name'     => __( 'New Best Suited For Name', 'main' ),
		'menu_name'         => __( 'Best Suited For', 'main' ),
		'not_found'         => __( 'No best suited for found', 'main' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array(
			'slug' => 'guides/product-best-suited-for',
		),
	);

	register_taxonomy( MAIN_PRODUCTS_BEST_SUITED_TAXONOMY, array( MAIN_PRODUCTS_CPT ), $args );
}
add_action( 'init', 'main_register_product_best_suited_taxonomy' );

/**
 * Insert default terms for Best Suited For taxonomy.
 */
function main_insert_default_best_suited_terms() {
	$taxonomy = MAIN_PRODUCTS_BEST_SUITED_TAXONOMY;
	$default_terms = array( 'Freelancers', 'Startups', 'SMBs', 'Agency', 'Enterprise' );

	// Check if terms already exist to avoid duplicates
	foreach ( $default_terms as $term_name ) {
		$term_exists = term_exists( $term_name, $taxonomy );
		
		if ( ! $term_exists ) {
			wp_insert_term(
				$term_name,
				$taxonomy,
				array(
					'description' => sprintf( __( 'Best suited for %s', 'main' ), $term_name ),
				)
			);
		}
	}
}
add_action( 'init', 'main_insert_default_best_suited_terms', 20 );

/**
 * Product awards configuration.
 *
 * @return array<string, array<string, string>>
 */
function main_get_product_awards() {
	return array(
		'budget-amazing' => array(
			'label' => __( 'Budget Amazing', 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-budget-amazing.svg',
		),
		'budget-good' => array(
			'label' => __( 'Budget Good', 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-budget-good.svg',
		),
		'budget-great' => array(
			'label' => __( 'Budget Great', 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-budget-good.svg',
		),
		'editorial-excellent' => array(
			'label' => __( "Editorial's Choice Excellent", 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-editorials-choice-excellent.svg',
		),
		'editorial-exceptional' => array(
			'label' => __( "Editorial's Choice Exceptional", 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-editorials-choice-exceptional.svg',
		),
		'innovation-amazing' => array(
			'label' => __( 'Innovation Amazing', 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-innovation-amazing.svg',
		),
		'innovation-excellent' => array(
			'label' => __( 'Innovation Excellent', 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-innovation-excellent.svg',
		),
		'innovation-exceptional' => array(
			'label' => __( 'Innovation Exceptional', 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-innovation-exceptional.svg',
		),
		'value-amazing' => array(
			'label' => __( 'Value Amazing', 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-value-amazing.svg',
		),
		'value-excellent' => array(
			'label' => __( 'Value Excellent', 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-value-excellent.svg',
		),
		'value-great' => array(
			'label' => __( 'Value Great', 'main' ),
			'image' => 'https://cdn.geekflare.com/general/award-value-great.svg',
		),
	);
}

/**
 * List of product meta fields with their configuration.
 *
 * @return array<string, array<string, mixed>>
 */
function main_get_product_meta_fields() {
	return array(
		'product_name'   => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_text_field',
			'default'  => '',
		),
		'product_logo'   => array(
			'type'     => 'integer',
			'sanitize' => 'absint',
			'default'  => 0,
		),
		'tagline'        => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_text_field',
			'default'  => '',
		),
		'website_url'    => array(
			'type'     => 'string',
			'sanitize' => 'esc_url_raw',
			'default'  => '',
		),
		'pricing_summary' => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_text_field',
			'default'  => '',
		),
		'our_rating'     => array(
			'type'     => 'number',
			'sanitize' => 'main_sanitize_product_rating',
			'default'  => '',
		),
		'has_free_trial' => array(
			'type'     => 'boolean',
			'sanitize' => 'main_sanitize_product_boolean',
			'default'  => false,
		),
		'has_free_plan'  => array(
			'type'     => 'boolean',
			'sanitize' => 'main_sanitize_product_boolean',
			'default'  => false,
		),
		'has_demo'       => array(
			'type'     => 'boolean',
			'sanitize' => 'main_sanitize_product_boolean',
			'default'  => false,
		),
		'open_source'    => array(
			'type'     => 'boolean',
			'sanitize' => 'main_sanitize_product_boolean',
			'default'  => false,
		),
		'ai_powered'     => array(
			'type'     => 'boolean',
			'sanitize' => 'main_sanitize_product_boolean',
			'default'  => false,
		),
		'award'          => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_text_field',
			'default'  => '',
		),
		'custom_note'    => array(
			'type'     => 'string',
			'sanitize' => 'sanitize_textarea_field',
			'default'  => '',
		),
		'affiliate_link' => array(
			'type'     => 'string',
			'sanitize' => 'esc_url_raw',
			'default'  => '',
		),
		'product_update_logs' => array(
			'type'     => 'string',
			'sanitize' => 'main_sanitize_product_update_logs',
			'default'  => '[]',
		),
		'score_breakdown' => array(
			'type'     => 'string',
			'sanitize' => 'main_sanitize_score_breakdown',
			'default'  => '[]',
		),
	);
}

/**
 * Register product meta fields so they are available via REST.
 */
function main_register_product_meta_fields() {
	$fields = main_get_product_meta_fields();

	foreach ( $fields as $key => $field ) {
		register_post_meta(
			MAIN_PRODUCTS_CPT,
			$key,
			array(
				'single'            => true,
				'type'              => $field['type'],
				'show_in_rest'      => true,
				'sanitize_callback' => $field['sanitize'],
				'auth_callback'     => 'main_products_meta_auth',
				'default'           => $field['default'],
			)
		);
	}
}
add_action( 'init', 'main_register_product_meta_fields', 11 );

/**
 * Permission check for product meta.
 *
 * @param bool   $allowed Default permission.
 * @param string $meta_key Meta key.
 * @param int    $post_id Post ID.
 * @param int    $user_id User ID.
 * @param string $cap Capability.
 * @param array  $caps User caps.
 *
 * @return bool
 */
function main_products_meta_auth( $allowed, $meta_key, $post_id, $user_id, $cap, $caps ) {
	unset( $allowed, $meta_key, $user_id, $cap, $caps );

	return current_user_can( 'edit_post', $post_id );
}

/**
 * Sanitize checkbox values.
 *
 * @param mixed $value Raw value.
 *
 * @return bool
 */
function main_sanitize_product_boolean( $value ) {
	return (bool) $value;
}

/**
 * Sanitize rating ensuring range 1-5 with 0.1 increments.
 *
 * @param mixed $value Raw value.
 *
 * @return float|string
 */
function main_sanitize_product_rating( $value ) {
	$value = floatval( $value );

	if ( $value < 1 || $value > 5 ) {
		return '';
	}

	return round( $value, 1 );
}

/**
 * Sanitize product update logs array.
 *
 * @param mixed $value Raw value.
 *
 * @return string JSON encoded array of update logs.
 */
function main_sanitize_product_update_logs( $value ) {
	// Handle empty values
	if ( empty( $value ) || $value === '[]' || $value === '' ) {
		return '[]';
	}

	// If it's a string, try to decode JSON
	if ( is_string( $value ) ) {
		$decoded = json_decode( $value, true );
		if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
			$value = $decoded;
		} else {
			// If JSON decode failed, return empty array
			return '[]';
		}
	}

	// Ensure we have an array
	if ( ! is_array( $value ) ) {
		return '[]';
	}

	// Sanitize each update log entry
	$sanitized = array();
	foreach ( $value as $update ) {
		if ( ! is_array( $update ) ) {
			continue;
		}

		$date        = isset( $update['date'] ) ? sanitize_text_field( $update['date'] ) : '';
		$description = isset( $update['description'] ) ? sanitize_textarea_field( $update['description'] ) : '';

		// Only add entries that have at least date or description
		if ( ! empty( $date ) || ! empty( $description ) ) {
			$sanitized[] = array(
				'date'        => $date,
				'description' => $description,
			);
		}
	}

	return wp_json_encode( $sanitized );
}

/**
 * Sanitize score breakdown array.
 *
 * @param mixed $value Raw value.
 *
 * @return string JSON encoded array of score breakdown criteria.
 */
function main_sanitize_score_breakdown( $value ) {
	// Handle empty values
	if ( empty( $value ) || $value === '[]' || $value === '' ) {
		return '[]';
	}

	// If it's a string, try to decode JSON
	if ( is_string( $value ) ) {
		$decoded = json_decode( $value, true );
		if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
			$value = $decoded;
		} else {
			// If JSON decode failed, return empty array
			return '[]';
		}
	}

	// Ensure we have an array
	if ( ! is_array( $value ) ) {
		return '[]';
	}

	// Sanitize each criterion entry
	$sanitized = array();
	foreach ( $value as $criterion ) {
		if ( ! is_array( $criterion ) ) {
			continue;
		}

		$name  = isset( $criterion['name'] ) ? sanitize_text_field( $criterion['name'] ) : '';
		$score = isset( $criterion['score'] ) ? floatval( $criterion['score'] ) : 0;

		// Validate score range (0-5)
		if ( $score < 0 || $score > 5 ) {
			$score = 0;
		}

		// Round to 1 decimal place
		$score = round( $score, 1 );

		// Only add entries that have a name
		if ( ! empty( $name ) ) {
			$sanitized[] = array(
				'name'  => $name,
				'score' => $score,
			);
		}
	}

	return wp_json_encode( $sanitized );
}

/**
 * Register meta boxes for Products.
 */
function main_register_product_meta_boxes() {
	add_meta_box(
		'main-product-details',
		__( 'Product Details', 'main' ),
		'main_render_product_details_meta_box',
		MAIN_PRODUCTS_CPT,
		'normal',
		'high'
	);

	add_meta_box(
		'main-product-rating',
		__( 'Score Breakdown & Rating', 'main' ),
		'main_render_product_rating_meta_box',
		MAIN_PRODUCTS_CPT,
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'main_register_product_meta_boxes' );

/**
 * Retrieve a stored product meta value.
 *
 * @param int         $post_id Post ID.
 * @param string      $key Meta key.
 * @param string|bool $default Default value.
 *
 * @return mixed
 */
function main_get_product_meta_value( $post_id, $key, $default = '' ) {
	$fields = main_get_product_meta_fields();
	$field  = $fields[ $key ] ?? array();
	$value  = get_post_meta( $post_id, $key, true );

	if ( '' === $value && array_key_exists( 'default', $field ) ) {
		$value = $field['default'];
	}

	if ( ( $field['type'] ?? '' ) === 'boolean' ) {
		return (bool) $value;
	}

	if ( ( $field['type'] ?? '' ) === 'number' ) {
		return '' === $value ? $default : (float) $value;
	}

	return '' === $value ? $default : $value;
}

/**
 * Render the meta box HTML.
 *
 * @param WP_Post $post Current post object.
 */
function main_render_product_details_meta_box( $post ) {
	wp_nonce_field( 'main_products_meta', 'main_products_meta_nonce' );

	$fields      = main_get_product_meta_fields();
	$awards      = main_get_product_awards();
	$logo_id     = main_get_product_meta_value( $post->ID, 'product_logo', 0 );
	$logo_src    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';
	$rating      = main_get_product_meta_value( $post->ID, 'our_rating' );
	$website_url = main_get_product_meta_value( $post->ID, 'website_url' );
	?>
	<div class="main-product-meta">
		<div class="main-product-meta__section">
			<label for="main-product-name"><strong><?php esc_html_e( 'Product Name', 'main' ); ?></strong></label>
			<input type="text" id="main-product-name" name="product_name" value="<?php echo esc_attr( main_get_product_meta_value( $post->ID, 'product_name' ) ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Defaults to the title if left blank.', 'main' ); ?>">
		</div>

		<div class="main-product-meta__grid">
			<div class="main-product-meta__field">
				<label for="main-product-tagline"><strong><?php esc_html_e( 'Tagline', 'main' ); ?></strong></label>
				<input type="text" id="main-product-tagline" name="tagline" value="<?php echo esc_attr( main_get_product_meta_value( $post->ID, 'tagline' ) ); ?>" class="widefat">
			</div>
			<div class="main-product-meta__field">
				<label for="main-product-website"><strong><?php esc_html_e( 'Website URL', 'main' ); ?></strong></label>
				<input type="url" id="main-product-website" name="website_url" value="<?php echo esc_attr( $website_url ); ?>" class="widefat">
			</div>
		</div>

		<div class="main-product-meta__field">
			<label for="main-product-pricing"><strong><?php esc_html_e( 'Pricing Summary', 'main' ); ?></strong></label>
			<input type="text" id="main-product-pricing" name="pricing_summary" value="<?php echo esc_attr( main_get_product_meta_value( $post->ID, 'pricing_summary' ) ); ?>" class="widefat">
		</div>

		<div class="main-product-meta__logo">
			<label><strong><?php esc_html_e( 'Product Logo', 'main' ); ?></strong></label>
			<div class="main-product-meta__logo-preview">
				<?php if ( $logo_src ) : ?>
					<img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php esc_attr_e( 'Product logo preview', 'main' ); ?>" />
				<?php else : ?>
					<span class="main-product-meta__logo-placeholder"><?php esc_html_e( 'No logo selected.', 'main' ); ?></span>
				<?php endif; ?>
			</div>
			<input type="hidden" id="main-product-logo" name="product_logo" value="<?php echo esc_attr( $logo_id ); ?>">
			<button type="button" class="button main-product-upload-logo"><?php esc_html_e( 'Upload/Select Logo', 'main' ); ?></button>
			<button type="button" class="button button-secondary main-product-fetch-logo" data-post-id="<?php echo absint( $post->ID ); ?>" data-website="<?php echo esc_url( $website_url ); ?>"><?php esc_html_e( 'Fetch from Website URL', 'main' ); ?></button>
		</div>

		<fieldset class="main-product-meta__toggles">
			<legend><?php esc_html_e( 'Availability Flags', 'main' ); ?></legend>
			<?php
			$checkboxes = array(
				'has_free_trial' => __( 'Has Free Trial', 'main' ),
				'has_free_plan'  => __( 'Has Free Plan', 'main' ),
				'has_demo'       => __( 'Has Demo', 'main' ),
				'open_source'    => __( 'Open Source', 'main' ),
				'ai_powered'     => __( 'AI-Powered', 'main' ),
			);
			foreach ( $checkboxes as $key => $label ) :
				$checked = main_get_product_meta_value( $post->ID, $key );
				?>
				<label class="main-product-meta__checkbox">
					<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $checked, true ); ?> >
					<?php echo esc_html( $label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>

		<div class="main-product-meta__grid">
			<div class="main-product-meta__field">
				<label for="main-product-award"><strong><?php esc_html_e( 'Award', 'main' ); ?></strong></label>
				<select id="main-product-award" name="award" class="widefat">
					<option value=""><?php esc_html_e( 'None', 'main' ); ?></option>
					<?php foreach ( $awards as $key => $award ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( main_get_product_meta_value( $post->ID, 'award' ), $key ); ?>>
							<?php echo esc_html( $award['label'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e( 'Award badge can be displayed using the image URL mapped to the selected option.', 'main' ); ?></p>
			</div>
			<div class="main-product-meta__field">
				<label for="main-product-affiliate"><strong><?php esc_html_e( 'Affiliate / CTA URL', 'main' ); ?></strong></label>
				<input type="url" id="main-product-affiliate" name="affiliate_link" value="<?php echo esc_url( main_get_product_meta_value( $post->ID, 'affiliate_link' ) ); ?>" class="widefat">
			</div>
		</div>

		<div class="main-product-meta__field">
			<label for="main-product-custom-note"><strong><?php esc_html_e( 'Custom Note', 'main' ); ?></strong></label>
			<textarea id="main-product-custom-note" name="custom_note" rows="3" class="widefat"><?php echo esc_textarea( main_get_product_meta_value( $post->ID, 'custom_note' ) ); ?></textarea>
		</div>

		<div class="main-product-meta__field">
			<label><strong><?php esc_html_e( 'Product Update Logs', 'main' ); ?></strong></label>
			<p class="description"><?php esc_html_e( 'Add dated entries showing product evolution. Most recent first.', 'main' ); ?></p>
			
			<?php
			$update_logs_json = main_get_product_meta_value( $post->ID, 'product_update_logs', '[]' );
			$update_logs = json_decode( $update_logs_json, true );
			if ( ! is_array( $update_logs ) ) {
				$update_logs = array();
			}
			?>
			
			<div id="main-product-update-logs-container" class="main-product-meta__update-logs">
				<?php foreach ( $update_logs as $index => $update ) : ?>
					<div class="main-product-meta__update-log-item" data-index="<?php echo esc_attr( $index ); ?>">
						<div class="main-product-meta__update-log-actions">
							<button type="button" class="button button-small main-product-update-log-move-up" <?php echo ( $index === 0 ) ? 'disabled' : ''; ?>><?php esc_html_e( '↑', 'main' ); ?></button>
							<button type="button" class="button button-small main-product-update-log-move-down" <?php echo ( $index === count( $update_logs ) - 1 ) ? 'disabled' : ''; ?>><?php esc_html_e( '↓', 'main' ); ?></button>
							<button type="button" class="button button-small button-link-delete main-product-update-log-remove"><?php esc_html_e( 'Remove', 'main' ); ?></button>
						</div>
						<input type="text" class="widefat main-product-update-log-date" placeholder="<?php esc_attr_e( 'e.g., October 2025', 'main' ); ?>" value="<?php echo esc_attr( $update['date'] ?? '' ); ?>" />
						<textarea class="widefat main-product-update-log-description" rows="2" placeholder="<?php esc_attr_e( 'What was updated?', 'main' ); ?>"><?php echo esc_textarea( $update['description'] ?? '' ); ?></textarea>
					</div>
				<?php endforeach; ?>
			</div>
			
			<input type="hidden" id="main-product-update-logs" name="product_update_logs" value="<?php echo esc_attr( $update_logs_json ); ?>" />
			<button type="button" class="button button-secondary main-product-add-update-log"><?php esc_html_e( '+ Add Update', 'main' ); ?></button>
		</div>
	</div>
	<?php
}

/**
 * Render the Score Breakdown & Rating meta box in sidebar.
 *
 * @param WP_Post $post Current post object.
 */
function main_render_product_rating_meta_box( $post ) {
	wp_nonce_field( 'main_products_meta', 'main_products_meta_nonce' );

	$score_breakdown_json = main_get_product_meta_value( $post->ID, 'score_breakdown', '[]' );
	$score_breakdown      = json_decode( $score_breakdown_json, true );
	if ( ! is_array( $score_breakdown ) ) {
		$score_breakdown = array();
	}

	$rating      = main_get_product_meta_value( $post->ID, 'our_rating' );
	$auto_rating = '';
	if ( ! empty( $score_breakdown ) ) {
		$total = 0;
		$count = 0;
		foreach ( $score_breakdown as $criterion ) {
			if ( isset( $criterion['score'] ) && is_numeric( $criterion['score'] ) ) {
				$total += floatval( $criterion['score'] );
				$count++;
			}
		}
		if ( $count > 0 ) {
			$auto_rating = round( $total / $count, 1 );
		}
	}
	?>
	<div class="main-product-rating-meta">
		<div class="main-product-meta__field">
			<label for="main-product-rating">
				<strong><?php esc_html_e( 'Our Rating (1.0 - 5.0)', 'main' ); ?></strong>
			</label>
			<input 
				type="number" 
				step="0.1" 
				min="1" 
				max="5" 
				id="main-product-rating" 
				name="our_rating" 
				value="<?php echo esc_attr( $rating ); ?>" 
				class="small-text"
				readonly
			>
			<p class="description">
				<?php esc_html_e( 'Auto-calculated from Score Breakdown below.', 'main' ); ?>
				<?php if ( $auto_rating ) : ?>
					<strong><?php echo esc_html( $auto_rating ); ?>/5</strong>
				<?php endif; ?>
			</p>
		</div>

		<div class="main-product-meta__field">
			<label><strong><?php esc_html_e( 'Score Breakdown', 'main' ); ?></strong></label>
			<p class="description"><?php esc_html_e( 'Add criteria and scores to calculate the overall rating.', 'main' ); ?></p>
			
			<div id="main-product-score-breakdown-container" class="main-product-meta__score-breakdown">
				<?php foreach ( $score_breakdown as $index => $criterion ) : ?>
					<div class="main-product-meta__score-criterion" data-index="<?php echo esc_attr( $index ); ?>">
						<div class="main-product-meta__score-criterion-header">
							<label>
								<?php esc_html_e( 'Criterion Name', 'main' ); ?>
								<input 
									type="text" 
									class="widefat main-product-score-criterion-name" 
									placeholder="<?php esc_attr_e( 'e.g., Ease of Use', 'main' ); ?>" 
									value="<?php echo esc_attr( $criterion['name'] ?? '' ); ?>" 
								/>
							</label>
							<button type="button" class="button button-small button-link-delete main-product-score-criterion-remove">
								<?php esc_html_e( 'Remove', 'main' ); ?>
							</button>
						</div>
						<label>
							<?php esc_html_e( 'Score', 'main' ); ?>: 
							<span class="main-product-score-criterion-score-display"><?php echo esc_html( $criterion['score'] ?? 3 ); ?></span>/5
						</label>
						<input 
							type="range" 
							class="main-product-score-criterion-score" 
							min="0" 
							max="5" 
							step="0.1" 
							value="<?php echo esc_attr( $criterion['score'] ?? 3 ); ?>" 
						/>
					</div>
				<?php endforeach; ?>
			</div>
			
			<input type="hidden" id="main-product-score-breakdown" name="score_breakdown" value="<?php echo esc_attr( $score_breakdown_json ); ?>" />
			<button type="button" class="button button-secondary main-product-add-score-criterion">
				<?php esc_html_e( '+ Add Criterion', 'main' ); ?>
			</button>
			<div class="main-product-meta__auto-rating-display" style="margin-top: 10px; padding: 8px; background: #f0f0f1; border-radius: 4px;">
				<strong><?php esc_html_e( 'Calculated Rating:', 'main' ); ?></strong> 
				<span id="main-product-auto-rating"><?php echo esc_html( $auto_rating ? $auto_rating . '/5' : 'N/A' ); ?></span>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Save product meta data.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post Post object.
 */
function main_save_product_meta( $post_id, $post ) {
	if ( ! isset( $_POST['main_products_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['main_products_meta_nonce'] ) ), 'main_products_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = main_get_product_meta_fields();

	foreach ( $fields as $key => $field ) {
		$value = $_POST[ $key ] ?? null;

		// Special handling for product_update_logs - always save it, even if empty
		if ( 'product_update_logs' === $key ) {
			$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '[]';
			// Ensure we always save it, even if empty
			if ( is_callable( $field['sanitize'] ) ) {
				$value = call_user_func( $field['sanitize'], $value );
			}
			update_post_meta( $post_id, $key, $value );
			continue;
		}

		// Special handling for score_breakdown - always save it, even if empty
		if ( 'score_breakdown' === $key ) {
			$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '[]';
			// Ensure we always save it, even if empty
			if ( is_callable( $field['sanitize'] ) ) {
				$value = call_user_func( $field['sanitize'], $value );
			}
			update_post_meta( $post_id, $key, $value );
			
			// Auto-calculate our_rating from score breakdown
			$breakdown_json = $value;
			$breakdown      = json_decode( $breakdown_json, true );
			if ( is_array( $breakdown ) && ! empty( $breakdown ) ) {
				$total = 0;
				$count = 0;
				foreach ( $breakdown as $criterion ) {
					if ( isset( $criterion['score'] ) && is_numeric( $criterion['score'] ) ) {
						$total += floatval( $criterion['score'] );
						$count++;
					}
				}
				if ( $count > 0 ) {
					$calculated_rating = round( $total / $count, 1 );
					update_post_meta( $post_id, 'our_rating', $calculated_rating );
				}
			}
			continue;
		}

		if ( null === $value ) {
			if ( 'boolean' === $field['type'] ) {
				update_post_meta( $post_id, $key, false );
			}

			continue;
		}

		if ( is_array( $value ) ) {
			$value = '';
		}

		if ( 'main_sanitize_product_boolean' === $field['sanitize'] ) {
			$value = (bool) $value;
		} elseif ( is_callable( $field['sanitize'] ) ) {
			$value = call_user_func( $field['sanitize'], $value );
		}

		update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post_' . MAIN_PRODUCTS_CPT, 'main_save_product_meta', 10, 2 );

/**
 * Enqueue admin assets for Products CPT.
 *
 * @param string $hook Current admin page hook.
 */
function main_products_admin_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || MAIN_PRODUCTS_CPT !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	
	// Enqueue jQuery UI datepicker for update log dates
	wp_enqueue_script( 'jquery-ui-datepicker' );
	// WordPress admin already includes jQuery UI CSS, so we don't need to enqueue it separately

	$script_path = MAIN_THEME_DIR . '/assets/js/admin-products.js';
	$style_path  = MAIN_THEME_DIR . '/assets/css/admin-products.css';
	$version     = file_exists( $script_path ) ? filemtime( $script_path ) : time();
	$style_ver   = file_exists( $style_path ) ? filemtime( $style_path ) : $version;

	if ( file_exists( $style_path ) ) {
		wp_enqueue_style(
			'main-products-admin',
			MAIN_THEME_URI . '/assets/css/admin-products.css',
			array(),
			$style_ver
		);
	}

	wp_enqueue_script(
		'main-products-admin',
		MAIN_THEME_URI . '/assets/js/admin-products.js',
		array( 'jquery' ),
		$version,
		true
	);

	wp_localize_script(
		'main-products-admin',
		'mainProductsAdmin',
		array(
			'nonce'             => wp_create_nonce( 'main_products_meta' ),
			'fetchAction'       => 'main_fetch_product_logo',
			'placeholder'       => __( 'No logo selected.', 'main' ),
			'fetchingText'      => __( 'Fetching logo…', 'main' ),
			'errorText'         => __( 'Unable to fetch the logo. Please upload manually.', 'main' ),
			'existingPrompt'    => __( 'A saved logo already exists for this website. Click OK to reuse it or Cancel to fetch a fresh copy.', 'main' ),
			'existingNoneError' => __( 'Unable to load the existing logo preview. Please try fetching again.', 'main' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'main_products_admin_assets' );

/**
 * AJAX handler to fetch product logo via favicon service.
 */
function main_fetch_product_logo() {
	check_ajax_referer( 'main_products_meta', 'nonce' );

	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Permission denied.', 'main' ),
			),
			403
		);
	}

	$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$website_url = isset( $_POST['website_url'] ) ? esc_url_raw( wp_unslash( $_POST['website_url'] ) ) : '';
	$force_fetch = ! empty( $_POST['force_fetch'] );

	if ( ! $post_id || empty( $website_url ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Post or website URL missing.', 'main' ),
			),
			400
		);
	}

	if ( ! preg_match( '#^https?://#i', $website_url ) ) {
		$website_url = 'https://' . ltrim( $website_url, '/' );
	}

	$domain = wp_parse_url( $website_url, PHP_URL_HOST );

	if ( empty( $domain ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Unable to detect domain from URL.', 'main' ),
			),
			400
		);
	}

	$normalized_domain = main_normalize_logo_domain( $domain );
	$existing_logos    = main_find_existing_product_logos( $normalized_domain );

	if ( ! $force_fetch && ! empty( $existing_logos ) ) {
		wp_send_json_success(
			array(
				'status'  => 'existing',
				'logos'   => $existing_logos,
				'message' => __( 'A saved logo already exists for this website.', 'main' ),
			)
		);
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$sources       = main_get_product_logo_sources( $domain );
	$attachment_id = 0;
	$error_message = __( 'Failed to download the logo.', 'main' );

	foreach ( $sources as $remote_url ) {
		$result = main_download_product_logo( $remote_url, $post_id, $normalized_domain );

		if ( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
			continue;
		}

		$attachment_id = $result;
		break;
	}

	if ( ! $attachment_id ) {
		wp_send_json_error(
			array(
				'message' => $error_message,
			),
			500
		);
	}

	wp_send_json_success(
		array(
			'status' => 'fetched',
			'id'     => $attachment_id,
			'url'    => wp_get_attachment_image_url( $attachment_id, 'thumbnail' ),
		)
	);
}
add_action( 'wp_ajax_main_fetch_product_logo', 'main_fetch_product_logo' );

/**
 * Normalize domain string for storage.
 *
 * @param string $domain Domain.
 *
 * @return string
 */
function main_normalize_logo_domain( $domain ) {
	$domain = strtolower( trim( $domain ) );
	$domain = preg_replace( '#^https?://#', '', $domain );
	$domain = preg_replace( '#^www\.#', '', $domain );

	return $domain;
}

/**
 * Find existing attachments for a domain.
 *
 * @param string $normalized_domain Normalized domain.
 *
 * @return array<int, array<string, mixed>>
 */
function main_find_existing_product_logos( $normalized_domain ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 5,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_key'       => '_main_logo_domain',
			'meta_value'     => $normalized_domain,
		)
	);

	$logos = array();

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $attachment ) {
			$url = wp_get_attachment_image_url( $attachment->ID, 'thumbnail' );

			if ( ! $url ) {
				continue;
			}

			$logos[] = array(
				'id'   => $attachment->ID,
				'url'  => $url,
				'name' => get_the_title( $attachment ),
			);
		}
	}

	wp_reset_postdata();

	return $logos;
}

/**
 * Possible favicon sources for a domain.
 *
 * @param string $domain Domain name.
 *
 * @return string[]
 */
function main_get_product_logo_sources( $domain ) {
	$domain = main_normalize_logo_domain( $domain );

	return array(
		sprintf( 'https://www.google.com/s2/favicons?domain=%s&sz=128', rawurlencode( $domain ) ),
		sprintf( 'https://icons.duckduckgo.com/ip3/%s.ico', rawurlencode( $domain ) ),
	);
}

/**
 * Download remote logo and create media attachment.
 *
 * @param string $remote_url Remote image URL.
 * @param int    $post_id Post ID.
 * @param string $domain Domain for naming.
 *
 * @return int|\WP_Error
 */
function main_download_product_logo( $remote_url, $post_id, $domain ) {
	$temp_file = download_url( $remote_url );

	if ( is_wp_error( $temp_file ) ) {
		return $temp_file;
	}

	$file_array = array(
		'name'     => sanitize_file_name( $domain . '-logo.png' ),
		'tmp_name' => $temp_file,
	);

	$attachment_id = media_handle_sideload( $file_array, $post_id );

	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $temp_file );
		return $attachment_id;
	}

	if ( ! is_wp_error( $attachment_id ) ) {
		update_post_meta( $attachment_id, '_main_logo_domain', $domain );
		update_post_meta( $attachment_id, '_main_logo_source', esc_url_raw( $remote_url ) );
	}

	return $attachment_id;
}

