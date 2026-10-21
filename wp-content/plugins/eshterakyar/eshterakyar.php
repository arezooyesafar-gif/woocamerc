<?php
/**
 * Plugin Name: اشتراک یار | Eshterakyar
 * Description: 🚀 افزونه حرفه‌ای و کامل مدیریت اشتراک‌ها، دانلود محصولات و سیستم عضویت برای ووکامرس | Professional subscription management, product downloads, and membership system for WooCommerce
 * Version: 1.5.0
 * Author: احمد اسماعیلی
 * Author URI: https://ahmadesmaeeli.ir
 * Text Domain: eshterakyar
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 6.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>افزونه اشتراک یار نیاز به ووکامرس دارد. لطفاً ابتدا ووکامرس را نصب و فعال کنید.</p></div>';
    });
    return;
}
define('ESHTERAKYAR_VERSION', '1.5.0');
define('ESHTERAKYAR_DB_REVISION', 4);
define('ESHTERAKYAR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ESHTERAKYAR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ESHTERAKYAR_PLUGIN_FILE', __FILE__);
define('ESHTERAKYAR_AUTHOR_URL', 'https://ahmadesmaeeli.ir');
define('ESHTERAKYAR_FEATUREBASE_ENABLED', true); 
define('ESHTERAKYAR_FEATUREBASE_URL', 'https://eshterakyar.featurebase.app'); 
define('ESHTERAKYAR_FEATUREBASE_WIDGET_ENABLED', true); 
define('ESHTERAKYAR_FEATUREBASE_WIDGET_ID', ''); 
class Eshterakyar {
    
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init();
    }
    
    private function init() {
        $this->load_dependencies();
        register_activation_hook(__FILE__, array('Eshterakyar', 'activate_plugin'));
        register_deactivation_hook(__FILE__, array('Eshterakyar', 'deactivate_plugin'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'load_classes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('before_woocommerce_init', array($this, 'declare_hpos_compatibility'));
    }
    
    public function declare_hpos_compatibility() {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        }
    }
    
    private function load_dependencies() {
        $debug_config_file = ESHTERAKYAR_PLUGIN_DIR . 'includes/debug-config.php';
        if (file_exists($debug_config_file)) {
            require_once $debug_config_file;
        }
        
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-database.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-cache.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-cleanup.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-subscription.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-plan.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-download.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-admin.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-frontend.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-woocommerce-integration.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-notifications.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-logs.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-ajax.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-subscription-page.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-content-access.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-auto-membership.php';
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('eshterakyar', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function load_classes() {
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-database.php';
        Eshterakyar_Database::update_tables();
        Eshterakyar_Admin::get_instance();
        Eshterakyar_Frontend::get_instance();
        Eshterakyar_Shortcodes::get_instance();
        Eshterakyar_WooCommerce_Integration::get_instance();
        Eshterakyar_Notifications::get_instance();
        Eshterakyar_Ajax::get_instance();
        Eshterakyar_Subscription_Page::get_instance();
        Eshterakyar_Content_Access::get_instance();
        Eshterakyar_Auto_Membership::get_instance();
    }
    
    public static function activate_plugin() {
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-database.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-subscription-page.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-frontend.php';
        
        Eshterakyar_Database::create_tables();
        Eshterakyar_Database::update_tables();
        Eshterakyar_Subscription_Page::create_subscription_page();
        

        $frontend = Eshterakyar_Frontend::get_instance();
        $frontend->add_account_endpoint();
        flush_rewrite_rules();
        

        update_option('eshterakyar_flush_rewrite_rules', true);
    }
    public static function deactivate_plugin() {
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-database.php';
        require_once ESHTERAKYAR_PLUGIN_DIR . 'includes/class-subscription-page.php';
        Eshterakyar_Subscription_Page::delete_subscription_page();
        Eshterakyar_Database::deactivate();
    }
    
    public function enqueue_scripts() {
        $should_load = $this->should_load_frontend_assets();
        $should_load = apply_filters('eshterakyar_should_load_assets', $should_load);

        if ($should_load) {
            self::enqueue_frontend_assets();
        }
    }

    /**
     * Detect whether frontend CSS/JS are needed on this request.
     *
     * @return bool
     */
    private function should_load_frontend_assets(): bool {
        if (function_exists('is_account_page') && is_account_page()) {
            return true;
        }

        if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url()) {
            return true;
        }

        if (function_exists('is_product') && is_product()) {
            return true;
        }

        $shortcode_tags = array(
            'user_subscriptions',
            'subscription_downloads',
            'subscription_days_left',
            'subscription_expired',
            'product_download_status',
            'eshterakyar_product_cta',
            'subscription_info',
            'subscription_badge',
            'eshterakyar_subscription_plans',
            'eshterakyar_plans',
            'eshterakyar_paywall',
        );
        $shortcode_pattern = '/\[(' . implode('|', array_map(static function ($tag) {
            return preg_quote($tag, '/');
        }, $shortcode_tags)) . ')(\s|\])/';

        global $post;
        if (is_a($post, 'WP_Post')) {
            $content = (string) $post->post_content;

            foreach ($shortcode_tags as $shortcode) {
                if (has_shortcode($content, $shortcode) || strpos($content, '[' . $shortcode) !== false) {
                    return true;
                }
            }

            if (preg_match($shortcode_pattern, $content)) {
                return true;
            }

            if (strpos($content, 'eshterakyar-') !== false) {
                return true;
            }

            // Elementor stores widgets in meta — shortcodes often never appear in post_content.
            $elementor_data = get_post_meta($post->ID, '_elementor_data', true);
            if (is_string($elementor_data) && $elementor_data !== '' && preg_match($shortcode_pattern, $elementor_data)) {
                return true;
            }
            if (is_string($elementor_data) && $elementor_data !== '' && stripos($elementor_data, 'eshterakyar') !== false) {
                return true;
            }

            if (Eshterakyar_Subscription::is_content_restriction_enabled()) {
                if (Eshterakyar_Subscription::post_content_has_paywall_shortcode($content) ||
                    get_post_meta($post->ID, '_eshterakyar_content_restricted', true)) {
                    return true;
                }
            }
        }

        if (function_exists('is_singular') && is_singular(Eshterakyar_Subscription::get_content_restriction_post_types())) {
            $queried_id = get_queried_object_id();
            if ($queried_id && Eshterakyar_Subscription::should_enforce_post_paywall($queried_id)) {
                return true;
            }
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $current_url = $_SERVER['REQUEST_URI'];
            $profile_patterns = array(
                'my-account',
                'user-profile',
                'profile',
                'dashboard',
                'account',
                'membership',
                'subscription',
            );

            foreach ($profile_patterns as $pattern) {
                if (strpos($current_url, $pattern) !== false) {
                    return true;
                }
            }
        }

        if (is_user_logged_in() && is_a($post, 'WP_Post') && is_page()) {
            $profile_page_ids = array(
                (int) get_option('woocommerce_myaccount_page_id'),
                (int) get_option('eshterakyar_subscription_page_id', 0),
            );
            $options = get_option('eshterakyar_options', array());
            if (!empty($options['subscription_plans_page_id'])) {
                $profile_page_ids[] = (int) $options['subscription_plans_page_id'];
            }

            if (in_array((int) $post->ID, $profile_page_ids, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register/enqueue frontend assets. Safe to call from shortcodes after wp_head.
     *
     * @param bool $force_print_late If true and head already ran, print style in footer.
     */
    public static function enqueue_frontend_assets(bool $force_print_late = false): void {
        $style_handle = 'eshterakyar-frontend';
        $script_handle = 'eshterakyar-frontend';

        if (!wp_style_is($style_handle, 'registered')) {
            wp_register_style(
                $style_handle,
                ESHTERAKYAR_PLUGIN_URL . 'assets/css/frontend.css',
                array(),
                ESHTERAKYAR_VERSION
            );
        }
        if (!wp_script_is($script_handle, 'registered')) {
            wp_register_script(
                $script_handle,
                ESHTERAKYAR_PLUGIN_URL . 'assets/js/frontend.js',
                array('jquery'),
                ESHTERAKYAR_VERSION,
                true
            );
        }

        wp_enqueue_style($style_handle);

        if (file_exists(ESHTERAKYAR_PLUGIN_DIR . 'assets/css/subscription-plans.css')) {
            wp_enqueue_style(
                'eshterakyar-subscription-plans',
                ESHTERAKYAR_PLUGIN_URL . 'assets/css/subscription-plans.css',
                array($style_handle),
                ESHTERAKYAR_VERSION
            );
        }
        if (file_exists(ESHTERAKYAR_PLUGIN_DIR . 'assets/css/content-paywall.css')) {
            wp_enqueue_style(
                'eshterakyar-content-paywall',
                ESHTERAKYAR_PLUGIN_URL . 'assets/css/content-paywall.css',
                array($style_handle),
                ESHTERAKYAR_VERSION
            );
        }

        wp_enqueue_script($script_handle);

        static $localized = false;
        if (!$localized) {
            $localized = true;
            wp_localize_script($script_handle, 'eshterakyar', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('eshterakyar_nonce'),
                'is_account_page' => function_exists('is_account_page') ? is_account_page() : false,
                'is_product_page' => function_exists('is_product') ? is_product() : false,
                'i18n' => array(
                    'confirm_download' => __('آیا مطمئن هستید که می‌خواهید این فایل را دانلود کنید؟', 'eshterakyar'),
                    'subscription_expired' => __('اشتراک شما منقضی شده است', 'eshterakyar'),
                    'download_limit_reached' => __('محدودیت دانلود شما به پایان رسیده است', 'eshterakyar'),
                    'loading' => __('در حال بارگذاری...', 'eshterakyar'),
                ),
            ));
        }

        // Shortcodes inside Elementor often render after wp_head — print CSS in footer.
        if ($force_print_late && did_action('wp_head') && !wp_style_is($style_handle, 'done')) {
            static $footer_hooked = false;
            if (!$footer_hooked) {
                $footer_hooked = true;
                add_action('wp_footer', static function () use ($style_handle) {
                    if (wp_style_is($style_handle, 'enqueued') && !wp_style_is($style_handle, 'done')) {
                        wp_print_styles($style_handle);
                    }
                    if (wp_style_is('eshterakyar-subscription-plans', 'enqueued') && !wp_style_is('eshterakyar-subscription-plans', 'done')) {
                        wp_print_styles('eshterakyar-subscription-plans');
                    }
                }, 5);
            }
        }
    }
}

function eshterakyar() {
    return Eshterakyar::get_instance();
}

/**
 * برمی‌گرداند متن قابل ویرایش کاربر برای برچسب‌های مهم افزونه (شورتکدها و صفحات).
 * اگر در تنظیمات متن دلخواه وارد شده باشد از آن استفاده می‌شود، وگرنه از مقدار پیش‌فرض.
 *
 * @param string $key    کلید برچسب (مثلاً buy_subscription)
 * @param string $default متن پیش‌فرض (معمولاً با __() ترجمه‌شده)
 * @return string
 */
function eshterakyar_get_label($key, $default = '') {
    $options = get_option('eshterakyar_options', array());
    $option_key = 'label_' . $key;
    if (isset($options[$option_key]) && (string) $options[$option_key] !== '') {
        return $options[$option_key];
    }
    return $default;
}

/**
 * Render an admin copyable code block (settings, help, shortcodes tab).
 *
 * @param string $code Raw source text.
 * @param array  $args {
 *     @type string $lang    shortcode|php|css|url|code
 *     @type string $title   Optional label above the block.
 *     @type bool   $compact Smaller variant.
 *     @type string $class   Extra CSS class.
 * }
 * @return string
 */
function eshterakyar_render_code_block(string $code, array $args = array()): string {
    if ($code === '') {
        return '';
    }

    $args = wp_parse_args($args, array(
        'lang' => 'code',
        'title' => '',
        'compact' => false,
        'class' => '',
    ));

    static $block_index = 0;
    $block_index++;

    $lang_labels = array(
        'shortcode' => __('شورتکد', 'eshterakyar'),
        'php' => 'PHP',
        'css' => 'CSS',
        'url' => 'URL',
        'html' => 'HTML',
        'code' => __('کد', 'eshterakyar'),
    );
    $lang_key = strtolower((string) $args['lang']);
    $lang_label = isset($lang_labels[$lang_key]) ? $lang_labels[$lang_key] : strtoupper($lang_key);

    $classes = array('eshterakyar-code-block');
    if (!empty($args['compact'])) {
        $classes[] = 'is-compact';
    }
    if (!empty($args['class'])) {
        $classes[] = $args['class'];
    }

    ob_start();
    ?>
    <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" dir="ltr" id="<?php echo esc_attr('eshterakyar-code-' . $block_index); ?>">
        <div class="eshterakyar-code-block-header">
            <?php if ($args['title'] !== '') : ?>
                <span class="eshterakyar-code-block-title"><?php echo esc_html($args['title']); ?></span>
            <?php endif; ?>
            <span class="eshterakyar-code-block-lang"><?php echo esc_html($lang_label); ?></span>
            <button type="button" class="eshterakyar-code-copy-btn" aria-label="<?php esc_attr_e('کپی کد', 'eshterakyar'); ?>">
                <span class="dashicons dashicons-admin-page" aria-hidden="true"></span>
                <span class="eshterakyar-code-copy-label"><?php esc_html_e('کپی', 'eshterakyar'); ?></span>
                <span class="eshterakyar-code-copy-done"><?php esc_html_e('کپی شد!', 'eshterakyar'); ?></span>
            </button>
        </div>
        <pre class="eshterakyar-code-block-pre"><code><?php echo esc_html($code); ?></code></pre>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * تشخیص پایدار IP کلاینت (پشت CDN، پروکسی، Cloudflare) برای اعتبارسنجی دانلود و ثبت IP.
 *
 * @return string
 */
function eshterakyar_detect_client_ip() {
    $candidates = array();

    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $candidates[] = trim(sanitize_text_field(wp_unslash($_SERVER['HTTP_CF_CONNECTING_IP'])));
    }
    if (!empty($_SERVER['HTTP_TRUE_CLIENT_IP'])) {
        $candidates[] = trim(sanitize_text_field(wp_unslash($_SERVER['HTTP_TRUE_CLIENT_IP'])));
    }
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $candidates[] = trim(sanitize_text_field(wp_unslash($_SERVER['HTTP_X_REAL_IP'])));
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $xff = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        foreach (explode(',', $xff) as $ip) {
            $candidates[] = trim($ip);
        }
    }
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $candidates[] = trim(sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP'])));
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $candidates[] = trim(sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])));
    }

    foreach ($candidates as $ip) {
        if ($ip !== '' && filter_var($ip, FILTER_VALIDATE_IP)) {
            return (string) apply_filters('eshterakyar_client_ip', $ip, $candidates);
        }
    }

    return '0.0.0.0';
}

eshterakyar();