<?php
get_header();
if (is_singular('product')) {
    if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {
        get_template_part('view/woocommerce/single-product-reviews');
    }
} else {
    do_action('pooshe_page_content');
}

get_footer();
