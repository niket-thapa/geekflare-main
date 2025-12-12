<?php
/**
 * Score Breakdown Block Template
 *
 * @var array    $attributes Block attributes.
 * @var string   $content Block content.
 * @var WP_Block $block Block instance.
 */

$product_id = $attributes['productId'] ?? 0;
$heading = $attributes['heading'] ?? __('Product Score Breakdown', 'main');

// Try to get product ID from parent product-item block context
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

// Get score breakdown from product meta
$score_breakdown_json = get_post_meta( $product_id, 'score_breakdown', true );
$score_breakdown      = json_decode( $score_breakdown_json, true );

if ( ! is_array( $score_breakdown ) || empty( $score_breakdown ) ) {
    return;
}

// Use score breakdown as criteria
$criteria = $score_breakdown;

// Calculate average
$total = 0;
$count = count($criteria);
foreach ($criteria as $criterion) {
    $total += floatval($criterion['score']);
}
$average = $count > 0 ? round($total / $count, 1) : 0;

// Helper function to get color based on score
if ( ! function_exists( 'get_score_color' ) ) {
    function get_score_color($score) {
        if ($score >= 4) return 'success'; // Green - >= 4
        if ($score >= 2) return 'warning'; // Yellow - >= 2 and < 4
        return 'error'; // Red - < 2
    }
}
?>

<div class="product-score-breakdown flex flex-col items-start p-0 gap-4 md:gap-5 flex-none self-stretch">
    <h3 class="text-sm md:text-base font-semibold leading-5 md:leading-6 flex items-center text-gray-800 flex-none m-0">
        <?php echo esc_html($heading); ?>
    </h3>

    <?php // Rating Cards Grid ?>
    <div class="product-score-cards grid grid-cols-2 lg:grid-cols-4 p-0 gap-3 md:gap-4 flex-none self-stretch">
        <?php foreach ($criteria as $criterion) :
            $score = floatval($criterion['score']);
            $full_bars = floor($score);
            $half_bar = ($score - $full_bars) >= 0.5;
            $empty_bars = 5 - $full_bars - ($half_bar ? 1 : 0);
            $color = get_score_color($score);
        ?>
            <div class="product-score-card box-border flex flex-col items-start p-4 gap-4 w-full md:w-auto flex-1 bg-white border border-gray-200 rounded-xl">
                <div class="flex flex-col items-start p-0 gap-1 flex-none self-stretch">
                    <div class="text-base md:text-lg font-bold leading-[110%] md:leading-[1.25rem] flex items-center text-gray-800 flex-none self-stretch">
                        <?php echo esc_html($score); ?>/5
                    </div>
                    <div class="text-xs md:text-base font-medium leading-4 md:leading-6 tracking-2p flex items-center text-gray-500 flex-none self-stretch">
                        <?php echo esc_html($criterion['name']); ?>
                    </div>
                </div>
                <div class="product-score-bar flex flex-row items-center p-0 gap-0.5 flex-none self-stretch mt-auto">
                    <?php
                    // Full bars
                    for ($i = 0; $i < $full_bars; $i++) {
                        echo '<div class="h-2 flex-1 bg-' . $color . '-600 rounded-full"></div>';
                    }
                    // Half bar
                    if ($half_bar) {
                        echo '<div class="product-score-bar__half h-2 flex-1 relative">
                            <div class="absolute inset-0 bg-' . $color . '-50 rounded-full"></div>
                            <div class="absolute inset-0 left-0 w-1/2 bg-' . $color . '-600 rounded-l-full"></div>
                        </div>';
                    }
                    // Empty bars
                    for ($i = 0; $i < $empty_bars; $i++) {
                        echo '<div class="h-2 flex-1 bg-' . $color . '-50 rounded-full"></div>';
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php // Geekflare Rating & Button ?>
    <div class="product-score-footer flex flex-col md:flex-row md:justify-between md:items-center p-0 gap-4 md:gap-5 flex-none self-stretch">
        <?php // Geekflare Rating Badge ?>
        <div class="geekflare-rating-badge box-border flex flex-row items-end py-1.5 px-3.5 md:px-2.5 gap-3 w-full md:w-auto bg-rating-50 border border-rating-border rounded-full flex-none">
            <div class="flex flex-row justify-between items-center p-0 gap-2 flex-none grow-1 md:grow-0 w-full md:w-auto">
                <span class="text-sm font-medium leading-5 tracking-2p text-gray-800 flex-none">
                    <?php esc_html_e('Geekflare rating:', 'main'); ?>
                </span>
                <div class="flex flex-row items-center p-0 gap-2 flex-none">
                    <?php echo main_get_rating_stars($average); ?>
                    <span class="text-sm md:text-base font-bold leading-[110%] md:leading-[1.125rem] flex items-end text-gray-800 flex-none">
                        <?php echo esc_html($average); ?>/5
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>