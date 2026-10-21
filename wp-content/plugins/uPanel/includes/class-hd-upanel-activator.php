<?php

/**
 * Fired during plugin activation
 *
 * @link       hashtagdevelop
 * @since      1.0.0
 *
 * @package    Hd_Upanel
 * @subpackage Hd_Upanel/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Hd_Upanel
 * @subpackage Hd_Upanel/includes
 * @author     HashtagDevelop <info@hashtagdevelop.com>
 */
class Hd_Upanel_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

// 	$directory = get_template_directory_uri();
// print_r($directory);
// die();

global $wpdb;
$charset_collate = $wpdb->get_charset_collate();


require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


$table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
if ( ! $wpdb->get_var( $query ) == $table_name ) {
$userssql = "CREATE TABLE $table_name (
	id int(9) NOT NULL AUTO_INCREMENT,
	setting_id int(9) DEFAULT 1,
	items MEDIUMTEXT,
	settings MEDIUMTEXT,
	all_settings MEDIUMTEXT,
	UNIQUE KEY id (id)
) $charset_collate;";
dbDelta( $userssql );

add_option( 'upanel_sms_content', "کد تایید شما X");
add_option( 'upanel_sms_reset_password', "کد تایید شما X");
$wpdb->insert( 
	$table_name, 
	array( 
		'settings'     => '{"settings":"","all_settings":"","dashboard_label":"\u062f\u0627\u0634\u0628\u0648\u0631\u062f","dashboard_active":"on","welcome_card_active":"on","welcome_label":"\u06a9\u0627\u0631\u0628\u0631 \u06af\u0631\u0627\u0645\u06cc name \u0628\u0647 \u067e\u0646\u0644 \u06a9\u0627\u0631\u0628\u0631\u06cc \u062e\u0648\u0634 \u0622\u0645\u062f\u06cc\u062f.","calendar_card_active":"on","calendar_img":"","jalali_card_active":"on","georgian_card_active":"on","comments_card_active":"on","comments_background":"#00ffb2","settings_card_active":"on","orders_card_active":"on","register_card_active":"on","register_background":"#4b00b0","register_card_show":"2","address_card_active":"on","log_out_card_active":"on","orders_label":"\u0633\u0641\u0627\u0631\u0634 \u0647\u0627","orders_active":"on","address_label":"\u0622\u062f\u0631\u0633 \u0647\u0627","address_active":"on","notifications_label":"\u0627\u0639\u0644\u0627\u0646 \u0647\u0627","notifications_active":"on","downloads_label":"\u062f\u0627\u0646\u0644\u0648\u062f \u0647\u0627","downloads_active":"on","settings_label":"\u062a\u0646\u0638\u06cc\u0645\u0627\u062a","settings_active":"on","logout_label":"\u062e\u0631\u0648\u062c \u0627\u0632 \u062d\u0633\u0627\u0628","logout_active":"on","divider_label":"30","divider_active":"on","submit":"\u0630\u062e\u06cc\u0631\u0647 \u062a\u063a\u06cc\u06cc\u0631\u0627\u062a","link_1_active":"off","link_1_newtab":"off","link_1_link":"link","link_1_label":"لینک یک","link_2_active":"off","link_2_newtab":"off","link_2_link":"link","link_2_label":"لینک دو","link_3_active":"off","link_3_newtab":"off","link_3_link":"link","link_3_label":"لینک سه","link_4_active":"off","link_4_newtab":"off","link_4_link":"link","link_4_label":"لینک چهار","link_5_active":"off","link_5_newtab":"off","link_5_link":"link","link_5_label":"لینک پنج","html_1_active":"off","html_1_link":"menu-1","html_1_label":"منو یک","html_2_active":"off","html_2_link":"menu-2","html_2_label":"منو دو","html_3_active":"off","html_3_link":"menu-3","html_3_label":"منو سه","html_4_active":"off","html_4_link":"menu-4","html_4_label":"منو چهار","html_5_active":"off","html_5_link":"menu-5","html_5_label":"منو پنج","tickets_active":"off","tickets_label":"پشتیبانی"
		}',
		'all_settings' => '{"template":"0","template":"0","url":"panel","login_page":"on","color":"#2052ff","upanel_login_content_color":"#ffffff","color_dark":"#ffb81f","main_logo":"","main_logo_dark":"","register_background":"","login_background":"","copyright":"\u00a9\u06f1\u06f4\u06f0\u06f0 - \u062a\u0645\u0627\u0645\u06cc \u062d\u0642\u0648\u0642 \u0645\u0627\u062f\u06cc \u0648 \u0645\u0639\u0646\u0648\u06cc \u0645\u062a\u0639\u0644\u0642 \u0628\u0647 \u0627\u06cc\u0646 \u0633\u0627\u06cc\u062a \u0645\u06cc\u200c\u0628\u0627\u0634\u062f.","dark_mode":"on","facebook_active":"on","facebook":"","twitter_active":"on","twitter":"","instagram_active":"on","instagram":"","telegram_active":"on","telegram":"","whatsapp_active":"on","whatsapp":"","avatar":"on","home_link":"on","account":"on","submit":"\u0630\u062e\u06cc\u0631\u0647 \u062a\u063a\u06cc\u06cc\u0631\u0627\u062a","upanel_login_content_color":"#ffffff","departments":"%D9%85%D8%A7%D9%84%DB%8C%2C%D9%81%D9%86%DB%8C","ticket_expire":5
		}',
		'items'     => '[\"dashboard\",\"orders\",\"notifications\",\"addresses\",\"downloads\",\"divider\",\"settings\",\"logout\",\"link_1\",\"link_2\",\"link_3\",\"link_4\",\"link_5\",\"html_1\",\"html_2\",\"html_3\",\"html_4\",\"html_5\",\"tickets\"]',
		
	)
);

}else {
	

	add_option( 'upanel_sms_content', "کد تایید شما X");
	add_option( 'upanel_sms_reset_password', "کد تایید شما X");
	$table = $wpdb->prefix.'hd_upanel_sidemenu';

	$table_colomns = $wpdb->get_results ( "SELECT * FROM $table LIMIT 1 " );
	$table_colomns[0]->items = json_decode(stripslashes($table_colomns[0]->items));
	$table_colomns[0]->settings = json_decode($table_colomns[0]->settings);
	$table_colomns[0]->all_settings = json_decode($table_colomns[0]->all_settings);



if(!in_array("link_1", $table_colomns[0]->items)){
	$update = true;	
	array_push($table_colomns[0]->items,"link_1");
	array_push($table_colomns[0]->items,"link_2");
	array_push($table_colomns[0]->items,"link_3");
	array_push($table_colomns[0]->items,"link_4");
	array_push($table_colomns[0]->items,"link_5");
	array_push($table_colomns[0]->items,"html_1");
	array_push($table_colomns[0]->items,"html_2");
	array_push($table_colomns[0]->items,"html_3");
	array_push($table_colomns[0]->items,"html_4");
	array_push($table_colomns[0]->items,"html_5");

	$table_colomns[0]->settings->link_1_active = 'off';
	$table_colomns[0]->settings->link_1_newtab = 'off';
	$table_colomns[0]->settings->link_1_link = 'link';
	$table_colomns[0]->settings->link_1_label = 'لینک یک';

	$table_colomns[0]->settings->link_2_active = 'off';
	$table_colomns[0]->settings->link_2_newtab = 'off';
	$table_colomns[0]->settings->link_2_link = 'link';
	$table_colomns[0]->settings->link_2_label = 'لینک دو';

	$table_colomns[0]->settings->link_3_active = 'off';
	$table_colomns[0]->settings->link_3_newtab = 'off';
	$table_colomns[0]->settings->link_3_link = 'link';
	$table_colomns[0]->settings->link_3_label = 'لینک سه';

	$table_colomns[0]->settings->link_4_active = 'off';
	$table_colomns[0]->settings->link_4_newtab = 'off';
	$table_colomns[0]->settings->link_4_link = 'link';
	$table_colomns[0]->settings->link_4_label = 'لینک چهار';

	$table_colomns[0]->settings->link_5_active = 'off';
	$table_colomns[0]->settings->link_5_newtab = 'off';
	$table_colomns[0]->settings->link_5_link = 'link';
	$table_colomns[0]->settings->link_5_label = 'لینک پنج';

	$table_colomns[0]->settings->html_1_active = 'off';
	$table_colomns[0]->settings->html_1_link = 'menu-1';
	$table_colomns[0]->settings->html_1_label = 'منو یک';

	$table_colomns[0]->settings->html_2_active = 'off';
	$table_colomns[0]->settings->html_2_link = 'menu-2';
	$table_colomns[0]->settings->html_2_label = 'منو دو';

	$table_colomns[0]->settings->html_3_active = 'off';
	$table_colomns[0]->settings->html_3_link = 'menu-3';
	$table_colomns[0]->settings->html_3_label = 'منو سه';

	$table_colomns[0]->settings->html_4_active = 'off';
	$table_colomns[0]->settings->html_4_link = 'menu-4';
	$table_colomns[0]->settings->html_4_label = 'منو چهار';

	$table_colomns[0]->settings->html_5_active = 'off';
	$table_colomns[0]->settings->html_5_link = 'menu-5';
	$table_colomns[0]->settings->html_5_label = 'منو پنج';

	$table_colomns[0]->all_settings->upanel_login_content_color = '#ffffff';
	

	$wpdb->update( $table_name, array(
		'items' => json_encode($table_colomns[0]->items),
		'settings' => json_encode($table_colomns[0]->settings),
		'all_settings' => json_encode($table_colomns[0]->all_settings)
	), array("id" => $table_colomns[0]->id));

}
if(!in_array("tickets", $table_colomns[0]->items)){
	array_push($table_colomns[0]->items,"tickets");
	$table_colomns[0]->settings->tickets_active = 'off';
	$table_colomns[0]->settings->tickets_label = 'پشتیبانی';

	$table_colomns[0]->all_settings->departments = urlencode("مالی,فنی");
	$table_colomns[0]->all_settings->ticket_expire = 5;
	
	$wpdb->update( $table_name, array(
		'items' => json_encode($table_colomns[0]->items),
		'settings' => json_encode($table_colomns[0]->settings),
		'all_settings' => json_encode($table_colomns[0]->all_settings)
	), array("id" => $table_colomns[0]->id));
}
if(!in_array("membership", $table_colomns[0]->items)){
	array_push($table_colomns[0]->items,"membership");
	$table_colomns[0]->settings->membership_active = 'off';
	$table_colomns[0]->settings->membership_label = 'اشتراک';

	
	$wpdb->update( $table_name, array(
		'items' => json_encode($table_colomns[0]->items),
		'settings' => json_encode($table_colomns[0]->settings)
	), array("id" => $table_colomns[0]->id));
}

if(!isset($table_colomns[0]->all_settings->template)){
	$table_colomns[0]->all_settings->template = '0';

	$wpdb->update( $table_name, array(
		'items' => json_encode($table_colomns[0]->items),
		'settings' => json_encode($table_colomns[0]->settings),
		'all_settings' => json_encode($table_colomns[0]->all_settings)
	), array("id" => $table_colomns[0]->id));
	
}


if(!isset($table_colomns[0]->all_settings->no_index)){
	$table_colomns[0]->all_settings->no_index = 'off';

	$wpdb->update( $table_name, array(
		'items' => json_encode($table_colomns[0]->items),
		'settings' => json_encode($table_colomns[0]->settings),
		'all_settings' => json_encode($table_colomns[0]->all_settings)
	), array("id" => $table_colomns[0]->id));
	
}

}



	

