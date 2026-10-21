<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tarh99_Perf_Module_Wc_Admin extends Tarh99_Perf_Module {

	public function id() {
		return 'wc_admin';
	}

	public function group() {
		return 'admin';
	}

	public function title() {
		return 'سبک‌سازی پیشخوان و WooCommerce Admin';
	}

	public function description() {
		return 'غیرفعال‌سازی بخش‌های تبلیغاتی و ریموت ووکامرس (پیشنهادهای مارکت‌پلیس، اعلان‌های ریموت، پیشنهاد افزونه و درگاه، بنرها)، تله‌متری، ویجت‌های سنگین داشبورد و محدودکردن تعداد ردیف لیست محصولات/سفارش‌ها. سفارش‌ها، محصولات، کوپن‌ها و تنظیمات دست نمی‌خورند.';
	}

	public function fields() {
		return array(
			'marketplace'      => array(
				'type'    => 'checkbox',
				'label'   => 'خاموش کردن پیشنهادهای مارکت‌پلیس ووکامرس',
				'default' => 1,
			),
			'remote_features'  => array(
				'type'    => 'checkbox',
				'label'   => 'خاموش کردن اعلان‌های ریموت (Inbox)، پیشنهاد افزونه/درگاه، بنرها و نظرسنجی‌ها',
				'default' => 1,
			),
			'tracking'         => array(
				'type'    => 'checkbox',
				'label'   => 'خاموش کردن ارسال آمار ووکامرس و المنتور (Tracker)',
				'default' => 1,
			),
			'analytics'        => array(
				'type'    => 'checkbox',
				'label'   => 'خاموش کردن WooCommerce Analytics (فقط اگر از منوی Analytics استفاده نمی‌شود)',
				'default' => 0,
			),
			'marketing'        => array(
				'type'    => 'checkbox',
				'label'   => 'خاموش کردن منوی Marketing (کوپن‌ها در ووکامرس ۱۱ زیر این منو هستند؛ بعد از روشن کردن دسترسی به کوپن‌ها را چک کنید)',
				'default' => 0,
			),
			'task_list'        => array(
				'type'    => 'checkbox',
				'label'   => 'مخفی کردن لیست کارهای راه‌اندازی ووکامرس',
				'default' => 0,
			),
			'widgets'          => array(
				'type'    => 'multicheck',
				'label'   => 'ویجت‌های داشبورد که حذف شوند',
				'options' => array(
					'woocommerce_dashboard_status'         => 'وضعیت ووکامرس (کوئری فروش و موجودی)',
					'woocommerce_dashboard_recent_reviews' => 'آخرین نقدهای ووکامرس',
					'dashboard_primary'                    => 'رویدادها و اخبار وردپرس (درخواست بیرونی)',
					'e-dashboard-overview'                 => 'نمای کلی المنتور (فید بیرونی)',
					'wpseo-dashboard-overview'             => 'نمای کلی یواست',
					'wpseo-wincher-dashboard-overview'     => 'Wincher یواست',
					'dashboard_quick_press'                => 'پیش‌نویس سریع',
					'dashboard_site_health'                => 'سلامت سایت',
					'dashboard_activity'                   => 'فعالیت‌ها',
				),
				'default' => array( 'woocommerce_dashboard_status', 'woocommerce_dashboard_recent_reviews', 'dashboard_primary', 'e-dashboard-overview', 'wpseo-dashboard-overview', 'wpseo-wincher-dashboard-overview', 'dashboard_quick_press' ),
			),
			'per_page_cap'     => array(
				'type'    => 'number',
				'label'   => 'حداکثر ردیف در هر صفحه لیست محصولات/سفارش‌ها (۰ = بدون محدودیت)',
				'default' => 100,
				'min'     => 0,
				'max'     => 999,
			),
			'months_dropdown'  => array(
				'type'    => 'checkbox',
				'label'   => 'حذف فیلتر «همه تاریخ‌ها» از لیست محصولات (یک کوئری DISTINCT روی کل جدول پست‌ها)',
				'default' => 0,
			),
		);
	}

	public function boot() {
		if ( $this->opt( 'marketplace' ) ) {
			add_filter( 'woocommerce_allow_marketplace_suggestions', '__return_false', 99 );
		}
		add_filter( 'woocommerce_admin_features', array( $this, 'features' ), 99 );
		if ( $this->opt( 'tracking' ) ) {
			add_filter( 'woocommerce_apply_user_tracking', '__return_false', 99 );
			add_filter( 'pre_option_woocommerce_allow_tracking', array( $this, 'no' ) );
			add_filter( 'pre_option_elementor_allow_tracking', array( $this, 'no' ) );
		}
		if ( $this->opt( 'task_list' ) ) {
			add_filter( 'pre_option_woocommerce_task_list_hidden', array( $this, 'yes' ) );
		}
		add_action( 'wp_dashboard_setup', array( $this, 'dashboard' ), 999 );
		if ( (int) $this->opt( 'per_page_cap' ) > 0 ) {
			add_filter( 'edit_posts_per_page', array( $this, 'cap_posts' ), 99, 2 );
			add_filter( 'edit_product_per_page', array( $this, 'cap' ), 99 );
			add_filter( 'edit_shop_order_per_page', array( $this, 'cap' ), 99 );
		}
		if ( $this->opt( 'months_dropdown' ) ) {
			add_filter( 'disable_months_dropdown', array( $this, 'months' ), 99, 2 );
		}
	}

	public function no() {
		return 'no';
	}

	public function yes() {
		return 'yes';
	}

	public function features( $features ) {
		if ( ! is_array( $features ) ) {
			return $features;
		}
		$remove = array();
		if ( $this->opt( 'remote_features' ) ) {
			$remove = array_merge(
				$remove,
				array(
					'remote-inbox-notifications',
					'remote-free-extensions',
					'payment-gateway-suggestions',
					'shipping-label-banner',
					'wc-pay-promotion',
					'wc-pay-welcome-page',
					'woo-mobile-welcome',
					'mobile-app-banner',
					'customer-effort-score-tracks',
					'printful',
					'experimental-fashion-sample-products',
					'shipping-setting-tour',
				)
			);
		}
		if ( $this->opt( 'analytics' ) ) {
			$remove[] = 'analytics';
			$remove[] = 'analytics-scheduled-import';
		}
		if ( $this->opt( 'marketing' ) ) {
			$remove[] = 'marketing';
		}
		if ( empty( $remove ) ) {
			return $features;
		}
		return array_values( array_diff( $features, $remove ) );
	}

	public function dashboard() {
		$widgets = $this->opt_list( 'widgets' );
		if ( empty( $widgets ) ) {
			return;
		}
		foreach ( $widgets as $widget ) {
			foreach ( array( 'normal', 'side', 'column3', 'column4' ) as $context ) {
				remove_meta_box( $widget, 'dashboard', $context );
			}
		}
	}

	public function cap( $value ) {
		$cap = (int) $this->opt( 'per_page_cap' );
		return ( $cap > 0 && (int) $value > $cap ) ? $cap : $value;
	}

	public function cap_posts( $value, $post_type = '' ) {
		if ( ! in_array( $post_type, array( 'product', 'shop_order' ), true ) ) {
			return $value;
		}
		return $this->cap( $value );
	}

	public function months( $disable, $post_type = '' ) {
		return 'product' === $post_type ? true : $disable;
	}
}
