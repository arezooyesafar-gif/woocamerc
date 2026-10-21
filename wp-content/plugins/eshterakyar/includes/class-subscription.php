<?php

if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Subscription {
    /**
     * Parse CSV term IDs from plan fields.
     *
     * @param string $raw_ids
     * @return array<int>
     */
    private static function parse_plan_term_ids(string $raw_ids): array {
        if ($raw_ids === '') {
            return array();
        }
        $ids = array_map('intval', explode(',', $raw_ids));
        $ids = array_filter($ids, static function($id) {
            return $id > 0;
        });
        return array_values(array_unique($ids));
    }

    /**
     * Include child categories for selected category IDs.
     *
     * @param array<int> $category_ids
     * @return array<int>
     */
    private static function expand_category_ids_with_children(array $category_ids): array {
        return self::expand_term_ids_with_children($category_ids, 'product_cat');
    }

    /**
     * Include child terms for selected term IDs.
     *
     * @param array<int> $term_ids
     * @param string     $taxonomy
     * @return array<int>
     */
    private static function expand_term_ids_with_children(array $term_ids, string $taxonomy): array {
        if (empty($term_ids)) {
            return array();
        }
        $all_ids = $term_ids;
        foreach ($term_ids as $term_id) {
            $children = get_term_children($term_id, $taxonomy);
            if (!is_wp_error($children) && !empty($children)) {
                $all_ids = array_merge($all_ids, array_map('intval', $children));
            }
        }
        $all_ids = array_filter($all_ids, static function($id) {
            return $id > 0;
        });
        return array_values(array_unique($all_ids));
    }

    /**
     * Determine whether plan category checks should include child categories.
     *
     * @param object $plan
     * @param string $field
     * @return bool
     */
    private static function should_include_child_categories($plan, string $field): bool {
        if (isset($plan->{$field})) {
            return (int) $plan->{$field} === 1;
        }
        return true;
    }

    /**
     * Whether product-centric subscription access mode is enabled in settings.
     *
     * @return bool
     */
    private static function is_product_centric_access_enabled(): bool {
        $options = get_option('eshterakyar_options', array());
        return !empty($options['product_centric_subscription_access']);
    }

    /**
     * Plan has at least one allowed category or tag rule.
     *
     * @param object $plan
     * @return bool
     */
    private static function plan_has_category_tag_allow_rules($plan): bool {
        return !empty($plan->allowed_categories) || !empty($plan->allowed_tags);
    }

    /**
     * Plan has no category/tag allow or exclude rules.
     *
     * @param object $plan
     * @return bool
     */
    private static function plan_has_no_category_tag_rules($plan): bool {
        return empty($plan->allowed_categories) && empty($plan->allowed_tags)
            && empty($plan->exclude_categories) && empty($plan->exclude_tags);
    }

    /**
     * Match product categories/tags against a plan's allow/exclude rules.
     *
     * @param int $product_id
     * @param object $plan
     * @return bool
     */
    private static function product_matches_plan_category_tag_access(int $product_id, $plan): bool {
        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
        $product_tags = wp_get_post_terms($product_id, 'product_tag', array('fields' => 'ids'));
        if (is_wp_error($product_categories)) {
            $product_categories = array();
        }
        if (is_wp_error($product_tags)) {
            $product_tags = array();
        }

        $has_access = true;

        if (!empty($plan->allowed_categories)) {
            $allowed_cats = self::parse_plan_term_ids((string) $plan->allowed_categories);
            if (self::should_include_child_categories($plan, 'include_children_allowed_categories')) {
                $allowed_cats = self::expand_category_ids_with_children($allowed_cats);
            }
            $has_allowed_cat = !empty(array_intersect($product_categories, $allowed_cats));
            if (!$has_allowed_cat) {
                $has_access = false;
            }
        }

        if (!empty($plan->allowed_tags) && $has_access) {
            $allowed_tags = self::parse_plan_term_ids((string) $plan->allowed_tags);
            $has_allowed_tag = !empty(array_intersect($product_tags, $allowed_tags));
            if (!$has_allowed_tag) {
                $has_access = false;
            }
        }

        if (!empty($plan->exclude_categories) && $has_access) {
            $exclude_cats = self::parse_plan_term_ids((string) $plan->exclude_categories);
            if (self::should_include_child_categories($plan, 'include_children_exclude_categories')) {
                $exclude_cats = self::expand_category_ids_with_children($exclude_cats);
            }
            $has_excluded_cat = !empty(array_intersect($product_categories, $exclude_cats));
            if ($has_excluded_cat) {
                $has_access = false;
            }
        }

        if (!empty($plan->exclude_tags) && $has_access) {
            $exclude_tags = self::parse_plan_term_ids((string) $plan->exclude_tags);
            $has_excluded_tag = !empty(array_intersect($product_tags, $exclude_tags));
            if ($has_excluded_tag) {
                $has_access = false;
            }
        }

        return $has_access;
    }

    /**
     * Create a new subscription
     * @param int $user_id
     * @param int $plan_id
     * @param int|null $order_id
     * @return int|false Subscription ID or false
     */
    public static function create(int $user_id, int $plan_id, ?int $order_id = null) {
        global $wpdb;
        
        if (!$user_id || $user_id <= 0) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Eshterakyar: Invalid user_id in create subscription: ' . $user_id);
            }
            return false;
        }
        
        $user = get_user_by('ID', $user_id);
        if (!$user) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Eshterakyar: User not found in create subscription: ' . $user_id);
            }
            return false;
        }
        
        $plan = Eshterakyar_Plan::get_by_id($plan_id);
        if (!$plan) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Eshterakyar: Plan not found in create subscription: ' . $plan_id);
            }
            return false;
        }
        
        $options = get_option('eshterakyar_options', array());
        $merge_with_existing = isset($options['merge_subscription_credits']) && $options['merge_subscription_credits'];
        
        $existing_subscription = self::get_active_subscription($user_id);
        
        // User has active subscription and new plan is not same group (or merge off) → create reserved subscription
        if ($existing_subscription && (!$merge_with_existing || !isset($plan->merge_with_existing) || !$plan->merge_with_existing || !self::is_same_plan_group($existing_subscription->plan_id, $plan_id))) {
            $table = $wpdb->prefix . 'subscriptions';
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'");
            if (!$table_exists && class_exists('Eshterakyar_Database')) {
                Eshterakyar_Database::create_tables();
                Eshterakyar_Database::update_tables();
            }
            $table_columns = $wpdb->get_col("DESC `{$table}`", 0);
            $reserved_data = array(
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'order_id' => $order_id,
                'start_date' => '0000-00-00 00:00:00',
                'end_date' => '0000-00-00 00:00:00',
                'status' => 'reserved',
                'is_active' => 0,
                'total_downloads' => $plan->total_limit,
                'daily_limit' => $plan->daily_limit,
                'downloads_used' => 0,
                'daily_downloads_count' => 0,
                'last_download_date' => null,
                'merged_old_days_left' => 0,
                'merged_old_daily_limit' => 0,
                'activates_after_subscription_id' => $existing_subscription->id
            );
            if (!in_array('activates_after_subscription_id', $table_columns)) {
                unset($reserved_data['activates_after_subscription_id']);
            }
            if (!in_array('merged_old_days_left', $table_columns)) {
                unset($reserved_data['merged_old_days_left']);
            }
            if (!in_array('merged_old_daily_limit', $table_columns)) {
                unset($reserved_data['merged_old_daily_limit']);
            }
            $res = $wpdb->insert($table, $reserved_data);
            if ($res) {
                $subscription_id = $wpdb->insert_id;
                if (class_exists('Eshterakyar_Cache')) {
                    Eshterakyar_Cache::clear_user_subscription_cache($user_id);
                }
                do_action('eshterakyar_subscription_created', $subscription_id, $user_id, $plan_id);
                return $subscription_id;
            }
            return false;
        }
        
        $start_date = current_time('mysql');
        $duration_days = $plan->duration_days;
        $total_downloads = $plan->total_limit;
        $daily_limit = $plan->daily_limit;
        $downloads_used = 0;
        $daily_downloads_count = 0;
        
        if ($merge_with_existing && isset($plan->merge_with_existing) && $plan->merge_with_existing && $existing_subscription && $existing_subscription->plan_id != $plan_id && self::is_same_plan_group($existing_subscription->plan_id, $plan_id)) {
            $existing_days_left = self::get_days_left($existing_subscription->id);
            $existing_remaining_downloads = max(0, $existing_subscription->total_downloads - $existing_subscription->downloads_used);
            
            if ($existing_days_left > 0) {
                $duration_days = $plan->duration_days + $existing_days_left;
            }
            if ($plan->total_limit > 0 && $existing_subscription->total_downloads > 0) {
                $total_downloads = $plan->total_limit + $existing_remaining_downloads;
            } elseif ($plan->total_limit > 0 && $existing_subscription->total_downloads == 0) {
                $total_downloads = $plan->total_limit;
            } elseif ($plan->total_limit == 0 && $existing_subscription->total_downloads > 0) {
                $total_downloads = 0; 
            } else {
                $total_downloads = 0; 
            }
            $daily_limit = $plan->daily_limit;
            $merged_old_days_left = $existing_days_left;
            $merged_old_daily_limit = $existing_subscription->daily_limit;
            $downloads_used = $existing_subscription->downloads_used;
            $daily_downloads_count = $existing_subscription->daily_downloads_count;
            
            self::deactivate($existing_subscription->id);
        }
        
        $end_date = date('Y-m-d H:i:s', strtotime($start_date . ' +' . $duration_days . ' days'));
        
        if (!isset($merged_old_days_left)) {
            $merged_old_days_left = 0;
        }
        if (!isset($merged_old_daily_limit)) {
            $merged_old_daily_limit = 0;
        }
        
        $data = array(
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'order_id' => $order_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => 'active',
            'is_active' => 1,
            'total_downloads' => $total_downloads,
            'daily_limit' => $daily_limit,
            'downloads_used' => $downloads_used,
            'daily_downloads_count' => $daily_downloads_count,
            'last_download_date' => null,
            'merged_old_days_left' => $merged_old_days_left,
            'merged_old_daily_limit' => $merged_old_daily_limit
        );
        
        $table = $wpdb->prefix . 'subscriptions';
        
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'");
        if (!$table_exists) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Eshterakyar: Table does not exist: ' . $table);
            }
            if (class_exists('Eshterakyar_Database')) {
                Eshterakyar_Database::create_tables();
                Eshterakyar_Database::update_tables();
            }
        } else {
            $table_columns = $wpdb->get_col("DESC `{$table}`", 0);
            
            if (!in_array('merged_old_days_left', $table_columns)) {
                $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN merged_old_days_left int(11) DEFAULT 0");
                $table_columns = $wpdb->get_col("DESC `{$table}`", 0);
            }
            
            if (!in_array('merged_old_daily_limit', $table_columns)) {
                $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN merged_old_daily_limit int(11) DEFAULT 0");
                $table_columns = $wpdb->get_col("DESC `{$table}`", 0);
            }
            
            if (!in_array('merged_old_days_left', $table_columns)) {
                unset($data['merged_old_days_left']);
            }
            if (!in_array('merged_old_daily_limit', $table_columns)) {
                unset($data['merged_old_daily_limit']);
            }
        }
        
        $result = $wpdb->insert($table, $data);
        
        if ($result) {
            $subscription_id = $wpdb->insert_id;
            self::check_and_update_status($user_id);
            
            if (class_exists('Eshterakyar_Cache')) {
                Eshterakyar_Cache::clear_user_subscription_cache($user_id);
            }
            
            do_action('eshterakyar_subscription_created', $subscription_id, $user_id, $plan_id);
            
            return $subscription_id;
        } else {
            $error_message = $wpdb->last_error ? $wpdb->last_error : 'خطای نامشخص در درج داده';
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Eshterakyar: Failed to create subscription: ' . $error_message);
            }
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Eshterakyar: Database insert failed: ' . $error_message);
                error_log('Eshterakyar: Insert data: ' . print_r($data, true));
            }
        }
        
        return false;
    }

    /**
     * Check if two plans belong to same group (both merge_with_existing and same subscription_group_id).
     * @param int $plan_id1
     * @param int $plan_id2
     * @return bool
     */
    private static function is_same_plan_group(int $plan_id1, int $plan_id2): bool {
        $plan1 = Eshterakyar_Plan::get_by_id($plan_id1);
        $plan2 = Eshterakyar_Plan::get_by_id($plan_id2);
        
        if (!$plan1 || !$plan2) {
            return false;
        }
        if (!(isset($plan1->merge_with_existing) && $plan1->merge_with_existing) || !(isset($plan2->merge_with_existing) && $plan2->merge_with_existing)) {
            return false;
        }
        $g1 = isset($plan1->subscription_group_id) ? trim((string) $plan1->subscription_group_id) : '';
        $g2 = isset($plan2->subscription_group_id) ? trim((string) $plan2->subscription_group_id) : '';
        return $g1 === $g2;
    }
    /**
     * Toggle subscription active status
     * @param int $subscription_id
     * @param int|null $is_active
     * @return bool|int
     */
    public static function toggle_active(int $subscription_id, ?int $is_active = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscriptions';
        
        if ($is_active === null) {
            $subscription = self::get_by_id($subscription_id);
            if (!$subscription) {
                return false;
            }
            $is_active = $subscription->is_active ? 0 : 1;
        }
        
        return $wpdb->update(
            $table,
            array('is_active' => $is_active),
            array('id' => $subscription_id)
        );
    }

    /**
     * Deactivate subscription
     * @param int $subscription_id
     * @return bool|int
     */
    public static function deactivate(int $subscription_id) {
        return self::toggle_active($subscription_id, 0);
    }

    /**
     * Activate subscription
     * @param int $subscription_id
     * @return bool|int
     */
    public static function activate(int $subscription_id) {
        return self::toggle_active($subscription_id, 1);
    }

    /**
     * Get active subscription for user
     * @param int $user_id
     * @return object|null
     */
    public static function get_active_subscription(int $user_id) {
        if (class_exists('Eshterakyar_Cache')) {
            return Eshterakyar_Cache::get_active_subscription($user_id);
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        $plans_table = $wpdb->prefix . 'subscription_plans';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT s.*, p.name as plan_name, p.description as plan_description 
             FROM `{$table_name}` s 
             LEFT JOIN `{$plans_table}` p ON s.plan_id = p.id 
             WHERE s.user_id = %d AND s.status = 'active' AND s.is_active = 1 AND s.end_date > NOW() 
             ORDER BY s.end_date DESC 
             LIMIT 1",
            $user_id
        ));
    }
    /**
     * Get user subscriptions
     * @param int $user_id
     * @param string|null $status
     * @return array
     */
    public static function get_user_subscriptions(int $user_id, ?string $status = null): array {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        $plans_table = $wpdb->prefix . 'subscription_plans';
        
        $user_id = absint($user_id);
        $where = "WHERE s.user_id = %d";
        $params = array($user_id);
        
        if ($status) {
            $status = sanitize_text_field($status);
            $allowed_statuses = array('active', 'expired', 'cancelled', 'inactive', 'reserved');
            if (in_array($status, $allowed_statuses)) {
            $where .= " AND s.status = %s";
            $params[] = $status;
            }
        }
        
        $query = "SELECT s.*, p.name as plan_name, p.description as plan_description 
                  FROM {$table_name} s 
                  LEFT JOIN {$plans_table} p ON s.plan_id = p.id 
                  {$where} 
                  ORDER BY s.created_at DESC";
        
        $results = $wpdb->get_results($wpdb->prepare($query, $params));
        return $results ? $results : array();
    }
    public static function get_by_id(int $subscription_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        $plans_table = $wpdb->prefix . 'subscription_plans';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT s.*, p.name as plan_name, p.description as plan_description 
             FROM `{$table_name}` s 
             LEFT JOIN `{$plans_table}` p ON s.plan_id = p.id 
             WHERE s.id = %d",
            $subscription_id
        ));
    }
    /**
     * Activate reserved subscriptions that were waiting for the given subscription to expire.
     * @param int $expired_subscription_id
     */
    private static function activate_reserved_after(int $expired_subscription_id): void {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        $table_columns = $wpdb->get_col("DESC `{$table_name}`", 0);
        if (!in_array('activates_after_subscription_id', $table_columns)) {
            return;
        }
        $reserved = $wpdb->get_results($wpdb->prepare(
            "SELECT id, plan_id FROM `{$table_name}` WHERE status = 'reserved' AND activates_after_subscription_id = %d",
            $expired_subscription_id
        ));
        $now = current_time('mysql');
        foreach ($reserved as $row) {
            $plan = Eshterakyar_Plan::get_by_id($row->plan_id);
            $duration_days = $plan ? (int) $plan->duration_days : 30;
            $end_date = date('Y-m-d H:i:s', strtotime($now . ' +' . $duration_days . ' days'));
            $wpdb->update(
                $table_name,
                array(
                    'start_date' => $now,
                    'end_date' => $end_date,
                    'status' => 'active',
                    'is_active' => 1,
                    'activates_after_subscription_id' => null
                ),
                array('id' => $row->id)
            );
            if (class_exists('Eshterakyar_Cache')) {
                $sub = self::get_by_id($row->id);
                if ($sub && !empty($sub->user_id)) {
                    Eshterakyar_Cache::clear_user_subscription_cache($sub->user_id);
                }
            }
        }
    }

    /**
     * Check and update subscription status
     * @param int|null $user_id
     * @return bool
     */
    public static function check_and_update_status(?int $user_id = null): bool {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscriptions';
        
        $where = "WHERE status = 'active' AND end_date <= NOW()";
        $params = array();
        
        if ($user_id) {
            $where .= " AND user_id = %d";
            $params[] = $user_id;
        }
        
        $to_expire = $wpdb->get_col(empty($params) ? "SELECT id FROM `{$table_name}` {$where}" : $wpdb->prepare("SELECT id FROM `{$table_name}` {$where}", $params));
        $to_expire = is_array($to_expire) ? $to_expire : array();
        
        if (!empty($params)) {
            $wpdb->query($wpdb->prepare(
                "UPDATE `{$table_name}` SET status = 'expired' {$where}",
                $params
            ));
        } else {
            $wpdb->query("UPDATE `{$table_name}` SET status = 'expired' {$where}");
        }
        
        foreach ($to_expire as $expired_id) {
            self::activate_reserved_after((int) $expired_id);
        }
        
        return true;
    }

    /**
     * Check if product is accessible for user
     * @param int $user_id
     * @param int $product_id
     * @param bool $is_redownload
     * @return array
     */
    public static function can_download(int $user_id, int $product_id, bool $is_redownload = false): array {
        // Lifetime access: re-download files fetched under a plan with lifetime_access enabled.
        if (Eshterakyar_Download::user_has_lifetime_access_to_product($user_id, $product_id)) {
            return array(
                'can' => true,
                'subscription' => null,
                'is_redownload' => true,
                'lifetime_access' => true,
                'message' => __('You have lifetime access to this file', 'eshterakyar'),
            );
        }
        
        $subscription = self::get_active_subscription($user_id);
        
        if (!$subscription) {
            return array('can' => false, 'message' => __('You have no active subscription', 'eshterakyar'));
        }
        
        if (!$subscription->is_active) {
            return array('can' => false, 'message' => __('Your subscription is inactive', 'eshterakyar'));
        }
        
        $plan = Eshterakyar_Plan::get_by_id($subscription->plan_id);
        if ($plan && !self::check_product_access($product_id, $plan)) {
            return array('can' => false, 'message' => __('This product is not available in your plan', 'eshterakyar'));
        }

        $options = get_option('eshterakyar_options', array());
        $enable_redownload = isset($options['enable_redownload']) ? (int) $options['enable_redownload'] : 1;
        if (!$enable_redownload && $is_redownload) {
            $is_redownload = false;
        }
        
        $today = current_time('Y-m-d');
        $today_start = $today . ' 00:00:00';
        $today_end = $today . ' 23:59:59';
        global $wpdb;
        $downloads_table = $wpdb->prefix . 'subscription_downloads';
        $daily_count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT product_id) FROM `{$downloads_table}` 
             WHERE subscription_id = %d AND download_date >= %s AND download_date <= %s",
            $subscription->id, $today_start, $today_end
        ));
        $effective_daily_limit = $subscription->daily_limit;
        $has_quota_for_new_grant = ($effective_daily_limit <= 0 || $daily_count < $effective_daily_limit);
        
        $max_per_product = isset($plan->max_downloads_per_product) ? intval($plan->max_downloads_per_product) : 0;
        $download_record = Eshterakyar_Download::get_download($user_id, $subscription->id, $product_id);
        $current_download_count = $download_record ? intval($download_record->download_count) : 0;
        if ($max_per_product > 0 && $current_download_count >= $max_per_product) {
            if (!$enable_redownload) {
                if ($has_quota_for_new_grant) {
                    return array('can' => true, 'subscription' => $subscription, 'is_redownload' => true, 'reset_product_window' => true, 'redownload_uses_quota' => true);
                }
                return array('can' => false, 'message' => __('تعداد مجاز دانلود این محصول به پایان رسیده است.', 'eshterakyar'));
            }
            if ($has_quota_for_new_grant) {
                return array('can' => true, 'subscription' => $subscription, 'is_redownload' => true, 'reset_product_window' => true);
            }
            return array('can' => false, 'message' => __('تعداد مجاز دانلود این محصول به پایان رسیده است. در صورت داشتن سهمیه روزانه می‌توانید با کسر اعتبار دسترسی مجدد بگیرید.', 'eshterakyar'));
        }

        if ($is_redownload) {
            $was_downloaded = Eshterakyar_Download::is_downloaded($user_id, $subscription->id, $product_id);
            if ($was_downloaded) {
                $download_access_days = isset($plan->download_access_days) ? intval($plan->download_access_days) : 0;
                if ($download_access_days > 0) {
                    $first_date = Eshterakyar_Download::get_first_download_date($user_id, $subscription->id, $product_id);
                    if ($first_date) {
                        $access_until = strtotime($first_date . ' +' . $download_access_days . ' days');
                        if (current_time('timestamp') > $access_until) {
                            if ($has_quota_for_new_grant) {
                                return array('can' => true, 'subscription' => $subscription, 'is_redownload' => true, 'reset_product_window' => true);
                            }
                            return array('can' => false, 'message' => __('مدت دسترسی به دانلود این محصول به پایان رسیده است. در صورت داشتن سهمیه روزانه می‌توانید با کسر اعتبار دسترسی مجدد بگیرید.', 'eshterakyar'));
                        }
                    }
                }
                return array('can' => true, 'subscription' => $subscription, 'is_redownload' => true);
            }
        }
        
        if ($subscription->total_downloads > 0 && $subscription->downloads_used >= $subscription->total_downloads) {
            $was_downloaded = Eshterakyar_Download::is_downloaded($user_id, $subscription->id, $product_id);
            if ($was_downloaded) {
                return array('can' => true, 'subscription' => $subscription, 'is_redownload' => true);
            }
            return array('can' => false, 'message' => __('محدودیت دانلود کلی شما به پایان رسیده است', 'eshterakyar'));
        }
        
        if ($effective_daily_limit > 0) {
            if ($daily_count >= $effective_daily_limit) {
                $was_downloaded = Eshterakyar_Download::is_downloaded($user_id, $subscription->id, $product_id);
                if ($was_downloaded) {
                    return array('can' => true, 'subscription' => $subscription, 'is_redownload' => true);
                }
                return array('can' => false, 'message' => __('محدودیت دانلود روزانه شما به پایان رسیده است', 'eshterakyar'));
            }
        }
        
        return array('can' => true, 'subscription' => $subscription, 'is_redownload' => false);
    }
    

    /**
     * Get days left for subscription
     * @param int $subscription_id
     * @return int
     */
    public static function get_days_left(int $subscription_id): int {
        $subscription = self::get_by_id($subscription_id);
        
        if (!$subscription || $subscription->status != 'active') {
            return 0;
        }
        
        $end_date = strtotime($subscription->end_date);
        $now = current_time('timestamp');
        $diff = $end_date - $now;
        
        if ($diff <= 0) {
            return 0;
        }
        
        return ceil($diff / DAY_IN_SECONDS);
    }
    

    /**
     * Cancel subscription
     * @param int $subscription_id
     * @return bool|int
     */
    public static function cancel(int $subscription_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscriptions';
        
        return $wpdb->update(
            $table,
            array('status' => 'cancelled', 'is_active' => 0),
            array('id' => $subscription_id)
        );
    }
    

    /**
     * Whether cumulative (hierarchical) plan download access is enabled site-wide.
     *
     * @return bool
     */
    public static function is_cumulative_plan_download_access_enabled(): bool {
        $options = get_option('eshterakyar_options', array());
        return !empty($options['cumulative_plan_download_access']);
    }

    /**
     * Highest sort_order among allowed plan IDs (active plans only).
     *
     * @param array<int> $allowed_plan_ids
     * @return int|null
     */
    private static function get_max_sort_order_for_allowed_plans(array $allowed_plan_ids): ?int {
        if (empty($allowed_plan_ids)) {
            return null;
        }

        $max_sort_order = null;
        foreach ($allowed_plan_ids as $allowed_plan_id) {
            $allowed_plan = Eshterakyar_Plan::get_by_id((int) $allowed_plan_id);
            if (!$allowed_plan || $allowed_plan->status === 'deleted' || $allowed_plan->status === 'inactive') {
                continue;
            }
            $sort_order = isset($allowed_plan->sort_order) ? (int) $allowed_plan->sort_order : 0;
            if ($max_sort_order === null || $sort_order > $max_sort_order) {
                $max_sort_order = $sort_order;
            }
        }

        return $max_sort_order;
    }

    /**
     * Check if a plan may download a product with specific allowed plan IDs.
     *
     * @param object $plan
     * @param array<int> $allowed_plan_ids
     * @return bool
     */
    public static function is_plan_in_product_download_access($plan, array $allowed_plan_ids): bool {
        $allowed_plan_ids = array_map('intval', $allowed_plan_ids);

        if (in_array((int) $plan->id, $allowed_plan_ids, true)) {
            return true;
        }

        if (!self::is_cumulative_plan_download_access_enabled()) {
            return false;
        }

        $max_sort_order = self::get_max_sort_order_for_allowed_plans($allowed_plan_ids);
        if ($max_sort_order === null) {
            return false;
        }

        $plan_sort_order = isset($plan->sort_order) ? (int) $plan->sort_order : 0;
        return $plan_sort_order <= $max_sort_order;
    }

        /**
     * بررسی اینکه آیا محصول در دسترس هر پلن فعالی است یا نه (بر اساس تگ و دسته‌بندی)
     * @param int $product_id
     * @return bool
     */
    public static function is_product_accessible_by_any_plan($product_id) {
        $is_subscription_downloadable = get_post_meta($product_id, '_eshterakyar_subscription_downloadable', true);
        
        if ($is_subscription_downloadable) {
            $download_all_plans = get_post_meta($product_id, '_eshterakyar_subscription_download_all_plans', true);
            if ($download_all_plans) {
                return true;
            }
            
            $allowed_plans = get_post_meta($product_id, '_eshterakyar_subscription_download_plans', true);
            if (!is_array($allowed_plans)) {
                $allowed_plans = array();
            }

            if (empty($allowed_plans)) {
                return false;
            }
            
            $plans = Eshterakyar_Plan::get_all('active');
            foreach ($plans as $plan) {
                if (self::is_plan_in_product_download_access($plan, $allowed_plans)) {
                    return true;
                }
            }
            
            return false;
        }
        
        $plans = Eshterakyar_Plan::get_all('active');
        $product_centric = self::is_product_centric_access_enabled();

        foreach ($plans as $plan) {
            if ($product_centric) {
                if (!self::plan_has_category_tag_allow_rules($plan)) {
                    continue;
                }
                if (self::product_matches_plan_category_tag_access($product_id, $plan)) {
                    return true;
                }
                continue;
            }

            if (self::plan_has_no_category_tag_rules($plan)) {
                return true;
            }

            if (self::product_matches_plan_category_tag_access($product_id, $plan)) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * بررسی اینکه آیا محصول با اشتراک قابل دانلود است یا نه
     * @param int $product_id
     * @param int|null $user_id
     * @return bool
     */
    public static function is_product_subscription_downloadable($product_id, $user_id = null) {
        $is_subscription_downloadable = get_post_meta($product_id, '_eshterakyar_subscription_downloadable', true);
        
        if ($is_subscription_downloadable) {
            return true;
        }
        
        if (self::is_product_accessible_by_any_plan($product_id)) {
            return true;
        }
        
        if ($user_id === null && is_user_logged_in()) {
            $user_id = get_current_user_id();
        }
        
        if ($user_id) {
            $subscription = self::get_active_subscription($user_id);
            
            if ($subscription) {
                $plan = Eshterakyar_Plan::get_by_id($subscription->plan_id);
                if ($plan && self::check_product_access($product_id, $plan)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check product access for plan
     * @param int $product_id
     * @param object $plan
     * @return bool
     */
    public static function check_product_access(int $product_id, $plan): bool {
        $is_subscription_product = get_post_meta($product_id, '_eshterakyar_is_subscription', true);
        $product_plan_id = get_post_meta($product_id, '_eshterakyar_plan_id', true);
        if ($is_subscription_product && $product_plan_id == $plan->id) {
            return true;
        }
        $is_subscription_downloadable = get_post_meta($product_id, '_eshterakyar_subscription_downloadable', true);
        
        if ($is_subscription_downloadable) {

            $download_all_plans = get_post_meta($product_id, '_eshterakyar_subscription_download_all_plans', true);
            
            if ($download_all_plans) {
                
                return true;
            } else {
                
                $allowed_plans = get_post_meta($product_id, '_eshterakyar_subscription_download_plans', true);
                
                if (!is_array($allowed_plans)) {
                    $allowed_plans = array();
                }

                return self::is_plan_in_product_download_access($plan, $allowed_plans);
            }
        }

        if (self::is_product_centric_access_enabled()) {
            if (!self::plan_has_category_tag_allow_rules($plan)) {
                return false;
            }
            return self::product_matches_plan_category_tag_access($product_id, $plan);
        }

        if (self::plan_has_no_category_tag_rules($plan)) {
            return true;
        }

        return self::product_matches_plan_category_tag_access($product_id, $plan);
    }

    /**
     * Whether cumulative (hierarchical) plan content access is enabled site-wide.
     *
     * @return bool
     */
    public static function is_cumulative_plan_content_access_enabled(): bool {
        $options = get_option('eshterakyar_options', array());
        return !empty($options['cumulative_plan_content_access']);
    }

    /**
     * Check if a plan may read content restricted to specific plan IDs on a post.
     *
     * @param object     $plan
     * @param array<int> $allowed_plan_ids
     * @return bool
     */
    public static function is_plan_in_post_content_access($plan, array $allowed_plan_ids): bool {
        $allowed_plan_ids = array_map('intval', $allowed_plan_ids);

        if (in_array((int) $plan->id, $allowed_plan_ids, true)) {
            return true;
        }

        if (!self::is_cumulative_plan_content_access_enabled()) {
            return false;
        }

        $max_sort_order = self::get_max_sort_order_for_allowed_plans($allowed_plan_ids);
        if ($max_sort_order === null) {
            return false;
        }

        $plan_sort_order = isset($plan->sort_order) ? (int) $plan->sort_order : 0;
        return $plan_sort_order <= $max_sort_order;
    }

    /**
     * Check post content access for plan (blog categories/tags).
     *
     * @param int    $post_id
     * @param object $plan
     * @return bool
     */
    public static function check_post_content_access(int $post_id, $plan): bool {
        if (empty($plan->allowed_post_categories) && empty($plan->allowed_post_tags) &&
            empty($plan->exclude_post_categories) && empty($plan->exclude_post_tags)) {
            return true;
        }

        $post_categories = wp_get_post_terms($post_id, 'category', array('fields' => 'ids'));
        $post_tags = wp_get_post_terms($post_id, 'post_tag', array('fields' => 'ids'));
        if (is_wp_error($post_categories)) {
            $post_categories = array();
        }
        if (is_wp_error($post_tags)) {
            $post_tags = array();
        }

        if (!empty($plan->allowed_post_categories)) {
            $allowed_cats = self::parse_plan_term_ids((string) $plan->allowed_post_categories);
            if (self::should_include_child_categories($plan, 'include_children_allowed_post_categories')) {
                $allowed_cats = self::expand_term_ids_with_children($allowed_cats, 'category');
            }
            $has_allowed_cat = !empty(array_intersect($post_categories, $allowed_cats));
            if (!$has_allowed_cat) {
                return false;
            }
        }

        if (!empty($plan->allowed_post_tags)) {
            $allowed_tags = self::parse_plan_term_ids((string) $plan->allowed_post_tags);
            $has_allowed_tag = !empty(array_intersect($post_tags, $allowed_tags));
            if (!$has_allowed_tag) {
                return false;
            }
        }

        if (!empty($plan->exclude_post_categories)) {
            $exclude_cats = self::parse_plan_term_ids((string) $plan->exclude_post_categories);
            if (self::should_include_child_categories($plan, 'include_children_exclude_post_categories')) {
                $exclude_cats = self::expand_term_ids_with_children($exclude_cats, 'category');
            }
            $has_excluded_cat = !empty(array_intersect($post_categories, $exclude_cats));
            if ($has_excluded_cat) {
                return false;
            }
        }

        if (!empty($plan->exclude_post_tags)) {
            $exclude_tags = self::parse_plan_term_ids((string) $plan->exclude_post_tags);
            $has_excluded_tag = !empty(array_intersect($post_tags, $exclude_tags));
            if ($has_excluded_tag) {
                return false;
            }
        }

        return true;
    }

    /**
     * Whether content restriction feature is enabled globally.
     *
     * @return bool
     */
    public static function is_content_restriction_enabled(): bool {
        $options = get_option('eshterakyar_options', array());
        return !empty($options['content_restriction_enabled']);
    }

    /**
     * Post types that may use content restriction.
     *
     * @return array<string>
     */
    public static function get_content_restriction_post_types(): array {
        $options = get_option('eshterakyar_options', array());
        $raw = isset($options['content_restriction_post_types']) ? $options['content_restriction_post_types'] : 'post';
        if (is_array($raw)) {
            $types = array_map('sanitize_key', $raw);
        } else {
            $types = array_map('trim', explode(',', (string) $raw));
            $types = array_map('sanitize_key', $types);
        }
        $types = array_filter($types);
        return !empty($types) ? array_values($types) : array('post');
    }

    /**
     * Detect paywall shortcode in raw post content.
     *
     * @param string $content
     * @return bool
     */
    public static function post_content_has_paywall_shortcode(string $content): bool {
        if (preg_match('/\[eshterakyar_paywall[^\]]*\]/i', $content)) {
            return true;
        }
        if (strpos($content, 'eshterakyar-paywall-marker') !== false) {
            return true;
        }
        if (strpos($content, 'eshterakyar_paywall') !== false && strpos($content, 'wp:shortcode') !== false) {
            return (bool) preg_match('/eshterakyar_paywall/i', $content);
        }
        return false;
    }

    /**
     * Post has explicit paywall (shortcode or metabox), regardless of global toggle.
     *
     * @param int $post_id
     * @return bool
     */
    public static function post_has_explicit_paywall(int $post_id): bool {
        $post = get_post($post_id);
        if (!$post || !in_array($post->post_type, self::get_content_restriction_post_types(), true)) {
            return false;
        }
        if (get_post_meta($post_id, '_eshterakyar_content_restricted', true)) {
            return true;
        }
        return self::post_content_has_paywall_shortcode((string) $post->post_content);
    }

    /**
     * Whether paywall rules should be enforced for this post.
     *
     * @param int $post_id
     * @return bool
     */
    public static function should_enforce_post_paywall(int $post_id): bool {
        if (self::post_has_explicit_paywall($post_id)) {
            return true;
        }
        if (!self::is_content_restriction_enabled()) {
            return false;
        }
        return self::is_post_content_restricted($post_id);
    }

    /**
     * Whether a post is subject to content restriction rules (global feature + auto by plan).
     *
     * @param int $post_id
     * @return bool
     */
    public static function is_post_content_restricted(int $post_id): bool {
        if (self::post_has_explicit_paywall($post_id)) {
            return true;
        }

        if (!self::is_content_restriction_enabled()) {
            return false;
        }

        $post = get_post($post_id);
        if (!$post) {
            return false;
        }

        if (!in_array($post->post_type, self::get_content_restriction_post_types(), true)) {
            return false;
        }

        $options = get_option('eshterakyar_options', array());
        if (!empty($options['content_auto_restrict_by_plan'])) {
            $plans = Eshterakyar_Plan::get_all('active');
            foreach ($plans as $plan) {
                if (!empty($plan->allowed_post_categories) || !empty($plan->allowed_post_tags) ||
                    !empty($plan->exclude_post_categories) || !empty($plan->exclude_post_tags)) {
                    if (self::check_post_content_access($post_id, $plan)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Capability required to bypass paywall on the front-end (view full article without subscription).
     *
     * @return string
     */
    public static function get_paywall_bypass_capability(): string {
        $cap = apply_filters('eshterakyar_paywall_bypass_capability', 'manage_options');
        return is_string($cap) && $cap !== '' ? $cap : 'manage_options';
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public static function user_can_bypass_post_paywall(int $user_id): bool {
        if (!$user_id) {
            return false;
        }
        if (isset($_GET['eshterakyar_force_paywall']) && (string) $_GET['eshterakyar_force_paywall'] === '1') {
            return false;
        }
        return user_can($user_id, self::get_paywall_bypass_capability());
    }

    /**
     * Whether the user can read the full post content.
     *
     * @param int $user_id
     * @param int $post_id
     * @return bool
     */
    public static function user_can_read_post_content(int $user_id, int $post_id): bool {
        if (!self::should_enforce_post_paywall($post_id)) {
            return true;
        }

        if (self::user_can_bypass_post_paywall($user_id)) {
            return true;
        }

        $content_all_plans = get_post_meta($post_id, '_eshterakyar_content_all_plans', true);
        $content_plans = get_post_meta($post_id, '_eshterakyar_content_plans', true);
        if (!is_array($content_plans)) {
            $content_plans = array();
        }
        $override_plan_rules = get_post_meta($post_id, '_eshterakyar_content_override_plan_rules', true);

        $subscription = $user_id ? self::get_active_subscription($user_id) : null;
        if (!$subscription) {
            return false;
        }

        $plan = Eshterakyar_Plan::get_by_id((int) $subscription->plan_id);
        if (!$plan) {
            return false;
        }

        if ($content_all_plans) {
            if ($override_plan_rules) {
                return true;
            }
            return self::check_post_content_access($post_id, $plan);
        }

        if (!empty($content_plans)) {
            if (!self::is_plan_in_post_content_access($plan, $content_plans)) {
                return false;
            }
            if ($override_plan_rules) {
                return true;
            }
            return self::check_post_content_access($post_id, $plan);
        }

        return self::check_post_content_access($post_id, $plan);
    }
}

