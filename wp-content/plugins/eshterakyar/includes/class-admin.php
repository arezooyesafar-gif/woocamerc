<?php

if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $rtlLicenseClassName  = 'RTL_License_cd36b7d39c4b02e3';
        $rtlLicenseFilePath   = __DIR__ . DIRECTORY_SEPARATOR . $rtlLicenseClassName . '.php';
        $rtlLicenseFileHash   = @sha1_file($rtlLicenseFilePath);
        if ( $rtlLicenseFileHash === '2bec39efd27c711cbabfd58b0705d3cb0e0d083f' && file_exists($rtlLicenseFilePath) ) {
            require_once $rtlLicenseFilePath;
            if ( class_exists($rtlLicenseClassName) && method_exists($rtlLicenseClassName, 'isActive') ) {
                $rtlLicenseClass = new $rtlLicenseClassName();
                if ( $rtlLicenseClass->{'isActive'}() === true ) {
                    add_action('admin_menu', array($this, 'add_admin_menu'));
                    add_action('admin_init', array($this, 'register_settings'));
                    add_action('admin_body_class', array($this, 'add_admin_body_class'));
                    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
                    add_action('wp_ajax_eshterakyar_set_delete_data_on_deactivate', array($this, 'ajax_set_delete_data_on_deactivate'));
                    add_action('wp_ajax_eshterakyar_get_nonce', array($this, 'ajax_get_nonce'));
                    add_action('wp_ajax_eshterakyar_save_plans_order', array($this, 'ajax_save_plans_order'));
                    add_action('wp_ajax_eshterakyar_toggle_recommended_plan', array($this, 'ajax_toggle_recommended_plan'));
                    add_action('wp_ajax_eshterakyar_create_manual_subscription', array($this, 'ajax_create_manual_subscription'));
                    add_action('wp_ajax_eshterakyar_search_users', array($this, 'ajax_search_users'));
                    add_action('wp_ajax_eshterakyar_auto_membership_batch', array($this, 'ajax_auto_membership_batch'));
                    add_action('wp_ajax_eshterakyar_auto_membership_count', array($this, 'ajax_auto_membership_count'));
                    add_action('admin_head', array($this, 'remove_wordpress_generator'), 1);
                    add_filter('admin_footer_text', array($this, 'remove_admin_footer_text'), 999);
                    add_action('admin_notices', array($this, 'remove_default_wordpress_notices'), 999);
                    add_action('admin_footer', array($this, 'remove_default_wordpress_notices_js'), 999);
                }
            }
        }
    }
    public function add_admin_body_class($classes) {
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'eshterakyar') !== false) {
            $classes .= ' eshterakyar-fullscreen';
        }
        return $classes;
    }
    public function remove_wordpress_generator() {
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'eshterakyar') !== false) {
            remove_action('admin_head', 'wp_generator');
        }
    }
    public function remove_admin_footer_text($text) {
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'eshterakyar') !== false) {
            return '';
        }
        return $text;
    }
    public function remove_default_wordpress_notices() {
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'eshterakyar') !== false) {
            global $wp_filter;
            if (isset($wp_filter['admin_notices'])) {
                foreach ($wp_filter['admin_notices']->callbacks as $priority => $callbacks) {
                    foreach ($callbacks as $callback_id => $callback) {
                        if (isset($callback['function']) && is_string($callback['function'])) {
                            if (strpos($callback['function'], 'wp_') === 0 || 
                                strpos($callback['function'], '_wp_') === 0 ||
                                strpos($callback['function'], 'default_') === 0) {
                                remove_action('admin_notices', $callback['function'], $priority);
                            }
                        } elseif (isset($callback['function']) && is_array($callback['function'])) {
                            if (isset($callback['function'][0]) && is_object($callback['function'][0])) {
                                $class_name = get_class($callback['function'][0]);
                                if (strpos($class_name, 'WP_') === 0 || 
                                    strpos($class_name, '_WP_') === 0 ||
                                    strpos($class_name, 'WP\\') === 0) {
                                    remove_action('admin_notices', $callback['function'], $priority);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function remove_default_wordpress_notices_js() {
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'eshterakyar') !== false) {
            ?>
            <script>
            jQuery(document).ready(function($) {
                function removeDefaultNotices() {
                    $('.notice:not(.eshterakyar-notice)').each(function() {
                        var $notice = $(this);
                        if (!$notice.hasClass('eshterakyar-notice') && 
                            !$notice.closest('.eshterakyar-plans-page').length &&
                            !$notice.closest('.eshterakyar-settings-content').length) {
                            var noticeText = $notice.text();
                            if (noticeText.indexOf('پلن با موفقیت') === -1 && 
                                noticeText.indexOf('تنظیمات با موفقیت') === -1 &&
                                noticeText.indexOf('خطا در ذخیره پلن') === -1 &&
                                noticeText.indexOf('پلن با موفقیت حذف شد') === -1) {
                                $notice.remove();
                            }
                        }
                    });
                    $('.update-nag, .error:not(.eshterakyar-notice-error), .updated:not(.eshterakyar-notice-success)').not('.eshterakyar-notice').remove();
                }
                
                removeDefaultNotices();
                
                setTimeout(removeDefaultNotices, 100);
                setTimeout(removeDefaultNotices, 300);
                setTimeout(removeDefaultNotices, 500);
                
                if (typeof MutationObserver !== 'undefined') {
                    var observer = new MutationObserver(function(mutations) {
                        removeDefaultNotices();
                    });
                    
                    observer.observe(document.body, {
                        childList: true,
                        subtree: true
                    });
                }
            });
            </script>
            <?php
        }
    }

    /**
     * Chart data for dashboard (last 7 days).
     *
     * @return array<int, array{date: string, date_label: string, count: int}>
     */
    private function get_dashboard_chart_data() {
        global $wpdb;

        $chart_data = array();
        for ($i = 6; $i >= 0; $i--) {
            $date_timestamp = strtotime("-{$i} days");
            $date = date('Y-m-d', $date_timestamp);
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}subscriptions WHERE DATE(created_at) = %s",
                $date
            ));

            if (function_exists('wp_date')) {
                $date_format = get_option('date_format', 'Y/m/d');
                $date_label = wp_date($date_format, $date_timestamp);
            } else {
                $date_format = get_option('date_format', 'Y/m/d');
                $date_label = date_i18n($date_format, $date_timestamp);
            }

            $chart_data[] = array(
                'date' => $date,
                'date_label' => $date_label,
                'count' => intval($count),
            );
        }

        return $chart_data;
    }

    /**
     * Enqueue Chart.js (bundled) and dashboard chart initializer.
     *
     * @param array<string, mixed> $stats
     * @param array<int, array<string, mixed>> $chart_data
     */
    private function enqueue_dashboard_charts(array $stats, array $chart_data) {
        wp_enqueue_script(
            'chart-js',
            ESHTERAKYAR_PLUGIN_URL . 'assets/js/vendor/chart.min.js',
            array(),
            '3.9.1',
            true
        );
        wp_enqueue_script(
            'eshterakyar-dashboard-charts',
            ESHTERAKYAR_PLUGIN_URL . 'assets/js/dashboard-charts.js',
            array('chart-js', 'jquery'),
            ESHTERAKYAR_VERSION,
            true
        );

        $total = intval($stats['total_subscriptions']);
        $active = intval($stats['active_subscriptions']);
        $expired = intval($stats['expired_subscriptions']);
        $cancelled = max(0, $total - $active - $expired);

        wp_localize_script('eshterakyar-dashboard-charts', 'eshterakyar_dashboard_charts', array(
            'labels' => array_column($chart_data, 'date_label'),
            'counts' => array_column($chart_data, 'count'),
            'status' => array(
                'active' => $active,
                'expired' => $expired,
                'cancelled' => $cancelled,
            ),
            'i18n' => array(
                'subscriptionsLabel' => __('تعداد اشتراک', 'eshterakyar'),
                'active' => __('فعال', 'eshterakyar'),
                'expired' => __('منقضی شده', 'eshterakyar'),
                'cancelled' => __('لغو شده', 'eshterakyar'),
            ),
            'renderCharts' => $total > 0,
        ));
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'eshterakyar') !== false) {
            wp_enqueue_style(
                'eshterakyar-admin-variables',
                ESHTERAKYAR_PLUGIN_URL . 'assets/css/admin-variables.css',
                array(),
                ESHTERAKYAR_VERSION
            );
            wp_enqueue_style(
                'eshterakyar-admin',
                ESHTERAKYAR_PLUGIN_URL . 'assets/css/admin.css',
                array('eshterakyar-admin-variables'),
                ESHTERAKYAR_VERSION
            );
            $needs_datepicker = (
                strpos($hook, 'eshterakyar-subscriptions') !== false
                || strpos($hook, 'eshterakyar-downloads') !== false
                || strpos($hook, 'eshterakyar-plans') !== false
            );
            if ($needs_datepicker) {
                wp_enqueue_style(
                    'jalalidatepicker-css',
                    'https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css',
                    array(),
                    '1.4.0'
                );
                wp_enqueue_script(
                    'jalaali-js',
                    'https://cdn.jsdelivr.net/npm/jalaali-js@1.2.0/dist/jalaali.min.js',
                    array(),
                    '1.2.0',
                    true
                );
                wp_enqueue_script(
                    'jalalidatepicker-js',
                    'https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js',
                    array('jquery', 'jalaali-js'),
                    '1.4.0',
                    true
                );
            }
            $admin_deps = array('jquery', 'jquery-ui-sortable');
            if ($needs_datepicker) {
                $admin_deps[] = 'jalalidatepicker-js';
                $admin_deps[] = 'jalaali-js';
            }
            wp_enqueue_script(
                'eshterakyar-admin',
                ESHTERAKYAR_PLUGIN_URL . 'assets/js/admin.js',
                $admin_deps,
                ESHTERAKYAR_VERSION,
                true
            );
            $has_parsidate = function_exists('parsidate');
            $has_gregdate = function_exists('gregdate');
            $has_persian_date = function_exists('persian_date');
            $has_persian_to_gregorian = function_exists('persian_to_gregorian');
            
            wp_localize_script('eshterakyar-admin', 'eshterakyar_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('eshterakyar_admin_nonce'),
                'date_format' => get_option('date_format', 'Y/m/d'),
                'has_persian_date' => ($has_parsidate || $has_persian_date),
                'has_parsidate' => $has_parsidate,
                'has_gregdate' => $has_gregdate,
                'has_persian_to_gregorian' => ($has_gregdate || $has_persian_to_gregorian),
            ));
        }
        if ($hook === 'plugins.php') {
            wp_enqueue_script(
                'eshterakyar-admin-script',
                ESHTERAKYAR_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                ESHTERAKYAR_VERSION,
                true
            );
            
            wp_localize_script('eshterakyar-admin-script', 'eshterakyar_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('eshterakyar_admin_nonce')
            ));
        }
    }
    public function ajax_set_delete_data_on_deactivate() {
        if (!isset($_POST['nonce']) || empty($_POST['nonce'])) {
            wp_send_json_error(array('message' => __('خطا در تأیید هویت.', 'eshterakyar')));
        }
        
        if (!wp_verify_nonce($_POST['nonce'], 'eshterakyar_admin_nonce')) {
            wp_send_json_error(array('message' => __('خطا در تأیید هویت.', 'eshterakyar')));
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز انجام این کار را ندارید.', 'eshterakyar')));
        }
        
        $delete_data = isset($_POST['delete_data']) ? sanitize_text_field($_POST['delete_data']) : 'no';
        
        update_option('eshterakyar_delete_data_on_deactivate', $delete_data === 'yes' ? true : false);
        
        wp_send_json_success(array('message' => __('تنظیمات ذخیره شد.', 'eshterakyar')));
    }
    public function ajax_get_nonce() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز انجام این کار را ندارید.', 'eshterakyar')));
        }
        
        wp_send_json_success(array('nonce' => wp_create_nonce('eshterakyar_admin_nonce')));
    }
    public function ajax_convert_persian_to_gregorian() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز انجام این کار را ندارید.', 'eshterakyar')));
        }
        
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'eshterakyar_admin_nonce')) {
            wp_send_json_error(array('message' => __('خطا در تأیید هویت.', 'eshterakyar')));
        }
        
        $persian_date = isset($_POST['persian_date']) ? sanitize_text_field($_POST['persian_date']) : '';
        
        if (empty($persian_date)) {
            wp_send_json_error(array('message' => __('تاریخ وارد نشده است.', 'eshterakyar')));
        }
        $gregorian_date = $this->persian_to_gregorian($persian_date);
        
        if (empty($gregorian_date)) {
            wp_send_json_error(array('message' => __('خطا در تبدیل تاریخ.', 'eshterakyar')));
        }
        
        wp_send_json_success(array('gregorian_date' => $gregorian_date));
    }

    public function ajax_search_users() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز انجام این کار را ندارید.', 'eshterakyar')));
        }

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'eshterakyar_admin_nonce')) {
            wp_send_json_error(array('message' => __('خطا در تأیید هویت.', 'eshterakyar')));
        }

        $term = isset($_POST['term']) ? sanitize_text_field(wp_unslash($_POST['term'])) : '';
        $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        $args = array(
            'number'  => $per_page + 1,
            'offset'  => $offset,
            'orderby' => 'display_name',
            'order'   => 'ASC',
        );

        if ($term !== '') {
            $args['search'] = '*' . $term . '*';
            $args['search_columns'] = array('user_login', 'user_nicename', 'user_email', 'display_name');
        }

        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();

        $has_more = false;
        if (count($users) > $per_page) {
            $has_more = true;
            $users = array_slice($users, 0, $per_page);
        }

        $items = array();
        foreach ($users as $user) {
            $items[] = array(
                'id'   => $user->ID,
                'text' => $user->display_name . ' (' . $user->user_email . ')',
            );
        }

        wp_send_json_success(array(
            'items'    => $items,
            'has_more' => $has_more,
            'page'     => $page,
        ));
    }

    public function ajax_auto_membership_batch() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز انجام این کار را ندارید.', 'eshterakyar')));
        }

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'eshterakyar_admin_nonce')) {
            wp_send_json_error(array('message' => __('خطا در تأیید هویت.', 'eshterakyar')));
        }

        if (!class_exists('Eshterakyar_Auto_Membership')) {
            wp_send_json_error(array('message' => __('ماژول عضویت خودکار در دسترس نیست.', 'eshterakyar')));
        }

        if (!Eshterakyar_Auto_Membership::is_enabled()) {
            wp_send_json_error(array('message' => __('عضویت خودکار فعال نیست یا پلن معتبر انتخاب نشده است.', 'eshterakyar')));
        }

        $options = Eshterakyar_Auto_Membership::get_options();
        if (empty($options['auto_membership_existing_users'])) {
            wp_send_json_error(array('message' => __('گزینه «کاربران موجود» در تنظیمات فعال نیست.', 'eshterakyar')));
        }

        $offset = isset($_POST['offset']) ? max(0, intval($_POST['offset'])) : 0;
        $stats = Eshterakyar_Auto_Membership::process_batch($offset, Eshterakyar_Auto_Membership::BATCH_SIZE);

        wp_send_json_success(array(
            'stats' => $stats,
            'last_batch' => get_option('eshterakyar_auto_membership_batch_completed_at', ''),
        ));
    }

    public function ajax_auto_membership_count() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز انجام این کار را ندارید.', 'eshterakyar')));
        }

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'eshterakyar_admin_nonce')) {
            wp_send_json_error(array('message' => __('خطا در تأیید هویت.', 'eshterakyar')));
        }

        if (!class_exists('Eshterakyar_Auto_Membership')) {
            wp_send_json_error(array('message' => __('ماژول عضویت خودکار در دسترس نیست.', 'eshterakyar')));
        }

        $force = !empty($_POST['force_refresh']);
        $count = Eshterakyar_Auto_Membership::count_eligible_users($force);

        wp_send_json_success(array(
            'count' => $count,
        ));
    }

    public function ajax_create_manual_subscription() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز انجام این کار را ندارید.', 'eshterakyar')));
        }
        
        if (!isset($_POST['eshterakyar_manual_subscription_nonce']) || !check_admin_referer('eshterakyar_create_manual_subscription', 'eshterakyar_manual_subscription_nonce')) {
            wp_send_json_error(array('message' => __('خطا در تأیید هویت.', 'eshterakyar')));
        }
        $user_id = isset($_POST['manual_user_id']) ? intval($_POST['manual_user_id']) : 0;
        $plan_id = isset($_POST['manual_plan_id']) ? intval($_POST['manual_plan_id']) : 0;
        
        if (!$user_id || !$plan_id) {
            wp_send_json_error(array('message' => __('لطفاً کاربر و پلن را انتخاب کنید.', 'eshterakyar')));
        }
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error(array('message' => __('کاربر یافت نشد.', 'eshterakyar')));
        }
        $plan = Eshterakyar_Plan::get_by_id($plan_id);
        if (!$plan) {
            wp_send_json_error(array('message' => __('پلن یافت نشد.', 'eshterakyar')));
        }
        $start_date = isset($_POST['manual_start_date']) ? sanitize_text_field($_POST['manual_start_date']) : '';
        $end_date   = isset($_POST['manual_end_date']) ? sanitize_text_field($_POST['manual_end_date']) : '';

        $raw_start_persian = isset($_POST['manual_start_date_persian']) ? sanitize_text_field($_POST['manual_start_date_persian']) : '';
        $raw_end_persian   = isset($_POST['manual_end_date_persian']) ? sanitize_text_field($_POST['manual_end_date_persian']) : '';

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('Manual subscription (AJAX) - incoming dates', array(
                'user_id'            => $user_id,
                'plan_id'            => $plan_id,
                'start_date_raw'     => $start_date,
                'end_date_raw'       => $end_date,
                'start_persian_raw'  => $raw_start_persian,
                'end_persian_raw'    => $raw_end_persian,
            ));
        }

        if (!empty($raw_start_persian) && preg_match('/^(\d{4})\/(\d{2})\/(\d{2})/', $raw_start_persian, $m)) {
            $jy = intval($m[1], 10);
            if ($jy >= 1200 && $jy <= 1600) {
                $start_date = $this->persian_to_gregorian($raw_start_persian);
                if ($start_date && !preg_match('/\d{2}:\d{2}/', $start_date)) {
                    $parts = explode(' ', $raw_start_persian);
                    $time  = isset($parts[1]) ? $parts[1] : '00:00:00';
                    if (strlen($time) === 5) {
                        $time .= ':00';
                    }
                    $start_date = $start_date . ' ' . $time;
                }
            }
        }
        
        if (!empty($raw_end_persian) && preg_match('/^(\d{4})\/(\d{2})\/(\d{2})/', $raw_end_persian, $m2)) {
            $jy2 = intval($m2[1], 10);
            if ($jy2 >= 1200 && $jy2 <= 1600) {
                $end_date = $this->persian_to_gregorian($raw_end_persian);
                if ($end_date && !preg_match('/\d{2}:\d{2}/', $end_date)) {
                    $parts = explode(' ', $raw_end_persian);
                    $time  = isset($parts[1]) ? $parts[1] : '00:00:00';
                    if (strlen($time) === 5) {
                        $time .= ':00';
                    }
                    $end_date = $end_date . ' ' . $time;
                }
            }
        }

        $start_timestamp = $start_date ? strtotime($start_date) : false;
        if (!$start_timestamp) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Manual subscription (AJAX) - invalid start_date, fallback to current_time', array(
                    'start_date_before' => $start_date,
                    'start_persian'     => $raw_start_persian,
                ));
            }
            $start_timestamp = current_time('timestamp');
        }
        $start_date = date('Y-m-d H:i:s', $start_timestamp);

        $end_timestamp = $end_date ? strtotime($end_date) : false;
        if (!$end_timestamp && !empty($end_date)) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Manual subscription (AJAX) - invalid end_date, discarding and recalculating from plan', array(
                    'end_date_before' => $end_date,
                    'end_persian'     => $raw_end_persian,
                ));
            }
            $end_timestamp = false;
        }

        if (!$end_timestamp) {
            $end_timestamp = strtotime('+'. (int) $plan->duration_days .' days', $start_timestamp);
        }

        if ($end_timestamp < $start_timestamp) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Manual subscription (AJAX) - end_date before start_date, adjusting', array(
                    'start_date'       => $start_date,
                    'end_date_before'  => date('Y-m-d H:i:s', $end_timestamp),
                    'plan_days'        => $plan->duration_days,
                ));
            }
            $end_timestamp = strtotime('+'. (int) $plan->duration_days .' days', $start_timestamp);
        }

        $end_date = date('Y-m-d H:i:s', $end_timestamp);

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('Manual subscription (AJAX) - normalized dates', array(
                'normalized_start_date' => $start_date,
                'normalized_end_date'   => $end_date,
            ));
        }
        
        $status = isset($_POST['manual_status']) ? sanitize_text_field($_POST['manual_status']) : 'active';
        global $wpdb;
        $table = $wpdb->prefix . 'subscriptions';
        
        $data = array(
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'order_id' => null,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $status,
            'is_active' => ($status === 'active') ? 1 : 0,
            'total_downloads' => $plan->total_limit,
            'daily_limit' => $plan->daily_limit,
            'downloads_used' => 0,
            'daily_downloads_count' => 0,
            'last_download_date' => null
        );
        
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            $subscription_id = $wpdb->insert_id;
            
            Eshterakyar_Subscription::check_and_update_status($user_id);
            
            if (class_exists('Eshterakyar_Cache')) {
                Eshterakyar_Cache::clear_user_subscription_cache($user_id);
            }
            
            $options = get_option('eshterakyar_options', array());
            if (isset($options['enable_notifications']) && $options['enable_notifications']) {
                Eshterakyar_Notifications::send_purchase_notification($user_id, $subscription_id);
            }
            
            wp_send_json_success(array('message' => __('اشتراک با موفقیت ایجاد شد.', 'eshterakyar')));
        } else {
            wp_send_json_error(array('message' => __('خطا در ایجاد اشتراک. لطفاً دوباره تلاش کنید.', 'eshterakyar')));
        }
    }
    public function ajax_save_plans_order() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز انجام این کار را ندارید.', 'eshterakyar')));
        }
        
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'eshterakyar_save_plans_order')) {
            wp_send_json_error(array('message' => __('خطا در تأیید هویت.', 'eshterakyar')));
        }
        
        if (!isset($_POST['plan_ids']) || !is_array($_POST['plan_ids'])) {
            wp_send_json_error(array('message' => __('داده‌های ارسالی نامعتبر است.', 'eshterakyar')));
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_plans';
        
        $table_columns = $wpdb->get_col("DESC `{$table}`", 0);
        if (!in_array('sort_order', $table_columns, true)) {
            $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN sort_order int(11) NOT NULL DEFAULT 0");
            $table_columns[] = 'sort_order';
        }
        
        $plan_ids = array_map('intval', $_POST['plan_ids']);
        $plan_ids = array_filter($plan_ids);
        
        if (empty($plan_ids)) {
            wp_send_json_error(array('message' => __('هیچ پلن معتبری ارسال نشده است.', 'eshterakyar')));
        }
        
        $sort_order = 1;
        foreach ($plan_ids as $plan_id) {
            $wpdb->update(
                $table,
                array('sort_order' => $sort_order),
                array('id' => $plan_id),
                array('%d'),
                array('%d')
            );
            $sort_order++;
        }
        
        if (class_exists('Eshterakyar_Cache')) {
            Eshterakyar_Cache::delete('plans_all');
            Eshterakyar_Cache::delete('plans_active');
            Eshterakyar_Cache::delete('plans_inactive');
        }
        wp_cache_delete('plans_all', 'eshterakyar');
        wp_cache_delete('plans_active', 'eshterakyar');
        wp_cache_delete('plans_inactive', 'eshterakyar');
        
        wp_send_json_success(array('message' => __('ترتیب پلن‌ها با موفقیت ذخیره شد.', 'eshterakyar')));
    }

    public function ajax_toggle_recommended_plan() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('شما مجوز انجام این کار را ندارید.', 'eshterakyar')));
        }
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'eshterakyar_toggle_recommended_plan')) {
            wp_send_json_error(array('message' => __('خطا در تأیید هویت.', 'eshterakyar')));
        }

        $plan_id = isset($_POST['plan_id']) ? (int) $_POST['plan_id'] : 0;
        $recommended = isset($_POST['recommended']) ? (int) $_POST['recommended'] : 0;
        if ($plan_id <= 0) {
            wp_send_json_error(array('message' => __('شناسه پلن نامعتبر است.', 'eshterakyar')));
        }
        $recommended = $recommended ? 1 : 0;

        global $wpdb;
        $table = $wpdb->prefix . 'subscription_plans';
        $cols = $wpdb->get_col("DESC `{$table}`", 0);
        if (!in_array('is_recommended', $cols, true)) {
            $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN is_recommended tinyint(1) DEFAULT 0");
        }

        if ($recommended === 1) {
            $wpdb->query($wpdb->prepare("UPDATE `{$table}` SET is_recommended = 0 WHERE id != %d", $plan_id));
        }
        $res = $wpdb->update($table, array('is_recommended' => $recommended), array('id' => $plan_id), array('%d'), array('%d'));
        if ($res === false) {
            wp_send_json_error(array('message' => __('خطا در ذخیره‌سازی.', 'eshterakyar')));
        }

        if (class_exists('Eshterakyar_Cache')) {
            Eshterakyar_Cache::delete('plans_all');
            Eshterakyar_Cache::delete('plans_active');
            Eshterakyar_Cache::delete('plans_inactive');
        }
        wp_cache_delete('plans_all', 'eshterakyar');
        wp_cache_delete('plans_active', 'eshterakyar');
        wp_cache_delete('plans_inactive', 'eshterakyar');

        wp_send_json_success(array('plan_id' => $plan_id, 'recommended' => $recommended));
    }
    public function add_admin_menu() {
        add_menu_page(
            __('اشتراک یار', 'eshterakyar'),
            __('اشتراک یار', 'eshterakyar'),
            'manage_options',
            'eshterakyar',
            array($this, 'dashboard_page'),
            'dashicons-groups',
            30
        );
        
        add_submenu_page(
            'eshterakyar',
            __('داشبورد', 'eshterakyar'),
            __('داشبورد', 'eshterakyar'),
            'manage_options',
            'eshterakyar',
            array($this, 'dashboard_page')
        );
        
        add_submenu_page(
            'eshterakyar',
            __('پلن‌های اشتراک', 'eshterakyar'),
            __('پلن‌ها', 'eshterakyar'),
            'manage_options',
            'eshterakyar-plans',
            array($this, 'plans_page')
        );
        
        add_submenu_page(
            'eshterakyar',
            __('مشتریان', 'eshterakyar'),
            __('مشتریان', 'eshterakyar'),
            'manage_options',
            'eshterakyar-customers',
            array($this, 'customers_page')
        );
        
        add_submenu_page(
            'eshterakyar',
            __('اشتراک‌ها', 'eshterakyar'),
            __('اشتراک‌ها', 'eshterakyar'),
            'manage_options',
            'eshterakyar-subscriptions',
            array($this, 'subscriptions_page')
        );
        
        add_submenu_page(
            'eshterakyar',
            __('دانلودها', 'eshterakyar'),
            __('دانلودها', 'eshterakyar'),
            'manage_options',
            'eshterakyar-downloads',
            array($this, 'downloads_page')
        );
        
        add_submenu_page(
            'eshterakyar',
            __('تنظیمات', 'eshterakyar'),
            __('تنظیمات', 'eshterakyar'),
            'manage_options',
            'eshterakyar-settings',
            array($this, 'settings_page')
        );
        
        add_submenu_page(
            'eshterakyar',
            __('بازخورد و پیشنهادات', 'eshterakyar'),
            __('بازخورد', 'eshterakyar'),
            'manage_options',
            'eshterakyar-feedback',
            array($this, 'feedback_page')
        );
        
        $show_logs_menu = false;
        if (defined('ESHTERAKYAR_DEBUG_LOGS_ENABLED')) {
            $show_logs_menu = ESHTERAKYAR_DEBUG_LOGS_ENABLED ? true : false;
        } else {
            $show_logs_menu = (defined('WP_DEBUG') && WP_DEBUG);
        }

        if ($show_logs_menu) {
            add_submenu_page(
                'eshterakyar',
                __('لاگ‌ها', 'eshterakyar'),
                __('لاگ‌ها', 'eshterakyar'),
                'manage_options',
                'eshterakyar-logs',
                array($this, 'logs_page')
            );
        }
    }
    
    public function register_settings() {
        register_setting('eshterakyar_settings', 'eshterakyar_options', array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));
        
        register_setting('eshterakyar_settings', 'eshterakyar_delete_data_on_uninstall');
        
        add_settings_section(
            'eshterakyar_general',
            __('تنظیمات عمومی', 'eshterakyar'),
            array($this, 'general_section_callback'),
            'eshterakyar_settings'
        );
        
        add_settings_field(
            'enable_notifications',
            __('فعال‌سازی اطلاع‌رسانی', 'eshterakyar'),
            array($this, 'checkbox_field_callback'),
            'eshterakyar_settings',
            'eshterakyar_general',
            array('field' => 'enable_notifications', 'label' => __('ارسال ایمیل و پیامک', 'eshterakyar'))
        );
        
        add_settings_field(
            'days_before_expiry',
            __('روز قبل از انقضا برای هشدار', 'eshterakyar'),
            array($this, 'number_field_callback'),
            'eshterakyar_settings',
            'eshterakyar_general',
            array('field' => 'days_before_expiry', 'default' => 7)
        );
        
        add_settings_section(
            'eshterakyar_email',
            __('تنظیمات ایمیل', 'eshterakyar'),
            array($this, 'email_section_callback'),
            'eshterakyar_settings'
        );
        
        add_settings_field(
            'email_subject_purchase',
            __('موضوع ایمیل خرید', 'eshterakyar'),
            array($this, 'text_field_callback'),
            'eshterakyar_settings',
            'eshterakyar_email',
            array('field' => 'email_subject_purchase', 'default' => __('خرید اشتراک شما با موفقیت انجام شد', 'eshterakyar'))
        );
        
        add_settings_field(
            'email_subject_expiry_warning',
            __('موضوع ایمیل هشدار انقضا', 'eshterakyar'),
            array($this, 'text_field_callback'),
            'eshterakyar_settings',
            'eshterakyar_email',
            array('field' => 'email_subject_expiry_warning', 'default' => __('اشتراک شما به زودی منقضی می‌شود', 'eshterakyar'))
        );
        
        add_settings_field(
            'email_subject_expired',
            __('موضوع ایمیل انقضا', 'eshterakyar'),
            array($this, 'text_field_callback'),
            'eshterakyar_settings',
            'eshterakyar_email',
            array('field' => 'email_subject_expired', 'default' => __('اشتراک شما منقضی شد', 'eshterakyar'))
        );
        
        add_settings_section(
            'eshterakyar_sms',
            __('تنظیمات پیامک', 'eshterakyar'),
            array($this, 'sms_section_callback'),
            'eshterakyar_settings'
        );
        
        add_settings_field(
            'sms_api_key',
            __('کلید API پیامک', 'eshterakyar'),
            array($this, 'text_field_callback'),
            'eshterakyar_settings',
            'eshterakyar_sms',
            array('field' => 'sms_api_key', 'type' => 'password')
        );
        
        add_settings_field(
            'sms_sender',
            __('شماره فرستنده', 'eshterakyar'),
            array($this, 'text_field_callback'),
            'eshterakyar_settings',
            'eshterakyar_sms',
            array('field' => 'sms_sender')
        );
    }
    
    public function dashboard_page() {
        global $wpdb;
        
        $options = get_option('eshterakyar_options', array());
        $subscriptions_table = $wpdb->prefix . 'subscriptions';
        $plans_table = $wpdb->prefix . 'subscription_plans';
        $downloads_table = $wpdb->prefix . 'subscription_downloads';
        
        $stats = array(
            'total_subscriptions' => $wpdb->get_var("SELECT COUNT(*) FROM $subscriptions_table"),
            'active_subscriptions' => $wpdb->get_var("SELECT COUNT(*) FROM $subscriptions_table WHERE status = 'active' AND end_date > NOW()"),
            'expired_subscriptions' => $wpdb->get_var("SELECT COUNT(*) FROM $subscriptions_table WHERE status = 'expired'"),
            'total_plans' => $wpdb->get_var("SELECT COUNT(*) FROM $plans_table WHERE status = 'active'"),
            'total_downloads' => $wpdb->get_var("SELECT COUNT(*) FROM $downloads_table"),
        );

        $chart_data = $this->get_dashboard_chart_data();
        $this->enqueue_dashboard_charts($stats, $chart_data);
        
        $this->render_page_with_wrapper('dashboard.php', array('stats' => $stats, 'chart_data' => $chart_data));
    }
    
    public function customers_page() {
        global $wpdb;
        
        if (isset($_POST['bulk_action']) && isset($_POST['customer_ids']) && check_admin_referer('eshterakyar_bulk_action', 'eshterakyar_bulk_nonce')) {
            $this->handle_bulk_action($_POST['bulk_action'], $_POST['customer_ids']);
        }
        
        $search = isset($_GET['search']) ? wp_unslash(sanitize_text_field(wp_unslash($_GET['search']))) : '';
        $subscription_status = isset($_GET['subscription_status']) ? sanitize_text_field($_GET['subscription_status']) : '';
        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 10;
        $offset = ($paged - 1) * $per_page;
        
        $where = "WHERE 1=1";
        $params = array();
        
        if ($search) {
            $where .= " AND (u.display_name LIKE %s OR u.user_email LIKE %s OR um.meta_value LIKE %s)";
            $search_like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $search_like;
            $params[] = $search_like;
            $params[] = $search_like;
        }
        
        $query = "SELECT DISTINCT u.ID as user_id, u.display_name, u.user_email,
                         MAX(CASE WHEN um.meta_key = 'billing_phone' THEN um.meta_value END) as billing_phone,
                         COUNT(DISTINCT CASE WHEN s.status = 'active' AND s.end_date > NOW() THEN s.id END) as active_count,
                         COUNT(DISTINCT CASE WHEN s.status = 'expired' THEN s.id END) as expired_count,
                         COUNT(DISTINCT d.id) as total_downloads
                  FROM {$wpdb->users} u
                  INNER JOIN {$wpdb->prefix}subscriptions s ON u.ID = s.user_id
                  LEFT JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
                  LEFT JOIN {$wpdb->prefix}subscription_downloads d ON u.ID = d.user_id
                  $where
                  GROUP BY u.ID, u.display_name, u.user_email";
        
        if ($subscription_status == 'has_active') {
            $query .= " HAVING active_count > 0";
        } elseif ($subscription_status == 'has_expired') {
            $query .= " HAVING expired_count > 0 AND active_count = 0";
        }
        
        $query .= " ORDER BY u.display_name ASC";
        
        $count_query = "SELECT COUNT(DISTINCT u.ID)
                  FROM {$wpdb->users} u
                  INNER JOIN {$wpdb->prefix}subscriptions s ON u.ID = s.user_id
                  LEFT JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
                  $where";
        
        if ($subscription_status == 'has_active') {
            $count_query .= " AND EXISTS (SELECT 1 FROM {$wpdb->prefix}subscriptions s2 WHERE s2.user_id = u.ID AND s2.status = 'active' AND s2.end_date > NOW())";
        } elseif ($subscription_status == 'has_expired') {
            $count_query .= " AND EXISTS (SELECT 1 FROM {$wpdb->prefix}subscriptions s2 WHERE s2.user_id = u.ID AND s2.status = 'expired')";
            $count_query .= " AND NOT EXISTS (SELECT 1 FROM {$wpdb->prefix}subscriptions s3 WHERE s3.user_id = u.ID AND s3.status = 'active' AND s3.end_date > NOW())";
        }
        
        if (!empty($params)) {
            $total_customers = $wpdb->get_var($wpdb->prepare($count_query, $params));
            $customers = $wpdb->get_results($wpdb->prepare($query . " LIMIT %d OFFSET %d", array_merge($params, array($per_page, $offset))));
        } else {
            $total_customers = $wpdb->get_var($count_query);
            $customers = $wpdb->get_results($query . $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset));
        }
        
        if (!empty($customers)) {
            $user_ids = wp_list_pluck($customers, 'user_id');
            if (function_exists('cache_users')) {
                cache_users($user_ids);
            }
        }
        
        $customers_with_active = $wpdb->get_var(
            "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}subscriptions WHERE status = 'active' AND end_date > NOW()"
        );
        
        $total_pages = ceil($total_customers / $per_page);
        $pagination = $this->get_pagination($paged, $total_pages);
        
        $this->render_page_with_wrapper('customers.php', array(
            'customers' => $customers,
            'total_customers' => $total_customers,
            'pagination' => $pagination,
            'customers_with_active' => $customers_with_active
        ));
    }
    private function handle_bulk_action($action, $customer_ids) {
        switch ($action) {
            case 'export':
                $this->export_customers($customer_ids);
                break;
        }
    }
    private function export_customers($customer_ids) {
        wp_redirect(admin_url('admin.php?page=eshterakyar-customers&exported=1'));
        exit;
    }
    public function plans_page() {
        if (isset($_POST['action']) && $_POST['action'] == 'save_plan') {
            check_admin_referer('eshterakyar_plan_nonce');
            $this->save_plan();
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['plan_id'])) {
            check_admin_referer('delete_plan_' . $_GET['plan_id']);
            Eshterakyar_Plan::delete(intval($_GET['plan_id']));
            wp_redirect(admin_url('admin.php?page=eshterakyar-plans&deleted=1'));
            exit;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'duplicate' && isset($_GET['plan_id'])) {
            $dup_id = intval($_GET['plan_id']);
            if (wp_verify_nonce(isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '', 'duplicate_plan_' . $dup_id)) {
                $source = Eshterakyar_Plan::get_by_id($dup_id);
                if ($source) {
                    $data = array(
                        'name' => sprintf(__('کپی از %s', 'eshterakyar'), $source->name),
                        'description' => isset($source->description) ? $source->description : '',
                        'price' => isset($source->price) ? intval($source->price) : 0,
                        'sale_price' => isset($source->sale_price) && $source->sale_price !== null && $source->sale_price !== '' ? floatval($source->sale_price) : null,
                        'duration_days' => isset($source->duration_days) ? intval($source->duration_days) : 30,
                        'daily_limit' => isset($source->daily_limit) ? intval($source->daily_limit) : 0,
                        'total_limit' => isset($source->total_limit) ? intval($source->total_limit) : 0,
                        'product_id' => isset($source->product_id) && $source->product_id > 0 ? $source->product_id : null,
                        'status' => (isset($source->status) && in_array($source->status, array('active', 'inactive'), true)) ? $source->status : 'active',
                        'merge_with_existing' => isset($source->merge_with_existing) && $source->merge_with_existing ? 1 : 0,
                        'subscription_group_id' => isset($source->subscription_group_id) ? $source->subscription_group_id : '',
                        'allowed_categories' => isset($source->allowed_categories) ? $source->allowed_categories : '',
                        'exclude_categories' => isset($source->exclude_categories) ? $source->exclude_categories : '',
                        'include_children_allowed_categories' => isset($source->include_children_allowed_categories) ? (int) $source->include_children_allowed_categories : 1,
                        'include_children_exclude_categories' => isset($source->include_children_exclude_categories) ? (int) $source->include_children_exclude_categories : 1,
                        'is_recommended' => 0,
                        'allowed_tags' => isset($source->allowed_tags) ? $source->allowed_tags : '',
                        'exclude_tags' => isset($source->exclude_tags) ? $source->exclude_tags : '',
                        'allowed_post_categories' => isset($source->allowed_post_categories) ? $source->allowed_post_categories : '',
                        'exclude_post_categories' => isset($source->exclude_post_categories) ? $source->exclude_post_categories : '',
                        'include_children_allowed_post_categories' => isset($source->include_children_allowed_post_categories) ? (int) $source->include_children_allowed_post_categories : 1,
                        'include_children_exclude_post_categories' => isset($source->include_children_exclude_post_categories) ? (int) $source->include_children_exclude_post_categories : 1,
                        'allowed_post_tags' => isset($source->allowed_post_tags) ? $source->allowed_post_tags : '',
                        'exclude_post_tags' => isset($source->exclude_post_tags) ? $source->exclude_post_tags : '',
                        'badge_text' => isset($source->badge_text) ? $source->badge_text : '',
                        'badge_color' => isset($source->badge_color) && $source->badge_color !== '' ? $source->badge_color : 'orange',
                        'custom_features' => isset($source->custom_features) ? $source->custom_features : null,
                        'download_access_days' => isset($source->download_access_days) ? intval($source->download_access_days) : 0,
                        'max_downloads_per_product' => isset($source->max_downloads_per_product) ? intval($source->max_downloads_per_product) : 0,
                        'lifetime_access' => isset($source->lifetime_access) && $source->lifetime_access ? 1 : 0,
                    );
                    $new_id = Eshterakyar_Plan::create($data);
                    if ($new_id && $new_id > 0) {
                        wp_redirect(admin_url('admin.php?page=eshterakyar-plans&plan_id=' . $new_id . '&duplicated=1'));
                        exit;
                    }
                }
            }
            wp_redirect(admin_url('admin.php?page=eshterakyar-plans&error=1'));
            exit;
        }
        
        $plan_id = isset($_GET['plan_id']) ? intval($_GET['plan_id']) : 0;
        $plan = $plan_id ? Eshterakyar_Plan::get_by_id($plan_id) : null;
        

        $plans = Eshterakyar_Plan::get_all('all');
        
        $this->render_page_with_wrapper('plans.php', array('plans' => $plans, 'plan' => $plan));
    }
    /**
     * فیلدهای دسته‌بندی/تگ به صورت CSV ارسال می‌شوند تا از محدودیت max_input_vars (صدها چک‌باکس) جلوگیری شود.
     *
     * @param string $post_key
     * @return string لیست شناسه‌ها با کاما
     */
    private function parse_term_ids_csv_post($post_key) {
        if (!isset($_POST[$post_key])) {
            return '';
        }
        $raw = sanitize_text_field(wp_unslash($_POST[$post_key]));
        if ($raw === '') {
            return '';
        }
        $ids = array_unique(array_filter(array_map('intval', explode(',', $raw))));
        sort($ids, SORT_NUMERIC);
        return implode(',', $ids);
    }

    private function save_plan() {
        if (!empty($_POST['eshterakyar_taxonomies_via_csv'])) {
            $allowed_categories = $this->parse_term_ids_csv_post('allowed_categories_csv');
            $exclude_categories = $this->parse_term_ids_csv_post('exclude_categories_csv');
            $allowed_tags = $this->parse_term_ids_csv_post('allowed_tags_csv');
            $exclude_tags = $this->parse_term_ids_csv_post('exclude_tags_csv');
            $allowed_post_categories = $this->parse_term_ids_csv_post('allowed_post_categories_csv');
            $exclude_post_categories = $this->parse_term_ids_csv_post('exclude_post_categories_csv');
            $allowed_post_tags = $this->parse_term_ids_csv_post('allowed_post_tags_csv');
            $exclude_post_tags = $this->parse_term_ids_csv_post('exclude_post_tags_csv');
        } else {
            $allowed_categories = isset($_POST['allowed_categories']) && is_array($_POST['allowed_categories'])
                ? implode(',', array_map('intval', $_POST['allowed_categories'])) : '';
            $exclude_categories = isset($_POST['exclude_categories']) && is_array($_POST['exclude_categories'])
                ? implode(',', array_map('intval', $_POST['exclude_categories'])) : '';
            $allowed_tags = isset($_POST['allowed_tags']) && is_array($_POST['allowed_tags'])
                ? implode(',', array_map('intval', $_POST['allowed_tags'])) : '';
            $exclude_tags = isset($_POST['exclude_tags']) && is_array($_POST['exclude_tags'])
                ? implode(',', array_map('intval', $_POST['exclude_tags'])) : '';
            $allowed_post_categories = isset($_POST['allowed_post_categories']) && is_array($_POST['allowed_post_categories'])
                ? implode(',', array_map('intval', $_POST['allowed_post_categories'])) : '';
            $exclude_post_categories = isset($_POST['exclude_post_categories']) && is_array($_POST['exclude_post_categories'])
                ? implode(',', array_map('intval', $_POST['exclude_post_categories'])) : '';
            $allowed_post_tags = isset($_POST['allowed_post_tags']) && is_array($_POST['allowed_post_tags'])
                ? implode(',', array_map('intval', $_POST['allowed_post_tags'])) : '';
            $exclude_post_tags = isset($_POST['exclude_post_tags']) && is_array($_POST['exclude_post_tags'])
                ? implode(',', array_map('intval', $_POST['exclude_post_tags'])) : '';
        }

        $sale_price_raw = isset($_POST['sale_price']) ? trim($_POST['sale_price']) : '';
        if ($sale_price_raw === '' || $sale_price_raw === '0' || $sale_price_raw === 0) {
            $sale_price_normalized = null;
        } else {
            $sale_price_normalized = intval($sale_price_raw);
            if ($sale_price_normalized <= 0) {
                $sale_price_normalized = null;
            }
        }

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => wp_kses_post($_POST['description']),
            'price' => intval($_POST['price']),
            'sale_price' => $sale_price_normalized, 
            'duration_days' => intval($_POST['duration_days']),
            'daily_limit' => intval($_POST['daily_limit']),
            'total_limit' => intval($_POST['total_limit']),
            'download_access_days' => isset($_POST['download_access_days']) ? max(0, intval($_POST['download_access_days'])) : 0,
            'max_downloads_per_product' => isset($_POST['max_downloads_per_product']) ? max(0, intval($_POST['max_downloads_per_product'])) : 0,
            'lifetime_access' => isset($_POST['lifetime_access']) ? 1 : 0,
            'product_id' => !empty($_POST['product_id']) ? intval($_POST['product_id']) : null,
            'status' => sanitize_text_field($_POST['status']),
            'merge_with_existing' => isset($_POST['merge_with_existing']) ? 1 : 0,
            'subscription_group_id' => isset($_POST['subscription_group_id']) ? preg_replace('/[^a-zA-Z0-9\-_]/', '', sanitize_text_field($_POST['subscription_group_id'])) : '',
            'allowed_categories' => $allowed_categories,
            'exclude_categories' => $exclude_categories,
            'include_children_allowed_categories' => isset($_POST['include_children_allowed_categories']) ? 1 : 0,
            'include_children_exclude_categories' => isset($_POST['include_children_exclude_categories']) ? 1 : 0,
            'is_recommended' => isset($_POST['is_recommended']) ? 1 : 0,
            'allowed_tags' => $allowed_tags,
            'exclude_tags' => $exclude_tags,
            'allowed_post_categories' => $allowed_post_categories,
            'exclude_post_categories' => $exclude_post_categories,
            'include_children_allowed_post_categories' => isset($_POST['include_children_allowed_post_categories']) ? 1 : 0,
            'include_children_exclude_post_categories' => isset($_POST['include_children_exclude_post_categories']) ? 1 : 0,
            'allowed_post_tags' => $allowed_post_tags,
            'exclude_post_tags' => $exclude_post_tags,
            'badge_text' => isset($_POST['badge_text']) ? sanitize_text_field(trim($_POST['badge_text'])) : '',
            'badge_color' => isset($_POST['badge_color']) && !empty($_POST['badge_color']) ? sanitize_text_field($_POST['badge_color']) : 'orange',
            'custom_features' => isset($_POST['custom_features']) ? sanitize_textarea_field($_POST['custom_features']) : ''
        );
        
        $plan_id = isset($_POST['plan_id']) && $_POST['plan_id'] > 0 ? intval($_POST['plan_id']) : null;
        $old_product_id = null;
        if ($plan_id) {
            $existing_plan = Eshterakyar_Plan::get_by_id($plan_id);
            $old_product_id = ($existing_plan && isset($existing_plan->product_id) && $existing_plan->product_id > 0) ? (int) $existing_plan->product_id : null;
        }

        if ($plan_id) {
            $result = Eshterakyar_Plan::update($plan_id, $data);
            if ($result !== false) {
                if (class_exists('Eshterakyar_Logs')) {
                    Eshterakyar_Logs::log_debug('Plan updated', array(
                        'plan_id' => $plan_id,
                        'taxonomies_csv' => !empty($_POST['eshterakyar_taxonomies_via_csv']),
                        'allowed_categories' => $allowed_categories,
                        'exclude_categories' => $exclude_categories,
                        'badge_text_len' => strlen((string) $data['badge_text']),
                    ));
                }
                $this->sync_plan_product_meta($plan_id, !empty($data['product_id']) ? (int) $data['product_id'] : null, $old_product_id);
                wp_redirect(admin_url('admin.php?page=eshterakyar-plans&updated=1'));
            } else {
                wp_redirect(admin_url('admin.php?page=eshterakyar-plans&error=1'));
            }
        } else {
            $new_plan_id = Eshterakyar_Plan::create($data);
            if ($new_plan_id !== false && $new_plan_id > 0) {
                if (class_exists('Eshterakyar_Logs')) {
                    Eshterakyar_Logs::log_debug('Plan created', array(
                        'plan_id' => $new_plan_id,
                        'taxonomies_csv' => !empty($_POST['eshterakyar_taxonomies_via_csv']),
                        'allowed_categories' => $allowed_categories,
                        'badge_text_len' => strlen((string) $data['badge_text']),
                    ));
                }
                $this->sync_plan_product_meta($new_plan_id, !empty($data['product_id']) ? (int) $data['product_id'] : null, null);
                wp_redirect(admin_url('admin.php?page=eshterakyar-plans&created=1'));
            } else {
                global $wpdb;
                $error_message = !empty($wpdb->last_error) ? $wpdb->last_error : 'Unknown database error';
                wp_redirect(admin_url('admin.php?page=eshterakyar-plans&error=1&error_msg=' . urlencode($error_message)));
            }
        }
        exit;
    }

    /**
     * سینک متای محصول با پلن: وقتی در صفحه پلن‌ها «محصول مرتبط» انتخاب می‌شود،
     * روی محصول هم _eshterakyar_is_subscription و _eshterakyar_plan_id ست می‌شود
     * تا با خرید آن محصول اشتراک فعال شود (هماهنگ با ویرایش محصول).
     *
     * @param int      $plan_id         شناسه پلن
     * @param int|null $new_product_id  محصول جدید مرتبط با پلن
     * @param int|null $old_product_id  محصول قبلی (فقط در ویرایش)
     */
    private function sync_plan_product_meta($plan_id, $new_product_id, $old_product_id) {
        if ($new_product_id && $new_product_id > 0) {
            update_post_meta($new_product_id, '_eshterakyar_is_subscription', 1);
            update_post_meta($new_product_id, '_eshterakyar_plan_id', $plan_id);
            global $wpdb;
            $table = $wpdb->prefix . 'subscription_plans';
            $wpdb->query($wpdb->prepare(
                "UPDATE `{$table}` SET product_id = NULL WHERE product_id = %d AND id != %d",
                $new_product_id,
                $plan_id
            ));
            if (class_exists('Eshterakyar_Cache')) {
                Eshterakyar_Cache::delete('plans_all');
                Eshterakyar_Cache::delete('plans_active');
                Eshterakyar_Cache::delete('plans_inactive');
            }
            wp_cache_delete('plans_all', 'eshterakyar');
            wp_cache_delete('plans_active', 'eshterakyar');
            wp_cache_delete('plans_inactive', 'eshterakyar');
        }
        $to_clear = null;
        if ($old_product_id && $old_product_id > 0 && $old_product_id !== $new_product_id) {
            $to_clear = $old_product_id;
        } elseif (!$new_product_id && $old_product_id && $old_product_id > 0) {
            $to_clear = $old_product_id;
        }
        if ($to_clear) {
            $linked_plan = get_post_meta($to_clear, '_eshterakyar_plan_id', true);
            if ($linked_plan == $plan_id) {
                delete_post_meta($to_clear, '_eshterakyar_is_subscription');
                delete_post_meta($to_clear, '_eshterakyar_plan_id');
            }
        }
    }

    public function subscriptions_page() {
        global $wpdb;
        $options = get_option('eshterakyar_options', array());
        if (isset($_POST['action']) && $_POST['action'] == 'create_manual_subscription' && check_admin_referer('eshterakyar_create_manual_subscription', 'eshterakyar_manual_subscription_nonce')) {
            $this->create_manual_subscription();
        }
        if (isset($_POST['bulk_action']) && isset($_POST['subscription_ids']) && check_admin_referer('eshterakyar_bulk_action', 'eshterakyar_bulk_nonce')) {
            $this->handle_subscription_bulk_action($_POST['bulk_action'], $_POST['subscription_ids']);
        }
        
        if (isset($_GET['action']) && isset($_GET['subscription_id'])) {
            $this->handle_single_subscription_action($_GET['action'], intval($_GET['subscription_id']));
        }
        
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : null;
        $plan_id = isset($_GET['plan_id']) ? intval($_GET['plan_id']) : null;
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
        $search = isset($_GET['s']) ? wp_unslash(sanitize_text_field(wp_unslash($_GET['s']))) : '';
        $expiring_soon = isset($_GET['expiring_soon']) ? true : false;
        
        $date_from = isset($_GET['date_from_gregorian']) && !empty($_GET['date_from_gregorian']) 
            ? sanitize_text_field($_GET['date_from_gregorian']) 
            : (isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '');
        $date_to = isset($_GET['date_to_gregorian']) && !empty($_GET['date_to_gregorian']) 
            ? sanitize_text_field($_GET['date_to_gregorian']) 
            : (isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '');
        
        if (!empty($date_from) && preg_match('/^\d{4}\/\d{2}\/\d{2}/', $date_from)) {
            $date_from = $this->persian_to_gregorian($date_from);
            if ($date_from) {
                $date_from = explode(' ', $date_from)[0];
            }
        }
        if (!empty($date_to) && preg_match('/^\d{4}\/\d{2}\/\d{2}/', $date_to)) {
            $date_to = $this->persian_to_gregorian($date_to);
            if ($date_to) {
                $date_to = explode(' ', $date_to)[0];
            }
        }
        
        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 10;
        $offset = ($paged - 1) * $per_page;
        
        $table = $wpdb->prefix . 'subscriptions';
        $where = "WHERE 1=1";
        $params = array();
        
        if ($user_id) {
            $where .= " AND s.user_id = %d";
            $params[] = $user_id;
        }
        
        if ($status) {
            $where .= " AND s.status = %s";
            $params[] = $status;
        }
        
        if ($plan_id) {
            $where .= " AND s.plan_id = %d";
            $params[] = $plan_id;
        }
        
        if ($category_id) {
            $where .= " AND EXISTS (
                SELECT 1 FROM {$wpdb->term_relationships} tr
                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                WHERE tt.taxonomy = 'product_cat' AND tt.term_id = %d
                AND tr.object_id IN (
                    SELECT product_id FROM {$wpdb->prefix}subscription_downloads 
                    WHERE subscription_id = s.id
                )
            )";
            $params[] = $category_id;
        }
        
        if ($search) {
            $where .= " AND (u.display_name LIKE %s OR u.user_email LIKE %s OR p.name LIKE %s)";
            $search_like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $search_like;
            $params[] = $search_like;
            $params[] = $search_like;
        }
        
        if ($expiring_soon) {
            $days = isset($options['days_before_expiry']) ? intval($options['days_before_expiry']) : 7;
            $where .= " AND s.status = 'active' AND s.end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL %d DAY)";
            $params[] = $days;
        }
        
        if ($date_from) {
            $date_from = sanitize_text_field($date_from);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)) {
            $where .= " AND DATE(s.created_at) >= %s";
            $params[] = $date_from;
            }
        }
        
        if ($date_to) {
            $date_to = sanitize_text_field($date_to);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) {
            $where .= " AND DATE(s.created_at) <= %s";
            $params[] = $date_to;
            }
        }
        
        $query = "SELECT s.*, 
                         p.name as plan_name, 
                         p.duration_days as plan_duration,
                         p.daily_limit as plan_daily_limit,
                         p.total_limit as plan_total_limit,
                         u.display_name as user_name, 
                         u.user_email,
                         COALESCE(s.is_active, 1) as is_active,
                         COALESCE(s.daily_limit, 0) as daily_limit,
                         COALESCE(s.total_downloads, 0) as total_downloads,
                         COALESCE(s.downloads_used, 0) as downloads_used,
                         COALESCE(s.daily_downloads_count, 0) as daily_downloads_count,
                         s.last_download_date,
                         s.order_id
                  FROM $table s 
                  LEFT JOIN {$wpdb->prefix}subscription_plans p ON s.plan_id = p.id 
                  LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID 
                  $where 
                  ORDER BY s.created_at DESC";
        
        $count_query = "SELECT COUNT(DISTINCT s.id)
                  FROM $table s 
                  LEFT JOIN {$wpdb->prefix}subscription_plans p ON s.plan_id = p.id 
                  LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID 
                  $where";
        
        if (!empty($params)) {
            $total = $wpdb->get_var($wpdb->prepare($count_query, $params));
            $subscriptions = $wpdb->get_results($wpdb->prepare($query . " LIMIT %d OFFSET %d", array_merge($params, array($per_page, $offset))));
        } else {
            $total = $wpdb->get_var($count_query);
            $subscriptions = $wpdb->get_results($query . $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset));
        }
        
        if (!empty($subscriptions)) {
            $user_ids = array_unique(wp_list_pluck($subscriptions, 'user_id'));
            if (!empty($user_ids) && function_exists('cache_users')) {
                cache_users($user_ids);
            }
            
            $product_ids = array_filter(array_unique(wp_list_pluck($subscriptions, 'order_id')));
            if (!empty($product_ids) && function_exists('update_post_meta_cache')) {
                update_post_meta_cache($product_ids);
            }
        }
        
        $plans = Eshterakyar_Plan::get_all();
        
        $total_pages = ceil($total / $per_page);
        $pagination = $this->get_pagination($paged, $total_pages);
        
        $this->render_page_with_wrapper('subscriptions.php', array(
            'subscriptions' => $subscriptions,
            'total' => $total,
            'pagination' => $pagination,
            'plans' => $plans,
            'status' => $status
        ));
    }
    private function handle_subscription_bulk_action($action, $subscription_ids) {
        foreach ($subscription_ids as $subscription_id) {
            $subscription_id = intval($subscription_id);
            switch ($action) {
                case 'cancel':
                    Eshterakyar_Subscription::cancel($subscription_id);
                    break;
                case 'activate':
                    Eshterakyar_Subscription::activate($subscription_id);
                    break;
                case 'deactivate':
                    Eshterakyar_Subscription::deactivate($subscription_id);
                    break;
                case 'extend':
                    break;
            }
        }
        wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&bulk_updated=1'));
        exit;
    }
    
    private function handle_single_subscription_action($action, $subscription_id) {
        switch ($action) {
            case 'cancel':
                check_admin_referer('cancel_subscription_' . $subscription_id);
                Eshterakyar_Subscription::cancel($subscription_id);
                wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&cancelled=1'));
                exit;
            case 'activate':
                check_admin_referer('toggle_subscription_' . $subscription_id);
                Eshterakyar_Subscription::activate($subscription_id);
                wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&activated=1'));
                exit;
            case 'deactivate':
                check_admin_referer('toggle_subscription_' . $subscription_id);
                Eshterakyar_Subscription::deactivate($subscription_id);
                wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&deactivated=1'));
                exit;
            case 'edit':
                break;
            case 'extend':
                check_admin_referer('extend_subscription_' . $subscription_id);
                $this->extend_subscription($subscription_id);
                wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&extended=1'));
                exit;
            case 'edit':
                break;
        }
    }
    public function update_subscription($subscription_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscriptions';
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
        $end_date   = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';

        $raw_start_persian = isset($_POST['start_date_persian']) ? sanitize_text_field($_POST['start_date_persian']) : '';
        $raw_end_persian   = isset($_POST['end_date_persian']) ? sanitize_text_field($_POST['end_date_persian']) : '';

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('Update subscription - incoming dates', array(
                'subscription_id'    => $subscription_id,
                'start_date_raw'     => $start_date,
                'end_date_raw'       => $end_date,
                'start_persian_raw'  => $raw_start_persian,
                'end_persian_raw'    => $raw_end_persian,
            ));
        }

        if (!empty($raw_start_persian)) {
            $start_date = $this->persian_to_gregorian($raw_start_persian);
            if ($start_date && !preg_match('/\d{2}:\d{2}/', $start_date)) {
                $parts = explode(' ', $raw_start_persian);
                $time  = isset($parts[1]) ? $parts[1] : '00:00:00';
                if (strlen($time) === 5) {
                    $time .= ':00';
                }
                $start_date = $start_date . ' ' . $time;
            }
        }

        if (!empty($raw_end_persian)) {
            $end_date = $this->persian_to_gregorian($raw_end_persian);
            if ($end_date && !preg_match('/\d{2}:\d{2}/', $end_date)) {
                $parts = explode(' ', $raw_end_persian);
                $time  = isset($parts[1]) ? $parts[1] : '00:00:00';
                if (strlen($time) === 5) {
                    $time .= ':00';
                }
                $end_date = $end_date . ' ' . $time;
            }
        }
        if (!empty($start_date) && preg_match('/^\d{4}\/\d{2}\/\d{2}/', $start_date)) {
            $start_date = $this->persian_to_gregorian($start_date);
        }
        if (!empty($end_date) && preg_match('/^\d{4}\/\d{2}\/\d{2}/', $end_date)) {
            $end_date = $this->persian_to_gregorian($end_date);
        }

        $start_timestamp = $start_date ? strtotime($start_date) : false;
        if (!$start_timestamp && !empty($start_date)) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Update subscription - invalid start_date, keeping original in DB', array(
                    'subscription_id'   => $subscription_id,
                    'start_date_before' => $start_date,
                    'start_persian'     => $raw_start_persian,
                ));
            }
            $start_date = '';
        } elseif ($start_timestamp) {
            $start_date = date('Y-m-d H:i:s', $start_timestamp);
        }

        $end_timestamp = $end_date ? strtotime($end_date) : false;
        if (!$end_timestamp && !empty($end_date)) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Update subscription - invalid end_date, keeping original in DB', array(
                    'subscription_id'  => $subscription_id,
                    'end_date_before'  => $end_date,
                    'end_persian'      => $raw_end_persian,
                ));
            }
            $end_date = '';
        } elseif ($end_timestamp) {
            $end_date = date('Y-m-d H:i:s', $end_timestamp);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $start_ts = strtotime($start_date);
            $end_ts   = strtotime($end_date);
            if ($start_ts && $end_ts && $end_ts < $start_ts && class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Update subscription - end_date before start_date (no auto-fix)', array(
                    'subscription_id' => $subscription_id,
                    'start_date'      => $start_date,
                    'end_date'        => $end_date,
                ));
            }
        }
        
        $data = array(
            'plan_id' => intval($_POST['plan_id']),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => sanitize_text_field($_POST['status']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'total_downloads' => intval($_POST['total_downloads']),
            'daily_limit' => intval($_POST['daily_limit']),
            'downloads_used' => intval($_POST['downloads_used'])
        );
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('Update subscription - normalized data before DB update', array(
                'subscription_id' => $subscription_id,
                'data'            => $data,
            ));
        }

        $result = $wpdb->update(
            $table,
            $data,
            array('id' => $subscription_id)
        );
        
        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('Update subscription - DB update result', array(
                'subscription_id' => $subscription_id,
                'result'          => $result,
                'last_error'      => $wpdb->last_error,
            ));
        }

        if ($result !== false) {
            $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
            if ($subscription) {
                if (!empty($subscription->end_date)) {
                    $end_timestamp = strtotime($subscription->end_date);
                    $now_timestamp = current_time('timestamp');
                    
                    if ($end_timestamp > $now_timestamp && $subscription->status === 'expired') {
                        $wpdb->update(
                            $table,
                            array('status' => 'active'),
                            array('id' => $subscription_id)
                        );
                    } elseif ($end_timestamp <= $now_timestamp && $subscription->status === 'active') {
                        $wpdb->update(
                            $table,
                            array('status' => 'expired'),
                            array('id' => $subscription_id)
                        );
                    }
                }
                
                if (class_exists('Eshterakyar_Cache')) {
                    Eshterakyar_Cache::clear_user_subscription_cache($subscription->user_id);
                }
            }
            
            wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&updated=1'));
            exit;
        }
        
        wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&error=1'));
        exit;
    }
    private function create_manual_subscription() {
        $user_id = isset($_POST['manual_user_id']) ? intval($_POST['manual_user_id']) : 0;
        $plan_id = isset($_POST['manual_plan_id']) ? intval($_POST['manual_plan_id']) : 0;
        $status = isset($_POST['manual_status']) ? sanitize_text_field($_POST['manual_status']) : 'active';
        
        if (!$user_id || !$plan_id) {
            wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&error=missing_fields'));
            exit;
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&error=user_not_found'));
            exit;
        }
        
        $plan = Eshterakyar_Plan::get_by_id($plan_id);
        if (!$plan) {
            wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&error=plan_not_found'));
            exit;
        }
        
        $start_date = isset($_POST['manual_start_date']) ? sanitize_text_field($_POST['manual_start_date']) : '';
        $end_date   = isset($_POST['manual_end_date']) ? sanitize_text_field($_POST['manual_end_date']) : '';

        $raw_start_persian = isset($_POST['manual_start_date_persian']) ? sanitize_text_field($_POST['manual_start_date_persian']) : '';
        $raw_end_persian   = isset($_POST['manual_end_date_persian']) ? sanitize_text_field($_POST['manual_end_date_persian']) : '';

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('Manual subscription (form) - incoming dates', array(
                'user_id'            => $user_id,
                'plan_id'            => $plan_id,
                'start_date_raw'     => $start_date,
                'end_date_raw'       => $end_date,
                'start_persian_raw'  => $raw_start_persian,
                'end_persian_raw'    => $raw_end_persian,
            ));
        }

        if (empty($start_date) && !empty($raw_start_persian)) {
            $start_date = $this->persian_to_gregorian($raw_start_persian);
            if ($start_date && !preg_match('/\d{2}:\d{2}/', $start_date)) {
                $parts = explode(' ', $raw_start_persian);
                $time  = isset($parts[1]) ? $parts[1] : '00:00:00';
                if (strlen($time) === 5) {
                    $time .= ':00';
                }
                $start_date = $start_date . ' ' . $time;
            }
        }
        
        if (empty($end_date) && !empty($raw_end_persian)) {
            $end_date = $this->persian_to_gregorian($raw_end_persian);
            if ($end_date && !preg_match('/\d{2}:\d{2}/', $end_date)) {
                $parts = explode(' ', $raw_end_persian);
                $time  = isset($parts[1]) ? $parts[1] : '00:00:00';
                if (strlen($time) === 5) {
                    $time .= ':00';
                }
                $end_date = $end_date . ' ' . $time;
            }
        }

        $start_timestamp = $start_date ? strtotime($start_date) : false;
        if (!$start_timestamp) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Manual subscription (form) - invalid start_date, fallback to current_time', array(
                    'start_date_before' => $start_date,
                    'start_persian'     => $raw_start_persian,
                ));
            }
            $start_timestamp = current_time('timestamp');
        }
        $start_date = date('Y-m-d H:i:s', $start_timestamp);

        $end_timestamp = $end_date ? strtotime($end_date) : false;
        if (!$end_timestamp && !empty($end_date)) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Manual subscription (form) - invalid end_date, discarding and recalculating from plan', array(
                    'end_date_before' => $end_date,
                    'end_persian'     => $raw_end_persian,
                ));
            }
            $end_timestamp = false;
        }

        if (!$end_timestamp) {
            $end_timestamp = strtotime('+'. (int) $plan->duration_days .' days', $start_timestamp);
        }

        if ($end_timestamp < $start_timestamp) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Manual subscription (form) - end_date before start_date, adjusting', array(
                    'start_date'       => $start_date,
                    'end_date_before'  => date('Y-m-d H:i:s', $end_timestamp),
                    'plan_days'        => $plan->duration_days,
                ));
            }
            $end_timestamp = strtotime('+'. (int) $plan->duration_days .' days', $start_timestamp);
        }

        $end_date = date('Y-m-d H:i:s', $end_timestamp);

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('Manual subscription (form) - normalized dates', array(
                'normalized_start_date' => $start_date,
                'normalized_end_date'   => $end_date,
            ));
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'subscriptions';
        
        $data = array(
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'order_id' => null, 
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $status,
            'is_active' => ($status === 'active') ? 1 : 0,
            'total_downloads' => $plan->total_limit,
            'daily_limit' => $plan->daily_limit,
            'downloads_used' => 0,
            'daily_downloads_count' => 0,
            'last_download_date' => null
        );
        
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            $subscription_id = $wpdb->insert_id;
            
            Eshterakyar_Subscription::check_and_update_status($user_id);
            if (class_exists('Eshterakyar_Cache')) {
                Eshterakyar_Cache::clear_user_subscription_cache($user_id);
            }
            $options = get_option('eshterakyar_options', array());
            if (isset($options['enable_notifications']) && $options['enable_notifications']) {
                Eshterakyar_Notifications::send_purchase_notification($user_id, $subscription_id);
            }
            
            wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&subscription_created=1'));
            exit;
        } else {
            wp_redirect(admin_url('admin.php?page=eshterakyar-subscriptions&action=create&error=creation_failed'));
            exit;
        }
    }
    private function extend_subscription($subscription_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscriptions';
        
        $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
        if (!$subscription) {
            return false;
        }
        
        $plan = Eshterakyar_Plan::get_by_id($subscription->plan_id);
        if (!$plan) {
            return false;
        }
        $new_end_date = date('Y-m-d H:i:s', strtotime($subscription->end_date . ' +' . $plan->duration_days . ' days'));
        
        return $wpdb->update(
            $table,
            array('end_date' => $new_end_date),
            array('id' => $subscription_id)
        );
    }
    private function get_pagination($current_page, $total_pages) {
        if ($total_pages <= 1) {
            return '';
        }
        $get_params = $_GET;
        unset($get_params['paged']);
        $query_string = http_build_query($get_params);
        $base_url = admin_url('admin.php' . ($query_string ? '?' . $query_string . '&' : '?') . 'paged=%#%');
        return paginate_links(array(
            'base' => $base_url,
            'format' => '',
            'current' => $current_page,
            'total' => $total_pages,
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
        ));
    }
    private function convert_persian_digits_to_english($str) {
        if (empty($str)) {
            return $str;
        }
        $persian_digits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        return str_replace($persian_digits, $english_digits, $str);
    }
    private function persian_to_gregorian($persian_date) {
        if (empty($persian_date)) {
            return '';
        }
        $persian_date = $this->convert_persian_digits_to_english($persian_date);
        $parts = explode(' ', trim($persian_date));
        $date_part = $parts[0];
        $time_part = isset($parts[1]) ? $parts[1] : '';
        
        if (function_exists('gregdate')) {
            try {
                if (function_exists('eng_number')) {
                    $date_part = eng_number($date_part);
                }
                $date_parts = preg_split('/[\/\-]/', $date_part);
                if (count($date_parts) === 3) {
                    $persian_date_formatted = $date_parts[0] . '-' . $date_parts[1] . '-' . $date_parts[2];
                    $gregorian_date = gregdate('Y-m-d', $persian_date_formatted);
                    
                    if ($gregorian_date && $gregorian_date !== $persian_date_formatted) {
                        if (!empty($time_part)) {
                            if (strlen($time_part) === 5) {
                                $time_part .= ':00';
                            }
                            $result = $gregorian_date . ' ' . $time_part;
                        } else {
                            $result = $gregorian_date . ' 00:00:00';
                        }
                        
                        return $result;
                    }
                }
            } catch (Exception $e) {
                if (class_exists('Eshterakyar_Logs')) {
                    Eshterakyar_Logs::log_debug('persian_to_gregorian (gregdate) exception', array(
                        'input' => $persian_date,
                        'error' => $e->getMessage(),
                    ));
                }
            }
        }
        if (function_exists('persian_to_gregorian')) {
            try {
                $date_parts = preg_split('/[\/\-]/', $date_part);
                if (count($date_parts) === 3) {
                    $year = intval($date_parts[0]);
                    $month = intval($date_parts[1]);
                    $day = intval($date_parts[2]);
                    
                    $gregorian = persian_to_gregorian($year, $month, $day);
                    if (is_array($gregorian)) {
                        $result = sprintf('%04d-%02d-%02d', $gregorian[0], $gregorian[1], $gregorian[2]);
                    } else {
                        $result = $gregorian;
                    }
                    if (!empty($time_part)) {
                        if (strlen($time_part) === 5) {
                            $time_part .= ':00';
                        }
                        $result .= ' ' . $time_part;
                    } else {
                        $result .= ' 00:00:00';
                    }
                    
                    return $result;
                }
            } catch (Exception $e) {
                if (class_exists('Eshterakyar_Logs')) {
                    Eshterakyar_Logs::log_debug('persian_to_gregorian (persian_to_gregorian fn) exception', array(
                        'input' => $persian_date,
                        'error' => $e->getMessage(),
                    ));
                }
            }
        }
        
        try {
            $date_parts = preg_split('/[\/\-]/', $date_part);
            if (count($date_parts) === 3) {
                $jy = intval($date_parts[0], 10);
                $jm = intval($date_parts[1], 10);
                $jd = intval($date_parts[2], 10);

                $jy2 = $jy - 979;
                $jm2 = $jm - 1;
                $jd2 = $jd - 1;

                $j_day_no = 365 * $jy2 + intdiv($jy2, 33) * 8 + intdiv(($jy2 % 33) + 3, 4);
                for ($i = 0; $i < $jm2; $i++) {
                    $j_day_no += ($i < 6) ? 31 : 30;
                }
                $j_day_no += $jd2;

                $g_day_no = $j_day_no + 79;

                $gy = 1600 + 400 * intdiv($g_day_no, 146097);
                $g_day_no = $g_day_no % 146097;

                $leap = true;
                if ($g_day_no >= 36525) {
                    $g_day_no--;
                    $gy += 100 * intdiv($g_day_no, 36524);
                    $g_day_no = $g_day_no % 36524;

                    if ($g_day_no >= 365) {
                        $g_day_no++;
                    } else {
                        $leap = false;
                    }
                }

                $gy += 4 * intdiv($g_day_no, 1461);
                $g_day_no = $g_day_no % 1461;

                if ($g_day_no >= 366) {
                    $leap = false;
                    $g_day_no--;
                    $gy += intdiv($g_day_no, 365);
                    $g_day_no = $g_day_no % 365;
                }

                $gd = $g_day_no + 1;

                $g_month_days = array(31, ($leap ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
                $gm = 0;
                while ($gm < 12 && $gd > $g_month_days[$gm]) {
                    $gd -= $g_month_days[$gm];
                    $gm++;
                }
                $gm++;

                $gregorian_date = sprintf('%04d-%02d-%02d', $gy, $gm, $gd);

                if (!empty($time_part)) {
                    if (strlen($time_part) === 5) {
                        $time_part .= ':00';
                    }
                    $gregorian_date .= ' ' . $time_part;
                } else {
                    $gregorian_date .= ' 00:00:00';
                }

                return $gregorian_date;
            }
        } catch (Exception $e) {
            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('persian_to_gregorian (fallback algo) exception', array(
                    'input' => $persian_date,
                    'error' => $e->getMessage(),
                ));
            }
        }

        if (class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('persian_to_gregorian failed to convert', array(
                'input'      => $persian_date,
                'date_part'  => $date_part,
                'time_part'  => $time_part,
            ));
        }
        return '';
    }
    public function downloads_page() {
        global $wpdb;
        if (isset($_POST['action']) && isset($_POST['download_id'])) {
            check_admin_referer('eshterakyar_download_action', 'eshterakyar_download_nonce');
            $this->handle_download_action($_POST['action'], intval($_POST['download_id']));
        }
        if (isset($_POST['reset_credits']) && isset($_POST['subscription_id'])) {
            check_admin_referer('eshterakyar_reset_credits', 'eshterakyar_reset_nonce');
            $this->reset_subscription_credits(intval($_POST['subscription_id']));
        }
        
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
        $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;
        $subscription_id = isset($_GET['subscription_id']) ? intval($_GET['subscription_id']) : null;
        $date_from = isset($_GET['date_from_gregorian']) && !empty($_GET['date_from_gregorian']) 
            ? sanitize_text_field($_GET['date_from_gregorian']) 
            : (isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '');
        $date_to = isset($_GET['date_to_gregorian']) && !empty($_GET['date_to_gregorian']) 
            ? sanitize_text_field($_GET['date_to_gregorian']) 
            : (isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '');
        if (!empty($date_from) && preg_match('/^\d{4}\/\d{2}\/\d{2}/', $date_from)) {
            $date_from = $this->persian_to_gregorian($date_from);
            if ($date_from) {
                $date_from = explode(' ', $date_from)[0];
            }
        }
        if (!empty($date_to) && preg_match('/^\d{4}\/\d{2}\/\d{2}/', $date_to)) {
            $date_to = $this->persian_to_gregorian($date_to);
            if ($date_to) {
                $date_to = explode(' ', $date_to)[0];
            }
        }
        
        $search = isset($_GET['s']) ? wp_unslash(sanitize_text_field(wp_unslash($_GET['s']))) : '';
        $date_range = isset($_GET['date_range']) ? sanitize_text_field($_GET['date_range']) : 'all';
        
        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 50;
        $offset = ($paged - 1) * $per_page;
        
        $table = $wpdb->prefix . 'subscription_downloads';
        $where = "WHERE 1=1";
        $params = array();
        
        $date_range = isset($_GET['date_range']) ? sanitize_text_field($_GET['date_range']) : 'all';
        
        $date_range_where = $this->get_date_range_where($date_range);
        if ($date_range_where) {
            $where .= " AND " . str_replace("WHERE ", "", str_replace("d.", "d.", $date_range_where));
        }
        
        if ($user_id) {
            $where .= " AND d.user_id = %d";
            $params[] = $user_id;
        }
        
        if ($product_id) {
            $where .= " AND d.product_id = %d";
            $params[] = $product_id;
        }
        
        if ($subscription_id) {
            $where .= " AND d.subscription_id = %d";
            $params[] = $subscription_id;
        }
        
        if ($date_from) {
            $date_from = sanitize_text_field($date_from);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)) {
            $where .= " AND DATE(d.download_date) >= %s";
            $params[] = $date_from;
            }
        }
        
        if ($date_to) {
            $date_to = sanitize_text_field($date_to);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) {
            $where .= " AND DATE(d.download_date) <= %s";
            $params[] = $date_to;
            }
        }
        
        if ($search) {
            $where .= " AND (p.post_title LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s)";
            $search_like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $search_like;
            $params[] = $search_like;
            $params[] = $search_like;
        }
        
        $query = "SELECT d.*, p.post_title as product_name, u.display_name as user_name, u.user_email,
                         s.plan_id, s.status as subscription_status
                  FROM $table d 
                  LEFT JOIN {$wpdb->posts} p ON d.product_id = p.ID 
                  LEFT JOIN {$wpdb->users} u ON d.user_id = u.ID 
                  LEFT JOIN {$wpdb->prefix}subscriptions s ON d.subscription_id = s.id
                  $where 
                  ORDER BY d.download_date DESC";
        
        $count_query = "SELECT COUNT(*) FROM ($query) as total";
        if (!empty($params)) {
            $total = $wpdb->get_var($wpdb->prepare($count_query, $params));
            $downloads = $wpdb->get_results($wpdb->prepare($query . " LIMIT %d OFFSET %d", array_merge($params, array($per_page, $offset))));
        } else {
            $total = $wpdb->get_var($count_query);
            $downloads = $wpdb->get_results($query . $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset));
        }
        
        if (!empty($downloads)) {
            $user_ids = array_unique(array_filter(wp_list_pluck($downloads, 'user_id')));
            if (!empty($user_ids) && function_exists('cache_users')) {
                cache_users($user_ids);
            }
            
            $product_ids = array_unique(array_filter(wp_list_pluck($downloads, 'product_id')));
            if (!empty($product_ids) && function_exists('update_post_meta_cache')) {
                update_post_meta_cache($product_ids);
            }
        }
        
        $total_pages = ceil($total / $per_page);
        $pagination = $this->get_pagination($paged, $total_pages);
        
        $date_range = isset($_GET['date_range']) ? sanitize_text_field($_GET['date_range']) : 'all';
        $date_where = $this->get_date_range_where($date_range);
        $stats = array(
            'total_downloads' => $wpdb->get_var("SELECT COUNT(*) FROM $table"),
            'unique_products' => $wpdb->get_var("SELECT COUNT(DISTINCT product_id) FROM $table"),
            'unique_users' => $wpdb->get_var("SELECT COUNT(DISTINCT user_id) FROM $table"),
            'today_downloads' => $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE DATE(download_date) = CURDATE()")
        );
        $insights = $this->get_download_insights($date_where);
        
        $this->render_page_with_wrapper('downloads.php', array(
            'downloads' => $downloads,
            'total' => $total,
            'pagination' => $pagination,
            'stats' => $stats,
            'insights' => $insights,
            'date_range' => $date_range
        ));
    }
    private function handle_download_action($action, $download_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_downloads';
        
        switch ($action) {
            case 'delete':
                $wpdb->delete($table, array('id' => $download_id));
                wp_redirect(admin_url('admin.php?page=eshterakyar-downloads&deleted=1'));
                exit;
        }
    }
    private function reset_subscription_credits($subscription_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscriptions';
        $downloads_table = $wpdb->prefix . 'subscription_downloads';
        
        $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
        if (!$subscription) {
            wp_redirect(admin_url('admin.php?page=eshterakyar-downloads&error=1'));
            exit;
        }
        
        $plan = Eshterakyar_Plan::get_by_id($subscription->plan_id);
        if (!$plan) {
            wp_redirect(admin_url('admin.php?page=eshterakyar-downloads&error=1'));
            exit;
        }
        $result = $wpdb->update(
            $table,
            array(
                'downloads_used' => 0,
                'daily_downloads_count' => 0,
                'last_download_date' => null
            ),
            array('id' => $subscription_id)
        );
        
        if ($result === false) {
            wp_redirect(admin_url('admin.php?page=eshterakyar-downloads&error=1'));
            exit;
        }
        
        $wpdb->query($wpdb->prepare(
            "DELETE FROM `{$downloads_table}` 
             WHERE subscription_id = %d",
            $subscription_id
        ));
        if (class_exists('Eshterakyar_Cache')) {
            Eshterakyar_Cache::clear_user_subscription_cache($subscription->user_id);
        }
        
        wp_redirect(admin_url('admin.php?page=eshterakyar-downloads&credits_reset=1'));
        exit;
    }
    private function get_date_range_where($date_range) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_downloads';
        
        switch ($date_range) {
            case 'today':
                return "WHERE DATE(d.download_date) = CURDATE()";
            case 'week':
                return "WHERE d.download_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case 'month':
                return "WHERE d.download_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case 'year':
                return "WHERE d.download_date >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
            default:
                return "";
        }
    }
    private function get_download_insights($date_where = "") {
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_downloads';
        $subscriptions_table = $wpdb->prefix . 'subscriptions';
        $plans_table = $wpdb->prefix . 'subscription_plans';
        
        $where_clause = '';
        if ($date_where && preg_match('/^WHERE\s+[a-zA-Z0-9_\.\s\(\)\>\<\=\-]+$/i', $date_where)) {
            $where_clause = $date_where;
        }
        
        $top_products_query = "SELECT d.product_id, p.post_title as product_name, COUNT(DISTINCT d.id) as download_count
                               FROM {$table} d
                               LEFT JOIN {$wpdb->posts} p ON d.product_id = p.ID
                               " . ($where_clause ? $where_clause : "") . "
                               GROUP BY d.product_id
                               ORDER BY download_count DESC
                               LIMIT 10";
        $top_products = $wpdb->get_results($top_products_query);
        
        $top_users_query = "SELECT d.user_id, u.display_name as user_name, u.user_email, COUNT(DISTINCT d.id) as download_count
                            FROM {$table} d
                            LEFT JOIN {$wpdb->users} u ON d.user_id = u.ID
                            " . ($where_clause ? $where_clause : "") . "
                            GROUP BY d.user_id
                            ORDER BY download_count DESC
                            LIMIT 10";
        $top_users = $wpdb->get_results($top_users_query);
        
        $popular_plans_query = "SELECT s.plan_id, pl.name as plan_name, COUNT(DISTINCT d.id) as download_count
                                FROM {$table} d
                                LEFT JOIN {$subscriptions_table} s ON d.subscription_id = s.id
                                LEFT JOIN {$plans_table} pl ON s.plan_id = pl.id
                                " . ($where_clause ? $where_clause : "") . "
                                GROUP BY s.plan_id
                                ORDER BY download_count DESC
                                LIMIT 10";
        $popular_plans = $wpdb->get_results($popular_plans_query);
        
        $range_stats = array();
        if ($where_clause) {
            $range_stats_query = "SELECT 
                                    COUNT(*) as total,
                                    COUNT(DISTINCT product_id) as unique_products,
                                    COUNT(DISTINCT user_id) as unique_users,
                                    AVG(daily_count) as avg_daily
                                  FROM (
                                    SELECT 
                                      DATE(download_date) as download_date,
                                      COUNT(*) as daily_count
                                    FROM {$table}
                                    " . ($where_clause ? str_replace('WHERE ', 'WHERE ', str_replace('d.', '', $where_clause)) : "") . "
                                    GROUP BY DATE(download_date)
                                  ) as daily_stats";
            $range_stats = $wpdb->get_row($range_stats_query);
        }
        
        $hourly_stats_query = "SELECT HOUR(download_date) as hour, COUNT(*) as count
                               FROM {$table}
                               WHERE DATE(download_date) = CURDATE()
                               GROUP BY HOUR(download_date)
                               ORDER BY hour";
        $hourly_stats = $wpdb->get_results($hourly_stats_query);
        
        return array(
            'top_products' => $top_products ? $top_products : array(),
            'top_users' => $top_users ? $top_users : array(),
            'popular_plans' => $popular_plans ? $popular_plans : array(),
            'range_stats' => $range_stats,
            'hourly_stats' => $hourly_stats ? $hourly_stats : array()
        );
    }
    public function settings_page() {

        if (isset($_GET['settings-updated'])) {

        }
        $this->render_page_with_wrapper('settings.php');
    }
    
    public function sanitize_settings($input) {
        if (!is_array($input)) {
            return array();
        }
        
        $sanitized = array();
        
 
        if (isset($input['enable_notifications'])) {
            $sanitized['enable_notifications'] = !empty($input['enable_notifications']) ? 1 : 0;
        }
        
        if (isset($input['days_before_expiry'])) {
            $sanitized['days_before_expiry'] = intval($input['days_before_expiry']);
        }
        
        if (isset($input['enable_redownload'])) {
            $sanitized['enable_redownload'] = !empty($input['enable_redownload']) ? 1 : 0;
        }
        
        if (isset($input['show_downloaded_badge'])) {
            $sanitized['show_downloaded_badge'] = !empty($input['show_downloaded_badge']) ? 1 : 0;
        }
        
        if (isset($input['merge_subscription_credits'])) {
            $sanitized['merge_subscription_credits'] = !empty($input['merge_subscription_credits']) ? 1 : 0;
        }

        if (isset($input['cumulative_plan_download_access'])) {
            $sanitized['cumulative_plan_download_access'] = !empty($input['cumulative_plan_download_access']) ? 1 : 0;
        }

        if (isset($input['content_restriction_enabled'])) {
            $sanitized['content_restriction_enabled'] = !empty($input['content_restriction_enabled']) ? 1 : 0;
        }
        if (isset($input['content_restriction_mode'])) {
            $mode = sanitize_text_field($input['content_restriction_mode']);
            $sanitized['content_restriction_mode'] = in_array($mode, array('inline', 'redirect'), true) ? $mode : 'inline';
        }
        if (isset($input['content_restriction_post_types'])) {
            $sanitized['content_restriction_post_types'] = sanitize_text_field($input['content_restriction_post_types']);
        }
        if (isset($input['content_auto_restrict_by_plan'])) {
            $sanitized['content_auto_restrict_by_plan'] = !empty($input['content_auto_restrict_by_plan']) ? 1 : 0;
        }
        if (isset($input['cumulative_plan_content_access'])) {
            $sanitized['cumulative_plan_content_access'] = !empty($input['cumulative_plan_content_access']) ? 1 : 0;
        }
        if (isset($input['content_show_login_hint'])) {
            $sanitized['content_show_login_hint'] = !empty($input['content_show_login_hint']) ? 1 : 0;
        }
        
        if (isset($input['auto_add_shortcodes'])) {
            $sanitized['auto_add_shortcodes'] = !empty($input['auto_add_shortcodes']) ? 1 : 0;
        }
        
        if (isset($input['show_download_box_for_non_subscription_products'])) {
            $sanitized['show_download_box_for_non_subscription_products'] = !empty($input['show_download_box_for_non_subscription_products']) ? 1 : 0;
        }

        if (isset($input['product_centric_subscription_access'])) {
            $sanitized['product_centric_subscription_access'] = !empty($input['product_centric_subscription_access']) ? 1 : 0;
        }

        if (isset($input['auto_membership_enabled'])) {
            $sanitized['auto_membership_enabled'] = !empty($input['auto_membership_enabled']) ? 1 : 0;
        }
        if (isset($input['auto_membership_plan_id'])) {
            $sanitized['auto_membership_plan_id'] = absint($input['auto_membership_plan_id']);
        }
        if (isset($input['auto_membership_new_users'])) {
            $sanitized['auto_membership_new_users'] = !empty($input['auto_membership_new_users']) ? 1 : 0;
        }
        if (isset($input['auto_membership_existing_users'])) {
            $sanitized['auto_membership_existing_users'] = !empty($input['auto_membership_existing_users']) ? 1 : 0;
        }
        if (isset($input['auto_membership_skip_reserved'])) {
            $sanitized['auto_membership_skip_reserved'] = !empty($input['auto_membership_skip_reserved']) ? 1 : 0;
        }
        if (isset($input['auto_membership_exclude_roles'])) {
            $sanitized['auto_membership_exclude_roles'] = sanitize_text_field($input['auto_membership_exclude_roles']);
        }
        if (isset($input['auto_membership_send_welcome'])) {
            $sanitized['auto_membership_send_welcome'] = !empty($input['auto_membership_send_welcome']) ? 1 : 0;
        }
        if (isset($input['auto_membership_daily_sync'])) {
            $sanitized['auto_membership_daily_sync'] = !empty($input['auto_membership_daily_sync']) ? 1 : 0;
        }
        
        if (isset($input['download_history_retention'])) {
            $sanitized['download_history_retention'] = intval($input['download_history_retention']);
        }
        

        if (isset($input['frontend_style'])) {
            $allowed_styles = array('style1', 'style2');
            $style = sanitize_text_field($input['frontend_style']);
            if (!in_array($style, $allowed_styles, true)) {
                $style = 'style1';
            }
            $sanitized['frontend_style'] = $style;
        }

        if (isset($input['subscription_plans_page_id'])) {
            $plans_page_id = absint($input['subscription_plans_page_id']);
            $sanitized['subscription_plans_page_id'] = $plans_page_id;
            if ($plans_page_id > 0 && get_post_status($plans_page_id)) {
                update_option('eshterakyar_subscription_page_id', $plans_page_id);
            }
        }

        if (array_key_exists('subscription_plans_custom_url', $input)) {
            $custom_plans_url = esc_url_raw(trim((string) wp_unslash($input['subscription_plans_custom_url'])));
            $sanitized['subscription_plans_custom_url'] = $custom_plans_url;
        }

        if (isset($input['email_subject_purchase'])) {
            $sanitized['email_subject_purchase'] = sanitize_text_field($input['email_subject_purchase']);
        }
        
        if (isset($input['email_subject_expiry_warning'])) {
            $sanitized['email_subject_expiry_warning'] = sanitize_text_field($input['email_subject_expiry_warning']);
        }
        
        if (isset($input['email_subject_expired'])) {
            $sanitized['email_subject_expired'] = sanitize_text_field($input['email_subject_expired']);
        }
        

        if (isset($input['sms_provider'])) {
            $sanitized['sms_provider'] = sanitize_text_field($input['sms_provider']);
        }
        
        if (isset($input['sms_api_key'])) {
            $sanitized['sms_api_key'] = sanitize_text_field($input['sms_api_key']);
        }
        
        if (isset($input['sms_sender'])) {
            $sanitized['sms_sender'] = sanitize_text_field($input['sms_sender']);
        }
        
        if (isset($input['sms_username'])) {
            $sanitized['sms_username'] = sanitize_text_field($input['sms_username']);
        }
        
        if (isset($input['sms_password'])) {
            $sanitized['sms_password'] = sanitize_text_field($input['sms_password']);
        }
        if (isset($input['sms_provider'])) {
            $p = sanitize_text_field($input['sms_provider']);
            $sanitized['sms_provider'] = in_array($p, array('melli', 'kavenegar', 'smsir'), true) ? $p : 'melli';
        }
        if (isset($input['sms_kavenegar_api_key'])) {
            $sanitized['sms_kavenegar_api_key'] = sanitize_text_field($input['sms_kavenegar_api_key']);
        }
        if (isset($input['sms_kavenegar_sender'])) {
            $sanitized['sms_kavenegar_sender'] = sanitize_text_field($input['sms_kavenegar_sender']);
        }
        if (isset($input['sms_smsir_api_key'])) {
            $sanitized['sms_smsir_api_key'] = sanitize_text_field($input['sms_smsir_api_key']);
        }
        if (isset($input['sms_smsir_line_number'])) {
            $sanitized['sms_smsir_line_number'] = sanitize_text_field($input['sms_smsir_line_number']);
        }
        if (isset($input['sms_smsir_verify_template_id'])) {
            $v = absint($input['sms_smsir_verify_template_id']);
            $sanitized['sms_smsir_verify_template_id'] = $v > 0 ? $v : 0;
        }
        if (isset($input['sms_smsir_verify_parameter_names'])) {
            $sanitized['sms_smsir_verify_parameter_names'] = sanitize_text_field($input['sms_smsir_verify_parameter_names']);
        }

        if (isset($input['sms_expiry_warnings']) && is_array($input['sms_expiry_warnings'])) {
            $sanitized['sms_expiry_warnings'] = array();
            foreach ($input['sms_expiry_warnings'] as $key => $warning) {
                if (is_array($warning)) {
                    $sanitized['sms_expiry_warnings'][$key] = array(
                        'enabled' => !empty($warning['enabled']) ? 1 : 0,
                        'days' => isset($warning['days']) ? intval($warning['days']) : 7,
                        'template_code' => isset($warning['template_code']) ? sanitize_text_field($warning['template_code']) : '',
                        'body_id' => isset($warning['body_id']) ? sanitize_text_field($warning['body_id']) : '',
                        'message' => isset($warning['message']) ? sanitize_textarea_field($warning['message']) : ''
                    );
                }
            }
        }
        

        if (isset($input['subscription_terms'])) {
            $sanitized['subscription_terms'] = wp_kses_post($input['subscription_terms']);
        } else {
            $sanitized['subscription_terms'] = '';
        }
        
        if (isset($input['subscription_terms_position'])) {
            $position = sanitize_text_field($input['subscription_terms_position']);
            $sanitized['subscription_terms_position'] = in_array($position, array('top', 'bottom')) ? $position : 'top';
        }

        foreach (array_keys($input) as $key) {
            if (strpos($key, 'label_') === 0) {
                $sanitized[$key] = sanitize_text_field($input[$key]);
            }
        }

        if (isset($input['sms_enable_purchase'])) {
            $sanitized['sms_enable_purchase'] = !empty($input['sms_enable_purchase']) ? 1 : 0;
        }
        if (isset($input['sms_body_id_purchase'])) {
            $sanitized['sms_body_id_purchase'] = sanitize_text_field($input['sms_body_id_purchase']);
        }
        if (isset($input['sms_message_purchase'])) {
            $sanitized['sms_message_purchase'] = sanitize_textarea_field($input['sms_message_purchase']);
        }
        

        if (isset($input['sms_enable_expired'])) {
            $sanitized['sms_enable_expired'] = !empty($input['sms_enable_expired']) ? 1 : 0;
        }
        if (isset($input['sms_body_id_expired'])) {
            $sanitized['sms_body_id_expired'] = sanitize_text_field($input['sms_body_id_expired']);
        }
        if (isset($input['sms_message_expired'])) {
            $sanitized['sms_message_expired'] = sanitize_textarea_field($input['sms_message_expired']);
        }
        

        if (isset($input['enable_cache'])) {
            $sanitized['enable_cache'] = !empty($input['enable_cache']) ? 1 : 0;
        }
        if (isset($input['cache_duration'])) {
            $sanitized['cache_duration'] = intval($input['cache_duration']);
        }
        if (isset($input['optimize_queries'])) {
            $sanitized['optimize_queries'] = !empty($input['optimize_queries']) ? 1 : 0;
        }
        

        if (isset($input['enable_ip_validation'])) {
            $sanitized['enable_ip_validation'] = !empty($input['enable_ip_validation']) ? 1 : 0;
        }
        if (isset($input['rate_limit_enabled'])) {
            $sanitized['rate_limit_enabled'] = !empty($input['rate_limit_enabled']) ? 1 : 0;
        }

        if (isset($input['rate_limit_max'])) {
            $rate_max = absint($input['rate_limit_max']);
            if ($rate_max < 1) {
                $rate_max = 10;
            }
            if ($rate_max > 500) {
                $rate_max = 500;
            }
            $sanitized['rate_limit_max'] = $rate_max;
        }

        if (isset($input['download_token_max_uses'])) {
            $token_uses = absint($input['download_token_max_uses']);
            if ($token_uses < 1) {
                $token_uses = 5;
            }
            if ($token_uses > 100) {
                $token_uses = 100;
            }
            $sanitized['download_token_max_uses'] = $token_uses;
        }

        if (isset($input['download_token_expiry_hours'])) {
            $token_hours = absint($input['download_token_expiry_hours']);
            if ($token_hours < 1) {
                $token_hours = 24;
            }
            if ($token_hours > 720) {
                $token_hours = 720;
            }
            $sanitized['download_token_expiry_hours'] = $token_hours;
        }
        

        if (isset($_POST['eshterakyar_delete_data_on_uninstall'])) {
            update_option('eshterakyar_delete_data_on_uninstall', true);
        } else {

            if (isset($_POST['option_page']) && $_POST['option_page'] === 'eshterakyar_settings') {
                update_option('eshterakyar_delete_data_on_uninstall', false);
            }
        }
        

        $checkbox_fields = array(
            'enable_notifications',
            'enable_redownload',
            'show_downloaded_badge',
            'merge_subscription_credits',
            'cumulative_plan_download_access',
            'content_restriction_enabled',
            'content_auto_restrict_by_plan',
            'cumulative_plan_content_access',
            'content_show_login_hint',
            'auto_add_shortcodes',
            'show_download_box_for_non_subscription_products',
            'product_centric_subscription_access',
            'auto_membership_enabled',
            'auto_membership_new_users',
            'auto_membership_existing_users',
            'auto_membership_skip_reserved',
            'auto_membership_send_welcome',
            'auto_membership_daily_sync',
            'sms_enable_purchase',
            'sms_enable_expired',
            'enable_cache',
            'optimize_queries',
            'enable_ip_validation',
            'rate_limit_enabled'
        );
        
        foreach ($checkbox_fields as $field) {
            if (!isset($input[$field])) {
                $sanitized[$field] = 0;
            }
        }
        
        if (!isset($input['subscription_terms_position'])) {
            $existing_options_temp = get_option('eshterakyar_options', array());
            if (isset($existing_options_temp['subscription_terms_position'])) {
                $sanitized['subscription_terms_position'] = $existing_options_temp['subscription_terms_position'];
            } else {
                $sanitized['subscription_terms_position'] = 'top';
            }
        }
        
        $existing_options = get_option('eshterakyar_options', array());
        return array_merge($existing_options, $sanitized);
    }

    public function feedback_page() {
        $this->render_page_with_wrapper('feedback.php');
    }
    
    public function logs_page() {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/eshterakyar-logs';
        
        $selected_date = isset($_GET['log_date']) ? sanitize_text_field($_GET['log_date']) : date('Y-m-d');
        $log_file = $log_dir . '/eshterakyar-' . $selected_date . '.log';
        
        $log_files = array();
        if (is_dir($log_dir)) {
            $files = glob($log_dir . '/eshterakyar-*.log');
            foreach ($files as $file) {
                $filename = basename($file);
                if (preg_match('/eshterakyar-(\d{4}-\d{2}-\d{2})\.log/', $filename, $matches)) {
                    $log_files[] = $matches[1];
                }
            }
            rsort($log_files); 
        }
        
        $log_content = '';
        $file_exists = false;
        if (file_exists($log_file) && is_readable($log_file)) {
            $file_exists = true;
            $log_content = file_get_contents($log_file);
            $lines = explode("\n", $log_content);
            if (count($lines) > 5000) {
                $lines = array_slice($lines, -5000);
                $log_content = implode("\n", $lines);
            }
        }
        
        $wp_debug_enabled = defined('WP_DEBUG') && WP_DEBUG;
        
        $debug_logs_enabled = defined('ESHTERAKYAR_DEBUG_LOGS_ENABLED') ? ESHTERAKYAR_DEBUG_LOGS_ENABLED : false;
        
        $logs_class_exists = class_exists('Eshterakyar_Logs');
        
        $vars = array(
            'log_content' => $log_content,
            'log_files' => $log_files,
            'selected_date' => $selected_date,
            'file_exists' => $file_exists,
            'wp_debug_enabled' => $wp_debug_enabled,
            'debug_logs_enabled' => $debug_logs_enabled,
            'logs_class_exists' => $logs_class_exists,
            'log_dir' => $log_dir
        );
        
        $this->render_page_with_wrapper('logs.php', $vars);
    }
        public function general_section_callback() {
        echo '<p>' . esc_html__('تنظیمات عمومی افزونه', 'eshterakyar') . '</p>';
    }
    
    public function email_section_callback() {
        echo '<p>' . esc_html__('تنظیمات مربوط به ایمیل‌های اطلاع‌رسانی', 'eshterakyar') . '</p>';
    }
    
    public function sms_section_callback() {
        echo '<p>' . esc_html__('تنظیمات مربوط به پیامک‌های اطلاع‌رسانی', 'eshterakyar') . '</p>';
    }
    
    public function text_field_callback($args) {
        $options = get_option('eshterakyar_options');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : (isset($args['default']) ? $args['default'] : '');
        $type = isset($args['type']) ? $args['type'] : 'text';
        echo '<input type="' . esc_attr($type) . '" name="eshterakyar_options[' . esc_attr($args['field']) . ']" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    
    public function number_field_callback($args) {
        $options = get_option('eshterakyar_options');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : (isset($args['default']) ? $args['default'] : 0);
        echo '<input type="number" name="eshterakyar_options[' . esc_attr($args['field']) . ']" value="' . esc_attr($value) . '" class="small-text" />';
    }
    
    public function checkbox_field_callback($args) {
        $options = get_option('eshterakyar_options');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : 0;
        echo '<input type="checkbox" name="eshterakyar_options[' . esc_attr($args['field']) . ']" value="1" ' . checked(1, $value, false) . ' />';
        if (isset($args['label'])) {
            echo ' <label>' . esc_html($args['label']) . '</label>';
        }
    }
    private function render_page_with_wrapper($template_file, $vars = array()) {
        extract($vars);
        
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/admin/page-wrapper.php';
        
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/admin/' . $template_file;
        
        echo '</div></div></div></div>';
    }
}

