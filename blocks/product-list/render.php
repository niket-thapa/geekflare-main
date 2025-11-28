<?php
/**
 * Product List Block Template
 */
?>
<div id="products" class="flex flex-col gap-7.5">
    <h2 class="text-2xl text-gray-800 font-bold md:text-4xl leading-none md:leading-none m-0">
        <?php echo esc_html( $attributes['heading'] ); ?>
    </h2>
    <?php echo $content; ?>
</div>