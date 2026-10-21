
<?php
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
$result = $wpdb->get_results ( "SELECT * FROM $table_name LIMIT 1" )[0];
$settings = json_decode($result->all_settings);
if(!isset($settings->template))
$settings->template = '0';

include('theme-'.$settings->template.'/index.php');


?>

