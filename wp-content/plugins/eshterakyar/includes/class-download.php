<?php

if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Download {
    
    /**
     * Record download
     * @param int $user_id
     * @param int $subscription_id
     * @param int $product_id
     * @param bool $is_redownload
     * @return int Download ID
     */
    public static function record_download(int $user_id, int $subscription_id, int $product_id, bool $is_redownload = false): int {
        global $wpdb;
        
        $existing = self::get_download($user_id, $subscription_id, $product_id);
        
        $table = $wpdb->prefix . 'subscription_downloads';
        $data = array(
            'user_id' => $user_id,
            'subscription_id' => $subscription_id,
            'product_id' => $product_id,
            'download_date' => current_time('mysql'),
            'ip_address' => self::get_client_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : null
        );
        
        if ($existing) {
            $wpdb->update(
                $table,
                array(
                    'download_date' => current_time('mysql'),
                    'download_count' => $existing->download_count + 1,
                    'ip_address' => self::get_client_ip()
                ),
                array('id' => $existing->id)
            );
            $download_id = $existing->id;
        } else {
            $wpdb->insert($table, $data);
            $download_id = $wpdb->insert_id;
        }

        $options = get_option('eshterakyar_options', array());
        $enable_redownload = isset($options['enable_redownload']) ? (int) $options['enable_redownload'] : 1;
        $counts_toward_quota = !$is_redownload || !$enable_redownload;
        if (!$existing || $counts_toward_quota) {
            self::update_subscription_stats($subscription_id);
        }
        
        return $download_id;
    }

    /**
     * Get download record
     * @param int $user_id
     * @param int $subscription_id
     * @param int $product_id
     * @return object|null
     */
    public static function get_download(int $user_id, int $subscription_id, int $product_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_downloads';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM `{$table_name}` 
             WHERE user_id = %d AND subscription_id = %d AND product_id = %d 
             ORDER BY download_date DESC LIMIT 1",
            $user_id, $subscription_id, $product_id
        ));
    }
    
    /**
     * Reset per-product access window so user can get a new grant (like WooCommerce re-purchase).
     * Sets download_date to now and download_count to 0 for the given (user, subscription, product).
     *
     * @param int $user_id
     * @param int $subscription_id
     * @param int $product_id
     * @return bool
     */
    public static function reset_product_window(int $user_id, int $subscription_id, int $product_id): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_downloads';
        $existing = self::get_download($user_id, $subscription_id, $product_id);
        if (!$existing) {
            return false;
        }
        $result = $wpdb->update(
            $table,
            array(
                'download_date' => current_time('mysql'),
                'download_count' => 0
            ),
            array('id' => $existing->id)
        );
        return $result !== false;
    }

    /**
     * Get first (earliest) download date for user/subscription/product
     * Used for per-plan download access window (e.g. 7 days from first download).
     *
     * @param int $user_id
     * @param int $subscription_id
     * @param int $product_id
     * @return string|null MySQL datetime or null
     */
    public static function get_first_download_date(int $user_id, int $subscription_id, int $product_id): ?string {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_downloads';
        $date = $wpdb->get_var($wpdb->prepare(
            "SELECT MIN(download_date) FROM `{$table_name}` 
             WHERE user_id = %d AND subscription_id = %d AND product_id = %d",
            $user_id, $subscription_id, $product_id
        ));
        return $date ? $date : null;
    }

    /**
     * Check if product was downloaded
     * @param int $user_id
     * @param int $subscription_id
     * @param int $product_id
     * @return bool
     */
    public static function is_downloaded(int $user_id, int $subscription_id, int $product_id): bool {
        $download = self::get_download($user_id, $subscription_id, $product_id);
        return $download ? true : false;
    }
    

    /**
     * Check if product was downloaded in any subscription
     * @param int $user_id
     * @param int $product_id
     * @return bool
     */
    public static function is_downloaded_any_subscription(int $user_id, int $product_id): bool {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_downloads';
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM `{$table_name}` 
             WHERE user_id = %d AND product_id = %d",
            $user_id, $product_id
        ));
        
        return $count > 0;
    }

    /**
     * Whether the user may re-download a product via a plan with lifetime access.
     *
     * @param int $user_id
     * @param int $product_id
     * @return bool
     */
    public static function user_has_lifetime_access_to_product(int $user_id, int $product_id): bool {
        global $wpdb;

        $downloads_table = $wpdb->prefix . 'subscription_downloads';
        $subscriptions_table = $wpdb->prefix . 'subscriptions';
        $plans_table = $wpdb->prefix . 'subscription_plans';

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM `{$downloads_table}` d
             INNER JOIN `{$subscriptions_table}` s ON d.subscription_id = s.id
             INNER JOIN `{$plans_table}` p ON s.plan_id = p.id
             WHERE d.user_id = %d AND d.product_id = %d AND p.lifetime_access = 1",
            $user_id,
            $product_id
        ));

        return (int) $count > 0;
    }
    
    public static function get_download_any_subscription(int $user_id, int $product_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_downloads';
        $subscriptions_table = $wpdb->prefix . 'subscriptions';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT d.*, s.status as subscription_status, s.end_date as subscription_end_date
             FROM `{$table_name}` d 
             LEFT JOIN `{$subscriptions_table}` s ON d.subscription_id = s.id
             WHERE d.user_id = %d AND d.product_id = %d 
             ORDER BY d.download_date DESC 
             LIMIT 1",
            $user_id, $product_id
        ));
    }
    

    /**
     * Get all user downloads
     * @param int $user_id
     * @return array
     */
    public static function get_all_user_downloads(int $user_id): array {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_downloads';
        $subscriptions_table = $wpdb->prefix . 'subscriptions';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT d.*, p.post_title as product_name, s.status as subscription_status
             FROM `{$table_name}` d 
             LEFT JOIN `{$wpdb->posts}` p ON d.product_id = p.ID 
             LEFT JOIN `{$subscriptions_table}` s ON d.subscription_id = s.id
             WHERE d.user_id = %d 
             ORDER BY d.download_date DESC",
            $user_id
        ));
        return $results ? $results : array();
    }
    
    /**
     * Get user downloads
     * @param int $user_id
     * @param int|null $subscription_id
     * @return array
     */
    public static function get_user_downloads(int $user_id, ?int $subscription_id = null): array {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_downloads';
        
        $user_id = absint($user_id);
        $where = "WHERE d.user_id = %d";
        $params = array($user_id);
        
        if ($subscription_id) {
            $subscription_id = absint($subscription_id);
            $where .= " AND d.subscription_id = %d";
            $params[] = $subscription_id;
        }
        
        $query = "SELECT d.*, p.post_title as product_name, p.guid as product_url 
                  FROM {$table_name} d 
                  LEFT JOIN {$wpdb->posts} p ON d.product_id = p.ID 
                  {$where} 
                  ORDER BY d.download_date DESC";
        
        $results = $wpdb->get_results($wpdb->prepare($query, $params));
        return $results ? $results : array();
    }

    /**
     * Update subscription download statistics
     * @param int $subscription_id
     * @return void
     */
    private static function update_subscription_stats(int $subscription_id): void {
        global $wpdb;
        $table = $wpdb->prefix . 'subscriptions';
        $downloads_table = $wpdb->prefix . 'subscription_downloads';
        
        $subscription = Eshterakyar_Subscription::get_by_id($subscription_id);
        if (!$subscription) {
            return;
        }
        
        $today = current_time('Y-m-d');
        $today_start = $today . ' 00:00:00';
        $today_end = $today . ' 23:59:59';
        
        $downloads_table_name = $wpdb->prefix . 'subscription_downloads';
        
        $daily_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT product_id) FROM `{$downloads_table_name}` 
             WHERE subscription_id = %d AND download_date >= %s AND download_date <= %s",
            $subscription_id, $today_start, $today_end
        ));
        
        $total_used = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT product_id) FROM `{$downloads_table_name}` 
             WHERE subscription_id = %d",
            $subscription_id
        ));
        
        $update_data = array(
            'downloads_used' => $total_used,
            'daily_downloads_count' => $daily_count,
            'last_download_date' => $today
        );
        
        $wpdb->update(
            $table,
            $update_data,
            array('id' => $subscription_id)
        );
    }
    
    /**
     * Get client IP address
     * @return string
     */
    public static function get_client_ip(): string {
        return function_exists('eshterakyar_detect_client_ip') ? eshterakyar_detect_client_ip() : '0.0.0.0';
    }
    
    /**
     * Generate a secure download token
     * @param int $user_id
     * @param int $product_id
     * @param int|null $subscription_id
     * @param int|null $expires_in_hours Hours until token expires (null = from settings)
     * @param int|null $max_uses Maximum uses (null = from settings)
     * @return string|false Token string or false on failure
     */
    public static function generate_download_token(int $user_id, int $product_id, ?int $subscription_id = null, ?int $expires_in_hours = null, ?int $max_uses = null) {
        global $wpdb;

        $options = get_option('eshterakyar_options', array());
        if ($expires_in_hours === null) {
            $expires_in_hours = isset($options['download_token_expiry_hours'])
                ? absint($options['download_token_expiry_hours'])
                : 24;
        }
        if ($max_uses === null) {
            $max_uses = isset($options['download_token_max_uses'])
                ? absint($options['download_token_max_uses'])
                : 5;
        }
        if ($expires_in_hours < 1) {
            $expires_in_hours = 24;
        }
        if ($max_uses < 1) {
            $max_uses = 5;
        }
        
        $table = $wpdb->prefix . 'subscription_download_tokens';
        
        $token = bin2hex(random_bytes(32));
        
        $expires_at = date('Y-m-d H:i:s', strtotime("+{$expires_in_hours} hours"));
        
        $data = array(
            'token' => $token,
            'user_id' => $user_id,
            'product_id' => $product_id,
            'subscription_id' => $subscription_id,
            'expires_at' => $expires_at,
            'max_uses' => $max_uses,
            'used_count' => 0
        );
        
        $result = $wpdb->insert($table, $data);
        
        if ($result === false) {
            return false;
        }
        
        return $token;
    }
    
    /**
     * Validate and get token data
     * @param string $token
     * @return object|null Token data or null if invalid
     */
    public static function validate_download_token(string $token): ?object {
        global $wpdb;
        
        $table = $wpdb->prefix . 'subscription_download_tokens';
        
        $token_data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM `{$table}` WHERE token = %s",
            $token
        ));
        
        if (!$token_data) {
            return null;
        }
        
        if (strtotime($token_data->expires_at) < time()) {
            $wpdb->delete($table, array('id' => $token_data->id));
            return null;
        }
        
        if ($token_data->used_count >= $token_data->max_uses) {
            return null;
        }
        
        return $token_data;
    }
    
    /**
     * Increment token usage count
     * @param string $token
     * @return bool
     */
    public static function increment_token_usage(string $token): bool {
        global $wpdb;
        
        $table = $wpdb->prefix . 'subscription_download_tokens';
        
        $result = $wpdb->query($wpdb->prepare(
            "UPDATE `{$table}` SET used_count = used_count + 1 WHERE token = %s",
            $token
        ));
        
        return $result !== false;
    }
    
    /**
     * لیست فایل‌های قابل دانلود ووکامرس برای نمایش در فرانت (ساده / متغیر، چند پارت).
     *
     * @param int $product_id شناسهٔ محصول اصلی (پدر در محصول متغیر)
     * @return array<int, array{file_ref: string, label: string}>
     */
    public static function get_product_download_entries(int $product_id): array {
        if (!function_exists('wc_get_product')) {
            return array();
        }
        $product = wc_get_product($product_id);
        if (!$product) {
            return array();
        }
        $out = array();
        $part_index = 1;
        if ($product->is_type('variable')) {
            foreach ($product->get_children() as $vid) {
                $v = wc_get_product($vid);
                if (!$v || !$v->is_downloadable()) {
                    continue;
                }
                $vdownloads = $v->get_downloads();
                if (empty($vdownloads)) {
                    continue;
                }
                $var_label = $v->get_name();
                foreach ($vdownloads as $dlid => $dl) {
                    $ref = (string) $vid . ':' . (string) $dlid;
                    $name = $dl->get_name();
                    if ($name === '' || $name === null) {
                        $name = sprintf(__('پارت %d', 'eshterakyar'), $part_index);
                    }
                    if ($var_label && $var_label !== $product->get_name()) {
                        $name = $var_label . ' — ' . $name;
                    }
                    $out[] = array('file_ref' => $ref, 'label' => $name);
                    $part_index++;
                }
            }
        } else {
            if (!$product->is_downloadable()) {
                return array();
            }
            foreach ($product->get_downloads() as $dlid => $dl) {
                $name = $dl->get_name();
                if ($name === '' || $name === null) {
                    $name = sprintf(__('پارت %d', 'eshterakyar'), $part_index);
                }
                $out[] = array('file_ref' => (string) $dlid, 'label' => $name);
                $part_index++;
            }
        }
        return $out;
    }

    /**
     * انتخاب شیء دانلود ووکامرس بر اساس مرجع فایل (شناسهٔ دانلود یا vid:dlid).
     *
     * @param int         $product_id شناسه محصول در فروشگاه
     * @param string|null $file_ref   null = اولین فایل موجود
     * @return array{product: WC_Product, download: \WC_Product_Download, file_ref: string}|null
     */
    public static function resolve_download_by_file_ref(int $product_id, ?string $file_ref): ?array {
        if (!function_exists('wc_get_product')) {
            return null;
        }
        $product = wc_get_product($product_id);
        if (!$product) {
            return null;
        }
        $norm = (is_string($file_ref) && $file_ref !== '') ? $file_ref : null;

        if ($product->is_type('variable')) {
            if ($norm !== null && strpos($norm, ':') !== false) {
                $pair = explode(':', $norm, 2);
                $vid = isset($pair[0]) ? (int) $pair[0] : 0;
                $dlid = isset($pair[1]) ? $pair[1] : '';
                if ($vid > 0 && $dlid !== '') {
                    $v = wc_get_product($vid);
                    if ($v && $v->is_downloadable()) {
                        $dls = $v->get_downloads();
                        if (isset($dls[$dlid])) {
                            return array('product' => $v, 'download' => $dls[$dlid], 'file_ref' => $norm);
                        }
                    }
                }
            }
            if ($norm !== null) {
                foreach ($product->get_children() as $vid) {
                    $v = wc_get_product($vid);
                    if (!$v || !$v->is_downloadable()) {
                        continue;
                    }
                    $dls = $v->get_downloads();
                    if (isset($dls[$norm])) {
                        return array('product' => $v, 'download' => $dls[$norm], 'file_ref' => $norm);
                    }
                }
            }
            foreach ($product->get_children() as $vid) {
                $v = wc_get_product($vid);
                if ($v && $v->is_downloadable()) {
                    $dls = $v->get_downloads();
                    if (!empty($dls)) {
                        $first_id = array_key_first($dls);
                        return array('product' => $v, 'download' => $dls[$first_id], 'file_ref' => (string) $first_id);
                    }
                }
            }
            return null;
        }

        if (!$product->is_downloadable()) {
            return null;
        }
        $dls = $product->get_downloads();
        if (empty($dls)) {
            return null;
        }
        if ($norm !== null && isset($dls[$norm])) {
            return array('product' => $product, 'download' => $dls[$norm], 'file_ref' => $norm);
        }
        if ($norm === null) {
            $first_id = array_key_first($dls);
            return array('product' => $product, 'download' => $dls[$first_id], 'file_ref' => (string) $first_id);
        }
        return null;
    }

    /**
     * لینک دانلود با nonce (بدون insert توکن) — برای لیست چندپارته تا از فشار به دیتابیس جلوگیری شود.
     *
     * @param int         $product_id
     * @param string|null $file_ref
     */
    public static function build_nonce_download_url(int $product_id, ?string $file_ref = null): string {
        $nonce = wp_create_nonce('eshterakyar_download_' . $product_id);
        $args = array(
            'eshterakyar_download' => $product_id,
            'nonce' => $nonce,
        );
        if (is_string($file_ref) && $file_ref !== '') {
            $args['eshterakyar_file'] = $file_ref;
        }
        return add_query_arg($args, home_url('/'));
    }

    /**
     * HTML منوی جمع‌شوندهٔ دانلود چندپارته (جزئیات بازشو + لیست اسکرول‌دار).
     *
     * @param int  $product_id
     * @param bool $redownload_uses_quota
     * @return string خالی اگر کمتر از ۲ پارت باشد
     */
    public static function render_multipart_download_list_html(int $product_id, bool $redownload_uses_quota, array $args = array()): string {
        $entries = self::get_product_download_entries($product_id);
        if (count($entries) < 2) {
            return '';
        }
        $link_class = isset($args['link_class']) ? $args['link_class'] : 'button eshterakyar-download-part-link';
        $summary_text = isset($args['summary_text']) ? $args['summary_text'] : sprintf(
            __('باز کردن فهرست دانلود (%d پارت)', 'eshterakyar'),
            count($entries)
        );
        ob_start();
        echo '<details class="eshterakyar-download-parts">';
        echo '<summary class="eshterakyar-download-parts-summary">' . esc_html($summary_text) . '</summary>';
        echo '<div class="eshterakyar-download-parts-inner"><ul class="eshterakyar-download-parts-list">';
        foreach ($entries as $row) {
            $url = self::build_nonce_download_url($product_id, $row['file_ref']);
            $cls = esc_attr($link_class . ($redownload_uses_quota ? ' eshterakyar-redownload-with-quota' : ''));
            echo '<li><a href="' . esc_url($url) . '" class="' . $cls . '" target="_blank" rel="noopener noreferrer">' . esc_html($row['label']) . '</a></li>';
        }
        echo '</ul></div></details>';
        return ob_get_clean();
    }

    /**
     * Get download URL with token
     * @param int         $user_id
     * @param int         $product_id
     * @param int|null    $subscription_id
     * @param string|null $file_ref مرجع فایل ووکامرس (اختیاری، برای چند پارتی با همان توکن)
     * @return string|false Download URL or false on failure
     */
    public static function get_tokenized_download_url(int $user_id, int $product_id, ?int $subscription_id = null, ?string $file_ref = null) {
        $previous_download = self::get_download_any_subscription($user_id, $product_id);
        $is_redownload = ($previous_download !== null);
        $can_download_result = Eshterakyar_Subscription::can_download($user_id, $product_id, $is_redownload);
        $can_download = $can_download_result['can'];
        if (!$can_download) {
            return false;
        }
        if (!empty($can_download_result['subscription'])) {
            $subscription_id = $can_download_result['subscription']->id;
        } elseif ($previous_download && isset($previous_download->subscription_id)) {
            $subscription_id = $previous_download->subscription_id;
        } elseif (!empty($can_download_result['lifetime_access']) && $can_download_result['lifetime_access']) {
            // For lifetime access, use null subscription_id to indicate no active subscription
            $subscription_id = null;
        }
        
        $token = self::generate_download_token($user_id, $product_id, $subscription_id);
        
        if ($token) {
            $url = add_query_arg(array(
                'eshterakyar_token' => $token,
            ), home_url('/'));
            if (is_string($file_ref) && $file_ref !== '') {
                return add_query_arg('eshterakyar_file', $file_ref, $url);
            }
            return $url;
        }
        
        if ($can_download) {
            return self::build_nonce_download_url($product_id, $file_ref);
        }
        
        return false;
    }
    
    /**
     * Clean up expired tokens
     * @return int Number of deleted tokens
     */
    public static function cleanup_expired_tokens(): int {
        global $wpdb;
        
        $table = $wpdb->prefix . 'subscription_download_tokens';
        
        $deleted = $wpdb->query(
            "DELETE FROM `{$table}` WHERE expires_at < NOW()"
        );
        
        return $deleted !== false ? $deleted : 0;
    }
}

