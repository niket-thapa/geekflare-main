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
$container_classes = 'info_box_block flex flex-col justify-center items-start p-5 gap-2 md:gap-3 border border-gray-200 rounded-2xl mb-4 md:mb-6';
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

// Get icon color class based on style
$icon_colors = array(
    'default' => 'text-gray-800',
    'success' => 'text-success-300',
    'warning' => 'text-warning-300',
    'pricing' => 'text-pricing-300',
);
$icon_color = $icon_colors[$style] ?? $icon_colors['default'];

// Get default icon SVG based on style
$default_icons = array(
    'default' => '<svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
    'success' => '<svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
    'warning' => '<svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>',
    'pricing' => '<svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>',
);

$default_icon = $default_icons[$style] ?? $default_icons['default'];
// Add the color class to the SVG
$default_icon = str_replace('class="w-5 h-5 flex-shrink-0"', 'class="w-5 h-5 flex-shrink-0 ' . esc_attr($icon_color) . '"', $default_icon);

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
