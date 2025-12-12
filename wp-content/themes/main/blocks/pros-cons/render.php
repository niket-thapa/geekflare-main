<?php
/**
 * Pros & Cons Block Template
 */

$pros = $attributes['pros'] ?? [''];
$cons = $attributes['cons'] ?? [''];
$mainHeading = $attributes['mainHeading'] ?? __('Pros & Cons', 'main');
$prosHeading = $attributes['prosHeading'] ?? __('PROS', 'main');
$consHeading = $attributes['consHeading'] ?? __('CONS', 'main');

// Filter empty items
$pros = array_filter($pros);
$cons = array_filter($cons);

if (empty($pros) && empty($cons)) {
    return;
}
?>

<div class="pros-cons flex flex-col items-start p-0 gap-4 md:gap-5 flex-none self-stretch">
    <h3 class="text-base font-semibold leading-6 flex items-center text-gray-800 flex-none m-0">
        <?php echo esc_html($mainHeading); ?>
    </h3>

    <div class="pros-cons-cards flex flex-col md:flex-row p-0 gap-3 md:gap-4 flex-none self-stretch">
        <?php // PROS Card ?>
        <?php if (!empty($pros)) : ?>
            <div class="pros-card box-border flex flex-col items-start p-4 gap-3 flex-1 bg-white border border-gray-200 rounded-xl">
                <h4 class="text-xs font-bold leading-4 tracking-[0.1em] uppercase text-success-600 flex-none m-0">
                    <?php echo esc_html($prosHeading); ?>
                </h4>
                
                <div class="pros-list flex flex-col items-start p-0 gap-2.5 md:gap-2 flex-none self-stretch">
                    <?php foreach ($pros as $pro) : ?>
                        <div class="pros-item flex flex-row items-start p-0 gap-1.5 flex-none self-stretch">
                            <svg class="w-5 h-5 flex-none" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
                                <path fill="currentColor" class="text-success-600" d="M10 1.875A8.125 8.125 0 1 0 18.125 10 8.133 8.133 0 0 0 10 1.875m3.567 6.692-4.375 4.375a.626.626 0 0 1-.884 0l-1.875-1.875a.625.625 0 1 1 .884-.884l1.433 1.433 3.933-3.933a.626.626 0 0 1 .884.884"/>
                            </svg>
                            <span class="text-sm md:text-base font-medium leading-5 md:leading-6 tracking-2p text-gray-900 flex-1">
                                <?php echo esc_html($pro); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php // CONS Card ?>
        <?php if (!empty($cons)) : ?>
            <div class="cons-card box-border flex flex-col items-start p-4 gap-3 flex-1 bg-white border border-gray-200 rounded-xl">
                <h4 class="text-xs font-bold leading-4 tracking-[0.1em] uppercase text-error-600 flex-none m-0">
                    <?php echo esc_html($consHeading); ?>
                </h4>
                
                <div class="cons-list flex flex-col items-start p-0 gap-2.5 md:gap-2 flex-none self-stretch">
                    <?php foreach ($cons as $con) : ?>
                        <div class="cons-item flex flex-row items-start p-0 gap-1.5 flex-none self-stretch">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
                                <path fill="currentColor" class="text-error-600" d="M10 1.875A8.125 8.125 0 1 0 18.125 10 8.133 8.133 0 0 0 10 1.875m2.942 10.183a.624.624 0 1 1-.884.884L10 10.884l-2.058 2.058a.624.624 0 1 1-.884-.884L9.116 10 7.058 7.942a.625.625 0 0 1 .884-.884L10 9.116l2.058-2.058a.626.626 0 0 1 .884.884L10.884 10z"/>
                            </svg>
                            <span class="text-sm md:text-base font-medium leading-5 md:leading-6 tracking-2p text-gray-900 flex-1">
                                <?php echo esc_html($con); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>