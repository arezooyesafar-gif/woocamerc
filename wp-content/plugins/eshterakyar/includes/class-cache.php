<?php

if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Cache {
    
    private static $cache_group = 'eshterakyar';
    private static $cache_duration = 3600; 
    private static $cache_enabled = null; 
    

    private static function is_cache_enabled() {
        if (self::$cache_enabled === null) {
            global $wpdb;
            $option_value = $wpdb->get_var($wpdb->prepare(
                "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
                'eshterakyar_options'
            ));
            
            $options = $option_value ? maybe_unserialize($option_value) : array();
            self::$cache_enabled = isset($options['enable_cache']) && $options['enable_cache'];
        }
        
        return self::$cache_enabled;
    }

    public static function get($key) {
        if (!self::is_cache_enabled()) {
            return false;
        }
        
        return wp_cache_get($key, self::$cache_group);
    }
    
    public static function set($key, $value, $duration = null) {
        if (!self::is_cache_enabled()) {
            return false;
        }
        
        if ($duration === null) {
            $duration = self::$cache_duration;
        }
        
        return wp_cache_set($key, $value, self::$cache_group, $duration);
    }
    
    public static function delete($key) {

        return wp_cache_delete($key, self::$cache_group);
    }
    

    public static function flush() {
        if (function_exists('wp_cache_flush_group')) {
            wp_cache_flush_group(self::$cache_group);
        }
    }
    

    public static function get_active_subscription($user_id) {
        $cache_key = 'active_subscription_' . $user_id;
        $subscription = self::get($cache_key);
        
        if ($subscription === false) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'subscriptions';
            $plans_table = $wpdb->prefix . 'subscription_plans';
            $user_id = intval($user_id);
            
            $subscription = $wpdb->get_row($wpdb->prepare(
                "SELECT s.*, p.name as plan_name, p.description as plan_description 
                 FROM `{$table_name}` s 
                 LEFT JOIN `{$plans_table}` p ON s.plan_id = p.id 
                 WHERE s.user_id = %d AND s.status = 'active' AND s.is_active = 1 AND s.end_date > NOW() 
                 ORDER BY s.end_date DESC 
                 LIMIT 1",
                $user_id
            ));
            
            if ($subscription) {
                self::set($cache_key, $subscription);
            }
        }
        
        return $subscription;
    }
    public static function clear_user_subscription_cache($user_id) {
        self::delete('active_subscription_' . $user_id);
        self::delete('user_subscriptions_' . $user_id);
    }
}

