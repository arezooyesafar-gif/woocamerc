<?php


if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Cleanup {
    

    public static function cleanup_old_downloads() {
        global $wpdb;
        
        $options = get_option('eshterakyar_options', array());
        $retention_days = isset($options['download_history_retention']) ? intval($options['download_history_retention']) : 365;
        
        if ($retention_days <= 0) {
            return; 
        }
        
        $table_name = $wpdb->prefix . 'subscription_downloads';
        $retention_days = intval($retention_days); 
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$retention_days} days"));
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM `{$table_name}` WHERE download_date < %s",
            $cutoff_date
        ));
        
        return $deleted;
    }
    

    public static function schedule_cleanup() {
        if (!wp_next_scheduled('eshterakyar_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'eshterakyar_daily_cleanup');
        }
    }

    public static function daily_cleanup() {
        self::cleanup_old_downloads();
        self::cleanup_expired_tokens();
    }
    
    /**
     * Clean up expired download tokens
     * @return int Number of deleted tokens
     */
    public static function cleanup_expired_tokens() {
        return Eshterakyar_Download::cleanup_expired_tokens();
    }
}

add_action('wp', array('Eshterakyar_Cleanup', 'schedule_cleanup'));
add_action('eshterakyar_daily_cleanup', array('Eshterakyar_Cleanup', 'daily_cleanup'));

