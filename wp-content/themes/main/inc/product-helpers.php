<?php
/**
 * Product Helper Functions
 *
 * Utility functions for querying and displaying products from the Products CPT.
 *
 * @package Main
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get product data by ID
 *
 * @param int $product_id Product post ID.
 * @return array|null Product data or null if not found.
 */
function main_get_product_data( $product_id ) {
    $post = get_post( $product_id );
    
    if ( ! $post || 'products' !== $post->post_type ) {
        return null;
    }
    
    // Get product logo
    $logo = get_post_meta( $product_id, 'product_logo', true );
    $logo_url = '';
    $logo_attachment_id = 0;
    
    if ( $logo ) {
        if ( is_numeric( $logo ) ) {
            // It's an attachment ID
            $logo_attachment_id = (int) $logo;
            $logo_url = wp_get_attachment_image_url( $logo_attachment_id, 'medium' );
        } else {
            // It's a URL
            $logo_url = $logo;
        }
    }
    
    // Get update logs
    $update_logs_json = get_post_meta( $product_id, 'product_update_logs', true );
    $update_logs = array();
    if ( ! empty( $update_logs_json ) ) {
        $decoded = json_decode( $update_logs_json, true );
        if ( is_array( $decoded ) ) {
            $update_logs = $decoded;
        }
    }
    
    // Get availability flags from taxonomy
    $availability_flags = array();
    if ( defined( 'MAIN_PRODUCTS_AVAILABILITY_TAXONOMY' ) ) {
        $terms = wp_get_post_terms( $product_id, MAIN_PRODUCTS_AVAILABILITY_TAXONOMY, array( 'fields' => 'names' ) );
        if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
            $availability_flags = $terms;
        }
    }
    
    // Map taxonomy terms to boolean flags for backward compatibility
    $has_free_trial = in_array( 'Has Free Trial', $availability_flags, true );
    $has_free_plan = in_array( 'Has Free Plan', $availability_flags, true );
    $has_demo = in_array( 'Has Demo', $availability_flags, true );
    $open_source = in_array( 'Open Source', $availability_flags, true );
    $ai_powered = in_array( 'AI-Powered', $availability_flags, true );
    
    return array(
        'id'                => $product_id,
        'name'              => get_post_meta( $product_id, 'product_name', true ) ?: $post->post_title,
        'title'             => $post->post_title,
        'tagline'           => get_post_meta( $product_id, 'tagline', true ),
        'logo'              => $logo_url,
        'logo_attachment_id' => $logo_attachment_id,
        'website_url'       => get_post_meta( $product_id, 'website_url', true ),
        'affiliate_link'     => get_post_meta( $product_id, 'affiliate_link', true ),
        'pricing'           => get_post_meta( $product_id, 'pricing_summary', true ),
        'rating'            => (float) get_post_meta( $product_id, 'our_rating', true ),
        'rating_count'      => (int) get_post_meta( $product_id, 'rating_count', true ),
        'availability_flags' => $availability_flags,
        'has_free_trial'    => $has_free_trial,
        'has_free_plan'     => $has_free_plan,
        'has_demo'          => $has_demo,
        'open_source'       => $open_source,
        'ai_powered'        => $ai_powered,
        'award'             => get_post_meta( $product_id, 'award', true ),
        'custom_note'       => get_post_meta( $product_id, 'custom_note', true ),
        'update_logs'       => $update_logs,
        'show_update_logs'  => (bool) get_post_meta( $product_id, 'show_update_logs', true ),
        'permalink'         => get_permalink( $product_id ),
    );
}

/**
 * Register custom REST API endpoint for product data
 */
function main_register_product_data_rest_route() {
    register_rest_route(
        'main/v1',
        '/product-data/(?P<id>\d+)',
        array(
            'methods'             => 'GET',
            'callback'            => 'main_get_product_data_rest',
            'permission_callback' => function() {
                return current_user_can( 'edit_posts' );
            },
            'args'                => array(
                'id' => array(
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),
            ),
        )
    );
}
add_action( 'rest_api_init', 'main_register_product_data_rest_route' );
add_action( 'rest_api_init', 'main_register_awards_rest_route' );
add_action( 'rest_api_init', 'main_register_products_by_category_rest_route' );

/**
 * REST API callback to get product data
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error
 */
