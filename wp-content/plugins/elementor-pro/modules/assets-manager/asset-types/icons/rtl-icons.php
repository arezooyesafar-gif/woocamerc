<?php
namespace ElementorPro\Modules\AssetsManager\AssetTypes\Icons;

use ElementorPro\Modules\AssetsManager\Classes\Assets_Base;
use Elementor\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class RTL_Icons extends  Assets_Base {

	public function get_name() {
		return esc_html__( 'RTL Icons', 'elementor-pro' );
	}
	public function get_type() {
		return 'rtlicons';
	}

	public function add_rtl_icons_font_pack( $settings ) {

        $json_url = ELEMENTOR_PRO_URL.'modules/assets-manager/assets/rtl-icons/config.json';
        $settings['rtlicons'] = [
            'name'          => __("RTL Icons", 'elementor-pro'),
            'label'         => esc_html__( 'RTL Icons', 'elementor-pro' ),
            'url'           => false,
            'enqueue'       => false,
            'prefix'        => 'rtlicons-',
            'displayPrefix' => '',
            'labelIcon'     => 'rtlicons-bank-bank-markazi',
            'ver'           => '1.0.0',
            'fetchJson'     => $json_url,
            'native'        => true,
        ];
        return $settings;
	}


	protected function actions() {
        add_filter( 'elementor/icons_manager/native', [ $this, 'add_rtl_icons_font_pack' ] , 20);
	}
}
