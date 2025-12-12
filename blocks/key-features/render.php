<?php
/**
 * Key Features Block Template
 */

$features = $attributes['features'] ?? [''];
$heading = $attributes['heading'] ?? __('Key Features', 'main');
$features = array_filter($features);

if (empty($features)) {
    return;
}
?>

<div class="key-features flex flex-col items-start p-0 gap-4 md:gap-5 flex-none self-stretch">
    <h3 class="text-base font-semibold leading-6 flex items-center text-gray-800 flex-none m-0">
        <?php echo esc_html($heading); ?>
    </h3>

    <div class="key-features-list flex flex-row flex-wrap items-start content-start p-0 gap-2 flex-none self-stretch">
        <?php foreach ($features as $feature) : ?>
            <div class="key-feature-badge box-border flex flex-row items-center py-1.5 px-2.5 md:px-3 gap-2 bg-gray-50 border border-gray-200 rounded-full max-w-full">
                <span class="text-sm md:text-base font-medium leading-4.5 md:leading-5.5 tracking-2p text-gray-800">
                    <?php echo esc_html($feature); ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>