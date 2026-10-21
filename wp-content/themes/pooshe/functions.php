<?php

if (version_compare(phpversion(), '7.4', '<')) {
    require get_template_directory() . '/includes/back-compat.php';

    return;
}

if (function_exists("WC")) {
    require_once __DIR__ . '/includes/woocommerce.php';
}

if (!function_exists('pooshe_setup')) :
    function pooshe_setup()
    {
        load_theme_textdomain('pooshe', get_template_directory() . '/languages');


        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('woocommerce');

        register_nav_menus([
            'main' => 'جایگاه اصلی',
        ]);
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
        ]);
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('wp-block-styles');
        add_theme_support('align-wide');
        add_theme_support('editor-styles');
        add_editor_style('style-editor.css');
        add_theme_support('responsive-embeds');
        update_option('yith_system_info', ['system_info' => [], 'errors' => false]);
        update_option('dokan_colors', [
            'btn_text' => '',
            'btn_primary' => '',
            'btn_primary_border' => '',
            'btn_hover_text' => '',
            'btn_hover' => '',
            'btn_hover_border' => '',
            'dash_active_link' => '',
            'dash_nav_text' => '',
            'dash_nav_bg' => '',
            'dash_nav_border' => '',
        ]);
    }
endif;
add_action('after_setup_theme', 'pooshe_setup');

function pooshe_widgets_init()
{
    $sidebars = [
        [
            'name' => 'وبلاگ',
            'id' => 'sidebar-blog',
            'desc' => 'جایگاه ابزارک های صفحه نوشته وبلاگ',
        ],
        [
            'name' => 'فروشگاه',
            'id' => 'sidebar-shop',
            'desc' => 'جایگاه ابزارک های صفحه فروشگاه',
        ],
        [
            'name' => 'صفحه محصول',
            'id' => 'sidebar-product',
            'desc' => 'جایگاه ابزارک های صفحه محصول',
        ],
        [
            'name' => 'حساب کاربری',
            'id' => 'sidebar-myaccount',
            'desc' => 'جایگاه ابزارک های حساب کاربری',
        ],
        [
            'name' => 'برگه ها',
            'id' => 'sidebar-page',
            'desc' => 'جایگاه ابزارک های صفحات آرشیو، برگه و...',
        ],
        [
            'name' => 'پابرگ 1',
            'id' => 'footer-1',
            'desc' => 'جایگاه ابزارک پابرگ 1',
        ],
        [
            'name' => 'پابرگ 2',
            'id' => 'footer-2',
            'desc' => 'جایگاه ابزارک پابرگ 2',
        ],
        [
            'name' => 'پابرگ 3',
            'id' => 'footer-3',
            'desc' => 'جایگاه ابزارک پابرگ 3',
        ],
        [
            'name' => 'پابرگ 4',
            'id' => 'footer-4',
            'desc' => 'جایگاه ابزارک پابرگ 4',
        ],
        [
            'name' => 'پابرگ 5',
            'id' => 'footer-5',
            'desc' => 'جایگاه ابزارک پابرگ 5',
        ],
    ];

    if (function_exists('dokan')) {
        $sidebars[] = [
            'name' => 'صفحه فروشندگان',
            'id' => 'sidebar-store',
            'desc' => 'جایگاه ابزارک افزونه دکان',
        ];
    }
    unregister_sidebar('sidebar-store');
    foreach ($sidebars as $sidebar) {
        register_sidebar([
            'name' => $sidebar['name'],
            'id' => $sidebar['id'],
            'description' => $sidebar['desc'],
            'before_widget' => '<section id="%1$s" class="sidebar-widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h5 class="widget-title">',
            'after_title' => '</h5>',
        ]);
    }

}

add_action('widgets_init', 'pooshe_widgets_init');

