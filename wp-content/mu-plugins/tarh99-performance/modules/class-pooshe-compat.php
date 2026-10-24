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
		return 'انتقال قابلیت‌های امتحان‌شده افزونه قبلی (TPS) به این افزونه: نمایش محصولات مرتبط قبل از اجرای Swiper (برای Delay JS راکت)، اندازه واکنش‌گرای تصاویر کارت محصول، اندازه تصویر ۴۲۰ پیکسلی کارت‌ها و حذف Font Awesome تکراری. وقتی TPS فعال است، هر بخش که TPS انجام می‌دهد اینجا اجرا نمی‌شود.';
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
				'default' => '(max-width: 480px) 48vw, (max-width: 768px) 33vw, 340px',
			),
			'card_420'         => array(
				'type'    => 'checkbox',
				'label'   => 'ادامه ساخت اندازه تصویر tps_card_420 برای آپلودهای جدید (همان اندازه‌ای که افزونه قبلی ثبت کرده بود)',
				'default' => 1,
			),
			'account_link'     => array(
				'type'    => 'checkbox',
				'label'   => 'اصلاح لینک آیکون حساب کاربری هدر: ابزارک قالب آدرس را با دو اسلش (/my-account//) می‌سازد و وردپرس اول یک ریدایرکت کامل (۴ تا ۵ ثانیه روی این هاست) به /my-account/ انجام می‌دهد؛ با این گزینه لینک مستقیم همان آدرس نهایی است',
				'default' => 1,
			),
			'ordering_select'  => array(
				'type'    => 'checkbox',
				'label'   => 'منوی کشویی مرتب‌سازی فروشگاه و دسته‌ها از اول مخفی باشد: قالب بعد از بارگذاری آن را مخفی می‌کند و دکمه‌های مرتب‌سازی را کنار عنوان می‌گذارد، و همین مخفی شدن کل لیست محصولات را ۲۶ پیکسل بالا می‌پراند (CLS دسکتاپ دسته‌ها ۰٫۲۵ تا ۰٫۳). ظاهر نهایی همان است.',
				'default' => 1,
			),
			'wooslider_grid'   => array(
				'type'    => 'checkbox',
				'label'   => 'شبکه محصولات Wooslider که اسلاید نمی‌شود (dont-swipe) در دسکتاپ و تبلت از اول نمایش داده شود: افزونه آن را ۱٫۷ ثانیه بعد از بارگذاری با محو شدن نشان می‌دهد و قسمت‌های زیرش پایین می‌پرند. چیدمان نهایی در این عرض‌ها فقط با CSS است و تغییر نمی‌کند؛ موبایل دست نمی‌خورد.',
				'default' => 1,
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
		if ( $this->opt( 'card_420' ) ) {
			add_action( 'after_setup_theme', array( $this, 'card_size' ), 31 );
		}
		if ( $this->opt( 'ordering_select' ) ) {
			add_action( 'wp_head', array( $this, 'ordering_style' ), 8 );
		}
		if ( $this->opt( 'wooslider_grid' ) ) {
			add_action( 'wp_head', array( $this, 'wooslider_style' ), 8 );
		}
		if ( $this->opt( 'account_link' ) ) {
			add_filter( 'woocommerce_get_endpoint_url', array( $this, 'account_link' ), 20, 4 );
		}
	}

	public function ordering_style() {
		if ( Tarh99_Perf_Env::bypass() || 'pooshe' !== get_template() ) {
			return;
		}
		echo '<style id="tarh99-ordering">.woocommerce-ordering select.orderby{display:none}</style>' . "\n";
	}

	public function wooslider_style() {
		if ( Tarh99_Perf_Env::bypass() || ! defined( 'WOOSBYMDZ_VER' ) ) {
			return;
		}
		echo '<style id="tarh99-wooslider">@media (min-width:768px){.woosbymdz-carousel-con .woosbymdz-swiper[dont-swipe="yes"].woosbymdz-hidden{display:block!important;width:100%!important;height:100%!important;opacity:1!important;pointer-events:auto!important}}</style>' . "\n";
	}

	public function account_link( $url, $endpoint = '', $value = '', $permalink = '' ) {
		if ( '' !== (string) $endpoint || '' !== (string) $value || ! is_string( $url ) || '' === (string) $permalink || false !== strpos( (string) $permalink, '?' ) ) {
			return $url;
		}
		return untrailingslashit( $url ) === untrailingslashit( (string) $permalink ) ? trailingslashit( (string) $permalink ) : $url;
	}

	public function card_size() {
		if ( ! has_image_size( 'tps_card_420' ) ) {
			add_image_size( 'tps_card_420', 420, 420, false );
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
