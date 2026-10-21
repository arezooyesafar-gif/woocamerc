<?php
//namespace ElementorPro\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use ElementorPro\Modules\DynamicTags\Tags\Base\Tag;
use ElementorPro\Modules\DynamicTags\Module;

use Woosbymdz\Helpers\Main\main as Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Get Deps
require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'main.php';
/***/

class Woosbymdz_Adv_Prices extends Tag {

	public function get_name() {
		return 'wooslider-adv-prices';
	}

	public function get_title() {
		return esc_html__( 'Advanced Product Prices (WooSlider)', 'wooslider-bymdez' );
	}

	public function get_group() {
		return Module::WOOCOMMERCE_GROUP;
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
		];
	}

	protected function register_controls() {

		$this->add_control( 
            'hide_sale_price',
            [
                'label' => esc_html__('Do not show Sale-Price', 'wooslider-bymdez'),
                'description' => esc_html__('If the products are on sale, the original price of the product (the crossed-out price) will not be displayed.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'off', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

		$this->add_control(
			'seperator_1',
			[
				'label' => esc_html__( 'Price & Currency Separator', 'wooslider-bymdez' ),
				'placeholder' => esc_html__( 'Default: empty', 'wooslider-bymdez' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'seperator_2',
			[
				'label' => esc_html__( 'Sale Prices Separator', 'wooslider-bymdez' ),
				'placeholder' => esc_html__( 'Default: empty', 'wooslider-bymdez' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control( 
            'dont_format_prices',
            [
                'label' => esc_html__('Do not Format Prices', 'wooslider-bymdez'),
                'description' => esc_html__('Displays price numbers in raw, unformatted form.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'off', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );


		/*$this->add_control(
			'link_to',
			[
				'label' => esc_html__( 'Link', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'elementor-pro' ),
					'comments_link' => esc_html__( 'Comments Link', 'elementor-pro' ),
				],
			]
		);*/
	}

	public function render() {

		$settings = $this->get_settings();

		$post_id = get_the_ID();
		$product_obj = '';
		$is_a_woo_pro = 0;

		if(function_exists('wc_get_product')){
			$product_obj = wc_get_product($post_id);

			if(is_a($product_obj, 'WC_Product')){
				$is_a_woo_pro = 1;
			}
		}



        $out_html = '';
        $is_on_sale = 'undef';
        if($is_a_woo_pro == 1){

			$wooslider_helper = Helper::get_instance();
			
            $is_on_sale = 'no';
            $reg_price = $product_obj->get_regular_price();
			$reg_price_formatted = number_format($wooslider_helper->float_or_int($reg_price));

            $sale_price = $product_obj->get_sale_price();
			$sale_price_formatted = number_format($wooslider_helper->float_or_int($sale_price));
            if(!empty($sale_price)){
                $is_on_sale = 'yes';
            }

            if(!empty($settings['hide_sale_price']) && $settings['hide_sale_price'] == 'yes'){
                $is_on_sale = 'no';
            }

            $current_price = $product_obj->get_price();
			$current_price_formatted = number_format($wooslider_helper->float_or_int($current_price));

            $currency_sign = get_woocommerce_currency_symbol();

			if(!empty($settings['dont_format_prices']) && $settings['dont_format_prices'] == 'yes'){
				$current_price_formatted = $current_price;
				$sale_price_formatted = $sale_price;
				$reg_price_formatted = $reg_price;
			}

            $separator_1 = '';
            $separator_2 = '';
            if(!empty($settings['seperator_1'])){
                $separator_1 = sanitize_text_field($settings['seperator_1']);
            }

            if(!empty($settings['seperator_2'])){
                $separator_2 = sanitize_text_field($settings['seperator_2']);
            }

            if($is_on_sale == 'yes'){
                $out_html = '<div class="wooslider-dynatag-price-con twin-prices-wrapper">
                    <div class="wooslider-inner-price saler-price">
                        <span>'.esc_html($sale_price_formatted).'</span>
                        <small>'.$separator_1.'</small>
                        <span>'.esc_html($currency_sign).'</span>
                    </div>

                    <div>'.$separator_2.'</div>

                    <div class="wooslider-inner-price regular-price">
                        <span>'.esc_html($reg_price_formatted).'</span>
                        <small>'.$separator_1.'</small>
                        <span>'.esc_html($currency_sign).'</span>
                    </div>
                </div>';

            }else {

                $out_html = '<div class="wooslider-dynatag-price-con sing-price-wrapper">
                    <div class="wooslider-inner-price normal-price">
                        <span>'.esc_html($current_price_formatted).'</span>
                        <small>'.$separator_1.'</small>
                        <span>'.esc_html($currency_sign).'</span>
                    </div>
                </div>';

            }
        }


		echo $out_html;
	}
}