function pooshe_scripts()
{
    $ver = wp_get_theme()->get('Version');

    wp_enqueue_style('pooshe-style', get_stylesheet_uri(), [], $ver);
    wp_enqueue_style('pooshe-print-style', get_theme_file_uri('/print.css'), [], $ver, 'print');
    wp_enqueue_style('pooshe-main', get_theme_file_uri('/dist/style/main.css'), [], filemtime(get_theme_file_path('/dist/style/main.css')));

    $font = pooshe_opt(STYLE_OPTS, 'font');

    if ('yekan' === $font) {
        wp_enqueue_style('pooshe-yekan', get_theme_file_uri('/dist/font/IRANYekan/css/fontiran.css'), [], $ver);
    }
    if ('iransans' === $font) {
        wp_enqueue_style('pooshe-iransans', get_theme_file_uri('/dist/font/iransans/css/fontiran.css'), [], $ver);
    }
    if ('kalameh' === $font) {
        wp_enqueue_style('pooshe-Kalameh', get_theme_file_uri('/dist/font/Kalameh/css/fontiran.css'), [], $ver);
    }
    if ('yekanbakh' === $font) {
        wp_enqueue_style('pooshe-yekanbakh', get_theme_file_uri('/dist/font/YekanBakh/css/fontiran.css'), [], $ver);
    }

    $colors = pooshe_get_colors();
    $color_main = $colors['color_main'];
    $color_btn = $colors['color_btn'];
    $color_btn_hover = $colors['color_btn_hover'];

    $hex = str_replace('#', '', $color_main);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $container_size = pooshe_opt(STYLE_OPTS, 'container_size', 1470);

    $css_vars = "
:root {
    --pooshe-color-main: {$color_main};
    --pooshe-color-main-rgb: {$r}, {$g}, {$b};
    --pooshe-color-btn: {$color_btn};
    --pooshe-color-btn-hover: {$color_btn_hover};
    --pooshe-container-size: {$container_size}px;
}";

    wp_add_inline_style('pooshe-main', $css_vars);

    wp_enqueue_script('pooshe-sms', get_theme_file_uri('/dist/js/smart-login.js'), [], filemtime(get_theme_file_path('/dist/js/smart-login.js')), true);
    wp_localize_script('pooshe-sms', 'pooshe_obj', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);

    wp_enqueue_script('pooshe-swiper', get_theme_file_uri('/dist/js/swiper.min.js'), [], filemtime(get_theme_file_path('/dist/js/swiper.min.js')), true);
    wp_enqueue_script('pooshe-app', get_theme_file_uri('/dist/js/app.js'), ['pooshe-swiper'], filemtime(get_theme_file_path('/dist/js/app.js')), true);
    wp_localize_script('pooshe-app', 'WP_OBJ', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'dist' => get_theme_file_uri('/dist/'),
        'site_url' => site_url(),
        'ajax_add_to_cart' => pooshe_opt(STYLE_OPTS, 'ajax_add_to_cart', true) ? '1' : '0',
    ]);

    $dark_mode_enabled = pooshe_opt(STYLE_OPTS, 'dark_mode', true);
    wp_enqueue_script('pooshe-dark-mode', get_theme_file_uri('/dist/js/dark-mode.js'), [], filemtime(get_theme_file_path('/dist/js/dark-mode.js')), true);
    wp_localize_script('pooshe-dark-mode', 'POOSHE_DARK_MODE', [
        'enabled' => $dark_mode_enabled ? '1' : '0',
    ]);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }


}

function pooshe_defer_scripts($tag, $handle)
{
    $defer_handles = ['pooshe-sms', 'pooshe-swiper', 'pooshe-app', 'pooshe-dark-mode'];

    if (in_array($handle, $defer_handles, true)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'pooshe_defer_scripts', 10, 2);

add_action('wp_enqueue_scripts', 'pooshe_scripts');


function pooshe_init()
{
    remove_filter('dokan_settings_sections', ['Dokan_Apperance', 'render_apperance_section']);
    remove_filter('dokan_settings_fields', ['Dokan_Apperance', 'render_apperance_settings']);

    remove_action('wp_head', ['Dokan_Apperance', 'load_styles']);
    remove_action('dokan_setup_wizard_styles', ['Dokan_Apperance', 'load_styles']);
}

add_action('init', 'pooshe_init', 999);

add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});

