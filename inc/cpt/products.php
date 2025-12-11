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
		'supports'            => array( 'title', 'author', 'revisions', 'custom-fields' ),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 22,
		'menu_icon'           => 'dashicons-screenoptions',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_in_rest'        => true,
		'rewrite'             => false,
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
 * Remove excerpt and thumbnail support for Products CPT after registration.
 * This ensures these features are completely disabled even if other plugins try to add them.
 */
function main_remove_products_excerpt_thumbnail_support() {
	remove_post_type_support( MAIN_PRODUCTS_CPT, 'excerpt' );
	remove_post_type_support( MAIN_PRODUCTS_CPT, 'thumbnail' );
}
add_action( 'init', 'main_remove_products_excerpt_thumbnail_support', 20 );

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
		'rest_base'         => 'product-category',
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
		'rest_base'         => 'product-features',
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
		'rating_count'   => array(
			'type'     => 'number',
			'sanitize' => 'absint',
			'default'  => 0,
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
	// Main content area meta box (all fields combined)
	add_meta_box(
		'main-product-details',
		__( 'Product Details', 'main' ),
		'main_render_product_details_meta_box',
		MAIN_PRODUCTS_CPT,
		'normal',
		'high'
	);

	// Sidebar meta boxes
	add_meta_box(
		'main-product-categories',
		__( 'Product Categories', 'main' ),
		'main_render_product_categories_meta_box',
		MAIN_PRODUCTS_CPT,
		'side',
		'default'
	);

	add_meta_box(
		'main-product-best-suited',
		__( 'Best Suited For', 'main' ),
		'main_render_product_best_suited_meta_box',
		MAIN_PRODUCTS_CPT,
		'side',
		'default'
	);

	add_meta_box(
		'main-product-availability-flags',
		__( 'Availability Flags', 'main' ),
		'main_render_product_availability_flags_meta_box',
		MAIN_PRODUCTS_CPT,
		'side',
		'default'
	);

	add_meta_box(
		'main-product-features',
		__( 'Product Features', 'main' ),
		'main_render_product_features_meta_box',
		MAIN_PRODUCTS_CPT,
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'main_register_product_meta_boxes' );

/**
 * Remove default taxonomy meta boxes from sidebar.
 */
function main_remove_product_taxonomy_meta_boxes() {
	// Hierarchical taxonomies use {taxonomy}div
	remove_meta_box( MAIN_PRODUCTS_TAXONOMY . 'div', MAIN_PRODUCTS_CPT, 'side' );
	remove_meta_box( MAIN_PRODUCTS_FEATURES_TAXONOMY . 'div', MAIN_PRODUCTS_CPT, 'side' );
	// Non-hierarchical taxonomies use tagsdiv-{taxonomy}
	remove_meta_box( 'tagsdiv-' . MAIN_PRODUCTS_BEST_SUITED_TAXONOMY, MAIN_PRODUCTS_CPT, 'side' );
}
// Use add_meta_boxes hook with high priority to ensure it runs after WordPress registers default meta boxes
add_action( 'add_meta_boxes', 'main_remove_product_taxonomy_meta_boxes', 999 );

/**
 * Remove permalink meta box from Products CPT.
 */
function main_remove_product_permalink_meta_box() {
	remove_meta_box( 'slugdiv', MAIN_PRODUCTS_CPT, 'normal' );
}
add_action( 'add_meta_boxes', 'main_remove_product_permalink_meta_box', 999 );

/**
 * Remove Custom Fields meta box from Products CPT.
 */
function main_remove_product_custom_fields_meta_box() {
	remove_meta_box( 'postcustom', MAIN_PRODUCTS_CPT, 'normal' );
}
add_action( 'add_meta_boxes', 'main_remove_product_custom_fields_meta_box', 999 );

/**
 * Hide permalink display in edit post screen for Products CPT.
 */
function main_hide_product_permalink_display( $return, $post_id, $new_title, $new_slug, $post ) {
	if ( isset( $post->post_type ) && $post->post_type === MAIN_PRODUCTS_CPT ) {
		return '';
	}
	return $return;
}
add_filter( 'get_sample_permalink_html', 'main_hide_product_permalink_display', 10, 5 );

/**
 * Render Product Categories meta box.
 *
 * @param WP_Post $post Current post object.
 */
function main_render_product_categories_meta_box( $post ) {
	$taxonomy = MAIN_PRODUCTS_TAXONOMY;
	?>
	<ul class="main-product-taxonomy-meta">
		<?php
		// Use WordPress's built-in taxonomy checklist
		wp_terms_checklist(
			$post->ID,
			array(
				'taxonomy'      => $taxonomy,
				'checked_ontop' => false,
			)
		);
		?>
	</ul>
	<?php
}

/**
 * Render Features meta box.
 *
 * @param WP_Post $post Current post object.
 */
function main_render_product_features_meta_box( $post ) {
	$taxonomy = MAIN_PRODUCTS_FEATURES_TAXONOMY;
	$terms    = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		)
	);
	$selected = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
	?>
	<div class="main-product-taxonomy-meta">
		<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
			<div class="main-product-taxonomy-checklist">
				<?php
				foreach ( $terms as $term ) :
					$checked = in_array( $term->term_id, $selected, true ) ? 'checked="checked"' : '';
					?>
					<label class="main-product-taxonomy-checkbox" style="display: block; margin-bottom: 8px;"">
						<input type="checkbox" name="tax_input[<?php echo esc_attr( $taxonomy ); ?>][]" value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo $checked; ?>>
						<?php echo esc_html( $term->name ); ?>
					</label>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'No features available. Please add features first.', 'main' ); ?></p>
		<?php endif; ?>
		<div class="main-add-new-feature" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
			<label for="new-feature-name-<?php echo esc_attr( $post->ID ); ?>" style="display: block; margin-bottom: 5px; font-weight: 600;">
				<?php esc_html_e( 'Add New Feature', 'main' ); ?>
			</label>
			<div style="display: flex; gap: 8px;">
				<input 
					type="text" 
					id="new-feature-name-<?php echo esc_attr( $post->ID ); ?>" 
					name="new_feature_name" 
					placeholder="<?php esc_attr_e( 'Enter feature name', 'main' ); ?>" 
					style="flex: 1; padding: 6px 8px;"
				/>
				<button 
					type="button" 
					class="add-new-feature-btn button button-secondary"
					data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
					data-post-id="<?php echo esc_attr( $post->ID ); ?>"
				>
					<?php esc_html_e( 'Add', 'main' ); ?>
				</button>
			</div>
			<p class="description" style="margin-top: 5px; margin-bottom: 0;">
				<?php esc_html_e( 'Enter a new feature name and click Add to create it.', 'main' ); ?>
			</p>
			<div class="add-feature-message" style="margin-top: 8px; display: none;"></div>
		</div>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.add-new-feature-btn').on('click', function(e) {
			e.preventDefault();
			var $button = $(this);
			var $input = $button.siblings('input[type="text"]');
			var $message = $button.closest('.main-add-new-feature').find('.add-feature-message');
			var featureName = $input.val().trim();
			var taxonomy = $button.data('taxonomy');
			var postId = $button.data('post-id');
			
			if (!featureName) {
				$message.html('<span style="color: #d63638;"><?php echo esc_js( __( 'Please enter a feature name.', 'main' ) ); ?></span>').show();
				return;
			}
			
			$button.prop('disabled', true).text('<?php echo esc_js( __( 'Adding...', 'main' ) ); ?>');
			$message.hide();
			
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'main_add_product_feature',
					nonce: '<?php echo wp_create_nonce( 'main_add_product_feature' ); ?>',
					taxonomy: taxonomy,
					feature_name: featureName,
					post_id: postId
				},
				success: function(response) {
					if (response.success) {
						$input.val('');
						$message.html('<span style="color: #00a32a;"><?php echo esc_js( __( 'Feature added successfully!', 'main' ) ); ?></span>').show();
						
						// Dynamically add the new feature to the checklist
						var termId = response.data.term_id;
						var termName = response.data.term_name;
						var taxonomy = response.data.taxonomy;
						var $checklist = $button.closest('.main-product-taxonomy-meta').find('.main-product-taxonomy-checklist');
						
						// If checklist doesn't exist (no features yet), create it
						if ($checklist.length === 0) {
							var $metaBox = $button.closest('.main-product-taxonomy-meta');
							var $noFeaturesMsg = $metaBox.find('p');
							if ($noFeaturesMsg.length > 0) {
								$noFeaturesMsg.remove();
							}
							$checklist = $('<div class="main-product-taxonomy-checklist"></div>');
							$metaBox.prepend($checklist);
						}
						
						// Create the new checkbox label
						var $newLabel = $('<label class="main-product-taxonomy-checkbox" style="display: block; margin-bottom: 8px;""></label>');
						var $checkbox = $('<input>', {
							type: 'checkbox',
							name: 'tax_input[' + taxonomy + '][]',
							value: termId,
							checked: true // Auto-check the newly added feature
						});
						$newLabel.append($checkbox);
						$newLabel.append(document.createTextNode(' ' + termName));
						
						// Add to checklist
						$checklist.append($newLabel);
						
						// Hide success message after 2 seconds
						setTimeout(function() {
							$message.fadeOut();
						}, 2000);
						
						$button.prop('disabled', false).text('<?php echo esc_js( __( 'Add', 'main' ) ); ?>');
					} else {
						$message.html('<span style="color: #d63638;">' + (response.data || '<?php echo esc_js( __( 'Error adding feature.', 'main' ) ); ?>') + '</span>').show();
						$button.prop('disabled', false).text('<?php echo esc_js( __( 'Add', 'main' ) ); ?>');
					}
				},
				error: function() {
					$message.html('<span style="color: #d63638;"><?php echo esc_js( __( 'Error adding feature. Please try again.', 'main' ) ); ?></span>').show();
					$button.prop('disabled', false).text('<?php echo esc_js( __( 'Add', 'main' ) ); ?>');
				}
			});
		});
		
		// Allow Enter key to submit
		$('input[name="new_feature_name"]').on('keypress', function(e) {
			if (e.which === 13) {
				e.preventDefault();
				$(this).siblings('.add-new-feature-btn').click();
			}
		});
	});
	</script>
	<?php
}

