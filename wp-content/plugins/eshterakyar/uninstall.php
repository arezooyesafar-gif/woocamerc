<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}
$delete_data = get_option('eshterakyar_delete_data_on_uninstall', false);
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
    delete_option('eshterakyar_delete_data_on_uninstall');
    $timestamp = wp_next_scheduled('eshterakyar_daily_expiry_check');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'eshterakyar_daily_expiry_check');
    }
    
    $timestamp = wp_next_scheduled('eshterakyar_daily_cleanup');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'eshterakyar_daily_cleanup');
    }
} else {
    delete_option('eshterakyar_delete_data_on_uninstall');
}

