<?php
/**
 * Info Box Block Template
 *
 * @var array    $attributes Block attributes.
 * @var string   $content Block content.
 * @var WP_Block $block Block instance.
 */

$style = $attributes['style'] ?? 'default';
$icon_type = $attributes['iconType'] ?? 'none';
$icon_url = $attributes['iconUrl'] ?? '';
$icon_id = $attributes['iconId'] ?? 0;
$heading = $attributes['heading'] ?? '';
$content = $attributes['content'] ?? '';

// Get container classes based on style
$container_classes = 'flex flex-col justify-center items-start p-5 gap-2 md:gap-3 border border-gray-200 rounded-2xl';
$style_bg_classes = array(
    'default' => 'bg-white',
    'success' => 'bg-success-100',
    'warning' => 'bg-warning-100',
    'pricing' => 'bg-pricing-100',
);
$container_classes .= ' ' . ($style_bg_classes[$style] ?? $style_bg_classes['default']);

// Get heading text color based on style
$heading_colors = array(
    'default' => 'text-gray-800',
    'success' => 'text-success-300',
    'warning' => 'text-warning-300',
    'pricing' => 'text-pricing-300',
);
$heading_color = $heading_colors[$style] ?? $heading_colors['default'];

// Get icon URL from attachment if icon_id is set but URL is not
if (empty($icon_url) && $icon_id > 0) {
    $icon_url = wp_get_attachment_image_url($icon_id, 'full');
}

// Default shield icon SVG
$default_icon = '<svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
    <path stroke="#252B37" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 1.667 2.5 5v5c0 4.584 3.25 8.875 7.5 10 4.25-1.125 7.5-5.416 7.5-10V5z" />
</svg>';

// Render icon based on type
$icon_html = '';
if ($icon_type === 'image' && !empty($icon_url)) {
    $icon_html = '<img src="' . esc_url($icon_url) . '" alt="' . esc_attr($heading ?: __('Icon', 'main')) . '" class="w-5 h-5 flex-shrink-0" />';
} else {
    $icon_html = $default_icon;
}
?>

<div class="<?php echo esc_attr($container_classes); ?>">
    <div class="flex items-center gap-2">
        <?php echo $icon_html; ?>
        
        <?php if (!empty($heading)) : ?>
            <h4 class="text-base font-semibold m-0 leading-6 <?php echo esc_attr($heading_color); ?>">
                <?php echo wp_kses_post($heading); ?>
            </h4>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($content)) : ?>
        <div class="text-sm font-medium leading-5 tracking-2p text-gray-600 [&_p]:m-0">
            <?php echo wp_kses_post(wpautop($content)); ?>
        </div>
    <?php endif; ?>
</div>
