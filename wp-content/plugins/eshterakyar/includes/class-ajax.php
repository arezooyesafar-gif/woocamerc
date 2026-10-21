<?php


if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Ajax {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_loaded', array($this, 'handle_download'), 1);
        add_action('wp_login', array($this, 'refresh_stored_ip_on_login'), 10, 2);

        add_action('wp_ajax_eshterakyar_get_subscription_info', array($this, 'get_subscription_info'));
        add_action('wp_ajax_eshterakyar_cancel_subscription', array($this, 'cancel_subscription'));
    }

    /**
     * پس از ورود موفق، IP ذخیره‌شده را با IP فعلی همگام می‌کند تا حلقهٔ «دوباره وارد شو» برطرف شود.
     *
     * @param string  $user_login
     * @param WP_User $user
     */
    public function refresh_stored_ip_on_login($user_login, $user) {
        $options = get_option('eshterakyar_options', array());
        if (empty($options['enable_ip_validation'])) {
            return;
        }
        if ($user instanceof WP_User && $user->ID) {
            update_user_meta($user->ID, 'eshterakyar_last_ip', $this->get_client_ip());
        }
    }
    public function handle_download() {
        if (isset($_GET['eshterakyar_token'])) {
            $this->handle_tokenized_download();
            return;
        }
        
        if (isset($_GET['eshterakyar_download']) && isset($_GET['nonce'])) {
            $this->handle_legacy_download();
            return;
        }
    }
    
    private function handle_tokenized_download() {
        $token = sanitize_text_field($_GET['eshterakyar_token']);
        
        if (empty($token)) {
            wp_die(esc_html__('توکن دانلود نامعتبر است', 'eshterakyar'), esc_html__('خطای امنیتی', 'eshterakyar'), array('response' => 403));
        }
        
        $token_data = Eshterakyar_Download::validate_download_token($token);
        
        if (!$token_data) {
            wp_die(esc_html__('توکن دانلود نامعتبر یا منقضی شده است', 'eshterakyar'), esc_html__('خطای امنیتی', 'eshterakyar'), array('response' => 403));
        }
        
        $user_id = intval($token_data->user_id);
        $product_id = intval($token_data->product_id);
        $subscription_id = $token_data->subscription_id ? intval($token_data->subscription_id) : null;
        
        if (!is_user_logged_in() || get_current_user_id() != $user_id) {
            wp_die(esc_html__('برای دانلود باید وارد حساب کاربری شوید', 'eshterakyar'), esc_html__('دسترسی غیرمجاز', 'eshterakyar'), array('response' => 403));
        }
        
        if (!$this->check_rate_limit($user_id)) {
            status_header(429);
            wp_die(esc_html__('تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً چند لحظه صبر کنید.', 'eshterakyar'), esc_html__('محدودیت درخواست', 'eshterakyar'), array('response' => 429));
        }
        
        $previous_download = Eshterakyar_Download::get_download_any_subscription($user_id, $product_id);
        $is_redownload = ($previous_download !== null);
        
        $can_download_result = Eshterakyar_Subscription::can_download($user_id, $product_id, $is_redownload);
        $can_download = $can_download_result['can'];
        $subscription = $can_download_result['subscription'] ?? null;
        
        if (!$can_download) {
            status_header(403);
            $message = isset($can_download_result['message']) ? $can_download_result['message'] : esc_html__('برای دانلود این محصول نیاز به اشتراک فعال دارید', 'eshterakyar');
            wp_die($message, esc_html__('دسترسی غیرمجاز', 'eshterakyar'), array('response' => 403));
        }
        
        if (!$subscription) {
            $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        }
        
        $options = get_option('eshterakyar_options', array());
        if (isset($options['enable_ip_validation']) && $options['enable_ip_validation']) {
            if (!$this->validate_ip_address($user_id)) {
                status_header(403);
                wp_die(esc_html__('آدرس IP شما تغییر کرده است. لطفاً دوباره وارد شوید.', 'eshterakyar'), esc_html__('خطای امنیتی', 'eshterakyar'), array('response' => 403));
            }
        }
        
        Eshterakyar_Download::increment_token_usage($token);
        
        $record_subscription_id = $subscription ? $subscription->id : ($previous_download ? $previous_download->subscription_id : null);
        if ($record_subscription_id) {
            if (!empty($can_download_result['reset_product_window'])) {
                Eshterakyar_Download::reset_product_window($user_id, $record_subscription_id, $product_id);
            }
            Eshterakyar_Download::record_download($user_id, $record_subscription_id, $product_id, $is_redownload);
        }
        
        $file_ref = isset($_GET['eshterakyar_file']) ? sanitize_text_field(wp_unslash($_GET['eshterakyar_file'])) : '';
        $this->serve_resolved_product_file($product_id, $file_ref !== '' ? $file_ref : null);
    }
    
    private function handle_legacy_download() {
        $product_id = intval($_GET['eshterakyar_download']);
        $nonce = sanitize_text_field($_GET['nonce']);
        
        if (!wp_verify_nonce($nonce, 'eshterakyar_download_' . $product_id)) {
            wp_die(esc_html__('خطای امنیتی: Nonce نامعتبر', 'eshterakyar'), esc_html__('خطای امنیتی', 'eshterakyar'), array('response' => 403));
        }
        
        if (!is_user_logged_in()) {
            wp_die(esc_html__('برای دانلود باید وارد حساب کاربری شوید', 'eshterakyar'), esc_html__('دسترسی غیرمجاز', 'eshterakyar'), array('response' => 403));
        }
        
        $user_id = get_current_user_id();
        
        if (!$this->check_rate_limit($user_id)) {
            status_header(429);
            wp_die(esc_html__('تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً چند لحظه صبر کنید.', 'eshterakyar'), esc_html__('محدودیت درخواست', 'eshterakyar'), array('response' => 429));
        }
        
        $previous_download = Eshterakyar_Download::get_download_any_subscription($user_id, $product_id);
        $is_redownload = ($previous_download !== null);
        
        $can_download_result = Eshterakyar_Subscription::can_download($user_id, $product_id, $is_redownload);
        $can_download = $can_download_result['can'];
        $subscription = $can_download_result['subscription'] ?? null;
        
        if (!$can_download) {
            status_header(403);
            $message = isset($can_download_result['message']) ? $can_download_result['message'] : esc_html__('برای دانلود این محصول نیاز به اشتراک فعال دارید', 'eshterakyar');
            wp_die($message, esc_html__('دسترسی غیرمجاز', 'eshterakyar'), array('response' => 403));
        }
        
        if (!$subscription) {
            $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        }
        
        $options = get_option('eshterakyar_options', array());
        if (isset($options['enable_ip_validation']) && $options['enable_ip_validation']) {
            if (!$this->validate_ip_address($user_id)) {
                status_header(403);
                wp_die(esc_html__('آدرس IP شما تغییر کرده است. لطفاً دوباره وارد شوید.', 'eshterakyar'), esc_html__('خطای امنیتی', 'eshterakyar'), array('response' => 403));
            }
        }
        
        $record_subscription_id = $subscription ? $subscription->id : ($previous_download ? $previous_download->subscription_id : null);
        if ($record_subscription_id) {
            if (!empty($can_download_result['reset_product_window'])) {
                Eshterakyar_Download::reset_product_window($user_id, $record_subscription_id, $product_id);
            }
            Eshterakyar_Download::record_download($user_id, $record_subscription_id, $product_id, $is_redownload);
        }
        
        $file_ref = isset($_GET['eshterakyar_file']) ? sanitize_text_field(wp_unslash($_GET['eshterakyar_file'])) : '';
        $this->serve_resolved_product_file($product_id, $file_ref !== '' ? $file_ref : null);
    }

    /**
     * ارائهٔ فایل انتخاب‌شده برای product_id با پشتیبانی چند پارت.
     *
     * @param int         $product_id شناسه محصول در URL / توکن
     * @param string|null $file_ref   مقدار eshterakyar_file یا null برای اولین فایل
     */
    private function serve_resolved_product_file($product_id, ?string $file_ref) {
        if (!class_exists('WooCommerce') || !function_exists('wc_get_product')) {
            status_header(500);
            wp_die(esc_html__('WooCommerce فعال نیست', 'eshterakyar'), esc_html__('خطا', 'eshterakyar'), array('response' => 500));
        }
        $resolved = Eshterakyar_Download::resolve_download_by_file_ref((int) $product_id, $file_ref);
        if (!$resolved) {
            status_header(404);
            wp_die(esc_html__('فایل دانلود درخواستی برای این محصول یافت نشد.', 'eshterakyar'), esc_html__('خطا', 'eshterakyar'), array('response' => 404));
        }
        $this->serve_file_securely($resolved['download']->get_file(), $product_id);
    }
    
    /**
     * Serve file securely through PHP to hide actual URL
     * @param string $file_url
     * @param int $product_id
     */
    private function serve_file_securely($file_url, $product_id) {
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $parsed_url = parse_url($file_url);
        $file_host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $site_host = parse_url(home_url(), PHP_URL_HOST);
        
        $file_host_clean = preg_replace('/^www\./', '', $file_host);
        $site_host_clean = preg_replace('/^www\./', '', $site_host);
        
        $file_domain = $this->get_base_domain($file_host_clean);
        $site_domain = $this->get_base_domain($site_host_clean);
        $is_external = ($file_host && ($file_host_clean !== $site_host_clean || $file_domain !== $site_domain));
        
        if ($is_external) {
            if (!headers_sent()) {
                header('Location: ' . $file_url, true, 302);
            } else {
                wp_send_json_success(array('redirect' => esc_url($file_url)));
            }
            exit;
        }
        
        $upload_dir = wp_upload_dir();
        $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_url);
        
        if (!file_exists($file_path)) {
            $file_path = str_replace(home_url('/'), ABSPATH, $file_url);
        }
        
        if (!file_exists($file_path) && isset($parsed_url['path'])) {
            $file_path = ABSPATH . ltrim($parsed_url['path'], '/');
        }
        
        if (!file_exists($file_path) || !is_file($file_path)) {
            if (!headers_sent()) {
                wp_safe_redirect($file_url, 302);
            } else {
                wp_send_json_success(array('redirect' => esc_url($file_url)));
            }
            exit;
        }
        
        $filename = basename($file_path);
        $file_size = filesize($file_path);
        $mime_type = wp_check_filetype($filename);
        $mime_type = $mime_type['type'] ? $mime_type['type'] : 'application/octet-stream';
        
        if (!headers_sent()) {
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . $file_size);
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Expires: 0');
            header('X-Content-Type-Options: nosniff');
            
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            $handle = fopen($file_path, 'rb');
            if ($handle) {
                while (!feof($handle)) {
                    echo fread($handle, 8192);
                    flush();
                }
                fclose($handle);
            }
        }
        
        exit;
    }
    private function check_rate_limit($user_id) {
        $options = get_option('eshterakyar_options', array());
        $enabled = !isset($options['rate_limit_enabled']) || !empty($options['rate_limit_enabled']);
        if (!$enabled) {
            return true;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_downloads';
        $ip = sanitize_text_field($this->get_client_ip());
        $user_id = intval($user_id);
        $time_limit = 60;
        $max_requests = isset($options['rate_limit_max']) ? absint($options['rate_limit_max']) : 10;
        if ($max_requests < 1) {
            $max_requests = 10;
        }

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM `{$table_name}` 
             WHERE (user_id = %d OR ip_address = %s) 
             AND download_date > DATE_SUB(NOW(), INTERVAL %d SECOND)",
            $user_id, $ip, $time_limit
        ));

        return (int) $count < $max_requests;
    }
    private function validate_ip_address($user_id) {
        $current_ip = $this->get_client_ip();
        $stored_ip = get_user_meta($user_id, 'eshterakyar_last_ip', true);
        
        if (empty($stored_ip)) {
            update_user_meta($user_id, 'eshterakyar_last_ip', $current_ip);
            return true;
        }
        
        return $current_ip === $stored_ip;
    }
    
    /**
     * بررسی اعتبار URL دانلود
     * 
     * @param string $url URL فایل
     * @param bool $from_woocommerce آیا فایل از WooCommerce آمده (یعنی در get_downloads() است)
     * @return bool
     */
    private function is_valid_download_url($url, $from_woocommerce = false) {
        if (empty($url)) {
            return false;
        }
        
        $parsed_url = parse_url($url);
        if (!$parsed_url || !isset($parsed_url['scheme'])) {
            return false;
        }
        
        if (!in_array($parsed_url['scheme'], array('http', 'https'))) {
            return false;
        }
        
        $url_host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        
        if (empty($url_host)) {
            return false;
        }
        
        $site_host = parse_url(home_url(), PHP_URL_HOST);
        $site_domain = $this->get_base_domain($site_host);
        
        $allowed_hosts = array(
            $site_host, 
            $site_domain, 
            'woocommerce.com',
            'wordpress.org'
        );
        
        $url_domain = $this->get_base_domain($url_host);
        if ($url_domain === $site_domain) {
            return true; 
        }
        
        foreach ($allowed_hosts as $host) {
            if ($url_host === $host || strpos($url_host, '.' . $host) !== false) {
                return true;
            }
        }
        
        $upload_dir = wp_upload_dir();
        $upload_path = parse_url($upload_dir['baseurl'], PHP_URL_PATH);
        
        if (strpos($url, $upload_path) !== false || strpos($url, '/wp-content/') !== false) {
            return true;
        }

        if ($from_woocommerce) {
            $cdn_hosts = array(
                'amazonaws.com', 
                's3.amazonaws.com',
                'cloudfront.net', 
                'googleapis.com', 
                'azureedge.net', 
                'r2.cloudflarestorage.com', 
                'digitaloceanspaces.com', 
                'backblazeb2.com', 
            );
            
            foreach ($cdn_hosts as $cdn_host) {
                if (strpos($url_host, $cdn_host) !== false) {
                    return true;
                }
            }
            return true;
        }
        
        return false;
    }
    private function get_base_domain($host) {
        $host = preg_replace('/^www\./', '', $host);
        
        $parts = explode('.', $host);
        
        if (count($parts) >= 2) {
            return $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
        }
        
        return $host;
    }
    
    private function get_client_ip() {
        return function_exists('eshterakyar_detect_client_ip') ? eshterakyar_detect_client_ip() : '0.0.0.0';
    }
    
    private function force_download($file_url) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        $upload_dir = wp_upload_dir();
        $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_url);
        
        if (!file_exists($file_path)) {
            if (!headers_sent()) {
                wp_safe_redirect($file_url);
            } else {
                echo '<script>window.location.href = "' . esc_js($file_url) . '";</script>';
            }
            exit;
        }
        
        $filename = basename($file_path);
        
        if (!headers_sent()) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Expires: 0');
        }
        
        readfile($file_path);
        exit;
    }
    public function get_subscription_info() {
        check_ajax_referer('eshterakyar_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => esc_html__('نیاز به ورود', 'eshterakyar')));
        }
        
        $user_id = get_current_user_id();
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        
        if (!$subscription) {
            wp_send_json_error(array('message' => esc_html__('اشتراک فعالی ندارید', 'eshterakyar')));
        }
        
        $days_left = Eshterakyar_Subscription::get_days_left($subscription->id);
        
        wp_send_json_success(array(
            'subscription' => array(
                'id' => $subscription->id,
                'plan_name' => $subscription->plan_name,
                'end_date' => $subscription->end_date,
                'days_left' => $days_left,
                'downloads_used' => $subscription->downloads_used,
                'total_downloads' => $subscription->total_downloads,
                'daily_downloads_count' => $subscription->daily_downloads_count,
                'daily_limit' => $subscription->daily_limit
            )
        ));
    }
    
    public function cancel_subscription() {
        check_ajax_referer('eshterakyar_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('دسترسی غیرمجاز', 'eshterakyar')));
        }
        
        $subscription_id = isset($_POST['subscription_id']) ? intval($_POST['subscription_id']) : 0;
        
        if (!$subscription_id) {
            wp_send_json_error(array('message' => esc_html__('شناسه اشتراک نامعتبر', 'eshterakyar')));
        }
        
        $result = Eshterakyar_Subscription::cancel($subscription_id);
        
        if ($result) {
            wp_send_json_success(array('message' => esc_html__('اشتراک با موفقیت لغو شد', 'eshterakyar')));
        } else {
            wp_send_json_error(array('message' => esc_html__('خطا در لغو اشتراک', 'eshterakyar')));
        }
    }
}

