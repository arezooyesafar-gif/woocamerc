<?php

class hd_upanel_hr_databases
{

    /**
     * Create or update sms providers table
     */
    function hd_upanel_create_sms_providers()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'hd_upanel_sms_providers';

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE $table_name (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `provider` int(11) NOT NULL,
            `title` varchar(255) NOT NULL,
            `username` varchar(255) NULL,
            `password` varchar(255) NULL,
            `token` varchar(255) NULL,
            `reset_password_pattern` varchar(255) NULL,
            `type` tinyint(1) NOT NULL DEFAULT 0,
            `number` varchar(255) NOT NULL,
            `is_default` tinyint(1) NOT NULL DEFAULT 0,
            `sms_time_check` int(11) NOT NULL DEFAULT 5,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `ordering` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        dbDelta($sql);

        // Ensure missing columns are added
        $this->maybe_add_column($table_name, 'ordering', "ALTER TABLE $table_name ADD `ordering` int(11) NOT NULL DEFAULT 0");
        $this->maybe_add_column($table_name, 'reset_password_pattern', "ALTER TABLE $table_name ADD `reset_password_pattern` varchar(255) NULL");

        // Default providers
        $providers = [
            [1, 1, 'کاوه نگار', 1, 1, 1],
            [2, 2, 'کاوه نگار (اعتبارسنجی-OTP)', 1, 0, 2],
            [3, 3, 'فراز', 0, 0, 3],
            [4, 4, 'ملی پیامک', 0, 0, 5],
            [5, 5, 'رایگان پیامک', 0, 0, 9],
            [6, 6, 'sms.ir', 1, 0, 7],
            [7, 7, 'ملی پیامک (اعتبارسنجی-OTP)', 0, 0, 6],
            [8, 8, 'فراز (اعتبارسنجی-OTP)', 1, 0, 4],
            [9, 9, 'sms.ir (اعتبارسنجی-OTP)', 1, 0, 8],
        ];

        foreach ($providers as $p) {
            [$id, $provider, $title, $type, $is_default, $ordering] = $p;
            $wpdb->query($wpdb->prepare(
                "INSERT INTO $table_name (id, provider, title, type, is_default, ordering)
     VALUES (%d, %d, %s, %d, %d, %d)
     ON DUPLICATE KEY UPDATE 
        title=VALUES(title), 
        type=VALUES(type), 
        ordering=VALUES(ordering)",
                $id,
                $provider,
                $title,
                $type,
                $is_default,
                $ordering
            ));

        }
    }

    /**
     * Create or update provider name table
     */
    function hd_upanel_create_provider_name()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'hd_upanel_sms_provider_name';

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE $table_name (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NULL,
            `className` varchar(255) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `className` (`className`)
        ) $charset_collate;";

        dbDelta($sql);

        // Reset and insert default rows
        $wpdb->query("TRUNCATE TABLE $table_name");

        $providers = [
            [1, 'کاوه نگار', 'kavenegarRest'],
            [2, 'کاوه نگار (اعتبارسنجی-OTP)', 'kavenegarUtp'],
            [3, 'فراز', 'farazsmsRest'],
            [4, 'ملی پیامک', 'melipayamakRest'],
            [5, 'رایگان پیامک', 'rayeganSMSRest'],
            [6, '(پنل جدید) sms.ir', 'smsirRest'],
            [7, 'ملی پیامک (اعتبارسنجی-OTP)', 'melipayamakUtp'],
            [8, 'فراز (اعتبارسنجی-OTP)', 'farazSmsUtp'],
            [9, '(اعتبارسنجی-OTP) sms.ir', 'smsirUtp'],
        ];

        foreach ($providers as $p) {
            [$id, $title, $className] = $p;
            $wpdb->insert($table_name, [
                'id' => $id,
                'title' => $title,
                'className' => $className,
                'created_at' => current_time('mysql'),
            ]);
        }
    }

    /**
     * Create sms and forgot email tables
     */
    function hd_upanel_create_sms_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // SMS table
        $sms_table = $wpdb->prefix . 'hd_upanel_sms_message';
        $sql1 = "CREATE TABLE $sms_table (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `provider_id` int(11) NOT NULL,
            `receptor` varchar(255) NOT NULL,
            `type` varchar(255) NOT NULL,
            `is_view` tinyint(1) NOT NULL DEFAULT 0,
            `content` text NOT NULL,
            `code` varchar(255) NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql1);

        // Forgot email table
        $email_table = $wpdb->prefix . 'hd_upanel_email_forgot';
        $sql2 = "CREATE TABLE $email_table (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `code` varchar(255) NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql2);
    }

    /**
     * Helper: add column if missing
     */
    private function maybe_add_column($table, $column, $alter_sql)
    {
        global $wpdb;
        $exists = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM $table LIKE %s", $column));
        if (!$exists) {
            $wpdb->query($alter_sql);
        }
    }
}
