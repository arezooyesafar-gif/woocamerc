<?php


if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Database {

    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_plans = $wpdb->prefix . 'subscription_plans';
        $sql_plans = "CREATE TABLE IF NOT EXISTS $table_plans (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            price decimal(10,2) NOT NULL,
            duration_days int(11) NOT NULL,
            daily_limit int(11) DEFAULT 0,
            total_limit int(11) DEFAULT 0,
            product_id bigint(20) DEFAULT NULL,
            status varchar(20) DEFAULT 'active',
            allowed_categories text DEFAULT NULL,
            allowed_tags text DEFAULT NULL,
            exclude_categories text DEFAULT NULL,
            exclude_tags text DEFAULT NULL,
            include_children_allowed_categories tinyint(1) DEFAULT 1,
            include_children_exclude_categories tinyint(1) DEFAULT 1,
            is_recommended tinyint(1) DEFAULT 0,
            merge_with_existing tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY product_id (product_id),
            KEY status (status)
        ) $charset_collate;";
        
        $table_subscriptions = $wpdb->prefix . 'subscriptions';
        $sql_subscriptions = "CREATE TABLE IF NOT EXISTS $table_subscriptions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            plan_id bigint(20) NOT NULL,
            order_id bigint(20) DEFAULT NULL,
            start_date datetime NOT NULL,
            end_date datetime NOT NULL,
            status varchar(20) DEFAULT 'active',
            is_active tinyint(1) DEFAULT 1,
            total_downloads int(11) DEFAULT 0,
            daily_limit int(11) DEFAULT 0,
            downloads_used int(11) DEFAULT 0,
            daily_downloads_count int(11) DEFAULT 0,
            last_download_date date DEFAULT NULL,
            merged_old_days_left int(11) DEFAULT 0,
            merged_old_daily_limit int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY plan_id (plan_id),
            KEY order_id (order_id),
            KEY status (status),
            KEY is_active (is_active),
            KEY end_date (end_date)
        ) $charset_collate;";
        
        $table_downloads = $wpdb->prefix . 'subscription_downloads';
        $sql_downloads = "CREATE TABLE IF NOT EXISTS $table_downloads (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            subscription_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            download_date datetime DEFAULT CURRENT_TIMESTAMP,
            download_count int(11) DEFAULT 1,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY subscription_id (subscription_id),
            KEY product_id (product_id),
            KEY download_date (download_date),
            KEY user_product (user_id, product_id),
            KEY subscription_product (subscription_id, product_id)
        ) $charset_collate;";
        
        $table_notifications = $wpdb->prefix . 'subscription_notifications';
        $sql_notifications = "CREATE TABLE IF NOT EXISTS $table_notifications (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            subscription_id bigint(20) NOT NULL,
            type varchar(50) NOT NULL,
            message text,
            sent_date datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'sent',
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY subscription_id (subscription_id),
            KEY type (type),
            KEY sent_date (sent_date)
        ) $charset_collate;";
        
        $table_download_tokens = $wpdb->prefix . 'subscription_download_tokens';
        $sql_download_tokens = "CREATE TABLE IF NOT EXISTS $table_download_tokens (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            token varchar(64) NOT NULL,
            user_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            subscription_id bigint(20) DEFAULT NULL,
            expires_at datetime NOT NULL,
            used_count int(11) DEFAULT 0,
            max_uses int(11) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY token (token),
            KEY user_id (user_id),
            KEY product_id (product_id),
            KEY subscription_id (subscription_id),
            KEY expires_at (expires_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_plans);
        dbDelta($sql_subscriptions);
        dbDelta($sql_downloads);
        dbDelta($sql_notifications);
        dbDelta($sql_download_tokens);
    }

    public static function deactivate() {
        $delete_data = get_option('eshterakyar_delete_data_on_deactivate', false);
        
        if ($delete_data) {
            global $wpdb;
            
            $tables = array(
                $wpdb->prefix . 'subscription_plans',
                $wpdb->prefix . 'subscriptions',
                $wpdb->prefix . 'subscription_downloads',
                $wpdb->prefix . 'subscription_notifications',
                $wpdb->prefix . 'subscription_download_tokens'
            );
            
            foreach ($tables as $table) {
                $wpdb->query("DROP TABLE IF EXISTS $table");
            }
            
            delete_option('eshterakyar_db_version');
            delete_option('eshterakyar_db_revision');
            delete_option('eshterakyar_options');
            delete_option('eshterakyar_delete_data_on_deactivate');
            
            $timestamp = wp_next_scheduled('eshterakyar_daily_expiry_check');
            if ($timestamp) {
                wp_unschedule_event($timestamp, 'eshterakyar_daily_expiry_check');
            }
            
            $timestamp = wp_next_scheduled('eshterakyar_daily_cleanup');
            if ($timestamp) {
                wp_unschedule_event($timestamp, 'eshterakyar_daily_cleanup');
            }
        } else {
            delete_option('eshterakyar_delete_data_on_deactivate');
        }
    }

    /**
     * اگر جدول اصلی وجود نداشته باشد (مثلاً حذف دستی در حالی که گزینهٔ ریویژن هنوز به‌روز است)، dbDelta باید اجرا شود.
     */
    private static function core_subscription_tables_missing() {
        global $wpdb;
        $plans = $wpdb->prefix . 'subscription_plans';

        return !$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $plans));
    }

    /**
     * ترمیم سبک ستون‌ها و ایندکس‌ها؛ ایمن برای تکرار. در هر درخواست فقط یک‌بار اجرا می‌شود.
     * حتی اگر eshterakyar_db_revision به‌روز باشد، ستون‌های گم‌شده را برمی‌گرداند.
     */
    private static function apply_incremental_schema_fixes() {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        global $wpdb;

        $plans_table = $wpdb->prefix . 'subscription_plans';
        $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $plans_table));
        
        if ($table_exists) {
            $plans_columns = $wpdb->get_col("DESC `{$plans_table}`", 0);
            
            if (!in_array('badge_text', $plans_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN badge_text varchar(100) DEFAULT NULL");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add badge_text column: ' . $wpdb->last_error);
                }
            }
            if (!in_array('badge_color', $plans_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN badge_color varchar(20) DEFAULT 'orange'");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add badge_color column: ' . $wpdb->last_error);
                }
            }
            if (!in_array('custom_features', $plans_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN custom_features text DEFAULT NULL");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add custom_features column: ' . $wpdb->last_error);
                }
            }
            if (!in_array('sale_price', $plans_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN sale_price decimal(10,2) DEFAULT NULL");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add sale_price column: ' . $wpdb->last_error);
                }
            }
            if (!in_array('sort_order', $plans_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN sort_order int(11) DEFAULT 0");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add sort_order column: ' . $wpdb->last_error);
                } else {
                    $wpdb->query("UPDATE `{$plans_table}` SET sort_order = id WHERE sort_order = 0");
                }
            }
            if (!in_array('allowed_categories', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN allowed_categories text DEFAULT NULL");
            }
            if (!in_array('allowed_tags', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN allowed_tags text DEFAULT NULL");
            }
            if (!in_array('exclude_categories', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN exclude_categories text DEFAULT NULL");
            }
            if (!in_array('exclude_tags', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN exclude_tags text DEFAULT NULL");
            }
            if (!in_array('include_children_allowed_categories', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN include_children_allowed_categories tinyint(1) DEFAULT 1");
            }
            if (!in_array('include_children_exclude_categories', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN include_children_exclude_categories tinyint(1) DEFAULT 1");
            }
            if (!in_array('is_recommended', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN is_recommended tinyint(1) DEFAULT 0");
            }
            if (!in_array('merge_with_existing', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN merge_with_existing tinyint(1) DEFAULT 0");
            }
            if (!in_array('download_access_days', $plans_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN download_access_days int(11) DEFAULT 0");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add download_access_days column: ' . $wpdb->last_error);
                }
            }
            if (!in_array('max_downloads_per_product', $plans_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN max_downloads_per_product int(11) DEFAULT 0");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add max_downloads_per_product column: ' . $wpdb->last_error);
                }
            }
            if (!in_array('subscription_group_id', $plans_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN subscription_group_id varchar(50) DEFAULT NULL");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add subscription_group_id column: ' . $wpdb->last_error);
                }
            }
            if (!in_array('lifetime_access', $plans_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN lifetime_access tinyint(1) DEFAULT 0");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add lifetime_access column: ' . $wpdb->last_error);
                }
            }
            if (!in_array('allowed_post_categories', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN allowed_post_categories text DEFAULT NULL");
            }
            if (!in_array('allowed_post_tags', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN allowed_post_tags text DEFAULT NULL");
            }
            if (!in_array('exclude_post_categories', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN exclude_post_categories text DEFAULT NULL");
            }
            if (!in_array('exclude_post_tags', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN exclude_post_tags text DEFAULT NULL");
            }
            if (!in_array('include_children_allowed_post_categories', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN include_children_allowed_post_categories tinyint(1) DEFAULT 1");
            }
            if (!in_array('include_children_exclude_post_categories', $plans_columns, true)) {
                $wpdb->query("ALTER TABLE `{$plans_table}` ADD COLUMN include_children_exclude_post_categories tinyint(1) DEFAULT 1");
            }
        }
        
        $subscriptions_table = $wpdb->prefix . 'subscriptions';
        $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $subscriptions_table));
        
        if ($table_exists) {
            $subscriptions_columns = $wpdb->get_col("DESC `{$subscriptions_table}`", 0);
            
            if (!in_array('is_active', $subscriptions_columns, true)) {
                $wpdb->query("ALTER TABLE `{$subscriptions_table}` ADD COLUMN is_active tinyint(1) DEFAULT 1");
                $wpdb->query("ALTER TABLE `{$subscriptions_table}` ADD INDEX is_active (is_active)");
            }
            
            if (!in_array('merged_old_days_left', $subscriptions_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$subscriptions_table}` ADD COLUMN merged_old_days_left int(11) DEFAULT 0");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add merged_old_days_left column: ' . $wpdb->last_error);
                }
            }
            
            if (!in_array('merged_old_daily_limit', $subscriptions_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$subscriptions_table}` ADD COLUMN merged_old_daily_limit int(11) DEFAULT 0");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add merged_old_daily_limit column: ' . $wpdb->last_error);
                }
            }
            if (!in_array('activates_after_subscription_id', $subscriptions_columns, true)) {
                $result = $wpdb->query("ALTER TABLE `{$subscriptions_table}` ADD COLUMN activates_after_subscription_id bigint(20) DEFAULT NULL");
                if ($result === false && !empty($wpdb->last_error) && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Failed to add activates_after_subscription_id column: ' . $wpdb->last_error);
                }
            }
        }
        
        $downloads_table = $wpdb->prefix . 'subscription_downloads';
        $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $downloads_table));
        
        if ($table_exists) {
            $downloads_columns = $wpdb->get_col("DESC `{$downloads_table}`", 0);
            
            if (!in_array('user_agent', $downloads_columns, true)) {
                $wpdb->query("ALTER TABLE `{$downloads_table}` ADD COLUMN user_agent text DEFAULT NULL");
            }
            
            $indexes = $wpdb->get_results("SHOW INDEX FROM `{$downloads_table}`");
            $index_names = array();
            foreach ($indexes as $index) {
                $index_names[] = $index->Key_name;
            }
            
            if (!in_array('user_product', $index_names, true)) {
                $wpdb->query("ALTER TABLE `{$downloads_table}` ADD INDEX user_product (user_id, product_id)");
            }
            
            if (!in_array('subscription_product', $index_names, true)) {
                $wpdb->query("ALTER TABLE `{$downloads_table}` ADD INDEX subscription_product (subscription_id, product_id)");
            }
        }
        
        $tokens_table = $wpdb->prefix . 'subscription_download_tokens';
        $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $tokens_table));
        
        if (!$table_exists) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql_tokens = "CREATE TABLE IF NOT EXISTS $tokens_table (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                token varchar(64) NOT NULL,
                user_id bigint(20) NOT NULL,
                product_id bigint(20) NOT NULL,
                subscription_id bigint(20) DEFAULT NULL,
                expires_at datetime NOT NULL,
                used_count int(11) DEFAULT 0,
                max_uses int(11) DEFAULT 1,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY token (token),
                KEY user_id (user_id),
                KEY product_id (product_id),
                KEY subscription_id (subscription_id),
                KEY expires_at (expires_at)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql_tokens);
        }
    }

    /**
     * مهاجرت دیتابیس:
     * - dbDelta فقط وقتی اجرا می‌شود که هنوز به ESHTERAKYAR_DB_REVISION نرسیده باشیم (سبک‌تر در خواست‌های بعدی).
     * - ترمیم ستون/ایندکس همیشه یک‌بار در هر درخواست بررسی می‌شود تا سایت‌های «ریویژن کامل ولی جدول ناقص» درست شوند.
     * - eshterakyar_db_version همیشه با نسخهٔ افزونه همگام است.
     */
    public static function update_tables() {
        static $in_progress = false;
        if ($in_progress) {
            return;
        }
        $in_progress = true;

        global $wpdb;

        $target_revision = defined('ESHTERAKYAR_DB_REVISION') ? (int) ESHTERAKYAR_DB_REVISION : 3;
        $stored_revision = (int) get_option('eshterakyar_db_revision', 0);
        $plugin_version = defined('ESHTERAKYAR_VERSION') ? ESHTERAKYAR_VERSION : '1.5.0';
        $needs_revision_migrate = ($stored_revision < $target_revision || $stored_revision === 0);
        $core_tables_missing = self::core_subscription_tables_missing();
        $run_db_delta = ($needs_revision_migrate || $core_tables_missing);

        if ($needs_revision_migrate && class_exists('Eshterakyar_Logs')) {
            Eshterakyar_Logs::log_debug('Eshterakyar DB migration starting', array(
                'stored_revision' => $stored_revision,
                'target_revision' => $target_revision,
                'plugin_version' => $plugin_version,
                'core_tables_missing' => $core_tables_missing,
            ));
        }

        if ($run_db_delta) {
            self::create_tables();
        }

        self::apply_incremental_schema_fixes();

        if ($needs_revision_migrate) {
            update_option('eshterakyar_db_revision', $target_revision);

            if (class_exists('Eshterakyar_Logs')) {
                Eshterakyar_Logs::log_debug('Eshterakyar DB migration finished', array(
                    'revision' => $target_revision,
                    'plugin_version' => $plugin_version,
                    'wpdb_last_error' => $wpdb->last_error ? $wpdb->last_error : '',
                ));
            }
        }

        update_option('eshterakyar_db_version', $plugin_version);

        $in_progress = false;
    }
}
