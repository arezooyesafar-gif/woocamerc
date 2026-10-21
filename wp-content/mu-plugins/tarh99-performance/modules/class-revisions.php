<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Revisions extends Tarh99_Perf_Module {

	public function id() {
		return 'revisions';
	}

	public function group() {
		return 'stability';
	}

	public function title() {
		return 'محدودیت تعداد revision';
	}

	public function description() {
		return 'از این به بعد برای هر نوشته/برگه/محصول حداکثر N نسخه قبلی نگه داشته می‌شود (وردپرس به‌صورت پیش‌فرض نامحدود نگه می‌دارد). اگر در wp-config عدد کمتری تعریف شده باشد همان رعایت می‌شود.';
	}

	public function fields() {
		return array(
			'keep' => array(
				'type'    => 'number',
				'label'   => 'تعداد revision مجاز',
				'default' => 5,
				'min'     => 0,
				'max'     => 100,
			),
		);
	}

	public function boot() {
		add_filter( 'wp_revisions_to_keep', array( $this, 'limit' ), 50, 2 );
	}

	public function limit( $num, $post = null ) {
		$keep = (int) $this->opt( 'keep' );
		$num  = (int) $num;
		if ( $num < 0 || $num > $keep ) {
			return $keep;
		}
		return $num;
	}
}
