<?php
/**
 * Summary Table Block Template
 *
 * @package Main
 * @var array $attributes Block attributes
 * @var string $content Block content
 */

if ( empty( $attributes['products'] ) ) {
    return;
}

$products_data = $attributes['products'];
$columns = isset( $attributes['columns'] ) ? $attributes['columns'] : array();
$last_column_config = isset( $attributes['lastColumnConfig'] ) ? $attributes['lastColumnConfig'] : array(
    'buttonText' => 'Try Now',
    'urlSource'  => 'affiliate',
    'customUrl'  => '',
);

// Field labels mapping
$field_labels = array(
    'tagline'        => 'Tagline',
    'pricing_summary' => 'Pricing',
    'our_rating'     => 'Rating',
    'has_free_plan'  => 'Free Plan',
    'has_free_trial' => 'Free Trial',
    'has_demo'       => 'Demo',
    'open_source'    => 'Open Source',
    'ai_powered'     => 'AI-Powered',
    'award'          => 'Award',
    'custom_note'    => 'Product Description',
);
?>

<div class="overflow-auto rounded-2xl md:rounded-3xl border border-gray-200 my-14 lg:mt-16 lg:mb-20 lg:-me-[21.5rem] lg:relative lg:z-30">
    <table class="product-compare-table border-none w-full max-w-full m-0">
        <thead>
            <tr>
                <!-- First Column: Product (Fixed) -->
                <th class="bg-gray-100 whitespace-nowrap text-left p-4 md:px-6 text-[0.625rem] md:text-xs leading-4 font-bold text-gray-500 uppercase tracking-[0.12em]">
                    <?php esc_html_e( 'Product', 'main' ); ?>
                </th>

                <!-- Dynamic Columns -->
                <?php foreach ( $columns as $column ) : 
                    // Handle custom columns
                    if ( isset( $column['type'] ) && 'custom' === $column['type'] ) {
                        $column_label = isset( $column['label'] ) ? $column['label'] : 'Custom Column';
                    } else {
                        // Field-based columns
                        $column_label = isset( $column['label'] ) ? $column['label'] : ( isset( $field_labels[ $column['field'] ] ) ? $field_labels[ $column['field'] ] : ( isset( $column['field'] ) ? $column['field'] : '' ) );
                    }
                    
                    // Get column width
                    $column_width = isset( $column['width'] ) && ! empty( $column['width'] ) ? $column['width'] : '';
                    $width_style = '';
                    if ( $column_width && 'auto' !== $column_width ) {
                        $width_style = ' style="width: ' . esc_attr( $column_width ) . '; min-width: ' . esc_attr( $column_width ) . ';"';
                    }
                ?>
                    <th class="bg-gray-100 whitespace-nowrap text-left p-4 md:px-6 text-[0.625rem] md:text-xs leading-4 font-bold text-gray-500 uppercase tracking-[0.12em]"<?php echo $width_style; ?>>
                        <?php echo esc_html( $column_label ); ?>
                    </th>
                <?php endforeach; ?>

                <!-- Last Column: Action (Fixed) -->
                <th class="bg-gray-100 whitespace-nowrap text-left p-4 md:px-6 text-[0.625rem] md:text-xs leading-4 font-bold text-gray-500 uppercase tracking-[0.12em]">
                    &nbsp;
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $products_data as $product_data ) : 
                $product = main_get_product_data( $product_data['id'] );
                if ( ! $product ) {
                    continue;
                }

                // Determine the URL for the last column
                $action_url = '#';
                if ( isset( $last_column_config['urlSource'] ) ) {
                    switch ( $last_column_config['urlSource'] ) {
                        case 'affiliate':
                            $action_url = ! empty( $product['affiliate_link'] ) ? $product['affiliate_link'] : ( ! empty( $product['website_url'] ) ? $product['website_url'] : $product['permalink'] );
                            break;
                        case 'website':
                            $action_url = ! empty( $product['website_url'] ) ? $product['website_url'] : $product['permalink'];
                            break;
                        case 'custom':
                            $action_url = ! empty( $last_column_config['customUrl'] ) ? $last_column_config['customUrl'] : '#';
                            break;
                        default:
                            $action_url = $product['permalink'];
                    }
                }

                $button_text = isset( $last_column_config['buttonText'] ) ? $last_column_config['buttonText'] : __( 'Try Now', 'main' );
            ?>
                <tr>
                    <!-- First Column: Product -->
                    <td class="p-0">
                        <div class="flex gap-2 items-center md:gap-3 px-4 py-[1.0625rem] md:px-6 md:py-5.5">
                            <?php if ( ! empty( $product['logo'] ) ) : ?>
                                <div class="product-logo-wrap w-6 [&_img]:w-full [&_img]:h-auto md:w-8 md:gap-3">
                                    <img class="m-0" src="<?php echo esc_url( $product['logo'] ); ?>" alt="<?php echo esc_attr( $product['name'] ); ?>" width="32" height="32" />
                                </div>
                            <?php endif; ?>
                            <div class="product-name-wrap flex-1 text-sm md:text-base font-semibold text-gray-800">
                                <?php echo esc_html( $product['name'] ); ?>
                            </div>
                        </div>
                    </td>

                    <!-- Dynamic Columns -->
                    <?php foreach ( $columns as $column ) : 
                        // Get column width
                        $column_width = isset( $column['width'] ) && ! empty( $column['width'] ) ? $column['width'] : '';
                        $width_style = '';
                        if ( $column_width && 'auto' !== $column_width ) {
                            $width_style = ' style="width: ' . esc_attr( $column_width ) . '; min-width: ' . esc_attr( $column_width ) . ';"';
                        }
                        
                        // Check if this is a custom column
                        if ( isset( $column['type'] ) && 'custom' === $column['type'] ) {
                            // Custom column - get value from product data
                            $value = '';
                            if ( isset( $product_data['customValues'] ) && isset( $product_data['customValues'][ $column['id'] ] ) ) {
                                $value = $product_data['customValues'][ $column['id'] ];
                            } elseif ( isset( $column['values'] ) && isset( $column['values'][ $product_data['id'] ] ) ) {
                                $value = $column['values'][ $product_data['id'] ];
                            }
                            ?>
                            <td class="p-0"<?php echo $width_style; ?>>
                                <div class="px-4 py-[1.0625rem] md:px-6 md:py-5.5 text-xs md:text-base font-medium text-gray-500 md:tracking-2p md:leading-6">
                                    <?php echo esc_html( $value ); ?>
                                </div>
                            </td>
                            <?php
                            continue;
                        }
                        
                        // Field-based column
                        $field = isset( $column['field'] ) ? $column['field'] : '';
                        $value = '';
                        
                        // Get value based on field type
                        switch ( $field ) {
                            case 'tagline':
                                $value = $product['tagline'];
                                break;
                            case 'pricing_summary':
                                $value = $product['pricing'];
                                break;
                            case 'our_rating':
                                $value = $product['rating'];
                                break;
                            case 'has_free_plan':
                                $value = $product['has_free_plan'];
                                break;
                            case 'has_free_trial':
                                $value = $product['has_free_trial'];
                                break;
                            case 'has_demo':
                                $value = $product['has_demo'];
                                break;
                            case 'open_source':
                                $value = $product['open_source'];
                                break;
                            case 'ai_powered':
                                $value = isset( $product['ai_powered'] ) ? $product['ai_powered'] : false;
                                break;
                            case 'award':
                                $value = $product['award'];
                                break;
                            case 'custom_note':
                                $value = isset( $product['custom_note'] ) ? $product['custom_note'] : '';
                                break;
                        }
                    ?>
                        <td class="p-0"<?php echo $width_style; ?>>
                            <div class="px-4 py-[1.0625rem] md:px-6 md:py-5.5">
                                <?php
                                // Render based on field type
                                if ( in_array( $field, array( 'has_free_plan', 'has_free_trial', 'has_demo', 'open_source', 'ai_powered' ), true ) ) {
                                    // Boolean fields - show checkmark or X
                                    if ( $value ) {
                                        ?>
                                        <div class="free-plan-wrap [&_img]:w-5 [&_img]:h-auto md:[&_img]:w-6 md:[&_img]:ms-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.999 3C14.3854 3.00252 16.6739 3.95124 18.3613 5.63867C19.9433 7.22065 20.8758 9.33081 20.9883 11.5547L21 12.001C20.9998 13.7806 20.4721 15.5203 19.4834 17C18.4945 18.4799 17.0887 19.6332 15.4443 20.3145C13.7998 20.9956 11.99 21.1744 10.2441 20.8271C8.49833 20.4799 6.89441 19.6229 5.63574 18.3643C4.37708 17.1056 3.52012 15.5017 3.17285 13.7559C2.82558 12.01 3.00436 10.2002 3.68555 8.55566C4.36675 6.91127 5.52005 5.50547 7 4.5166C8.47973 3.52792 10.2194 3.00019 11.999 3ZM15.75 8.25C15.553 8.25 15.3578 8.28886 15.1758 8.36426C14.9938 8.43966 14.8287 8.55017 14.6895 8.68945L10.5 12.8779L9.31152 11.6895H9.31055C9.02916 11.4081 8.64793 11.25 8.25 11.25C7.85207 11.25 7.47084 11.4081 7.18945 11.6895C6.90807 11.9708 6.75 12.3521 6.75 12.75L6.75684 12.8984C6.79097 13.2419 6.94328 13.5644 7.18945 13.8105L9.43848 16.0605C9.57779 16.2 9.74369 16.3112 9.92578 16.3867C10.1078 16.4621 10.303 16.501 10.5 16.501C10.697 16.501 10.8922 16.4621 11.0742 16.3867C11.2563 16.3112 11.4222 16.2 11.5615 16.0605L11.5605 16.0596L16.8105 10.8105C16.9498 10.6713 17.0603 10.5062 17.1357 10.3242C17.2111 10.1422 17.25 9.94704 17.25 9.75C17.25 9.55296 17.2111 9.35782 17.1357 9.17578C17.0603 8.99382 16.9498 8.82873 16.8105 8.68945C16.6713 8.55018 16.5062 8.43966 16.3242 8.36426C16.1422 8.28885 15.947 8.25 15.75 8.25Z" fill="#039855" stroke="#039855" stroke-width="1.5"/>
                                            </svg>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="free-plan-wrap [&_img]:w-5 [&_img]:h-auto md:[&_img]:w-6 md:[&_img]:ms-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.999 3C14.3854 3.00252 16.6739 3.95124 18.3613 5.63867C19.9433 7.22065 20.8758 9.33081 20.9883 11.5547L21 12.001C20.9998 13.7806 20.4721 15.5203 19.4834 17C18.4945 18.4799 17.0887 19.6332 15.4443 20.3145C13.7998 20.9956 11.99 21.1744 10.2441 20.8271C8.49833 20.4799 6.89441 19.6229 5.63574 18.3643C4.37708 17.1056 3.52012 15.5017 3.17285 13.7559C2.82558 12.01 3.00436 10.2002 3.68555 8.55566C4.36675 6.91127 5.52005 5.50547 7 4.5166C8.47973 3.52792 10.2194 3.00019 11.999 3ZM15 7.5C14.803 7.5 14.6078 7.53886 14.4258 7.61426C14.2438 7.68966 14.0787 7.80017 13.9395 7.93945H13.9385L12 9.87793L10.0615 7.93945H10.0605C9.77916 7.65807 9.39793 7.5 9 7.5C8.60207 7.5 8.22084 7.65807 7.93945 7.93945C7.65807 8.22084 7.5 8.60207 7.5 9L7.50684 9.14844C7.54097 9.49187 7.69327 9.81437 7.93945 10.0605V10.0615L9.87793 12L7.93945 13.9385V13.9395C7.80018 14.0787 7.68966 14.2438 7.61426 14.4258C7.53885 14.6078 7.5 14.803 7.5 15C7.5 15.197 7.53885 15.3922 7.61426 15.5742C7.68966 15.7562 7.80018 15.9213 7.93945 16.0605C8.07874 16.1998 8.24383 16.3103 8.42578 16.3857C8.60781 16.4611 8.80296 16.5 9 16.5C9.19704 16.5 9.39218 16.4611 9.57422 16.3857C9.75618 16.3103 9.92126 16.1998 10.0605 16.0605H10.0615L12 14.1211L13.9385 16.0605H13.9395C14.0787 16.1998 14.2438 16.3103 14.4258 16.3857C14.6078 16.4611 14.803 16.5 15 16.5C15.197 16.5 15.3922 16.4611 15.5742 16.3857C15.7562 16.3103 15.9213 16.1998 16.0605 16.0605C16.1998 15.9213 16.3103 15.7562 16.3857 15.5742C16.4611 15.3922 16.5 15.197 16.5 15C16.5 14.803 16.4611 14.6078 16.3857 14.4258C16.3103 14.2438 16.1998 14.0787 16.0605 13.9395V13.9385L14.1211 12L16.0605 10.0615V10.0605C16.1998 9.92127 16.3103 9.75618 16.3857 9.57422C16.4611 9.39218 16.5 9.19704 16.5 9C16.5 8.80296 16.4611 8.60782 16.3857 8.42578C16.3103 8.24382 16.1998 8.07873 16.0605 7.93945C15.9213 7.80018 15.7562 7.68966 15.5742 7.61426C15.3922 7.53885 15.197 7.5 15 7.5Z" fill="#D92D20" stroke="#D92D20" stroke-width="1.5"/>
                                            </svg>
                                        </div>
                                        <?php
                                    }
                                } elseif ( 'our_rating' === $field && ! empty( $value ) ) {
                                    // Rating field - show stars
                                    echo main_get_rating_stars( floatval( $value ) );
                                } elseif ( 'pricing_summary' === $field ) {
                                    // Pricing field - special formatting
                                    ?>
                                    <div class="pricing-wrap flex flex-col">
                                        <div class="text-sm md:text-base font-semibold text-gray-800">
                                            <?php echo esc_html( $value ); ?>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    // Text fields
                                    ?>
                                    <div class="text-xs md:text-base font-medium text-gray-500 md:tracking-2p md:leading-6">
                                        <?php echo esc_html( $value ); ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </td>
                    <?php endforeach; ?>

                    <!-- Last Column: Action -->
                    <td class="p-0">
                        <div class="flex flex-col px-4 py-[1.0625rem] md:px-6 md:py-5.5">
                            <a href="<?php echo esc_url( $action_url ); ?>" 
                               class="btn btn--primary rounded-full whitespace-nowrap md:py-3 md:px-4.5"
                               target="_blank"
                               rel="nofollow noopener">
                                <?php echo esc_html( $button_text ); ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon" width="16" height="16" fill="none" viewBox="0 0 16 16">
                                    <path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 3.333 10.667 8 6 12.666"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