remove_action('comment_form', 'wp_comment_form_unfiltered_html_nonce');
add_filter('learn-press/override-templates', function () {
    return true;
});

function pooshe_learn_press_profile_statistics_info($statistics_data)
{
    if (isset($statistics_data['total_course'])) {
        $statistics_data['total_course']['title'] = 'کل دوره';
        $statistics_data['total_course']['label'] = 'کل دوره';
    }

    if (isset($statistics_data['pending_course'])) {
        $statistics_data['pending_course']['title'] = 'دوره در حال انتظار';
        $statistics_data['pending_course']['label'] = 'دوره در حال انتظار';
    }

    if (isset($statistics_data['total_student'])) {
        $statistics_data['total_student']['title'] = 'کل دانشجو';
        $statistics_data['total_student']['label'] = 'کل دانشجو';
    }

    if (isset($statistics_data['student_completed'])) {
        $statistics_data['student_completed']['title'] = 'دانشجو تکمیل شده';
        $statistics_data['student_completed']['label'] = 'دانشجو تکمیل شده';
    }

    if (isset($statistics_data['student_in_progress'])) {
        $statistics_data['student_in_progress']['title'] = 'دانشجو در حال پیشرفت';
        $statistics_data['student_in_progress']['label'] = 'دانشجو در حال پیشرفت';
    }
    if (isset($statistics_data['published_course'])) {
        $statistics_data['published_course']['title'] = 'دوره منتشر شده';
        $statistics_data['published_course']['label'] = 'دوره منتشر شده';
    }
    if (isset($statistics_data['passed_courses'])) {
        $statistics_data['passed_courses']['title'] = 'دوره های گذرانده ';
        $statistics_data['passed_courses']['label'] = 'دوره های گذرانده ';
    }
    if (isset($statistics_data['failed_courses'])) {
        $statistics_data['failed_courses']['title'] = 'دوره های شکست خورده';
        $statistics_data['failed_courses']['label'] = 'دوره های شکست خورده';
    }

    if (isset($statistics_data['in_progress_course'])) {
        $statistics_data['in_progress_course']['title'] = 'دوره های شکست خورده';
        $statistics_data['in_progress_course']['label'] = 'دوره های شکست خورده';
    }

    return $statistics_data;
}

add_filter('learn-press/profile/instructor-statistics/info', 'pooshe_learn_press_profile_statistics_info');
add_filter('learn-press/profile/student-statistics/info', 'pooshe_learn_press_profile_statistics_info');


require get_template_directory() . '/includes/jsonld-breadcrumbs.php';
require get_template_directory() . '/includes/lazy-loading.php';
require get_template_directory() . '/includes/dequeue-assets.php';
require get_template_directory() . '/includes/support-tickets.php';
require get_template_directory() . '/includes/encode/_init-0.php';
require get_template_directory() . '/includes/class-walker-comment.php';

if (function_exists('WC')) {
    require get_template_directory() . '/includes/product-badges.php';
    require get_template_directory() . '/includes/quick-view.php';
    require get_template_directory() . '/includes/advanced-filters.php';
    require get_template_directory() . '/includes/tiered-discount.php';
    require get_template_directory() . '/includes/order-sms-notification.php';
    require get_template_directory() . '/includes/watermark.php';
    require get_template_directory() . '/includes/affiliate.php';
    require get_template_directory() . '/includes/download-subscription.php';
    require get_template_directory() . '/includes/download-library.php';
}

if (pooshe_opt(STYLE_OPTS, 'post_views_counter', true)) {
    require get_template_directory() . '/includes/post-views-counter.php';
}

if (is_admin()) {
    require get_template_directory() . '/includes/version-notifier.php';
}
add_action('woocommerce_after_main_content', 'woocommerce_taxonomy_archive_description', 5);


