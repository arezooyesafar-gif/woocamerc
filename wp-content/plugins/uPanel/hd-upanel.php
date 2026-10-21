<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              hashtagdevelop
 * @since             4.1.2
 * @package           Hd_Upanel
 *
 * @wordpress-plugin
 * Plugin Name:       افزونه پنل کاربری uPanel 
 * Plugin URI:        https://www.rtl-theme.com/upanel-wordpress-plugin/
 * Description:       با استفاده از این افزونه پنل کاربری ووکامرس وبسایتتان را به یک پنل زیبا، مدرن و کاربر پسند تبدیل کنید.
 * Version:           4.1.2
 * Author:            هشتگ توسعه
 * Author URI:        https://www.rtl-theme.com/author/hashtagdevelop/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hd-upanel
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

define('HD_UPANEL_NAME', 'hd-upanel');
define('HD_UPANEL_VERSION', '4.1.2');
define('HD_UPANEL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HD_UPANEL_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hd-upanel-activator.php
 */

function activate_hd_upanel()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-hd-upanel-activator.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-hd-upanel-databases.php';
    $database = new hd_upanel_hr_databases();
    $database->hd_upanel_create_provider_name();
    $database->hd_upanel_create_sms_providers();
    $database->hd_upanel_create_sms_table();



    Hd_Upanel_Activator::activate();
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hd-upanel-deactivator.php
 */
function deactivate_hd_upanel()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-hd-upanel-deactivator.php';
    Hd_Upanel_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_hd_upanel');
register_deactivation_hook(__FILE__, 'deactivate_hd_upanel');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-hd-upanel.php';






require_once plugin_dir_path(__FILE__) . 'includes/hd-upanel-main-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-hd-upanel-functions.php';
if (class_exists('upanel')) {
    new upanel();
}

function run_hd_upanel()
{

    $plugin = new Hd_Upanel();

    $plugin->run();
    if (function_exists('redirect_my_account_to_hd_upanel')) {
        add_action('template_redirect', 'redirect_my_account_to_hd_upanel', 1);
    }

}
run_hd_upanel();




function add_capabilities_to_shop_manager()
{
    global $wp_roles, $wpdb;

    // Get all roles
    $all_roles = $wp_roles->roles;

    // Get settings from the database
    $table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
    $result = $wpdb->get_results("SELECT * FROM $table_name LIMIT 1")[0];
    $settings = json_decode($result->all_settings);
    $user = wp_get_current_user();

    $user_roles = (array) $user->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);
    $user->remove_cap('hd_access');
    foreach ($editable_roles as $key => $value) {
        $role_string = strtolower(str_replace(" ", "_", $value['name']));
        $role = get_role($role_string);
        $role = $role_string;
        $role_setting = 'role-' . $role_string;
        if (isset($settings->$role_setting) && $settings->$role_setting == 'on' && in_array($role, $user_roles)) {

            if (!$user->has_cap('hd_access')) {
                $user->add_cap('hd_access');
            }
        }
    }

    // $user->add_cap('hd_access');

    if (in_array('administrator', $user_roles)) {
        // Add capabilities
        if (!$user->has_cap('hd_access')) {
            $user->add_cap('hd_access');
        }
    }
}

add_action('admin_init', 'add_capabilities_to_shop_manager');

