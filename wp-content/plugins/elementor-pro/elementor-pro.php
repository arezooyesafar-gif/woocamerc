<?php
/**
 * Plugin Name: Elementor Pro
 * Description: Elevate your designs and unlock the full power of Elementor. Gain access to dozens of Pro widgets and kits, Theme Builder, Pop Ups, Forms and WooCommerce building capabilities.
 * Plugin URI: https://go.elementor.com/wp-dash-wp-plugins-author-uri/
 * Version: 4.2.3
 * Author: Elementor.com
 * Author URI: https://go.elementor.com/wp-dash-wp-plugins-author-uri/
 * Text Domain: elementor-pro
 * Requires Plugins: elementor
 * Elementor tested up to: 3.30.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ELEMENTOR_PRO_VERSION', '4.2.2' );

require_once plugin_dir_path(__FILE__) . 'includes/custom-options-maker.php';

/**
 * All versions should be `major.minor`, without patch, in order to compare them properly.
 * Therefore, we can't set a patch version as a requirement.
 * (e.g. Core 3.15.0-beta1 and Core 3.15.0-cloud2 should be fine when requiring 3.15, while
 * requiring 3.15.2 is not allowed)
 */
define( 'ELEMENTOR_PRO_REQUIRED_CORE_VERSION', '4.0' );
define( 'ELEMENTOR_PRO_RECOMMENDED_CORE_VERSION', '4.2' );

define( 'ELEMENTOR_PRO__FILE__', __FILE__ );
define( 'ELEMENTOR_PRO_PLUGIN_BASE', plugin_basename( ELEMENTOR_PRO__FILE__ ) );
define( 'ELEMENTOR_PRO_PATH', plugin_dir_path( ELEMENTOR_PRO__FILE__ ) );
define( 'ELEMENTOR_PRO_ASSETS_PATH', ELEMENTOR_PRO_PATH . 'assets/' );
define( 'ELEMENTOR_PRO_MODULES_PATH', ELEMENTOR_PRO_PATH . 'modules/' );
define( 'ELEMENTOR_PRO_URL', plugins_url( '/', ELEMENTOR_PRO__FILE__ ) );
define( 'ELEMENTOR_PRO_ASSETS_URL', ELEMENTOR_PRO_URL . 'assets/' );
define( 'ELEMENTOR_PRO_MODULES_URL', ELEMENTOR_PRO_URL . 'modules/' );

// Include Composer's autoloader
if ( file_exists( ELEMENTOR_PRO_PATH . 'vendor/autoload.php' ) ) {
	require_once ELEMENTOR_PRO_PATH . 'vendor/autoload.php';
	// We need this file because of the DI\create function that we are using.
	// Autoload classmap doesn't include this file.
	
}

/**
 * Load gettext translate for our text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementor_pro_load_plugin() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'elementor_pro_fail_load' );

		return;
	}

	$core_version = ELEMENTOR_VERSION;
	$core_version_required = ELEMENTOR_PRO_REQUIRED_CORE_VERSION;
	$core_version_recommended = ELEMENTOR_PRO_RECOMMENDED_CORE_VERSION;

	if ( ! elementor_pro_compare_major_version( $core_version, $core_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'elementor_pro_fail_load_out_of_date' );

		return;
	}

	if ( ! elementor_pro_compare_major_version( $core_version, $core_version_recommended, '>=' ) ) {
		add_action( 'admin_notices', 'elementor_pro_admin_notice_upgrade_recommendation' );
	}


    /*RTL Customizations*/
	load_plugin_textdomain( 'elementor-pro' , false, dirname( ELEMENTOR_PRO_PLUGIN_BASE ).DIRECTORY_SEPARATOR.'languages');
	require_once ELEMENTOR_PRO_PATH . '/includes/custom-panel-option.php';
	require_once ELEMENTOR_PRO_PATH . '/includes/custom-panel-option-helper.php';
	require_once ELEMENTOR_PRO_PATH . '/includes/custom-template-manager.php';
    require_once ELEMENTOR_PRO_PATH . '/includes/jdate.php';

	require ELEMENTOR_PRO_PATH . 'plugin.php';
	add_action( 'rest_api_init', function () {

    register_rest_route(
        'elementor-pro/v1',
        '/license/tier-features',
        [
            [
                'methods'  => WP_REST_Server::READABLE,
                'permission_callback' => function () {
                    return is_user_logged_in();
                },
                'callback' => function () {
                    $license_data = \ElementorPro\License\API::get_license_data();
                    $features = ! empty( $license_data['features'] )
                        ? $license_data['features']
                        : [];

                    return new WP_REST_Response(
                        [
                            'features' => $features,
                        ],
                        200
                    );
                },
            ],
        ]
    );

    register_rest_route(
        'elementor-pro/v1',
        '/license/get-license-status',
        [
            [
                'methods'  => WP_REST_Server::READABLE,
                'permission_callback' => function () {
                    return is_user_logged_in();
                },
                'callback' => function () {
                    return new WP_REST_Response(
                        [
                            'isExpired' => \ElementorPro\License\API::is_license_expired(),
                        ],
                        200
                    );
                },
            ],
        ]
    );

} );
}
function __elementor_pro_activation_hook() {
    $local_free_translation = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.dirname( ELEMENTOR_PRO_PLUGIN_BASE ).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.'elementor-fa_IR.mo';
    $wp_content_translation_file = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'elementor-fa_IR.mo';
    @copy($local_free_translation, $wp_content_translation_file);
}
register_activation_hook( __FILE__, '__elementor_pro_activation_hook' );