function main_get_product_data_rest( $request ) {
    $product_id = $request->get_param( 'id' );
    $product_data = main_get_product_data( $product_id );
    
    if ( ! $product_data ) {
        return new WP_Error(
            'product_not_found',
            __( 'Product not found.', 'main' ),
            array( 'status' => 404 )
        );
    }
    
    return rest_ensure_response( $product_data );
}

/**
 * Register REST API endpoint for awards configuration
 */
function main_register_awards_rest_route() {
    register_rest_route(
        'main/v1',
        '/awards',
        array(
            'methods'             => 'GET',
            'callback'            => 'main_get_awards_rest',
            'permission_callback' => function() {
                return current_user_can( 'edit_posts' );
            },
        )
    );
}

/**
 * REST API callback to get awards configuration
 *
 * @return WP_REST_Response
 */
function main_get_awards_rest() {
    if ( ! function_exists( 'main_get_product_awards' ) ) {
        return rest_ensure_response( array() );
    }
    
    $awards = main_get_product_awards();
    return rest_ensure_response( $awards );
}

/**
 * Register REST API endpoint for products by category
 */
function main_register_products_by_category_rest_route() {
    register_rest_route(
        'main/v1',
        '/products-by-category/(?P<id>\d+)',
        array(
            'methods'             => 'GET',
            'callback'            => 'main_get_products_by_category_rest',
            'permission_callback' => function() {
                return current_user_can( 'edit_posts' );
            },
            'args'                => array(
                'id' => array(
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint',
                ),
            ),
        )
    );
}

/**
 * REST API callback to get products by category
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error
 */
function main_get_products_by_category_rest( $request ) {
    $category_id = $request->get_param( 'id' );
    $limit = $request->get_param( 'limit' ) ? absint( $request->get_param( 'limit' ) ) : 100;
    
    // Verify category exists
    $term = get_term( $category_id, 'product-category' );
    if ( ! $term || is_wp_error( $term ) ) {
        return new WP_Error(
            'category_not_found',
            __( 'Product category not found.', 'main' ),
            array( 'status' => 404 )
        );
    }
    
    $products = main_get_products_by_category( $category_id, $limit );
    
    return rest_ensure_response( $products );
}

/**
 * Search products by title (AJAX endpoint)
 */
function main_ajax_search_products() {
    check_ajax_referer( 'main-products-nonce', 'nonce' );
    
    $search = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
    $products = main_search_products( $search, 50 );
    
    wp_send_json_success( $products );
}
add_action( 'wp_ajax_main_search_products', 'main_ajax_search_products' );

/**
 * Search products by title
 *
 * @param string $search Search term.
 * @param int    $limit  Number of results to return.
 * @return array Array of product data.
 */
function main_search_products( $search = '', $limit = 20 ) {
    $args = array(
        'post_type'      => 'products',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    
    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }
    
    $query = new WP_Query( $args );
    $products = array();
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $product = main_get_product_data( get_the_ID() );
            if ( $product ) {
                $products[] = $product;
            }
        }
        wp_reset_postdata();
    }
    
    return $products;
}

/**
 * Get products by category
 *
 * @param int $category_id Category term ID.
 * @param int $limit       Number of results to return.
 * @return array Array of product data.
 */
function main_get_products_by_category( $category_id, $limit = 20 ) {
    $args = array(
        'post_type'      => 'products',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product-category',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        ),
    );
    
    $query = new WP_Query( $args );
    $products = array();
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $product = main_get_product_data( get_the_ID() );
            if ( $product ) {
                $products[] = $product;
            }
        }
        wp_reset_postdata();
    }
    
    return $products;
}

/**
 * Generate product badges HTML
 *
 * @param array $product Product data.
 * @return string HTML for product badges.
 */