/**
 * Render Best Suited For meta box.
 *
 * @param WP_Post $post Current post object.
 */
function main_render_product_best_suited_meta_box( $post ) {
	$taxonomy = MAIN_PRODUCTS_BEST_SUITED_TAXONOMY;
	$terms    = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		)
	);
	$selected = wp_get_post_terms( $post->ID, $taxonomy, array( 'fields' => 'names' ) );
	?>
	<div class="main-product-taxonomy-meta">
		<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
			<div class="main-product-taxonomy-checklist">
				<?php
				// For non-hierarchical taxonomies, WordPress expects term names, not IDs
				foreach ( $terms as $term ) :
					$checked = in_array( $term->name, $selected, true ) ? 'checked="checked"' : '';
					?>
					<label class="main-product-taxonomy-checkbox" style="display: block; margin-bottom: 8px;"">
						<input type="checkbox" name="tax_input[<?php echo esc_attr( $taxonomy ); ?>][]" value="<?php echo esc_attr( $term->name ); ?>" <?php echo $checked; ?>>
						<?php echo esc_html( $term->name ); ?>
					</label>
				<?php endforeach; ?>
			</div>
			<?php
			// Add a hidden input to ensure the taxonomy is always present in POST data
			// This allows WordPress to process empty selections (clearing all terms)
			?>
			<input type="hidden" name="tax_input[<?php echo esc_attr( $taxonomy ); ?>][]" value="">
		<?php else : ?>
			<p><?php esc_html_e( 'No "Best Suited For" options available. Please add options first.', 'main' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render Availability Flags meta box in sidebar.
 *
 * @param WP_Post $post Current post object.
 */
function main_render_product_availability_flags_meta_box( $post ) {
	wp_nonce_field( 'main_products_meta', 'main_products_meta_nonce' );
	?>
	<div class="main-product-meta__toggles">
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
			<label class="main-product-meta__checkbox" style="display: block; margin-bottom: 8px;">
				<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $checked, true ); ?> >
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endforeach; ?>
	</div>
	<?php
}

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
	$website_url = main_get_product_meta_value( $post->ID, 'website_url' );

	// Get rating data
	$score_breakdown_json = main_get_product_meta_value( $post->ID, 'score_breakdown', '[]' );
	$score_breakdown      = json_decode( $score_breakdown_json, true );
	if ( ! is_array( $score_breakdown ) ) {
		$score_breakdown = array();
	}

	// Auto-populate default criteria if empty (for display only)
	$default_criteria = array(
		array( 'name' => 'Ease of Use', 'score' => 3 ),
		array( 'name' => 'Features', 'score' => 3 ),
		array( 'name' => 'Customer Support', 'score' => 3 ),
		array( 'name' => 'Value for Money', 'score' => 3 ),
	);

	// Use defaults if no criteria exist
	$is_new_post = ( 'auto-draft' === $post->post_status || empty( $score_breakdown_json ) || $score_breakdown_json === '[]' );
	if ( $is_new_post && empty( $score_breakdown ) ) {
		$score_breakdown = $default_criteria;
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
	// Use auto-calculated rating if saved rating is empty
	$display_rating = ! empty( $rating ) ? $rating : $auto_rating;
	?>
	<div class="main-product-meta">
		<!-- Product Name -->
		<div class="main-product-meta__section">
			<label for="main-product-name"><strong><?php esc_html_e( 'Product Name', 'main' ); ?></strong></label>
			<input type="text" id="main-product-name" name="product_name" value="<?php echo esc_attr( main_get_product_meta_value( $post->ID, 'product_name' ) ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Defaults to the title if left blank.', 'main' ); ?>">
		</div>

		<!-- Product Tagline -->
		<div class="main-product-meta__field">
			<label for="main-product-tagline"><strong><?php esc_html_e( 'Product Tagline', 'main' ); ?></strong></label>
			<input type="text" id="main-product-tagline" name="tagline" value="<?php echo esc_attr( main_get_product_meta_value( $post->ID, 'tagline' ) ); ?>" class="widefat">
		</div>

		<!-- Product Description -->
		<div class="main-product-meta__field">
			<label for="main-product-custom-note"><strong><?php esc_html_e( 'Product Description', 'main' ); ?></strong></label>
			<textarea id="main-product-custom-note" name="custom_note" rows="3" class="widefat"><?php echo esc_textarea( main_get_product_meta_value( $post->ID, 'custom_note' ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'This description is used in honorable mentions and top alternatives product cards.', 'main' ); ?></p>
		</div>

		<!-- Product Website -->
		<div class="main-product-meta__field">
			<label for="main-product-website"><strong><?php esc_html_e( 'Product Website', 'main' ); ?></strong></label>
			<input type="url" id="main-product-website" name="website_url" value="<?php echo esc_attr( $website_url ); ?>" class="widefat">
		</div>

		<!-- Product Logo -->
		<div class="main-product-meta__logo">
			<label><strong><?php esc_html_e( 'Product Logo', 'main' ); ?></strong></label>
			<div class="main-product-meta__logo-preview">
				<?php if ( $logo_src ) : ?>
					<img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php esc_attr_e( 'Product logo preview', 'main' ); ?>" width="150" />
				<?php else : ?>
					<span class="main-product-meta__logo-placeholder"><?php esc_html_e( 'No logo selected.', 'main' ); ?></span>
				<?php endif; ?>
			</div>
			<input type="hidden" id="main-product-logo" name="product_logo" value="<?php echo esc_attr( $logo_id ); ?>">
			<button type="button" class="button main-product-upload-logo"><?php esc_html_e( 'Upload/Select Logo', 'main' ); ?></button>
			<button type="button" class="button button-secondary main-product-fetch-logo" data-post-id="<?php echo absint( $post->ID ); ?>" data-website="<?php echo esc_url( $website_url ); ?>"><?php esc_html_e( 'Fetch from Website URL', 'main' ); ?></button>
		</div>

		<!-- Product Pricing Summary -->
		<div class="main-product-meta__field">
			<label for="main-product-pricing"><strong><?php esc_html_e( 'Product Pricing Summary', 'main' ); ?></strong></label>
			<input type="text" id="main-product-pricing" name="pricing_summary" value="<?php echo esc_attr( main_get_product_meta_value( $post->ID, 'pricing_summary' ) ); ?>" class="widefat">
		</div>

		<!-- Product Award -->
		<div class="main-product-meta__field">
			<label for="main-product-award"><strong><?php esc_html_e( 'Product Award', 'main' ); ?></strong></label>
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

		<!-- Product Rating -->
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
				value="<?php echo esc_attr( $display_rating ); ?>" 
				class="small-text"
				readonly
			>
			<p class="description">
				<?php esc_html_e( 'Auto-calculated from Score Breakdown below.', 'main' ); ?>
				<?php if ( $auto_rating ) : ?>
					<strong><?php echo esc_html( $auto_rating ); ?>/5</strong>
				<?php endif; ?>
			</p>

			<div style="margin-top: 15px;">
				<label><strong><?php esc_html_e( 'Score Breakdown', 'main' ); ?></strong></label>
				<p class="description">
					<?php esc_html_e( 'Add criteria and scores to calculate the overall rating.', 'main' ); ?>
					<?php if ( $is_new_post && ! empty( $score_breakdown ) ) : ?>
						<br><em><?php esc_html_e( 'Default criteria are pre-populated. You can remove or modify them as needed.', 'main' ); ?></em>
					<?php endif; ?>
				</p>
				
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
				
				<input type="hidden" id="main-product-score-breakdown" name="score_breakdown" value="<?php echo esc_attr( wp_json_encode( $score_breakdown ) ); ?>" />
				<button type="button" class="button button-secondary main-product-add-score-criterion">
					<?php esc_html_e( '+ Add Criterion', 'main' ); ?>
				</button>
				<div class="main-product-meta__auto-rating-display" style="margin-top: 10px; padding: 8px; background: #f0f0f1; border-radius: 4px;">
					<strong><?php esc_html_e( 'Calculated Rating:', 'main' ); ?></strong> 
					<span id="main-product-auto-rating"><?php echo esc_html( $auto_rating ? $auto_rating . '/5' : 'N/A' ); ?></span>
				</div>
			</div>
		</div>

		<!-- Rating Count -->
		<div class="main-product-meta__field" style="margin-top: 15px;">
				<label for="main-product-rating-count">
						<strong><?php esc_html_e( 'Rating Count', 'main' ); ?></strong>
				</label>
				<input 
						type="number" 
						min="0" 
						step="1"
						id="main-product-rating-count" 
						name="rating_count" 
						value="<?php echo esc_attr( main_get_product_meta_value( $post->ID, 'rating_count', 0 ) ); ?>" 
						class="small-text"
				>
				<p class="description">
						<?php esc_html_e( 'Number of ratings/reviews for this product. Used in structured data (Schema.org).', 'main' ); ?>
				</p>
		</div>

		<!-- Affiliate / CTA URL -->
		<div class="main-product-meta__field">
			<label for="main-product-affiliate"><strong><?php esc_html_e( 'Affiliate / CTA URL', 'main' ); ?></strong></label>
			<input type="url" id="main-product-affiliate" name="affiliate_link" value="<?php echo esc_url( main_get_product_meta_value( $post->ID, 'affiliate_link' ) ); ?>" class="widefat">
		</div>

		<!-- Product Update Logs -->
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

	// Handle Best Suited For taxonomy - ensure empty selections are saved (clear all terms)
	$best_suited_taxonomy = MAIN_PRODUCTS_BEST_SUITED_TAXONOMY;
	if ( isset( $_POST['tax_input'][ $best_suited_taxonomy ] ) ) {
		$submitted_terms = $_POST['tax_input'][ $best_suited_taxonomy ];
		
		// Filter out empty values from the array
		$submitted_terms = array_filter( (array) $submitted_terms, function( $term ) {
			return ! empty( $term );
		} );
		
		// If array is empty or only contains empty strings, clear all terms
		if ( empty( $submitted_terms ) ) {
			wp_set_post_terms( $post_id, array(), $best_suited_taxonomy, false );
		} else {
			// WordPress will handle the term assignment normally
			// We just ensure empty values are filtered out
			$_POST['tax_input'][ $best_suited_taxonomy ] = array_values( $submitted_terms );
		}
	} else {
		// If tax_input is not present at all, it means no checkboxes were rendered
		// In this case, we don't modify the existing terms
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
 * AJAX handler to add new product feature from edit screen.
 */
function main_ajax_add_product_feature() {
	// Verify nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'main_add_product_feature' ) ) {
		wp_send_json_error( array( 'message' => __( 'Security check failed.', 'main' ) ) );
	}

	// Check user permissions
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to perform this action.', 'main' ) ) );
	}

	$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) : '';
	$feature_name = isset( $_POST['feature_name'] ) ? sanitize_text_field( wp_unslash( $_POST['feature_name'] ) ) : '';
	$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

	if ( empty( $taxonomy ) || empty( $feature_name ) ) {
		wp_send_json_error( array( 'message' => __( 'Feature name is required.', 'main' ) ) );
	}

	// Verify taxonomy exists and is valid
	if ( ! taxonomy_exists( $taxonomy ) || $taxonomy !== MAIN_PRODUCTS_FEATURES_TAXONOMY ) {
		wp_send_json_error( array( 'message' => __( 'Invalid taxonomy.', 'main' ) ) );
	}

	// Check if term already exists
	$existing_term = term_exists( $feature_name, $taxonomy );
	
	if ( $existing_term ) {
		// Term exists, assign it to the post
		$term_id = is_array( $existing_term ) ? $existing_term['term_id'] : $existing_term;
		wp_set_post_terms( $post_id, array( $term_id ), $taxonomy, true );
		$term = get_term( $term_id, $taxonomy );
		if ( ! $term || is_wp_error( $term ) ) {
			wp_send_json_error( array( 'message' => __( 'Error retrieving term data.', 'main' ) ) );
		}
		wp_send_json_success( array( 
			'message' => __( 'Feature added successfully.', 'main' ),
			'term_id' => $term_id,
			'term_name' => $term->name,
			'taxonomy' => $taxonomy
		) );
	}

	// Create new term
	$term_result = wp_insert_term( $feature_name, $taxonomy );

	if ( is_wp_error( $term_result ) ) {
		wp_send_json_error( array( 'message' => $term_result->get_error_message() ) );
	}

	$term_id = $term_result['term_id'];

	// Assign term to the post
	wp_set_post_terms( $post_id, array( $term_id ), $taxonomy, true );
	
	$term = get_term( $term_id, $taxonomy );
	if ( ! $term || is_wp_error( $term ) ) {
		wp_send_json_error( array( 'message' => __( 'Error retrieving term data.', 'main' ) ) );
	}

	wp_send_json_success( array( 
		'message' => __( 'Feature added successfully.', 'main' ),
		'term_id' => $term_id,
		'term_name' => $term->name,
		'taxonomy' => $taxonomy
	) );
}
add_action( 'wp_ajax_main_add_product_feature', 'main_ajax_add_product_feature' );

