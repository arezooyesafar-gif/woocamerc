<?php
//namespace ElementorPro\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use ElementorPro\Modules\DynamicTags\Tags\Base\Tag;
use ElementorPro\Modules\DynamicTags\Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Get Deps
/***/

class Woosbymdz_Pros_SalePercent extends Tag {

	public function get_name() {
		return 'wooslider-sale-perc';
	}

	public function get_title() {
		return esc_html__( 'Products Sale Percent (WooSlider)', 'wooslider-bymdez' );
	}

	public function get_group() {
		return Module::WOOCOMMERCE_GROUP;
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
            Module::NUMBER_CATEGORY,
		];
	}

	protected function register_controls() {

		$this->add_control( 
            'hide_percent_sign',
            [
                'label' => esc_html__('Do not show Percent-Sign', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'off', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( 
            'hide_neg_sign',
            [
                'label' => esc_html__('Do not show Negative-Sign', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'off', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

        $this->add_control( 
            'switch_pos',
            [
                'label' => esc_html__('Switch Positions', 'wooslider-bymdez'),
                'description' => esc_html__('The layout type changes the negative sign and percentage to the discount number.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'off', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

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

            $is_on_sale = 'no';
            $reg_price = $product_obj->get_regular_price();
            $sale_price = $product_obj->get_sale_price();
            if(!empty($sale_price)){
                $is_on_sale = 'yes';
            }


            $current_price = $product_obj->get_price();
            $negative_sign = '-';
            $percent_sign = '%';
            $layout_mode = 1;

            if(!empty($settings['hide_neg_sign']) && $settings['hide_neg_sign'] == 'yes'){
                $negative_sign = '';
            }

            if(!empty($settings['hide_percent_sign']) && $settings['hide_percent_sign'] == 'yes'){
                $percent_sign = '';
            }

            if(!empty($settings['switch_pos']) && $settings['switch_pos'] == 'yes'){
                $layout_mode = 2;
            }

            if($is_on_sale == 'yes'){
                $sale_price = intval($sale_price);
                $reg_price = intval($reg_price);
                $sale_perc = (($sale_price*100)/$reg_price)-100;
                $sale_perc = abs($sale_perc);

                $sale_perc = intval($sale_perc);

                if($layout_mode == 1){
                    $out_html = '<div style="direction: ltr;">'.$negative_sign.$sale_perc.$percent_sign.'</div>';
                }else $out_html = '<div style="direction: ltr;">'.$percent_sign.$sale_perc.$negative_sign.'</div>';
                
            }
        }


		echo $out_html;
	}
}