function main_get_product_badges( $product ) {
    $badges = array();
    
    // Get availability flags from taxonomy
    $availability_flags = array();
    if ( isset( $product['availability_flags'] ) && is_array( $product['availability_flags'] ) ) {
        $availability_flags = $product['availability_flags'];
    } elseif ( isset( $product['id'] ) ) {
        // Fallback: fetch from taxonomy if not in product data
        if ( defined( 'MAIN_PRODUCTS_AVAILABILITY_TAXONOMY' ) ) {
            $terms = wp_get_post_terms( $product['id'], MAIN_PRODUCTS_AVAILABILITY_TAXONOMY, array( 'fields' => 'names' ) );
            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                $availability_flags = $terms;
            }
        }
    }
    
    // Generate badges dynamically from taxonomy terms
    foreach ( $availability_flags as $flag ) {
        $flag_name = esc_html( $flag );
        
        // Remove "Has" prefix if present for display
        $display_name = preg_replace( '/^Has\s+/i', '', $flag_name );
        
        // AI-Powered gets gradient badge, others get outline badge
        if ( stripos( $flag, 'AI' ) !== false || stripos( $flag, 'AI-Powered' ) !== false ) {
            $badges[] = '<span class="product-badge product-badge--gradient flex flex-row justify-center items-center gap-2.5 rounded-lg md:rounded-xl">
                <span class="text-sm md:text-base font-semibold leading-[1.375rem] text-gray-800">' . $display_name . '</span>
            </span>';
        } else {
            $badges[] = '<span class="product-badge product-badge--outline flex flex-row justify-center items-center py-[0.1875rem] md:py-1.5 px-3 md:px-4 gap-2.5 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl">
                <span class="text-sm md:text-base font-medium leading-[1.375rem] text-gray-800 tracking-2p">' . $display_name . '</span>
            </span>';
        }
    }
    
    return implode( "\n", $badges );
}

/**
 * Generate rating stars HTML
 *
 * @param float $rating Rating value (1.0 - 5.0).
 * @return string HTML for star rating.
 */
