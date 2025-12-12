<?php
/**
 * Awards Block Template
 *
 * @var array    $attributes Block attributes.
 * @var string   $content Block content.
 * @var WP_Block $block Block instance.
 */

$heading = $attributes['heading'] ?? __( 'Awards', 'main' );
$product_id = $attributes['productId'] ?? 0;

// Try to get product ID from parent product-item block context
// WordPress automatically passes parent block attributes as context
if ( ! $product_id && isset( $block->context['main/product-item/productId'] ) ) {
    $product_id = (int) $block->context['main/product-item/productId'];
}

// Also check direct context key
if ( ! $product_id && isset( $block->context['productId'] ) ) {
    $product_id = (int) $block->context['productId'];
}

if ( ! $product_id ) {
    return;
}

$product = main_get_product_data( $product_id );
if ( ! $product ) {
    return;
}

$award_key = $product['award'] ?? '';
if ( empty( $award_key ) ) {
    return;
}

$awards = main_get_product_awards();
$award_data = $awards[ $award_key ] ?? null;

if ( ! $award_data || empty( $award_data['image'] ) ) {
    return;
}

$award_image_url = $award_data['image'];
$award_label = $award_data['label'] ?? '';
?>

<div class="awards flex flex-col gap-5">
    <h3 class="text-base font-semibold leading-6 text-gray-800 m-0">
        <?php echo esc_html( $heading ); ?>
    </h3>

    <div class="awards-list flex flex-wrap gap-6">
        <figure class="award-item w-[5.8125rem] m-0">
            <img
                src="<?php echo esc_url( $award_image_url ); ?>"
                alt="<?php echo esc_attr( $award_label ?: __( 'Award', 'main' ) ); ?>"
                width="93"
                height="100"
                class="w-full h-auto object-contain m-0"
                loading="lazy" />
            <figcaption class="sr-only">
                <?php echo esc_html( $award_label ?: __( 'Award', 'main' ) ); ?>
            </figcaption>
        </figure>
    </div>
</div>