function elementor_pro_compare_major_version( $left, $right, $operator ) {
	$pattern = '/^(\d+\.\d+).*/';
	$replace = '$1.0';

	$left  = preg_replace( $pattern, $replace, $left );
	$right = preg_replace( $pattern, $replace, $right );

	return version_compare( $left, $right, $operator );
}

add_action( 'plugins_loaded', 'elementor_pro_load_plugin' );

function print_error( $message ) {
	if ( ! $message ) {
		return;
	}
	// PHPCS - $message should not be escaped
	echo '<div class="error">' . $message . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementor_pro_fail_load() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	$plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

		$message = '<h3>' . esc_html__( 'You\'re not using Elementor Pro yet!', 'elementor-pro' ) . '</h3>';
		$message .= '<p>' . esc_html__( 'Activate the Elementor plugin to start using all of Elementor Pro plugin’s features.', 'elementor-pro' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Activate Now', 'elementor-pro' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

		$message = '<h3>' . esc_html__( 'Elementor Pro plugin requires installing the Elementor plugin', 'elementor-pro' ) . '</h3>';
		$message .= '<p>' . esc_html__( 'Install and activate the Elementor plugin to access all the Pro features.', 'elementor-pro' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Install Now', 'elementor-pro' ) ) . '</p>';
	}
}

function elementor_pro_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );

	$message = sprintf(
		'<h3>%1$s</h3><p>%2$s <a href="%3$s" class="button-primary">%4$s</a></p>',
		esc_html__( 'Elementor Pro requires newer version of the Elementor plugin', 'elementor-pro' ),
		esc_html__( 'Update the Elementor plugin to reactivate the Elementor Pro plugin.', 'elementor-pro' ),
		$upgrade_link,
		esc_html__( 'Update Now', 'elementor-pro' )
	);

	print_error( $message );
}

function elementor_pro_admin_notice_upgrade_recommendation() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );

	$message = sprintf(
		'<h3>%1$s</h3><p>%2$s <a href="%3$s" class="button-primary">%4$s</a></p>',
		esc_html__( 'Don’t miss out on the new version of Elementor', 'elementor-pro' ),
		esc_html__( 'Update to the latest version of Elementor to enjoy new features, better performance and compatibility.', 'elementor-pro' ),
		$upgrade_link,
		esc_html__( 'Update Now', 'elementor-pro' )
	);

	print_error( $message );
}

if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}
add_action(
    'plugins_loaded',
    function() {
        global $l10n, $wp_textdomain_registry;


        $domain = 'elementor-pro';


        $locale = get_locale();


        $lang_dir = WP_PLUGIN_DIR . '/elementor-pro/languages';


        $wp_textdomain_registry->set($domain, $locale, $lang_dir);


        if (isset($l10n[$domain])) {
            unset($l10n[$domain]);
        }


        load_plugin_textdomain($domain, false, 'elementor-pro/languages');
    }
);
add_action('admin_head', 'force_fix_elementor_menu_final_ultimate');
add_filter( 'elementor/components/enable_component_based', '__return_true' );
add_filter( 'elementor_pro/utils/is_component_available', '__return_true' );

function force_fix_elementor_menu_final_ultimate() {
    echo '<style>
        /* ۱. حذف شروع سریع: فقط لینکی که دقیقا به صفحه elementor ختم میشود (بدون پارامتر اضافی) */
        .elementor-submenu-flyout li a[href$="page=elementor"] {
            display: none !important;
        }

        /* ۲. حذف ارسال‌های تکراری: حذف مواردی که در لیست اصلی (بیرونی) هستند */
        /* این کد باعث میشود فقط آن موردی که داخل لیست Editor (داخلی) هست باقی بماند */
        .wp-submenu-wrap > li > a[href*="page=e-form-submissions"] {
            display: none !important;
        }

        /* ۳. حذف لینک ارتقا دهید */
        a[href*="page=elementor-one-upgrade"] {
            display: none !important;
        }

        /* ۴. حذف نقطه جداکننده و پروکسی یادداشت برای تمیزی کامل */
        .elementor-flyout-divider,
        a[href*="page=elementor-pro-notes-proxy"] {
            display: none !important;
        }

        /* ۵. اطمینان از نمایش تنها یک "ارسال‌ها" داخل منوی Editor */
        .elementor-submenu-flyout li:has(a[href*="e-form-submissions"]) {
            display: block !important;
        }
    </style>';
}
add_filter( 'elementor_pro/modules/components/should_register', '__return_true' );
add_filter( 'elementor_pro/features/components/is_active', '__return_true' );
add_filter( 'elementor_pro/permissions/components', fn() => true );
add_action('admin_head', function () {
    echo '<style>
    #toplevel_page_elementor-home .wp-submenu a[href="admin.php?page=elementor-home"] {
        display:none !important;
    }
    </style>';
});
add_action('load-toplevel_page_elementor-home', function () {
    if (isset($_GET['page']) && $_GET['page'] === 'elementor-home') {
        wp_redirect(admin_url('admin.php?page=elementor-settings'));
        exit;
    }
});