$table_name = $wpdb->prefix . 'hd_upanel_notifications';
$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
if ( ! $wpdb->get_var( $query ) == $table_name ) {
$userssql = "CREATE TABLE $table_name (
	id int(9) NOT NULL AUTO_INCREMENT,
	title TEXT,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	description MEDIUMTEXT,
	user_type INT(1),
	image varchar(255) ,
	user varchar(255) ,
	UNIQUE KEY id (id)
) $charset_collate;";
dbDelta( $userssql );
} else {
	
	$table = $wpdb->prefix.'hd_upanel_notifications';
	$results = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'user_type'");
	if(count($results) == 0){
	     $wpdb->query("ALTER TABLE $table ADD  user_type INT(1) NOT NULL");
	}

}


$table_name = $wpdb->prefix . 'hd_upanel_notifications_read';
$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
if ( ! $wpdb->get_var( $query ) == $table_name ) {
$userssql = "CREATE TABLE $table_name (
	id int(9) NOT NULL AUTO_INCREMENT,
	user_id int(11),
	image varchar(255) DEFAULT null,
	notification_id MEDIUMTEXT ,
	UNIQUE KEY id (id)
) $charset_collate;";
dbDelta( $userssql );
}


$table_name = $wpdb->prefix . 'hd_upanel_tickets';
$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
if ( !$wpdb->get_var( $query ) == $table_name ) {
$userssql = "CREATE TABLE $table_name (
	id int(9) NOT NULL AUTO_INCREMENT,
	ticket_id int(11),
	title varchar(255) DEFAULT null,
	ticket_text LONGTEXT,
	ticket_priority int(11) ,
	ticket_status int(11) DEFAULT 0,
	checked int(1) DEFAULT 0,
	parent int(11),
	department varchar(255) DEFAULT null,
	department_color varchar(255) DEFAULT null,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME,
	user_id int(11),
	ticket_type varchar(255) DEFAULT null,
	UNIQUE KEY id (id)
) $charset_collate;";
dbDelta( $userssql );
}


	}

}