function main_get_rating_stars( $rating ) {
    $full_stars = floor( $rating );
    $half_star = ( $rating - $full_stars ) >= 0.5;
    $empty_stars = 5 - $full_stars - ( $half_star ? 1 : 0 );
    
    $full_star_svg = '<svg class="w-4 h-4" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.6431 7.17815L11.8306 9.60502L12.6875 13.2344C12.7347 13.4314 12.7226 13.638 12.6525 13.8281C12.5824 14.0182 12.4575 14.1833 12.2937 14.3025C12.1298 14.4217 11.9343 14.4896 11.7319 14.4977C11.5294 14.5059 11.3291 14.4538 11.1562 14.3481L7.99996 12.4056L4.84184 14.3481C4.66898 14.4532 4.4689 14.5048 4.2668 14.4963C4.06469 14.4879 3.8696 14.4199 3.70609 14.3008C3.54257 14.1817 3.41795 14.0169 3.3479 13.8272C3.27786 13.6374 3.26553 13.4312 3.31246 13.2344L4.17246 9.60502L1.35996 7.17815C1.20702 7.04597 1.09641 6.87166 1.04195 6.67699C0.987486 6.48232 0.99158 6.27592 1.05372 6.08356C1.11586 5.89121 1.23329 5.72142 1.39135 5.59541C1.54941 5.4694 1.7411 5.39274 1.94246 5.37502L5.62996 5.07752L7.05246 1.63502C7.12946 1.44741 7.2605 1.28693 7.42894 1.17398C7.59738 1.06104 7.7956 1.00073 7.9984 1.00073C8.2012 1.00073 8.39942 1.06104 8.56785 1.17398C8.73629 1.28693 8.86734 1.44741 8.94434 1.63502L10.3662 5.07752L14.0537 5.37502C14.2555 5.39209 14.4477 5.46831 14.6064 5.59415C14.765 5.71999 14.883 5.88984 14.9455 6.08243C15.008 6.27502 15.0123 6.48178 14.9579 6.6768C14.9034 6.87183 14.7926 7.04644 14.6393 7.17877L14.6431 7.17815Z" fill="currentColor" class="text-rating-star"/></svg>';
    
    $half_star_svg = '<svg class="w-4 h-4" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.9483 6.07866C14.8858 5.88649 14.7678 5.71712 14.6092 5.59188C14.4506 5.46665 14.2585 5.39116 14.0571 5.37491L10.3696 5.07741L8.9458 1.63429C8.86881 1.44667 8.73776 1.28619 8.56932 1.17325C8.40088 1.06031 8.20267 1 7.99987 1C7.79707 1 7.59885 1.06031 7.43041 1.17325C7.26197 1.28619 7.13093 1.44667 7.05393 1.63429L5.63143 5.07679L1.94205 5.37491C1.74029 5.39198 1.54805 5.4682 1.38941 5.59404C1.23078 5.71988 1.11281 5.88973 1.05028 6.08232C0.987751 6.27491 0.983448 6.48167 1.03791 6.67669C1.09237 6.87172 1.20317 7.04633 1.35643 7.17866L4.16893 9.60554L3.31205 13.2343C3.26478 13.4313 3.27695 13.6379 3.34704 13.828C3.41713 14.0181 3.54199 14.1832 3.70584 14.3024C3.8697 14.4216 4.0652 14.4895 4.26765 14.4976C4.4701 14.5058 4.67042 14.4537 4.8433 14.348L7.99955 12.4055L11.1577 14.348C11.3305 14.4531 11.5306 14.5047 11.7327 14.4962C11.9348 14.4878 12.1299 14.4198 12.2934 14.3007C12.4569 14.1816 12.5816 14.0168 12.6516 13.8271C12.7217 13.6373 12.734 13.431 12.6871 13.2343L11.8271 9.60491L14.6396 7.17804C14.7941 7.04593 14.9059 6.87093 14.9608 6.67522C15.0158 6.47951 15.0114 6.27189 14.9483 6.07866ZM13.9896 6.42054L10.9458 9.04554C10.8764 9.10537 10.8248 9.18312 10.7965 9.27031C10.7683 9.35749 10.7646 9.45076 10.7858 9.53991L11.7158 13.4649C11.7182 13.4703 11.7184 13.4764 11.7165 13.482C11.7145 13.4876 11.7105 13.4922 11.7052 13.4949C11.6939 13.5037 11.6908 13.5018 11.6814 13.4949L8.26143 11.3918C8.18257 11.3437 8.09192 11.3184 7.99955 11.3187V1.99991C8.01455 1.99991 8.01643 2.00491 8.02143 2.01616L9.56205 5.74116C9.59722 5.82631 9.65523 5.90008 9.72967 5.95434C9.80412 6.00861 9.89211 6.04125 9.98393 6.04866L13.9783 6.37116C13.9883 6.37116 13.9939 6.37116 13.9996 6.38929C14.0052 6.40741 13.9996 6.41429 13.9896 6.42054Z" fill="currentColor" class="text-rating-star"/></svg>';
    
    $empty_star_svg = '<svg class="w-4 h-4" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.9483 6.07866C14.8858 5.88649 14.7678 5.71712 14.6092 5.59188C14.4506 5.46665 14.2585 5.39116 14.0571 5.37491L10.3696 5.07741L8.9458 1.63429C8.86881 1.44667 8.73776 1.28619 8.56932 1.17325C8.40088 1.06031 8.20267 1 7.99987 1C7.79707 1 7.59885 1.06031 7.43041 1.17325C7.26197 1.28619 7.13093 1.44667 7.05393 1.63429L5.63143 5.07679L1.94205 5.37491C1.74029 5.39198 1.54805 5.4682 1.38941 5.59404C1.23078 5.71988 1.11281 5.88973 1.05028 6.08232C0.987751 6.27491 0.983448 6.48167 1.03791 6.67669C1.09237 6.87172 1.20317 7.04633 1.35643 7.17866L4.16893 9.60554L3.31205 13.2343C3.26478 13.4313 3.27695 13.6379 3.34704 13.828C3.41713 14.0181 3.54199 14.1832 3.70584 14.3024C3.8697 14.4216 4.0652 14.4895 4.26765 14.4976C4.4701 14.5058 4.67042 14.4537 4.8433 14.348L7.99955 12.4055L11.1577 14.348C11.3305 14.4531 11.5306 14.5047 11.7327 14.4962C11.9348 14.4878 12.1299 14.4198 12.2934 14.3007C12.4569 14.1816 12.5816 14.0168 12.6516 13.8271C12.7217 13.6373 12.734 13.431 12.6871 13.2343L11.8271 9.60491L14.6396 7.17804C14.7941 7.04593 14.9059 6.87093 14.9608 6.67522C15.0158 6.47951 15.0114 6.27189 14.9483 6.07866Z" fill="currentColor" class="text-gray-300"/></svg>';
    
    $html = '<div class="flex flex-row items-start p-0 gap-0.5 flex-none">';
    $html .= str_repeat( $full_star_svg, $full_stars );
    if ( $half_star ) {
        $html .= $half_star_svg;
    }
    $html .= str_repeat( $empty_star_svg, $empty_stars );
    $html .= '</div>';
    
    return $html;
}