<?php


if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Plan {
    
    /**
     * دریافت تمام پلن‌ها
     * @param string $status 'active', 'inactive', 'deleted' یا 'all' برای همه پلن‌ها
     */
    public static function get_all($status = 'active') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_plans';
        
        
        $table_columns = $wpdb->get_col("DESC `{$table_name}`", 0);
        
        
        $desired_columns = array(
            'id', 'name', 'description', 'price', 'sale_price', 
            'duration_days', 'daily_limit', 'total_limit', 'product_id', 
            'status', 'badge_text', 'badge_color', 'merge_with_existing',
            'allowed_categories', 'allowed_tags', 'exclude_categories', 'exclude_tags',
            'include_children_allowed_categories', 'include_children_exclude_categories',
            'allowed_post_categories', 'allowed_post_tags', 'exclude_post_categories', 'exclude_post_tags',
            'include_children_allowed_post_categories', 'include_children_exclude_post_categories',
            'is_recommended',
            'download_access_days', 'max_downloads_per_product', 'subscription_group_id', 'custom_features', 'lifetime_access'
        );
        
        
        $available_columns = array();
        foreach ($desired_columns as $col) {
            if (in_array($col, $table_columns)) {
                $available_columns[] = $col;
            }
        }
        
       
        if (empty($available_columns)) {
            $available_columns = array('id', 'name', 'description', 'price', 'duration_days', 'daily_limit', 'total_limit', 'product_id', 'status');
        }
        
        $sql_columns = implode(', ', $available_columns);
        
        
        $has_sort_order = in_array('sort_order', $table_columns);
        $order_by = $has_sort_order ? 'sort_order ASC, id ASC' : 'price ASC';
        
        if ($status === 'all' || $status === null || $status === '') {
            $query = "SELECT {$sql_columns} FROM {$table_name} WHERE status != 'deleted' ORDER BY {$order_by}";
            $results = $wpdb->get_results($query);
        } else {
            $status = sanitize_text_field($status);
            $allowed_statuses = array('active', 'inactive', 'deleted');
            if (!in_array($status, $allowed_statuses)) {
                $status = 'active';
            }
            $query = $wpdb->prepare(
                "SELECT {$sql_columns} FROM {$table_name} WHERE status = %s ORDER BY {$order_by}",
                $status
            );
            $results = $wpdb->get_results($query);
        }
        
        $results = $results ? $results : array();
        return $results;
    }
    

    public static function get_by_id($plan_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_plans';
        $plan_id = intval($plan_id);
        
        
        $table_columns = $wpdb->get_col("DESC `{$table_name}`", 0);
        
        
        $desired_columns = array(
            'id', 'name', 'description', 'price', 'sale_price', 
            'duration_days', 'daily_limit', 'total_limit', 'product_id', 
            'status', 'badge_text', 'badge_color', 'merge_with_existing',
            'allowed_categories', 'allowed_tags', 'exclude_categories', 'exclude_tags',
            'include_children_allowed_categories', 'include_children_exclude_categories',
            'allowed_post_categories', 'allowed_post_tags', 'exclude_post_categories', 'exclude_post_tags',
            'include_children_allowed_post_categories', 'include_children_exclude_post_categories',
            'is_recommended',
            'download_access_days', 'max_downloads_per_product', 'subscription_group_id', 'custom_features', 'lifetime_access'
        );
        
        
        $available_columns = array();
        foreach ($desired_columns as $col) {
            if (in_array($col, $table_columns)) {
                $available_columns[] = $col;
            }
        }
        
       
        if (empty($available_columns)) {
            $available_columns = array('id', 'name', 'description', 'price', 'duration_days', 'daily_limit', 'total_limit', 'product_id', 'status');
        }
        
        $sql_columns = implode(', ', $available_columns);
        
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT {$sql_columns} FROM {$table_name} WHERE id = %d",
            $plan_id
        ));
        
        return $plan;
    }

    public static function get_by_product($product_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscription_plans';
        $product_id = intval($product_id);
        
        
        $table_columns = $wpdb->get_col("DESC `{$table_name}`", 0);
        
        
        $desired_columns = array(
            'id', 'name', 'description', 'price', 'sale_price', 
            'duration_days', 'daily_limit', 'total_limit', 'product_id', 
            'status', 'badge_text', 'badge_color', 'merge_with_existing', 'sort_order',
            'allowed_categories', 'allowed_tags', 'exclude_categories', 'exclude_tags',
            'include_children_allowed_categories', 'include_children_exclude_categories',
            'allowed_post_categories', 'allowed_post_tags', 'exclude_post_categories', 'exclude_post_tags',
            'include_children_allowed_post_categories', 'include_children_exclude_post_categories',
            'is_recommended',
            'download_access_days', 'max_downloads_per_product', 'subscription_group_id', 'custom_features', 'lifetime_access'
        );
        
        
        $available_columns = array();
        foreach ($desired_columns as $col) {
            if (in_array($col, $table_columns)) {
                $available_columns[] = $col;
            }
        }
        
        
        if (empty($available_columns)) {
            $available_columns = array('id', 'name', 'description', 'price', 'duration_days', 'daily_limit', 'total_limit', 'product_id', 'status');
        }
        
        $sql_columns = implode(', ', $available_columns);
        

        $has_sort_order = in_array('sort_order', $table_columns);
        $order_by = $has_sort_order ? 'sort_order ASC, id ASC' : 'price ASC';
        
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT {$sql_columns} FROM {$table_name} WHERE product_id = %d AND status = 'active' ORDER BY {$order_by} LIMIT 1",
            $product_id
        ));
        
        return $plan;
    }
    

    public static function create($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_plans';
        
        $table_columns = $wpdb->get_col("DESC `{$table}`", 0);
        
        $columns_to_ensure = array(
            'custom_features' => "ALTER TABLE `{$table}` ADD COLUMN custom_features text DEFAULT NULL",
            'allowed_categories' => "ALTER TABLE `{$table}` ADD COLUMN allowed_categories text DEFAULT NULL",
            'allowed_tags' => "ALTER TABLE `{$table}` ADD COLUMN allowed_tags text DEFAULT NULL",
            'exclude_categories' => "ALTER TABLE `{$table}` ADD COLUMN exclude_categories text DEFAULT NULL",
            'exclude_tags' => "ALTER TABLE `{$table}` ADD COLUMN exclude_tags text DEFAULT NULL",
            'include_children_allowed_categories' => "ALTER TABLE `{$table}` ADD COLUMN include_children_allowed_categories tinyint(1) DEFAULT 1",
            'include_children_exclude_categories' => "ALTER TABLE `{$table}` ADD COLUMN include_children_exclude_categories tinyint(1) DEFAULT 1",
            'allowed_post_categories' => "ALTER TABLE `{$table}` ADD COLUMN allowed_post_categories text DEFAULT NULL",
            'allowed_post_tags' => "ALTER TABLE `{$table}` ADD COLUMN allowed_post_tags text DEFAULT NULL",
            'exclude_post_categories' => "ALTER TABLE `{$table}` ADD COLUMN exclude_post_categories text DEFAULT NULL",
            'exclude_post_tags' => "ALTER TABLE `{$table}` ADD COLUMN exclude_post_tags text DEFAULT NULL",
            'include_children_allowed_post_categories' => "ALTER TABLE `{$table}` ADD COLUMN include_children_allowed_post_categories tinyint(1) DEFAULT 1",
            'include_children_exclude_post_categories' => "ALTER TABLE `{$table}` ADD COLUMN include_children_exclude_post_categories tinyint(1) DEFAULT 1",
            'is_recommended' => "ALTER TABLE `{$table}` ADD COLUMN is_recommended tinyint(1) DEFAULT 0"
        );
        foreach ($columns_to_ensure as $col => $sql) {
            if (!in_array($col, $table_columns)) {
                $wpdb->query($sql);
                $table_columns[] = $col;
            }
        }
        
        $defaults = array(
            'name' => '',
            'description' => '',
            'price' => 0,
            'duration_days' => 30,
            'daily_limit' => 0,
            'total_limit' => 0,
            'product_id' => null,
            'status' => 'active',
            'badge_text' => null,
            'badge_color' => 'orange',
            'custom_features' => '',
            'allowed_categories' => '',
            'allowed_tags' => '',
            'exclude_categories' => '',
            'exclude_tags' => '',
            'include_children_allowed_categories' => 1,
            'include_children_exclude_categories' => 1,
            'allowed_post_categories' => '',
            'allowed_post_tags' => '',
            'exclude_post_categories' => '',
            'exclude_post_tags' => '',
            'include_children_allowed_post_categories' => 1,
            'include_children_exclude_post_categories' => 1,
            'is_recommended' => 0
        );
        
        $data = wp_parse_args($data, $defaults);
        
        $allowed_fields = array('name', 'description', 'price', 'sale_price', 'duration_days', 'daily_limit', 'total_limit', 'product_id', 'status', 'allowed_categories', 'allowed_tags', 'exclude_categories', 'exclude_tags', 'include_children_allowed_categories', 'include_children_exclude_categories', 'allowed_post_categories', 'allowed_post_tags', 'exclude_post_categories', 'exclude_post_tags', 'include_children_allowed_post_categories', 'include_children_exclude_post_categories', 'is_recommended', 'merge_with_existing', 'badge_text', 'badge_color', 'custom_features', 'download_access_days', 'max_downloads_per_product', 'subscription_group_id', 'lifetime_access');
        $data = array_intersect_key($data, array_flip($allowed_fields));
        
        foreach ($data as $key => $value) {
            if (!in_array($key, $table_columns)) {
                unset($data[$key]);
            }
        }
        
        if (in_array('badge_text', $table_columns)) {
            if (!isset($data['badge_text']) || $data['badge_text'] === null) {
                $data['badge_text'] = '';
            } else {

                $data['badge_text'] = (string) $data['badge_text'];
            }
        }
        if (in_array('badge_color', $table_columns)) {
            if (!isset($data['badge_color']) || $data['badge_color'] === null || $data['badge_color'] === '') {
                $data['badge_color'] = 'orange';
            } else {

                $data['badge_color'] = (string) $data['badge_color'];
            }
        }
        if (in_array('custom_features', $table_columns)) {
            if (!isset($data['custom_features']) || $data['custom_features'] === null) {
                $data['custom_features'] = '';
            } else {
                $data['custom_features'] = (string) $data['custom_features'];
            }
        }
        foreach (array('allowed_categories', 'allowed_tags', 'exclude_categories', 'exclude_tags', 'allowed_post_categories', 'allowed_post_tags', 'exclude_post_categories', 'exclude_post_tags') as $field) {
            if (in_array($field, $table_columns)) {
                if (!isset($data[$field]) || $data[$field] === null) {
                    $data[$field] = '';
                } else {
                    $data[$field] = (string) $data[$field];
                }
            }
        }
        foreach (array('include_children_allowed_categories', 'include_children_exclude_categories', 'include_children_allowed_post_categories', 'include_children_exclude_post_categories') as $field) {
            if (in_array($field, $table_columns)) {
                $data[$field] = isset($data[$field]) ? (int) !empty($data[$field]) : 1;
            }
        }
        if (in_array('is_recommended', $table_columns)) {
            $data['is_recommended'] = isset($data['is_recommended']) ? (int) !empty($data['is_recommended']) : 0;
        }
        if (in_array('sale_price', $table_columns)) {
            if (isset($data['sale_price']) && ($data['sale_price'] === null || $data['sale_price'] === '' || $data['sale_price'] === 0)) {
                $data['sale_price'] = null;
            } else if (isset($data['sale_price'])) {
                $data['sale_price'] = floatval($data['sale_price']);
            }
        }
        
        $text_fields = array('description', 'allowed_categories', 'allowed_tags', 'exclude_categories', 'exclude_tags', 'allowed_post_categories', 'allowed_post_tags', 'exclude_post_categories', 'exclude_post_tags', 'custom_features');
        foreach ($text_fields as $field) {
            if (isset($data[$field]) && $data[$field] === '' && in_array($field, $table_columns)) {

            }
        }
        

        foreach ($data as $key => $value) {
            if ($value === null && $key !== 'product_id') {
                unset($data[$key]);
            }
        }
        
        if (isset($data['product_id']) && ($data['product_id'] === '' || $data['product_id'] === 0)) {
            $data['product_id'] = null;
        }
        
        if (empty($data['name'])) {
            $data['name'] = '';
        }
        if (!isset($data['price'])) {
            $data['price'] = 0;
        }
        if (!isset($data['duration_days'])) {
            $data['duration_days'] = 30;
        }
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        if (!isset($data['download_access_days'])) {
            $data['download_access_days'] = 0;
        }
        if (!isset($data['max_downloads_per_product'])) {
            $data['max_downloads_per_product'] = 0;
        }
        if (!isset($data['lifetime_access'])) {
            $data['lifetime_access'] = 0;
        }
        
        $result = $wpdb->insert($table, $data);
        
        
        if ($result) {
            $plan_id = $wpdb->insert_id;
            if (in_array('is_recommended', $table_columns, true) && isset($data['is_recommended']) && (int) $data['is_recommended'] === 1) {
                $wpdb->query($wpdb->prepare(
                    "UPDATE `{$table}` SET is_recommended = 0 WHERE id != %d",
                    $plan_id
                ));
            }
            wp_cache_delete('plans_all', 'eshterakyar');
            wp_cache_delete('plans_active', 'eshterakyar');
            wp_cache_delete('plans_inactive', 'eshterakyar');
            if (class_exists('Eshterakyar_Cache')) {
                Eshterakyar_Cache::delete('plans_all');
                Eshterakyar_Cache::delete('plans_active');
                Eshterakyar_Cache::delete('plans_inactive');
            }
            return $plan_id;
        }
        
        return false;
    }
    

    public static function update($plan_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_plans';
        
        $table_columns = $wpdb->get_col("DESC `{$table}`", 0);
        
        $new_fields = array('badge_text', 'badge_color', 'custom_features', 'sale_price', 'download_access_days', 'max_downloads_per_product', 'subscription_group_id', 'include_children_allowed_categories', 'include_children_exclude_categories', 'is_recommended');
        foreach ($new_fields as $field) {
            if (!in_array($field, $table_columns)) {
                if ($field === 'subscription_group_id') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN subscription_group_id varchar(50) DEFAULT NULL");
                    $table_columns[] = 'subscription_group_id';
                } elseif ($field === 'download_access_days') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN download_access_days int(11) DEFAULT 0");
                    $table_columns[] = 'download_access_days';
                } elseif ($field === 'max_downloads_per_product') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN max_downloads_per_product int(11) DEFAULT 0");
                    $table_columns[] = 'max_downloads_per_product';
                } elseif ($field === 'badge_text') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN badge_text varchar(100) DEFAULT NULL");
                    $table_columns[] = 'badge_text'; 
                } elseif ($field === 'badge_color') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN badge_color varchar(20) DEFAULT 'orange'");
                    $table_columns[] = 'badge_color'; 
                } elseif ($field === 'custom_features') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN custom_features text DEFAULT NULL");
                    $table_columns[] = 'custom_features'; 
                } elseif ($field === 'sale_price') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN sale_price decimal(10,2) DEFAULT NULL");
                    $table_columns[] = 'sale_price'; 
                } elseif ($field === 'include_children_allowed_categories') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN include_children_allowed_categories tinyint(1) DEFAULT 1");
                    $table_columns[] = 'include_children_allowed_categories';
                } elseif ($field === 'include_children_exclude_categories') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN include_children_exclude_categories tinyint(1) DEFAULT 1");
                    $table_columns[] = 'include_children_exclude_categories';
                } elseif ($field === 'is_recommended') {
                    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN is_recommended tinyint(1) DEFAULT 0");
                    $table_columns[] = 'is_recommended';
                }
            }
        }
        
        foreach ($data as $key => $value) {
            if (!in_array($key, $table_columns)) {
                unset($data[$key]);
            }
        }
        
        if (in_array('badge_text', $table_columns)) {
            if (!isset($data['badge_text']) || $data['badge_text'] === null) {
                $data['badge_text'] = '';
            } else {

                $data['badge_text'] = (string) $data['badge_text'];
            }
        }
        if (in_array('badge_color', $table_columns)) {
            if (!isset($data['badge_color']) || $data['badge_color'] === null || $data['badge_color'] === '') {
                $data['badge_color'] = 'orange';
            } else {

                $data['badge_color'] = (string) $data['badge_color'];
            }
        }
        if (in_array('custom_features', $table_columns)) {
            if (isset($data['custom_features']) && $data['custom_features'] === null) {
                $data['custom_features'] = '';
            }
        }

        $sale_price_to_update = null;
        $has_sale_price_in_data = false;
        if (in_array('sale_price', $table_columns)) {
            if (array_key_exists('sale_price', $data)) {
                $has_sale_price_in_data = true;
                $sale_price_value = $data['sale_price'];
                
                if ($sale_price_value === null || $sale_price_value === '' || $sale_price_value === 0 || $sale_price_value === '0' || $sale_price_value === false) {
                    $sale_price_to_update = null;
                } else {
                    $sale_price_to_update = floatval($sale_price_value);
                    if ($sale_price_to_update <= 0) {
                        $sale_price_to_update = null;
                    }
                }
                unset($data['sale_price']);
            }
        }

        $preserve_fields = array('product_id', 'badge_text', 'badge_color', 'allowed_categories', 'allowed_tags', 'exclude_categories', 'exclude_tags', 'allowed_post_categories', 'allowed_post_tags', 'exclude_post_categories', 'exclude_post_tags');
        
        foreach ($data as $key => $value) {
            if ($value === null && !in_array($key, $preserve_fields)) {
                unset($data[$key]);
            }
        }
        
        if (in_array('allowed_categories', $table_columns)) {
            if (!isset($data['allowed_categories'])) {
                $data['allowed_categories'] = '';
            }
        }
        if (in_array('allowed_tags', $table_columns)) {
            if (!isset($data['allowed_tags'])) {
                $data['allowed_tags'] = '';
            }
        }
        if (in_array('exclude_categories', $table_columns)) {
            if (!isset($data['exclude_categories'])) {
                $data['exclude_categories'] = '';
            }
        }
        if (in_array('exclude_tags', $table_columns)) {
            if (!isset($data['exclude_tags'])) {
                $data['exclude_tags'] = '';
            }
        }
        foreach (array('allowed_post_categories', 'allowed_post_tags', 'exclude_post_categories', 'exclude_post_tags') as $post_field) {
            if (in_array($post_field, $table_columns) && !isset($data[$post_field])) {
                $data[$post_field] = '';
            }
        }
        if (in_array('include_children_allowed_categories', $table_columns) && isset($data['include_children_allowed_categories'])) {
            $data['include_children_allowed_categories'] = (int) !empty($data['include_children_allowed_categories']);
        }
        if (in_array('include_children_exclude_categories', $table_columns) && isset($data['include_children_exclude_categories'])) {
            $data['include_children_exclude_categories'] = (int) !empty($data['include_children_exclude_categories']);
        }
        if (in_array('include_children_allowed_post_categories', $table_columns) && isset($data['include_children_allowed_post_categories'])) {
            $data['include_children_allowed_post_categories'] = (int) !empty($data['include_children_allowed_post_categories']);
        }
        if (in_array('include_children_exclude_post_categories', $table_columns) && isset($data['include_children_exclude_post_categories'])) {
            $data['include_children_exclude_post_categories'] = (int) !empty($data['include_children_exclude_post_categories']);
        }
        if (in_array('lifetime_access', $table_columns)) {
            $data['lifetime_access'] = isset($data['lifetime_access']) ? (int) !empty($data['lifetime_access']) : 0;
        }
        
        if (isset($data['product_id']) && ($data['product_id'] === '' || $data['product_id'] === 0)) {
            $data['product_id'] = null;
        }
        

        if ($has_sale_price_in_data && in_array('sale_price', $table_columns)) {
            if ($sale_price_to_update === null) {
                $wpdb->query($wpdb->prepare(
                    "UPDATE `{$table}` SET sale_price = NULL WHERE id = %d",
                    $plan_id
                ));
            } else {
                $wpdb->query($wpdb->prepare(
                    "UPDATE `{$table}` SET sale_price = %f WHERE id = %d",
                    $sale_price_to_update,
                    $plan_id
                ));
            }
        }
        
        if (empty($data)) {
            return $has_sale_price_in_data;
        }
        
        $result = $wpdb->update(
            $table,
            $data,
            array('id' => $plan_id)
        );

        if (in_array('is_recommended', $table_columns, true) && isset($data['is_recommended']) && (int) $data['is_recommended'] === 1) {
            $wpdb->query($wpdb->prepare(
                "UPDATE `{$table}` SET is_recommended = 0 WHERE id != %d",
                $plan_id
            ));
        }
        

        if ($has_sale_price_in_data) {
            $result = ($result !== false) ? $result : true;
        }
        
        
       
        if ($result !== false) {
            if (class_exists('Eshterakyar_Cache')) {
                Eshterakyar_Cache::delete('plans_all');
                Eshterakyar_Cache::delete('plans_active');
                Eshterakyar_Cache::delete('plans_inactive');
                Eshterakyar_Cache::delete('plan_' . $plan_id);
                if (isset($data['product_id']) && $data['product_id']) {
                    Eshterakyar_Cache::delete('plan_product_' . $data['product_id']);
                }
            }
           
            wp_cache_delete('plans_all', 'eshterakyar');
            wp_cache_delete('plans_active', 'eshterakyar');
            wp_cache_delete('plans_inactive', 'eshterakyar');
            wp_cache_delete('plan_' . $plan_id, 'eshterakyar');
            if (isset($data['product_id']) && $data['product_id']) {
                wp_cache_delete('plan_product_' . $data['product_id'], 'eshterakyar');
            }
        }
        
        return $result;
    }
    

    public static function delete($plan_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_plans';
        
        $result = $wpdb->update(
            $table,
            array('status' => 'deleted'),
            array('id' => $plan_id)
        );
        
       
        if ($result !== false) {
            if (class_exists('Eshterakyar_Cache')) {
                Eshterakyar_Cache::delete('plans_all');
                Eshterakyar_Cache::delete('plans_active');
                Eshterakyar_Cache::delete('plans_inactive');
                Eshterakyar_Cache::delete('plan_' . $plan_id);
            }
            
            wp_cache_delete('plans_all', 'eshterakyar');
            wp_cache_delete('plans_active', 'eshterakyar');
            wp_cache_delete('plans_inactive', 'eshterakyar');
            wp_cache_delete('plan_' . $plan_id, 'eshterakyar');
        }
        
        return $result;
    }
}