/**
 * Register REST field to include product features in product REST response.
 */
function main_register_product_features_rest_field() {
	register_rest_field(
		MAIN_PRODUCTS_CPT,
		'product_features_data',
		array(
			'get_callback' => function ( $post ) {
				$terms = wp_get_post_terms( $post['id'], MAIN_PRODUCTS_FEATURES_TAXONOMY );
				if ( is_wp_error( $terms ) || empty( $terms ) ) {
					return array();
				}
				return array_map(
					function ( $term ) {
						return array(
							'id'   => $term->term_id,
							'name' => $term->name,
							'slug' => $term->slug,
						);
					},
					$terms
				);
			},
			'schema'       => array(
				'description' => __( 'Product features taxonomy terms', 'main' ),
				'type'        => 'array',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'   => array( 'type' => 'integer' ),
						'name' => array( 'type' => 'string' ),
						'slug' => array( 'type' => 'string' ),
					),
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'main_register_product_features_rest_field' );

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

	// Remove TLD from domain for filename (e.g., "monday.com" -> "monday")
	$domain_parts = explode( '.', $domain );
	if ( count( $domain_parts ) > 1 ) {
		// Remove the last part (TLD)
		array_pop( $domain_parts );
		$domain_name = implode( '.', $domain_parts );
	} else {
		$domain_name = $domain;
	}

	$file_array = array(
		'name'     => sanitize_file_name( $domain_name . '-logo.png' ),
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

/**
 * Extract numeric pricing value from pricing summary string.
 *
 * @param string $pricing_summary Pricing summary text.
 * @return float|null Extracted price or null.
 */
function main_extract_pricing_value( $pricing_summary ) {
	if ( empty( $pricing_summary ) ) {
		return null;
	}

	// Remove common text patterns and convert to lowercase
	$pricing_summary = strtolower( trim( $pricing_summary ) );
	
	// Check for "free" pricing
	if ( strpos( $pricing_summary, 'free' ) !== false ) {
		return 0;
	}

	// Extract first number found (supports formats like $10, 10$, $10/mo, €10, etc.)
	preg_match( '/(\d+(?:\.\d{1,2})?)/', $pricing_summary, $matches );
	
	if ( ! empty( $matches[1] ) ) {
		return floatval( $matches[1] );
	}

	return null;
}