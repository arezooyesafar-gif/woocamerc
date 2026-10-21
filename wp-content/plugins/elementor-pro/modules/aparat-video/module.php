<?php
namespace ElementorPro\Modules\AparatVideo;

use ElementorPro\Base\Module_Base;
use Elementor\Core\Experiments\Manager;
use ElementorPro\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends Module_Base {

	public function get_name() {
		return 'aparat';
	}

	public function get_widgets() {
		return 	[
            'AparatVideo'
        ];
	}
}
