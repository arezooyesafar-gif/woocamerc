<?php

namespace ElementorPro\Core\App\Modules\ImportExportCustomization;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ElementorPro\License\API;

class Utils {

	public static function is_high_tier(): bool {
		try {
			// دریافت اطلاعات لایسنس
			$license_data = API::get_license_data();

			if ( empty( $license_data ) || empty( $license_data['tier'] ) ) {
				return false;
			}

			$tier = strtolower( $license_data['tier'] );

			// بررسی پلن‌های Expert و Agency
			return in_array( $tier, [ 'expert', 'agency' ], true );

		} catch ( \Exception $exception ) {
			return false;
		}
	}
}


