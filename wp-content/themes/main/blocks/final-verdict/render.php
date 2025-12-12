<?php
/**
 * Final Verdict Block Template
 */

$heading = $attributes['heading'] ?? __('Final Verdict: Who is Monday.com for?', 'main');
$perfectForHeading = $attributes['perfectForHeading'] ?? __('Perfect for:', 'main');
$notIdealForHeading = $attributes['notIdealForHeading'] ?? __('Not ideal for:', 'main');
$perfectFor = $attributes['perfectFor'] ?? [''];
$notIdealFor = $attributes['notIdealFor'] ?? [''];

// Filter empty items
$perfectFor = array_filter($perfectFor);
$notIdealFor = array_filter($notIdealFor);

if (empty($perfectFor) && empty($notIdealFor)) {
    return;
}
?>

<div class="final_verdict flex flex-col gap-4 md:gap-6 lg:gap-8 [&_p]:m-0">
    <h2 class="text-2xl md:text-4xl font-bold leading-none md:leading-none text-gray-800 m-0">
        <?php echo esc_html($heading); ?>
    </h2>

    <div class="flex flex-col gap-3 md:gap-4">
        <?php // Perfect for Section ?>
        <?php if (!empty($perfectFor)) : ?>
            <div class="flex flex-col justify-center items-start p-5 gap-3 bg-[#F6FEF9] border border-[#D1FADF] rounded-2xl">
                <h4 class="text-base font-semibold leading-6 text-gray-800 flex-none m-0">
                    <?php echo esc_html($perfectForHeading); ?>
                </h4>

                <?php foreach ($perfectFor as $item) : ?>
                    <div class="flex flex-row items-center gap-1.5 self-stretch">
                        <svg class="w-5 h-5 flex-none" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
                            <path fill="currentColor" class="text-success-600" d="M10 1.875A8.125 8.125 0 1 0 18.125 10 8.133 8.133 0 0 0 10 1.875m3.567 6.692-4.375 4.375a.626.626 0 0 1-.884 0l-1.875-1.875a.625.625 0 1 1 .884-.884l1.433 1.433 3.933-3.933a.626.626 0 0 1 .884.884" />
                        </svg>
                        <p class="text-sm font-medium leading-5 tracking-2p text-gray-800 flex-1">
                            <?php echo esc_html($item); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php // Not ideal for Section ?>
        <?php if (!empty($notIdealFor)) : ?>
            <div class="flex flex-col justify-center items-start p-5 gap-3 bg-[#FEF3F2] border border-[#FEE4E2] rounded-2xl">
                <h4 class="text-base font-semibold leading-6 text-gray-800 flex-none m-0">
                    <?php echo esc_html($notIdealForHeading); ?>
                </h4>

                <?php foreach ($notIdealFor as $item) : ?>
                    <div class="flex flex-row items-center gap-1.5 self-stretch">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
                            <path fill="currentColor" class="text-error-600" d="M10 1.875A8.125 8.125 0 1 0 18.125 10 8.133 8.133 0 0 0 10 1.875m2.942 10.183a.624.624 0 1 1-.884.884L10 10.884l-2.058 2.058a.624.624 0 1 1-.884-.884L9.116 10 7.058 7.942a.625.625 0 0 1 .884-.884L10 9.116l2.058-2.058a.626.626 0 0 1 .884.884L10.884 10z" />
                        </svg>
                        <p class="text-sm font-medium leading-5 tracking-2p text-gray-800 flex-1">
                            <?php echo esc_html($item); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

