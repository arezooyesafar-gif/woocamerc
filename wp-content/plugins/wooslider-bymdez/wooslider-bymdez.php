<?php
/**
 * @link              https://profiles.wordpress.org/mdesignfa/
 * @since             1.0.0
 * @package           woosliderbymdez
 * 
 * @wordpress-plugin
 * Plugin Name:      Wooslider-WooProducts Touch Slider
 * Plugin URI:       https://wooslider.ir/
 * Description:      وواسلایدر، اسلایدر مدرن و بهینه برای محصولات سایت وردپرسی شما   
 * Version:          6.1.12
 * Author:           M_Design
 * License:          GPL-3.0+
 * License URI:      http://www.gnu.org/licenses/gpl-3.0.txt
 * Author URI:       https://landing.mdezign.ir/
 * Text Domain:      wooslider-bymdez
 * Domain Path:      /languages
 */

if (!function_exists('add_action'))
{
    echo '<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>';
    exit();
}
else if (!defined('ABSPATH'))
{
    echo '<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>';
    exit();
}

/*Define Consts*/
if (!defined('WOOSBYMDZ_IS_WPHOOLU')) { define('WOOSBYMDZ_IS_WPHOOLU', 0); }
if (!defined('WOOSBYMDZ_VER')) { define('WOOSBYMDZ_VER', '6.1.12'); }
if (!defined('WOOSBYMDZ_ELEMPRO_TESTED_VER')) { define('WOOSBYMDZ_ELEMPRO_TESTED_VER', '3.32.1'); }
if (!defined('WOOSBYMDZ_SW_VER')) { define('WOOSBYMDZ_SW_VER', '7.4.1'); }
if (!defined('WOOSBYMDZ_PATH')) { define('WOOSBYMDZ_PATH', trailingslashit(plugin_dir_path(__FILE__))); }
if (!defined('WOOSBYMDZ_URL')) { define('WOOSBYMDZ_URL', trailingslashit(plugin_dir_url(__FILE__))); }
if (!defined('WOOSBYMDZ_PLG')) { define('WOOSBYMDZ_PLG', __FILE__); }//def self plugin
/**/

/*add settings btn*/
if(!function_exists('woosbymdz_addsettingsbtn'))
{
    function woosbymdz_addsettingsbtn( array $links ) 
    {
        $url = get_admin_url() . 'admin.php?page=woosbymdz-settings';
        $settings_link = '<a href="' . $url . '">' . esc_html__('Settings', 'wooslider-bymdez'). '</a>';
        $links[] = $settings_link;
        return $links;
    }
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'woosbymdz_addsettingsbtn' );
/**/

/*Translations*/
if (!function_exists('woosbymdz_i18n'))
{
    function woosbymdz_i18n() 
    {
        load_plugin_textdomain( 'wooslider-bymdez', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
}
add_action('init', 'woosbymdz_i18n');
/**/

/*run the plugin core*/
include_once 'includes'.DIRECTORY_SEPARATOR.'plugin-runner.php';
/***/

/* Omit closing PHP tag to avoid "Headers already sent" issues. */