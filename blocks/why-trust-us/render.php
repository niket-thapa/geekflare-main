<?php
/**
 * Why Trust Us Block Template
 */

$heading = $attributes['heading'] ?? 'Why Trust Our Guide';
$description = $attributes['description'] ?? '';
$data_points = $attributes['dataPoints'] ?? [];
?>

<div class="why_trust_us flex flex-col gap-2 md:gap-4 lg:gap-4.5">
    <h2 class="text-base text-gray-800 font-bold md:text-lg md:leading-5 m-0">
        <?php echo esc_html($heading); ?>
    </h2>
    
    <?php if ($description) : ?>
        <div class="text-sm text-gray-700 md:text-base md:tracking-2p font-medium">
            <?php echo wp_kses_post($description); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($data_points)) : ?>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4 lg:gap-4 pt-4 md:pt-1">
            <?php foreach ($data_points as $point) : ?>
                <div class="rounded-2xl border border-gray-200 bg-white px-3 py-2.5 md:py-3.5 md:px-5 flex flex-col justify-center gap-2 md:gap-2.5">
                    <span class="text-2xl md:text-3xl leading-none font-bold text-transparent bg-gradient-to-r from-[#FF8A00] to-primary bg-clip-text">
                        <?php echo esc_html($point['value']); ?>
                    </span>
                    <span class="text-xs md:text-base tracking-2p font-medium text-gray-500">
                        <?php echo esc_html($point['label']); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>