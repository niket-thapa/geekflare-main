<?php
/**
 * Product List Block Template
 *
 * @var array    $attributes Block attributes.
 * @var string   $content Block content.
 * @var WP_Block $block Block instance.
 */

$show_product_number = isset( $attributes['showProductNumber'] ) ? (bool) $attributes['showProductNumber'] : true;
$wrapper_class = 'flex flex-col gap-7.5 pb-8 md:pb-12 lg:pb-20';
if ( ! $show_product_number ) {
    $wrapper_class .= ' hide-product-numbers';
}
?>
<div id="products" class="<?php echo esc_attr( $wrapper_class ); ?>">
    <?php echo $content; ?>
</div>