<?php

namespace PoosheElementor;

use Elementor\Plugin;

class Elements {

	private static $_instance = null;

	public static function instance () {
		if ( is_null(self::$_instance) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function include_widgets_files () {
		require_once(get_template_directory() . '/views/elements/element-dark-mode-toggle.php');
		require_once(get_template_directory() . '/views/elements/element-header_logo.php');
		require_once(get_template_directory() . '/views/elements/element-responsive_menu.php');
		require_once(get_template_directory() . '/views/elements/element-main_menu.php');
		require_once(get_template_directory() . '/views/elements/element-bar_menu.php');
		require_once(get_template_directory() . '/views/elements/element-header_info.php');
		require_once(get_template_directory() . '/views/elements/element-header_search.php');
		require_once(get_template_directory() . '/views/elements/element-header_basket.php');
		require_once(get_template_directory() . '/views/elements/element-header_account.php');
		require_once(get_template_directory() . '/views/elements/element-footer_copyright.php');
		require_once(get_template_directory() . '/views/elements/element-footer_socials.php');
		require_once(get_template_directory() . '/views/elements/element-footer_totop.php');
		require_once(get_template_directory() . '/views/elements/element-shop-carousel.php');
		require_once(get_template_directory() . '/views/elements/element-shop-category.php');
		require_once(get_template_directory() . '/views/elements/element-shop-small-grid-product.php');
		require_once(get_template_directory() . '/views/elements/element-shop-small-list-product.php');
		require_once(get_template_directory() . '/views/elements/element-categories.php');
		require_once(get_template_directory() . '/views/elements/product/productAddToCart.php');
		require_once(get_template_directory() . '/views/elements/product/productAdditionalInfo.php');
		require_once(get_template_directory() . '/views/elements/product/productThumbnails.php');
		require_once(get_template_directory() . '/views/elements/product/productShare.php');
		require_once(get_template_directory() . '/views/elements/product/productLogo.php');
		require_once(get_template_directory() . '/views/elements/product/productFeatures.php');
		require_once(get_template_directory() . '/views/elements/product/productTitle.php');
		require_once(get_template_directory() . '/views/elements/product/productActions.php');
		require_once(get_template_directory() . '/views/elements/product/productBoughtAlert.php');
		require_once(get_template_directory() . '/views/elements/product/productFreeFiles.php');
		require_once(get_template_directory() . '/views/elements/product/productSaleTimer.php');
	}

	public function add_elementor_widget_categories ($elements_manager) {

		$elements_manager->add_category('pooshe_header', [
				'title' => 'ابزارک های سربرگ <b style="color: #ff9e22;">قالب پوشه</b>',
				'icon'  => 'eicon-archive-title',
			]);
		$elements_manager->add_category('pooshe_main', [
				'title' => 'ابزارک های محتوای <b style="color: #ff9e22;">قالب پوشه</b>',
				'icon'  => 'eicon-post-info',
			]);
		$elements_manager->add_category('pooshe_product', [
				'title' => 'ابزارک های صفحه محصول <b style="color: #ff9e22;">قالب پوشه</b>',
				'icon'  => 'eicon-post-info',
			]);
		$elements_manager->add_category('pooshe_footer', [
				'title' => 'ابزارک های پابرگ <b style="color: #ff9e22;">قالب پوشه</b>',
				'icon'  => 'eicon-form-horizontal',
			]);
		$elements_manager->add_category('pooshe_learn', [
				'title' => 'ابزارک های دوره <b style="color: #ff9e22;">قالب پوشه</b>',
				'icon'  => 'eicon-form-horizontal',
			]);

	}

	public function register_widgets () {
		$this->include_widgets_files();
		Plugin::instance()->widgets_manager->unregister_widget_type('woocommerce-breadcrumb');
		Plugin::instance()->widgets_manager->unregister_widget_type('woocommerce-menu-cart');
		Plugin::instance()->widgets_manager->unregister_widget_type('wc-elements');
		Plugin::instance()->widgets_manager->unregister_widget_type('woocommerce-products');
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_dark_mode_toggle_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_header_logo_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_main_menu_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_responsive_menu_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_bar_menu_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_header_info_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_header_search_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_header_basket_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_header_account_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_footer_copyright());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_footer_socials());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_footer_totop());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_shop_carousel_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_shop_category_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_shop_small_grid_product_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_shop_small_list_product_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Elementor_Categories_Widget());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productAddToCart());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productAdditionalInfo());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productThumbnails());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productShare());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productLogo());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productFeatures());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productTitle());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productActions());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productBoughtAlert());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productFreeFiles());
		Plugin::instance()->widgets_manager->register_widget_type(new Widgets\productSaleTimer());
	}

	public function pooshe_register_elementor_locations ($elementor_theme_manager) {

		$elementor_theme_manager->register_location('responsive-header', [
			'label' => 'سربرگ حالت موبایل',
		]);
		$elementor_theme_manager->register_all_core_location();

	}

	public function __construct () {
		// Register widgets
		add_action('elementor/theme/register_locations', [$this, 'pooshe_register_elementor_locations']);
		add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
		add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets'], 11);
	}
}

// Instantiate Plugin Class
Elements::instance();

