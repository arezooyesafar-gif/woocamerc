<?php
namespace ElementorPro\Core\Editor;

use Elementor\Core\Base\App;
use Elementor\Core\Utils\Assets_Config_Provider;
use Elementor\Core\Utils\Assets_Translation_Loader;
use ElementorPro\License\Admin as License_Admin;
use ElementorPro\License\API as License_API;
use ElementorPro\Plugin;
use ElementorPro\Modules\DisplayConditions\Module as Display_Conditions_Module;
use Elementor\Modules\AtomicWidgets\Module as AtomicWidgetsModule;

use ElementorPro\Includes\ElementorCustomOptionHelper; // RTL : USE HELPER TO AVOID WRITING WHOLE NAMESPACE
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Editor extends App {
	const APP_BAR_DEPS_V2 = [
		'editor-site-navigation-extended',
		'editor-documents-extended',
	];
	const EDITOR_V4_PACKAGES = [
		'license-api',
		'editor-controls-extended',
		'editor-editing-panel-extended',
		'editor-components-extended',
	];

	/**
	 * Get app name.
	 *
	 * Retrieve the app name.
	 *
	 * @return string app name.
	 * @since  2.6.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'pro-editor';
	}

	public function __construct() {
		add_action( 'elementor/init', [ $this, 'on_elementor_init' ] );
		add_action( 'elementor/editor/init', [ $this, 'on_elementor_editor_init' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_filter( 'elementor/editor/localize_settings', [ $this, 'localize_settings' ] );

		add_action( 'elementor/editor/v2/scripts/enqueue', function () {
			$this->enqueue_editor_v2_scripts();
		} );
		

		
	//// RTL ////	
        add_action('elementor/preview/enqueue_styles', [ $this,	'enqueue_preview_custom_rtl']);
	
        /*There's no frontend class in elementor pro, We are loading them from this class directly*/

        add_action('elementor/frontend/before_register_scripts', function () {
            if(
                is_rtl()
                && \ElementorPro\Includes\ElementorCustomOptionHelper::is_feature_enabled('jalali_date')
            ) {
                if (wp_script_is('flatpickr', 'registered')) {
                    wp_deregister_script('flatpickr');
                }
                if (wp_script_is('flatpickr', 'enqueued')) {
                    wp_dequeue_script('flatpickr');
                }

                /*Includes the jdate*/
                wp_enqueue_script(
                    'elementor-jdate',
                    $this->get_js_assets_url('jdate-rtl', null, 'default'),
                    [
                        'jquery',
                    ],
                    ELEMENTOR_PRO_VERSION,
                    true
                );
                /*Sets the window date to jdate*/
                wp_add_inline_script('elementor-jdate', "window.Date=window.JDate;", 'after');

                /*Load the custom flatpickr*/
                wp_enqueue_script(
                    'flatpickr',
                    $this->get_js_assets_url('flatpickr-rtl', null, 'default'),
                    [
                        'elementor-jdate'
                    ],
                    ELEMENTOR_PRO_VERSION,
                    true
                );

                wp_enqueue_script(
                    'flatpickr-customizations',
                    $this->get_js_assets_url('flatpickr-customizations-rtl', null, ''),
                    [
                        'flatpickr'
                    ],
                    ELEMENTOR_PRO_VERSION,
                    true
                );

            }

        });



    }


	public function get_init_settings() {
		$settings = [
			'version' => ELEMENTOR_PRO_VERSION,
			'isActive' => License_API::is_license_active(),
			'urls' => [
				'modules' => ELEMENTOR_PRO_MODULES_URL,
				'connect' => License_Admin::get_url(),
			],
		];

		/**
		 * Localized editor settings.
		 *
		 * Filters the localized settings used in the editor as JavaScript variables.
		 *
		 * By default Elementor Pro passes some editor settings to be consumed as JavaScript
		 * variables. This hook allows developers to add extra settings values to be consumed
		 * using JavaScript in the editor.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Localized editor settings.
		 */
		$settings = apply_filters( 'elementor_pro/editor/localize_settings', $settings );

		return $settings;
	}

	public function enqueue_editor_styles() {
		wp_enqueue_style(
			'elementor-pro',
			$this->get_css_assets_url( 'editor' ),
			[
				'elementor-editor',
			],
			ELEMENTOR_PRO_VERSION
		);
    /*Deregister gregorian flatpickr css and load jalali flatpickr css*/
        if(is_rtl()) {
            wp_enqueue_style(
                'elementor-custom-rtl',
                $this->get_css_assets_url('custom-rtl', null, 'default', false),
                [],
                ELEMENTOR_PRO_VERSION
            );
            /*Load flatpickr jalali_date is enabled*/
            if (ElementorCustomOptionHelper::is_feature_enabled('jalali_date')) {
                if (wp_style_is('flatpickr', 'registered')) {
                    wp_deregister_style('flatpickr');
	}
                if (wp_style_is('flatpickr', 'enqueue')) {
                    wp_dequeue_style('flatpickr');
                }
                wp_enqueue_style(
                    'flatpickr',
                    $this->get_css_assets_url('flatpickr', null, 'default', true),
                    [],
                    ELEMENTOR_PRO_VERSION
                );
            }

            add_filter('mce_css', function () {
                $file_path = $this->get_js_assets_url('custom-rtl', null, '');
                return $file_path;
            });
        }
        /*Load pro assets, even in ltr mode*/
        if (ElementorCustomOptionHelper::is_feature_enabled('rtl_icons')) {
            wp_register_style(
                'elementor-custom-rtl-icons',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/rtlicons/rtlicons.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
            wp_enqueue_style('elementor-custom-rtl-icons');
        }
        /*Load enabled fonts only*/
        if (ElementorCustomOptionHelper::is_feature_enabled('anjoman_font')) {
            wp_enqueue_style(
                'elementor-frontend-anjoman-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/anjoman/anjoman.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('byekan_font')) {
            wp_enqueue_style(
                'elementor-frontend-byekan-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/byekan/byekan.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('estedad_font')) {
            wp_enqueue_style(
                'elementor-frontend-estedad-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/estedad/estedad.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
            wp_enqueue_style(
                'elementor-frontend-estedad-fn-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/estedad-fn/estedad-fn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('iransans_font')) {
            wp_enqueue_style(
                'elementor-frontend-iransans-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/iransans/iransans.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('iransansx_font')) {
            wp_enqueue_style(
                'elementor-frontend-iransansx-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/iransansx/iransansx.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('iranyekan_font')) {
            wp_enqueue_style(
                'elementor-frontend-iranyekan-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/iranyekan/iranyekan.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
            wp_enqueue_style(
                'elementor-frontend-iranyekan-fn-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/iranyekan-fn/iranyekan-fn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('kara_font')) {
            wp_enqueue_style(
                'elementor-frontend-kara-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/kara/kara.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('mikhak_font')) {
            wp_enqueue_style(
                'elementor-frontend-mikhak-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/mikhak/mikhak.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('nahid_font')) {
            wp_enqueue_style(
                'elementor-frontend-nahid-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/nahid-fn/nahid-fn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('parastoo_font')) {
            wp_enqueue_style(
                'elementor-frontend-parastoo-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/parastoo/parastoo.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('pelak_font')) {
            wp_enqueue_style(
                'elementor-frontend-pelak-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/pelak/pelak.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('pinar_font')) {
            wp_enqueue_style(
                'elementor-frontend-pinar-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/pinar/pinar.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
            wp_enqueue_style(
                'elementor-frontend-pinar-fn-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/pinar/pinar-fn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('sahel_font')) {
            wp_enqueue_style(
                'elementor-frontend-sahel-fn-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/sahel-fn/sahel-fn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('samim_font')) {
            wp_enqueue_style(
                'elementor-frontend-samim-fn-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/samim-fn/samim-fn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('shabnam_font')) {
            wp_enqueue_style(
                'elementor-frontend-shabnam-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/shabnam/shabnam.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
            wp_enqueue_style(
                'elementor-frontend-shabnam-fn-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/shabnam-fn/shabnam-fn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('tanha_font')) {
            wp_enqueue_style(
                'elementor-frontend-tanha-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/tanha/tanha.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
            wp_enqueue_style(
                'elementor-frontend-tanha-fn-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/tanha-fn/tanha-fn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('vazir_font')) {
            wp_enqueue_style(
                'elementor-frontend-vazir-fn-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/vazir-fn/vazir-fn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
            wp_enqueue_style(
                'elementor-frontend-vazirmatn-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/vazirmatn/vazirmatn.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
        if (ElementorCustomOptionHelper::is_feature_enabled('yekan_font')) {
            wp_enqueue_style(
                'elementor-frontend-yekan-font',
                ELEMENTOR_PRO_ASSETS_URL . 'fonts/yekan/yekan.css',
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
    }
    public function enqueue_preview_custom_rtl(){
        if(is_rtl()){
            wp_enqueue_style(
                'preview-custom-rtl',
                $this->get_css_assets_url('preview-custom-rtl', null, '', false),
                [],
                ELEMENTOR_PRO_VERSION
            );
        }
    }	public function enqueue_editor_scripts() {
		wp_enqueue_script(
			'elementor-pro',
			$this->get_js_assets_url( 'editor' ),
			[
				'backbone-marionette',
				'elementor-common',
				'elementor-editor-modules',
				'elementor-editor-document',
			],
			ELEMENTOR_PRO_VERSION,
			true
		);

	  /*Deregister main flatpickr js file and enqueue the jdate and jalali flatpickr*/
        if(is_rtl()){
            if(\ElementorPro\Includes\ElementorCustomOptionHelper::is_feature_enabled('jalali_date')) {
                if (wp_script_is('flatpickr', 'registered')) {
                    wp_deregister_script('flatpickr');
                }
                if (wp_script_is('flatpickr', 'enqueued')) {
                    wp_dequeue_script('flatpickr');
                }
                wp_enqueue_script(
                    'elementor-jdate',
                    $this->get_js_assets_url('jdate-rtl', null, 'default'),
                    [
                        'jquery',
                    ],
                    ELEMENTOR_PRO_VERSION,
                    true
                );
                wp_add_inline_script('elementor-jdate', "window.Date=window.JDate;", 'after');
                wp_enqueue_script(
                    'flatpickr',
                    $this->get_js_assets_url('flatpickr-rtl', null, 'default'),
                    [
                        'elementor-jdate'
                    ],
                    ELEMENTOR_PRO_VERSION,
                    true
                );
                wp_enqueue_script(
                    'flatpickr-customizations',
                    $this->get_js_assets_url('flatpickr-customizations-rtl', null, ''),
                    [
                        'flatpickr'
                    ],
                    ELEMENTOR_PRO_VERSION,
                    true
                );
            }
        }
        // RTL Custom templates add to library kit
        if(\ElementorPro\Includes\ElementorCustomOptionHelper::is_feature_enabled('custom_templates')) {
            wp_enqueue_script(
                'editor-customizations',
                $this->get_js_assets_url('editor-customizations-rtl', null, ''),
                [
                    'jquery'
                ],
                ELEMENTOR_PRO_VERSION,
                true
            );
        }
	// RTL : FIX TRANSLATIONS FOR ELEMENTOR PRO
        wp_set_script_translations( 'elementor-pro', 'elementor-pro', ELEMENTOR_PRO_PATH . 'languages');
		$this->print_config( 'elementor-pro' );
	}

	public function enqueue_editor_v2_scripts() {
		$assets_config = ( new Assets_Config_Provider() )
			->set_path_resolver( function ( $name ) {
				return ELEMENTOR_PRO_ASSETS_PATH . "js/packages/{$name}/{$name}.asset.php";
			} );

		$packages_to_load = array_merge( self::APP_BAR_DEPS_V2 );

		if ( Plugin::elementor()->experiments->is_feature_active( AtomicWidgetsModule::EXPERIMENT_NAME ) ) {
			$packages_to_load = array_merge( $packages_to_load, self::EDITOR_V4_PACKAGES );
		}

		$packages = apply_filters( 'elementor-pro/editor/v2/packages', $packages_to_load );

		foreach ( $packages as $package ) {
			$assets_config->load( $package );
		}

		foreach ( $assets_config->all() as $package => $config ) {
			wp_enqueue_script(
				$config['handle'],
				$this->get_js_assets_url( "packages/{$package}/{$package}" ),
				$config['deps'],
				ELEMENTOR_PRO_VERSION,
				true
			);

			wp_set_script_translations( $config['handle'], 'elementor-pro' );
		}

		if ( class_exists( Assets_Translation_Loader::class ) ) {
			$packages_handles = $assets_config->pluck( 'handle' )->all();

			Assets_Translation_Loader::for_handles( $packages_handles );
		}
	}

	public function localize_settings( array $settings ) {
		$settings['elementPromotionURL'] = Plugin::instance()->license_admin->get_connect_url([
			'utm_source' => '%s', // Will be replaced in the frontend to the widget name
			'utm_medium' => 'wp-dash',
			'utm_campaign' => 'connect-and-activate-license',
			'utm_content' => 'editor-widget-promotion',
		]);

		$settings['dynamicPromotionURL'] = Plugin::instance()->license_admin->get_connect_url( [
			'utm_source' => '%s', // Will be replaced in the frontend to the control name
			'utm_medium' => 'wp-dash',
			'utm_campaign' => 'connect-and-activate-license',
			'utm_content' => 'editor-dynamic-promotion',
		] );

		if ( ! isset( $settings['promotionWidgets'] ) ) {
			$settings['promotionWidgets'] = License_API::get_promotion_widgets();
		}

		if ( Display_Conditions_Module::can_use_display_conditions() ) {
			$settings['displayConditions'] = Display_Conditions_Module::instance()
				->get_conditions_manager()
				->get_conditions_config();
		}

		return $settings;
	}

	public function on_elementor_init() {
		Plugin::elementor()->editor->notice_bar = new Notice_Bar();

		if ( isset( Plugin::elementor()->editor->promotion ) ) {
			Plugin::elementor()->editor->promotion = new Promotion();
		}
	}

	public function on_elementor_editor_init() {
		Plugin::elementor()->common->add_template( __DIR__ . '/template.php' );
	}

	protected function get_assets_base_url() {
		return ELEMENTOR_PRO_URL;
	}
}
