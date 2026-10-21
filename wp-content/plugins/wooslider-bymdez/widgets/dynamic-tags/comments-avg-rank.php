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

class Woosbymdz_ComentsAvg extends Tag {

	public function get_name() {
		return 'wooslider-comments-avgrank';
	}

	public function get_title() {
		return esc_html__( 'Comments Average Rate (WooSlider)', 'wooslider-bymdez' );
	}

	public function get_group() {
		return Module::COMMENTS_GROUP;
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
			Module::NUMBER_CATEGORY,
		];
	}

	protected function register_controls() {

		$this->add_control( //calc unverfs
            'calc_un_verf_comments',
            [
                'label' => esc_html__('Calculate Unverified Comments?', 'wooslider-bymdez'),
                'description' => esc_html__('Use this option if you want to also calculate the score of unapproved comments.', 'wooslider-bymdez'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'on', 'wooslider-bymdez' ),
				'label_off' => esc_html__( 'off', 'wooslider-bymdez' ),
				'return_value' => 'yes',
				'default' => 'no',
            ]
        );

		$this->add_control(
			'custom_comments_metakey',
			[
				'label' => esc_html__( 'Comments MetaKey', 'wooslider-bymdez' ),
				'placeholder' => esc_html__( 'Default: rating', 'wooslider-bymdez' ),
				'ai' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'custom_post_meta',
			[
				'label' => esc_html__( 'Custom Post Meta', 'wooslider-bymdez' ),
				'description' => esc_html__('Directly fetches the post avg-rating from entered meta key.', 'wooslider-bymdez'),
				'placeholder' => esc_html__( 'No default-value', 'wooslider-bymdez' ),
				'ai' => [
					'active' => true,
				],
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

		$wooslider_helper = Helper::get_instance();

		if(!empty($settings['custom_post_meta'])){

			$cust_meta = sanitize_text_field($settings['custom_post_meta']);
			$avg_rating = $wooslider_helper->calculate_average_post_rating_from_meta($post_id, $cust_meta);

		}else if($is_a_woo_pro == 1){
			
			$avg_rating = $wooslider_helper->calculate_average_post_rating_from_meta($post_id, '_wc_average_rating');

		}else {

			$comments_metakey = 'rating';
			$is_unverf_ok = 'no';

			if(!empty($settings['custom_comments_metakey'])){
				$comments_metakey = sanitize_text_field($settings['custom_comments_metakey']);
			}

			if(!empty($settings['calc_un_verf_comments']) && $settings['calc_un_verf_comments'] == 'yes'){
				$is_unverf_ok = 'yes';
			}

			$avg_rating = $wooslider_helper->calculate_average_post_rating_from_meta($post_id, '', $comments_metakey, $is_unverf_ok);
		}
		
		/*if ( 'comments_link' == $settings['link_to'] ) {
			$avg_rating = sprintf( '<a href="%s">%s</a>', get_comments_link(), $avg_rating );
		}*/

		echo $avg_rating;
	}
}
