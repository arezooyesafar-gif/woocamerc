<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Pooshe_Compat extends Tarh99_Perf_Module {

	public function id() {
		return 'pooshe_compat';
	}

	public function title() {
		return 'سازگاری قالب پوشه';
	}

	public function description() {
		return 'انتقال قابلیت‌های امتحان‌شده افزونه قبلی (TPS) به این افزونه: نمایش محصولات مرتبط قبل از اجرای Swiper (برای Delay JS راکت)، اندازه واکنش‌گرای تصاویر کارت محصول و حذف Font Awesome تکراری. وقتی TPS فعال است، هر بخش که TPS انجام می‌دهد اینجا اجرا نمی‌شود.';
	}

	public function fields() {
		return array(
			'related_fallback' => array(
				'type'    => 'checkbox',
				'label'   => 'نمایش محصولات مرتبط به‌صورت گرید تا قبل از راه‌اندازی Swiper',
				'default' => 1,
			),
			'card_sizes'       => array(
				'type'    => 'checkbox',
				'label'   => 'ویژگی sizes واکنش‌گرا برای تصاویر کارت محصول',
				'default' => 1,
			),
			'sizes_value'      => array(
				'type'    => 'text',
				'label'   => 'مقدار sizes',
				'default' => '(max-width: 480px) 48vw, (max-width: 768px) 33vw, (max-width: 1200px) 24vw, 300px',
			),
			'fa_dedupe'        => array(
				'type'    => 'checkbox',
				'label'   => 'حذف فایل‌های Font Awesome که دقیقاً تکراری enqueue شده‌اند',
				'default' => 1,
			),
		);
	}

	public function boot() {
		if ( $this->opt( 'related_fallback' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'related_css' ), 200 );
		}
		if ( $this->opt( 'card_sizes' ) ) {
			add_filter( 'wp_get_attachment_image_attributes', array( $this, 'card_sizes' ), 30, 3 );
		}
		if ( $this->opt( 'fa_dedupe' ) ) {
			add_action( 'wp_print_styles', array( $this, 'fa_dedupe' ), 999 );
		}
	}

	public function related_css() {
		if ( Tarh99_Perf_Env::tps( 'pooshe_related_fallback' ) || ! Tarh99_Perf_Env::front_ok() || ! Tarh99_Perf_Env::is_pooshe() ) {
			return;
		}
		if ( ! Tarh99_Perf_Env::wc() || ! is_product() || ! wp_style_is( 'pooshe-main', 'enqueued' ) ) {
			return;
		}
		$scope = '.related.products.pooshe-carousel .swiper-container:not(.swiper-container-initialized)';
		$css   = $scope . '{display:block!important;visibility:visible!important;overflow:hidden}'
			. $scope . '>.swiper-wrapper{display:grid!important;grid-template-columns:minmax(0,1fr);gap:16px;transform:none!important;height:auto!important}'
			. $scope . '>.swiper-wrapper>.swiper-slide{width:auto!important;min-width:0;max-width:100%;height:auto;margin:0!important;transform:none!important}'
			. '@media(min-width:768px){' . $scope . '>.swiper-wrapper{grid-template-columns:repeat(2,minmax(0,1fr))}}'
			. '@media(min-width:992px){' . $scope . '>.swiper-wrapper{grid-template-columns:repeat(4,minmax(0,1fr))}}';
		wp_add_inline_style( 'pooshe-main', $css );
	}

	public function card_sizes( $attr, $attachment, $size ) {
		if ( ! is_array( $attr ) || Tarh99_Perf_Env::tps( 'wc_image_sizes' ) || ! Tarh99_Perf_Env::front_ok() ) {
			return $attr;
		}
		$classes = isset( $attr['class'] ) ? (string) $attr['class'] : '';
		if ( false === strpos( $classes, 'attachment-woocommerce_thumbnail' ) && false === strpos( $classes, 'size-woocommerce_thumbnail' ) ) {
			return $attr;
		}
		if ( empty( $attr['srcset'] ) ) {
			return $attr;
		}
		$value = trim( (string) $this->opt( 'sizes_value' ) );
		if ( '' !== $value ) {
			$attr['sizes'] = $value;
		}
		return $attr;
	}

	public function fa_dedupe() {
		if ( Tarh99_Perf_Env::tps( 'fa_dedupe' ) || ! Tarh99_Perf_Env::front_ok() ) {
			return;
		}
		$styles = wp_styles();
		$seen   = array();
		foreach ( (array) $styles->queue as $handle ) {
			if ( ! isset( $styles->registered[ $handle ] ) ) {
				continue;
			}
			$src = (string) $styles->registered[ $handle ]->src;
			if ( '' === $src || ! preg_match( '/font[\-_]?awesome/i', $handle . ' ' . $src ) ) {
				continue;
			}
			$key = strtolower( preg_replace( '/\?.*$/', '', $src ) );
			if ( isset( $seen[ $key ] ) ) {
				Tarh99_Perf_Assets::safe_dequeue( 'css', $handle );
			} else {
				$seen[ $key ] = $handle;
			}
		}
	}
}
