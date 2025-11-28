<?php
/**
 * Honorable Mentions Block Template
 */

$products = $attributes['products'] ?? [];

if (empty($products)) {
    return;
}

// Sort by rank
usort($products, function($a, $b) {
    return ($a['rank'] ?? 0) - ($b['rank'] ?? 0);
});
?>

<section id="honorable-mentions" class="flex flex-col gap-7 md:gap-8 py-8 md:py-12 lg:py-20">
    <h2 class="text-2xl md:text-4xl font-bold leading-none text-gray-800">
        <?php echo esc_html($attributes['heading']); ?>
    </h2>

    <div class="flex flex-col md:flex-row gap-4">
        <?php foreach ($products as $product_data) : 
            $product = main_get_product_data($product_data['id']);
            if (!$product) continue;
        ?>
            <article class="flex flex-col gap-6 p-5 bg-white border border-gray-200 rounded-2xl flex-1">
                <!-- Header with Logo and Badge -->
                <div class="flex justify-between items-center gap-4">
                    <?php if ($product['logo']) : ?>
                        <div class="w-8 h-8 [&_img]:w-full [&_img]:h-auto">
                            <img src="<?php echo esc_url($product['logo']); ?>" 
                                 alt="<?php echo esc_attr($product['name']); ?>" 
                                 width="32" height="32" loading="lazy" />
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-center items-center py-1 px-6 bg-gray-100 rounded-full">
                        <span class="text-sm md:text-base font-semibold leading-5 md:leading-6 text-gray-800">
                            #<?php echo esc_html($product_data['rank']); ?>
                        </span>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="flex flex-col gap-1.5 md:gap-2">
                    <h3 class="text-base md:text-xl font-semibold leading-6 md:leading-7 text-gray-800">
                        <?php echo esc_html($product['name']); ?>
                    </h3>
                    <?php if ($product['tagline']) : ?>
                        <div class="text-sm md:text-base font-medium leading-5 md:leading-6 tracking-2p md:tracking-1p text-gray-500 line-clamp-2">
                            <?php echo esc_html($product['tagline']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>