add_action('init', function () {
    if (function_exists('rcp_get_restricted_content_message') && !is_textdomain_loaded('rcp')) {
        $rcp_lang_paths = array(
            WP_PLUGIN_DIR . '/restrict-content-pro/languages',
            WP_PLUGIN_DIR . '/rcp/languages',
            WP_PLUGIN_DIR . '/rcp-premium/languages',
        );
        foreach ($rcp_lang_paths as $path) {
            if (is_dir($path)) {
                load_plugin_textdomain('rcp', false, $path);
                break;
            }
        }
    }
}, 0);

// افزودن نام اشتراک مورد نیاز به صفحه محصول ووکامرس
add_action('woocommerce_single_product_summary', 'show_required_subscription_name', 40);
function show_required_subscription_name()
{
    if (!function_exists('rcp_get_restricted_content_message')) {
        return;
    }
    global $product;
    $subscribe_levels = $product->get_meta('_rcp_woo_subscription_levels_to_purchase', true);

    if (is_array($subscribe_levels) && !empty($subscribe_levels)) {
        $subscription_levels = rcp_get_subscription_levels();
        echo '<div class="access_levels_single"> اشتراک مورد نیاز';
        foreach ($subscription_levels as $level) {
            if (!in_array($level->id, $subscribe_levels)) {
                continue;
            }
            echo ' <span>" '.$level->name .' "</span>';
        }
        echo '</div>';
    }

}

remove_filter('woocommerce_account_menu_items', 'pooshe_woocommerce_account_menu_items', 99, 2);
add_filter( 'woocommerce_payment_complete_order_status', 'woocommerceir_autocomplete_processing_orders', 9999 );
 
function woocommerceir_autocomplete_processing_orders() {
   return 'completed';
}
add_filter( 'woocommerce_breadcrumb_defaults', 'prefix_change_breadcrumb_home_text' );
/**
* Rename "home" in WooCommerce breadcrumb
*/
function prefix_change_breadcrumb_home_text( $defaults ) {
// Change the breadcrumb home text from 'خانه' to 'Shop'
$defaults['home'] = 'طرح 99';
return $defaults;
}
/**
 * حذف پیام "لطفا با حساب کاربری خود وارد شوید..."
 * از خروجی HTML قبل از ارسال به مرورگر
 */
add_action('template_redirect', function () {

    if ( is_admin() || is_user_logged_in() ) {
        return;
    }

    ob_start(function ($buffer) {

        $buffer = preg_replace(
            '#<div[^>]*>\s*<p>\s*لطفا با حساب کاربری خود وارد شوید تا لینک دانلود به شما نمایش داده شود\.\s*</p>\s*</div>#u',
            '',
            $buffer,
            1
        );

        return $buffer;
    });

}, 0);
function pooshe_preload_yekanbakh_fonts() {
    $font = pooshe_opt(STYLE_OPTS, 'font');

    if ('yekanbakh' !== $font) {
        return;
    }

    $regular  = get_theme_file_uri('/dist/font/YekanBakh/fonts/woff2/YekanBakh-Regular.woff2');
    $semibold = get_theme_file_uri('/dist/font/YekanBakh/fonts/woff2/YekanBakh-SemiBold.woff2');
    $bold     = get_theme_file_uri('/dist/font/YekanBakh/fonts/woff2/YekanBakh-Bold.woff2');

    echo '<link rel="preload" href="' . esc_url($regular) . '" as="font" type="font/woff2" crossorigin>' . "\n";
    echo '<link rel="preload" href="' . esc_url($semibold) . '" as="font" type="font/woff2" crossorigin>' . "\n";
    echo '<link rel="preload" href="' . esc_url($bold) . '" as="font" type="font/woff2" crossorigin>' . "\n";
}
add_action('wp_head', 'pooshe_preload_yekanbakh_fonts', 1);
/**
 * Disable WordPress Emojis
 */
function tarh99_disable_wp_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    add_filter('tiny_mce_plugins', function($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, array('wpemoji'));
        }
        return array();
    });

    add_filter('emoji_svg_url', '__return_false');
}
add_action('init', 'tarh99_disable_wp_emojis');


