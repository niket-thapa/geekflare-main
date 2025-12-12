<?php
/**
 * Product Item Block Template
 *
 * @var array    $attributes Block attributes.
 * @var string   $content Block content.
 * @var WP_Block $block Block instance.
 */

$product_id = $attributes['productId'] ?? 0;
if ( ! $product_id ) {
    return;
}

$product = main_get_product_data( $product_id );
if ( ! $product ) {
    return;
}

// Get logo attachment ID from product data
$logo_attachment_id = $product['logo_attachment_id'] ?? 0;

// Product number is auto-generated in the editor based on position
$product_number = isset( $attributes['productNumber'] ) && $attributes['productNumber'] > 0 
    ? (int) $attributes['productNumber'] 
    : 1;

$is_highlighted = $attributes['isHighlighted'] ?? false;
$show_update_logs = isset( $attributes['showUpdateLogs'] ) ? (bool) $attributes['showUpdateLogs'] : true;
$update_logs = $product['update_logs'] ?? array();

$product_slug = sanitize_title( $product['name'] );

// Prepare filter data
$pricing_value = get_post_meta( $product_id, 'pricing_summary', true );
if ( empty( $pricing_value ) && ! is_numeric( $pricing_value ) ) {
	$pricing_value = null;
}

// Get Best Suited For terms
$best_suited_terms = wp_get_post_terms( $product_id, MAIN_PRODUCTS_BEST_SUITED_TAXONOMY, array( 'fields' => 'slugs' ) );
if ( is_wp_error( $best_suited_terms ) ) {
    $best_suited_terms = array();
}

// Get Features terms
$features_terms = wp_get_post_terms( $product_id, MAIN_PRODUCTS_FEATURES_TAXONOMY, array( 'fields' => 'slugs' ) );
if ( is_wp_error( $features_terms ) ) {
    $features_terms = array();
}

// Get Availability terms
$availability_terms = wp_get_post_terms( $product_id, MAIN_PRODUCTS_AVAILABILITY_TAXONOMY, array( 'fields' => 'slugs' ) );
if ( is_wp_error( $availability_terms ) ) {
    $availability_terms = array();
}

// Combine availability terms with features terms for filtering
$combined_features_terms = array_merge( $availability_terms, $features_terms );

// Prepare filter data as JSON
$filter_data = array(
    'pricing' => $pricing_value,
    'bestSuited' => $best_suited_terms,
    'features' => $combined_features_terms,
);
?>

