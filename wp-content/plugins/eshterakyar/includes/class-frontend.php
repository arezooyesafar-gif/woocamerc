<?php


if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $options = get_option('eshterakyar_options', array());
        $auto_shortcodes = isset($options['auto_add_shortcodes']) ? $options['auto_add_shortcodes'] : 1;
        
        add_action('init', array($this, 'add_account_endpoint'), 1);
        
        add_filter('woocommerce_get_query_vars', array($this, 'add_query_vars'), 10);
        
        add_filter('woocommerce_account_menu_items', array($this, 'add_account_menu_item'), 10);
        
        add_action('woocommerce_account_eshterakyar-subscriptions_endpoint', array($this, 'subscriptions_endpoint_content'));
        add_action('woocommerce_account_eshterakyar-downloads_endpoint', array($this, 'downloads_endpoint_content'));
        
        if (get_option('eshterakyar_flush_rewrite_rules', false) === false) {
            add_action('init', array($this, 'maybe_flush_rewrite_rules'), 99);
        }
        
        add_action('admin_init', array($this, 'maybe_force_flush_rewrite_rules'));
        
        if ($auto_shortcodes) {
            add_action('woocommerce_account_dashboard', array($this, 'auto_add_subscription_info'));
        }
    }
    
    public function maybe_flush_rewrite_rules() {
        flush_rewrite_rules(false);
        update_option('eshterakyar_flush_rewrite_rules', true);
    }
    
    public function maybe_force_flush_rewrite_rules() {
        if (isset($_GET['eshterakyar_flush_rules']) && current_user_can('manage_options')) {
            if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'eshterakyar_flush_rules')) {
                $this->add_account_endpoint();
                flush_rewrite_rules(false);
                update_option('eshterakyar_flush_rewrite_rules', true);
                
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Rewrite rules با موفقیت بازسازی شدند.', 'eshterakyar') . '</p></div>';
                });
                
                wp_redirect(remove_query_arg(array('eshterakyar_flush_rules', '_wpnonce')));
                exit;
            }
        }
    }
    
    public function auto_add_subscription_info() {
        $user_id = get_current_user_id();
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        
        if ($subscription) {
            echo '<div class="eshterakyar-auto-subscription-info">';
            echo do_shortcode('[subscription_info show_plan="yes" show_days="yes" show_downloads="yes"]');
            echo '</div>';
        }
    }
    

    public function add_account_menu_item($items) {
        $new_items = array();
        
        foreach ($items as $key => $item) {
            $new_items[$key] = $item;
            
            if ($key === 'orders') {
                $new_items['eshterakyar-subscriptions'] = eshterakyar_get_label('menu_subscriptions', __('اشتراک‌ها', 'eshterakyar'));
                $new_items['eshterakyar-downloads'] = eshterakyar_get_label('menu_subscription_downloads', __('دانلودهای اشتراک', 'eshterakyar'));
            }
        }
        
        return $new_items;
    }

    public function add_account_endpoint() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        add_rewrite_endpoint('eshterakyar-subscriptions', EP_ROOT | EP_PAGES);
        add_rewrite_endpoint('eshterakyar-downloads', EP_ROOT | EP_PAGES);

        if (function_exists('wc_get_page_id')) {
            $account_page_id = wc_get_page_id('myaccount');
            
            if ($account_page_id && $account_page_id > 0) {
                $account_slug = get_post_field('post_name', $account_page_id);
                
                if (!empty($account_slug)) {
                    add_rewrite_rule(
                        '^' . $account_slug . '/eshterakyar-subscriptions/?$',
                        'index.php?pagename=' . $account_slug . '&eshterakyar-subscriptions=1',
                        'top'
                    );
                    
                    add_rewrite_rule(
                        '^' . $account_slug . '/eshterakyar-downloads/?$',
                        'index.php?pagename=' . $account_slug . '&eshterakyar-downloads=1',
                        'top'
                    );
                }
            }
        }
    }
    
    public function add_query_vars($vars) {
        $vars['eshterakyar-subscriptions'] = 'eshterakyar-subscriptions';
        $vars['eshterakyar-downloads'] = 'eshterakyar-downloads';
        return $vars;
    }

    public function subscriptions_endpoint_content() {
        $user_id = get_current_user_id();
        $subscriptions = Eshterakyar_Subscription::get_user_subscriptions($user_id);
        
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/frontend/subscriptions.php';
    }

    public function downloads_endpoint_content() {
        $user_id = get_current_user_id();
        $active_subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        
        $all_rows = Eshterakyar_Download::get_all_user_downloads($user_id);
        // یک ردیف به ازای هر محصول: ترجیح ردیفی که مربوط به اشتراک فعلی است تا نمایش و can_download با صفحه محصول یکسان باشد
        $by_product = array();
        foreach ($all_rows as $d) {
            $pid = (int) $d->product_id;
            if (!isset($by_product[$pid])) {
                $by_product[$pid] = $d;
            } elseif ($active_subscription && (int) $d->subscription_id === (int) $active_subscription->id) {
                $by_product[$pid] = $d;
            }
        }
        $all_downloads = array_values($by_product);
        
        $subscription_downloads = array();
        $other_downloads = array();
        
        if ($active_subscription) {
            foreach ($all_downloads as $download) {
                if (isset($download->subscription_id) && (int) $download->subscription_id === (int) $active_subscription->id) {
                    $subscription_downloads[] = $download;
                } else {
                    $other_downloads[] = $download;
                }
            }
        } else {
            $other_downloads = $all_downloads;
        }
        
        $downloads = $all_downloads;
        
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/frontend/downloads.php';
        
        $this->display_featurebase_widget();
    }
    

    private function display_featurebase_widget() {
        $featurebase_enabled = defined('ESHTERAKYAR_FEATUREBASE_ENABLED') ? ESHTERAKYAR_FEATUREBASE_ENABLED : false;
        $featurebase_url = defined('ESHTERAKYAR_FEATUREBASE_URL') ? ESHTERAKYAR_FEATUREBASE_URL : '';
        $featurebase_widget_enabled = defined('ESHTERAKYAR_FEATUREBASE_WIDGET_ENABLED') ? ESHTERAKYAR_FEATUREBASE_WIDGET_ENABLED : false;
        $featurebase_widget_id = defined('ESHTERAKYAR_FEATUREBASE_WIDGET_ID') ? ESHTERAKYAR_FEATUREBASE_WIDGET_ID : '';
        
        if (!$featurebase_enabled || !$featurebase_widget_enabled || empty($featurebase_widget_id)) {
            return;
        }
        
        echo '<div class="eshterakyar-featurebase-widget-frontend">';
        echo '<h3>' . esc_html__('گزارش باگ و پیشنهاد ویژگی', 'eshterakyar') . '</h3>';
        echo '<p>' . esc_html__('اگر مشکلی دارید یا پیشنهادی دارید، لطفاً از طریق فرم زیر با ما در میان بگذارید:', 'eshterakyar') . '</p>';
        
        if (!empty($featurebase_url)) {
            echo '<p><a href="' . esc_url($featurebase_url) . '" target="_blank" class="button">' . esc_html__('مشاهده پورتال بازخورد', 'eshterakyar') . '</a></p>';
        }
        
        echo '<div id="featurebase-widget-frontend">';
        echo '<iframe 
            src="https://feedback.featurebaseapp.com/widget/' . esc_attr($featurebase_widget_id) . '" 
            width="100%" 
            height="500" 
            frameborder="0">
        </iframe>';
        echo '</div>';
        echo '</div>';
    }
}

