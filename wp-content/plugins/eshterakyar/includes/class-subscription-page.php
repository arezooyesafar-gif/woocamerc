<?php

if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Subscription_Page {
    
    private static $instance = null;
    private static $page_slug = 'subscription-plans';
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('eshterakyar_subscription_plans', array($this, 'render_subscription_plans'));
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        add_action('template_redirect', array($this, 'remove_wordpress_generator'));
        add_action('wp_head', array($this, 'remove_wordpress_generator_from_head'), 1);
        add_filter('the_generator', array($this, 'remove_generator'), 10, 2);
        
        add_filter('elementor/frontend/before_render', array($this, 'prevent_elementor_shortcode_replacement'), 10, 1);
        
        add_filter('elementor/frontend/the_content', array($this, 'preserve_shortcode_in_content'), 10, 1);
    }
    
    public function prevent_elementor_shortcode_replacement($element) {
        return $element;
    }
    
    public function preserve_shortcode_in_content($content) {
        return $content;
    }
    

    public static function create_subscription_page() {
        $options = get_option('eshterakyar_options', array());
        $custom_page_id = isset($options['subscription_plans_page_id']) ? absint($options['subscription_plans_page_id']) : 0;
        if ($custom_page_id > 0 && get_post_status($custom_page_id) === 'publish') {
            return $custom_page_id;
        }

        $page = get_page_by_path(self::$page_slug);
        
        if (!$page) {
            $page_data = array(
                'post_title' => __('پلن‌ها و قیمت‌گذاری', 'eshterakyar'),
                'post_content' => '[eshterakyar_subscription_plans]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => self::$page_slug,
                'post_author' => 1,
                'comment_status' => 'closed',
                'ping_status' => 'closed'
            );
            
            $page_id = wp_insert_post($page_data);
            
            if ($page_id) {
                update_option('eshterakyar_subscription_page_id', $page_id);
                return $page_id;
            }
        } else {
            // Keep auto page content in sync only when no custom plans page is selected.
            if ($custom_page_id <= 0) {
                $page->post_content = '[eshterakyar_subscription_plans]';
                wp_update_post($page);
            }
            update_option('eshterakyar_subscription_page_id', $page->ID);
            return $page->ID;
        }
        
        return false;
    }
    
    public static function delete_subscription_page() {
        $page_id = get_option('eshterakyar_subscription_page_id');
        
        if ($page_id) {
            wp_delete_post($page_id, true);
            delete_option('eshterakyar_subscription_page_id');
        } else {
            $page = get_page_by_path(self::$page_slug);
            if ($page) {
                wp_delete_post($page->ID, true);
            }
        }
    }
    public function enqueue_assets() {
        $post = get_post();
        $has_shortcode = $post && has_shortcode($post->post_content ?? '', 'eshterakyar_subscription_plans');
        
        if (is_page(self::$page_slug) || $has_shortcode) {
            wp_enqueue_style(
                'eshterakyar-subscription-plans',
                ESHTERAKYAR_PLUGIN_URL . 'assets/css/subscription-plans.css',
                array(),
                ESHTERAKYAR_VERSION
            );
            
            wp_enqueue_script(
                'eshterakyar-subscription-plans',
                ESHTERAKYAR_PLUGIN_URL . 'assets/js/subscription-plans.js',
                array('jquery'),
                ESHTERAKYAR_VERSION,
                true
            );
            
            wp_localize_script('eshterakyar-subscription-plans', 'eshterakyar_plans', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('eshterakyar_plans_nonce'),
                'i18n' => array(
                    'loading' => __('در حال بارگذاری...', 'eshterakyar'),
                    'error' => __('خطا در بارگذاری پلن‌ها', 'eshterakyar'),
                )
            ));
        }
    }
    
    public function render_subscription_plans($atts) {
        $atts = shortcode_atts(array(
            'show_header' => 'false',
            'show_terms' => 'false',
            'terms_content' => '',
            'terms_position' => '',
        ), $atts);
        
        $is_elementor_edit_mode = false;
        if (class_exists('\Elementor\Plugin')) {
            $is_elementor_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
        }
        
        if ($is_elementor_edit_mode) {
            return '<div class="eshterakyar-shortcode-placeholder" style="padding: 20px; border: 2px dashed #ccc; text-align: center; background: #f9f9f9;">
                <p style="margin: 0; color: #666; font-size: 14px;">' . esc_html__('پلن‌های اشتراک - این بخش در حالت پیش‌نمایش نمایش داده می‌شود', 'eshterakyar') . '</p>
            </div>';
        }
        
        $is_logged_in = is_user_logged_in();
        $user_id = $is_logged_in ? get_current_user_id() : 0;
        
        $active_subscription = null;
        if ($user_id) {
            $active_subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        }
        
        $plans = Eshterakyar_Plan::get_all('active');
        
        $options = get_option('eshterakyar_options', array());
        
        if (!empty($atts['terms_content'])) {
            $terms_content = $atts['terms_content'];
        } else {
            $terms_content = isset($options['subscription_terms']) ? $options['subscription_terms'] : '';
        }
        
        if (!empty($atts['terms_position'])) {
            $raw_terms_position = sanitize_text_field($atts['terms_position']);
        } else {
            $raw_terms_position = isset($options['subscription_terms_position']) ? sanitize_text_field($options['subscription_terms_position']) : 'top';
        }
        $terms_position = in_array($raw_terms_position, array('top', 'bottom'), true) ? $raw_terms_position : 'top';
        
        $currency_symbol = get_woocommerce_currency_symbol();
        $currency_position = get_option('woocommerce_currency_pos', 'right');
        
        $is_subscription_page = is_page(self::$page_slug);
        $show_header = $is_subscription_page || ($atts['show_header'] === 'true' || $atts['show_header'] === 'yes' || $atts['show_header'] === '1');
        

        $show_terms = false;
        if ($is_subscription_page) {
            $show_terms = !empty($terms_content);
        } else {
            $show_terms = ($atts['show_terms'] === 'true' || $atts['show_terms'] === 'yes' || $atts['show_terms'] === '1') && !empty($terms_content);
        }
        
        ob_start();
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/frontend/subscription-plans.php';
        return ob_get_clean();
    }
    
    public function remove_wordpress_generator() {
        $post = get_post();
        $has_shortcode = $post && has_shortcode($post->post_content ?? '', 'eshterakyar_subscription_plans');
        
        if (is_page(self::$page_slug) || $has_shortcode) {
            remove_action('wp_head', 'wp_generator');
            
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wp_shortlink_wp_head');
        }
    }

    public function remove_wordpress_generator_from_head() {
        $post = get_post();
        $has_shortcode = $post && has_shortcode($post->post_content ?? '', 'eshterakyar_subscription_plans');
        
        if (is_page(self::$page_slug) || $has_shortcode) {
            remove_action('wp_head', 'wp_generator');
        }
    }
    
    public function remove_generator($generator, $type) {
        $post = get_post();
        $has_shortcode = $post && has_shortcode($post->post_content ?? '', 'eshterakyar_subscription_plans');
        
        if (is_page(self::$page_slug) || $has_shortcode) {
            return '';
        }
        
        return $generator;
    }

    /**
     * Resolved plans page ID (settings override, then auto-created page).
     *
     * @return int
     */
    public static function get_page_id(): int {
        $options = get_option('eshterakyar_options', array());
        $custom_page_id = isset($options['subscription_plans_page_id']) ? absint($options['subscription_plans_page_id']) : 0;
        if ($custom_page_id > 0 && get_post($custom_page_id)) {
            return $custom_page_id;
        }

        $legacy_id = absint(get_option('eshterakyar_subscription_page_id', 0));
        if ($legacy_id > 0 && get_post($legacy_id)) {
            return $legacy_id;
        }

        $page = get_page_by_path(self::$page_slug);
        return $page ? (int) $page->ID : 0;
    }

    /**
     * URL used by all «خرید اشتراک» / renew CTAs across the plugin.
     *
     * Priority: custom URL → selected page → auto-created subscription-plans page.
     *
     * @return string
     */
    public static function get_page_url() {
        $options = get_option('eshterakyar_options', array());

        $custom_url = isset($options['subscription_plans_custom_url'])
            ? trim((string) $options['subscription_plans_custom_url'])
            : '';
        if ($custom_url !== '') {
            return esc_url_raw($custom_url);
        }

        $page_id = self::get_page_id();
        if ($page_id > 0) {
            $permalink = get_permalink($page_id);
            if ($permalink) {
                return $permalink;
            }
        }

        $page = get_page_by_path(self::$page_slug);
        if ($page) {
            return get_permalink($page->ID);
        }

        return '';
    }
}