function hd_upanel_support_menu_count()
{
    global $menu;
    global $post;
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'hd_upanel_tickets';

    $sql = "SELECT COUNT(*) as count FROM $table_name WHERE parent=0 AND checked=0";

    $ipquery = $wpdb->get_results($sql);

    $notification_count = $ipquery[0]->count;

    global $current_user;

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'hd_upanel_tickets';
    $contact = $wpdb->get_results("SELECT * FROM $table_name WHERE checked = 0 ");

    if ($notification_count > 0) {

        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function() {';
        echo 'var menuItem = document.querySelector("a[href=\"admin.php?page=hd-upanel-tickets\"]"); console.log(menuItem);';
        echo 'if (menuItem) {';
        echo 'var notification = document.createElement("span");';
        echo 'notification.className = "update-plugins count-' . $notification_count . '";';
        echo 'notification.style = "margin-right:5px";';
        echo 'notification.innerHTML = "<span class=\"update-count\" >' . $notification_count . '</span>";';
        echo 'menuItem.appendChild(notification);';
        echo '}';
        echo '});';
        echo '</script>';
    }


}
add_action('admin_enqueue_scripts', 'hd_upanel_support_menu_count');
function hd_upanel_register_menu()
{


    global $current_user;

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'hd_upanel_tickets';
    $contact = $wpdb->get_results("SELECT * FROM $table_name WHERE checked = 0 ");


    $menu_title = 'تیکت پشتیبانی';
    if (count($contact) > 0) {
        $menu_title .= sprintf('<span class="awaiting-mod">%d</span>', count($contact));
    }




    add_menu_page(
        'HD Upanel',
        'پنل کاربری uPanel',
        'hd_access',
        'hd-upanel',  // Use a unique slug
        'hd_upanel_sidemenu',
        HD_UPANEL_PLUGIN_URL . 'public/img/dashboard.png',
        81
    );
    add_submenu_page('hd-upanel', 'منو کناری', 'منو کناری ', 'hd_access', 'hd-upanel', 'hd_upanel_sidemenu');
    add_submenu_page('hd-upanel', 'اعلان ها', ' اعلان ها ', 'hd_access', 'hd-upanel-notifications', 'hd_upanel_notifications');
    add_submenu_page(
        'hd-upanel',
        $menu_title,
        'تیکت پشتیبانی',
        'hd_access',
        'hd-upanel-tickets',
        'hd_upanel_tickets'
    );

    add_submenu_page('hd-upanel', 'تنظیمات', 'تنظیمات', 'hd_access', 'hd-upanel-settings', 'hd_upanel_settings');
    add_submenu_page('hd-upanel-notifications-new', ' اعلان جدید', ' اعلان جدید', 'hd_access', 'hd-upanel-new-notification', 'hd_upanel_notification_new');
    add_submenu_page('hd-upanel-notifications-edit', ' ویرایش اعلان ', '  ویرایش اعلان', 'hd_access', 'hd-upanel-edit-notification', 'hd_upanel_notification_edit');
    add_submenu_page('hd-upanel-notifications-edit', ' ویرایش تیکت ', '  ویرایش تیکت', 'hd_access', 'hd-upanel-tickets-edit', 'hd_upanel_tickets_edit');
}
add_action('admin_menu', 'hd_upanel_register_menu');







function hd_upanel_notification_edit()
{
    require_once plugin_dir_path(__FILE__) . 'includes/hd_upanel_notification_edit.php';
}
function hd_upanel_notification_new()
{
    require_once plugin_dir_path(__FILE__) . 'includes/hd_upanel_notification_new.php';
}
function hd_upanel_notifications()
{
    require_once plugin_dir_path(__FILE__) . 'includes/hd_upanel_notifications.php';
}
function hd_upanel_sidemenu()
{
    require_once plugin_dir_path(__FILE__) . 'includes/hd_upanel_sidemenu.php';
}
function hd_upanel_themes()
{
    require_once plugin_dir_path(__FILE__) . 'includes/hd_upanel_themes.php';
}
function hd_upanel_settings()
{
    require_once plugin_dir_path(__FILE__) . 'includes/hd_upanel_settings.php';
}
function hd_upanel_tickets()
{
    require_once plugin_dir_path(__FILE__) . 'includes/hd_upanel_tickets.php';
}
function hd_upanel_tickets_edit()
{
    require_once plugin_dir_path(__FILE__) . 'includes/hd_upanel_tickets_edit.php';
}


add_action( 'rest_api_init', function () {
    register_rest_route( 'hd-upanel/v1', '/countries', [
        'methods'  => 'GET',
        'callback' => function () {
            $countries_obj = new WC_Countries();
            return [
                'countries' => $countries_obj->get_countries(),
                'states'    => $countries_obj->get_states(),
            ];
        },
        'permission_callback' => '__return_true',
    ]);
});