<article 
    id="<?php echo esc_attr( $product_slug ); ?>"
    class="buying_guide_item bg-white border border-gray-200 rounded-2xl md:rounded-3xl relative <?php echo $is_highlighted ? 'mt-[1.3125rem] md:mt-0' : 'mt-[1.3125rem] md:mt-0'; ?>"
    data-product-filter='<?php echo wp_json_encode( $filter_data ); ?>'>
    
    <?php // Product Number ?>
    <div class="product-number <?php echo $is_highlighted ? 'active' : ''; ?>" aria-label="Rank <?php echo esc_attr( $product_number ); ?>">
        <span aria-hidden="true"><?php echo esc_html( $product_number ); ?></span>
    </div>
    
    <div class="p-5 md:p-6 lg:p-7 xl:p-8 flex flex-col gap-4 md:gap-6 [&_p]:my-0 [&_p:empty]:hidden">
        <?php // Product Header ?>
        <div class="flex flex-col md:flex-row md:justify-between md:items-start md:flex-wrap gap-6 md:gap-y-4">
            <div class="flex gap-4 md:gap-6 items-center">
                <?php if ( $product['logo'] ) : ?>
                    <div class="product-logo-wrap w-12 [&_img]:w-full [&_img]:h-auto md:w-20 [&_img]:m-0">
                        <?php
                        if ( $logo_attachment_id ) {
                            echo wp_get_attachment_image(
                                $logo_attachment_id,
                                'medium',
                                false,
                                array(
                                    'alt'     => esc_attr( $product['name'] ),
                                    'loading' => 'lazy',
                                    'class'   => 'w-full h-auto object-cover m-0',
                                )
                            );
                        } else {
                            ?>
                        <img src="<?php echo esc_url( $product['logo'] ); ?>" alt="<?php echo esc_attr( $product['name'] ); ?>" width="80" height="64" loading="lazy" />
                            <?php
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <div class="product-name-wrap flex-1 flex flex-col gap-0.5 md:gap-1.5">
                    <h3 class="text-lg md:text-2xl leading-5 md:leading-none font-bold text-gray-800 m-0">
                        <?php echo esc_html( $product['name'] ); ?>
                    </h3>
                    <?php if ( $product['tagline'] ) : ?>
                        <div class="text-gray-500 text-sm md:text-base font-medium md:tracking-2p md:leading-6">
                            <?php echo esc_html( $product['tagline'] ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php // Product Badges ?>
            <div class="flex flex-row flex-wrap items-start content-start p-0 gap-2 md:order-2 md:w-full md:flex-[100%]">
                <?php echo main_get_product_badges( $product ); ?>
            </div>
            
            <?php // CTA Button ?>
            <a href="<?php echo esc_url( ! empty( $product['affiliate_link'] ) ? $product['affiliate_link'] : ( ! empty( $product['website_url'] ) ? $product['website_url'] : $product['permalink'] ) ); ?>" 
               class="btn btn--primary rounded-full"
               target="_blank"
               rel="nofollow noopener">
                <?php esc_html_e( 'Visit Site', 'main' ); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14">
                    <path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.416 4.083H3.5A1.167 1.167 0 0 0 2.333 5.25v5.25A1.167 1.167 0 0 0 3.5 11.667h5.25A1.167 1.167 0 0 0 9.916 10.5V7.583M5.833 8.167l5.833-5.834M8.75 2.333h2.917V5.25"/>
                </svg>
            </a>
        </div>
        
        <?php // Product Content (InnerBlocks) ?>
        <?php echo $content; ?>
    </div>
        
    <?php if ( $show_update_logs && ! empty( $update_logs ) ) : ?>
        <?php $update_log_id = 'product-update-log-' . wp_unique_id(); ?>
        <div class="product-update-log flex flex-col items-start p-0 gap-0 flex-none self-stretch rounded-b-2xl md:rounded-b-3xl overflow-hidden">
            <button
                type="button"
                class="product-update-log__header box-border flex flex-row justify-between items-center py-3.5 md:py-5 px-5 md:px-8 gap-5 w-full bg-gray-50 border-t border-gray-200 flex-none self-stretch transition-colors"
                aria-expanded="false"
                aria-controls="<?php echo esc_attr( $update_log_id ); ?>"
                data-toggle="product-update-log">
                <h3 class="text-sm md:text-base font-semibold leading-6 flex items-center text-gray-800 flex-none m-0">
                    <?php esc_html_e( 'Product Update Log', 'main' ); ?>
                </h3>
                <svg
                    class="product-update-log__chevron w-6 h-6 flex-none transition-transform duration-200"
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    fill="none"
                    viewBox="0 0 20 20">
                    <path
                        stroke="#1d2939"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="m5 7.5 5 5 5-5"/>
                </svg>
            </button>

            <div
                id="<?php echo esc_attr( $update_log_id ); ?>"
                class="product-update-log__content accordion-panel items-start self-stretch overflow-hidden"
                role="region"
                aria-live="polite"
                style="max-height: 0;">
                <div class="accordion-panel__inner flex flex-col gap-2 p-6 md:px-8 border-t border-gray-200">
                    <?php foreach ( $update_logs as $update ) : ?>
                        <?php if ( empty( $update['date'] ) && empty( $update['description'] ) ) {
                            continue;
                        } ?>
                        <div class="text-base font-medium leading-6 tracking-2p text-gray-600 flex-none self-stretch">
                            <?php if ( ! empty( $update['date'] ) ) : ?>
                                <strong class="font-semibold text-gray-800">
                                    <?php echo esc_html( $update['date'] ); ?>:
                                </strong>
                            <?php endif; ?>
                            <?php if ( ! empty( $update['description'] ) ) : ?>
                                <?php echo esc_html( $update['description'] ); ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php
    // Prepare SoftwareApplication structured data
    $structured_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'SoftwareApplication',
        'name' => $product['name'],
        'applicationCategory' => 'BusinessApplication',
    );
    
    // Add aggregateRating if rating exists
    if ( ! empty( $product['rating'] ) && $product['rating'] > 0 ) {
        $structured_data['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => (float) $product['rating'],
        );
        
        // Add ratingCount if available
        if ( ! empty( $product['rating_count'] ) && $product['rating_count'] > 0 ) {
            $structured_data['aggregateRating']['ratingCount'] = (int) $product['rating_count'];
        }
    }
    
    // Output JSON-LD structured data
    ?>
    <script type="application/ld+json">
    <?php echo wp_json_encode( $structured_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
    </script>
</article>