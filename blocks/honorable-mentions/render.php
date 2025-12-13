<?php
/**
 * Honorable Mentions Block Template
 */

$products = $attributes['products'] ?? [];
$heading = $attributes['heading'] ?? 'Honorable Mentions';
$design_type = $attributes['designType'] ?? 'honorable-mentions';

if (empty($products)) {
    return;
}

// Sort by rank
usort($products, function($a, $b) {
    return ($a['rank'] ?? 0) - ($b['rank'] ?? 0);
});
?>

<section id="honorable-mentions" class="flex flex-col gap-7 md:gap-8 pb-4 md:pb-6">
    <div class="flex flex-col md:grid md:grid-cols-2 gap-4">
        <?php foreach ($products as $product_data) : 
            $product = main_get_product_data($product_data['id']);
            if (!$product) continue;
            
            $rank = isset($product_data['rank']) ? $product_data['rank'] : '';
            $read_review_url = isset($product_data['readReviewUrl']) ? $product_data['readReviewUrl'] : '';
            $read_review_text = isset($product_data['readReviewText']) ? $product_data['readReviewText'] : 'Read Review';
            $custom_note = !empty($product['custom_note']) ? $product['custom_note'] : (!empty($product['tagline']) ? $product['tagline'] : '');
            $rating = !empty($product['rating']) ? (float) $product['rating'] : null;
            
            // For top alternatives, use permalink if readReviewUrl is empty
            if ($design_type === 'top-alternatives' && empty($read_review_url)) {
                $read_review_url = $product['permalink'];
            }
        ?>
            <article class="honorable-mention-item flex flex-col gap-6 p-5 bg-white border border-gray-200 rounded-2xl flex-1">
                <?php // Header with Logo and Badge ?>
                <div class="flex justify-between items-center gap-4">
                    <?php if (!empty($product['logo']) || !empty($product['logo_attachment_id'])) : ?>
                        <div class="w-8 h-8 [&_img]:w-full [&_img]:h-auto">
                            <?php 
                            if (!empty($product['logo_attachment_id']) && $product['logo_attachment_id'] > 0) {
                                // Use WordPress attachment image function
                                echo wp_get_attachment_image(
                                    $product['logo_attachment_id'],
                                    array(32, 32),
                                    false,
                                    array(
                                        'alt' => esc_attr($product['name']),
                                        'class' => 'w-full h-auto m-0',
                                    )
                                );
                            } elseif (!empty($product['logo'])) {
                                // Fallback to URL if no attachment ID
                                ?>
                                <img src="<?php echo esc_url($product['logo']); ?>" 
                                     alt="<?php echo esc_attr($product['name']); ?>" 
                                     width="32" height="32" loading="lazy" />
                                <?php
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($design_type === 'top-alternatives' && $rating && $rating > 0) : ?>
                        <?php // Rating Badge for Top Alternatives ?>
                        <div class="flex justify-center items-center py-[0.1875rem] px-2 bg-[#FFFAEB] rounded-full gap-1">
                            <svg
                                class="w-3.5 md:w-4 h-auto"
                                xmlns="http://www.w3.org/2000/svg"
                                width="14"
                                height="14"
                                fill="none"
                                viewBox="0 0 14 14">
                                <path
                                    fill="#f79009"
                                    d="m12.813 6.28-2.46 2.124.749 3.176a.897.897 0 0 1-1.34.975L7 10.855l-2.763 1.7A.897.897 0 0 1 2.9 11.58l.752-3.176-2.46-2.123a.9.9 0 0 1 .51-1.578l3.226-.26L6.17 1.43a.895.895 0 0 1 1.656 0L9.07 4.443l3.226.26a.9.9 0 0 1 .513 1.578z" />
                            </svg>
                            <span class="text-xs md:text-sm font-semibold text-gray-800 -mb-0.5"><?php echo esc_html(number_format($rating, 1)); ?></span>
                        </div>
                    <?php else : ?>
                        <?php // Number Badge for Honorable Mentions or Top Alternatives without rating ?>
                        <?php if ($rank) : ?>
                            <div class="flex justify-center items-center py-1 px-3.5 bg-gray-100 rounded-full">
                                <span class="text-sm md:text-base font-semibold leading-5 md:leading-6 text-gray-800">
                                    #<?php echo esc_html($rank); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php // Product Info ?>
                <div class="flex flex-col gap-1.5 md:gap-2">
                    <h3 class="text-base md:text-xl font-semibold leading-6 md:leading-7 text-gray-800 m-0">
                        <?php echo esc_html($product['name']); ?>
                    </h3>
                    <?php if (!empty($custom_note)) : ?>
                        <div class="text-sm md:text-base font-medium leading-5 md:leading-6 tracking-2p md:tracking-1p text-gray-500 line-clamp-2 honorable-mention-item__text">
                            <?php echo esc_html($custom_note); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($design_type === 'top-alternatives' && !empty($read_review_text) && !empty($read_review_url)) : ?>
                        <a
                            href="<?php echo esc_url($read_review_url); ?>"
                            class="flex items-center gap-1 text-sm font-semibold leading-5 text-eva-prime-600 hover:text-primary transition-colors">
                            <?php echo esc_html($read_review_text); ?>
                            <svg
                                class="w-4 h-4 flex-shrink-0"
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                fill="none"
                                viewBox="0 0 16 16">
                                <path
                                    stroke="#e84300"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M3.333 8h9.333M8.667 12l4-4M8.667 4l4 4" />
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
