<?php

namespace PoosheElementor\Widgets;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class productAddToCart extends Widget_Base {

	public function get_name () : string {
		return 'poosheProductAddToCart';
	}

	public function get_title () : string {
		return 'افزودن به سبد خرید';
	}

	public function get_icon () : string {
		return 'eicon-product-add-to-cart';
	}

	public function get_categories () : array {
		return ['pooshe_product'];
	}

	protected function _register_controls () {

		$this->start_controls_section('pooshe_styles', [
			'label' => 'طراحی',
			'tab'   => Controls_Manager::TAB_STYLE,
		]);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'outer_boxshadow',
			'label'    => 'سایه جعبه',
			'selector' => '{{WRAPPER}} .add-to-cart-product',
		]);
		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'outer_background',
			'label'    => 'رنگ زمینه',
			'types'    => ['classic', 'gradient', 'video'],
			'selector' => '{{WRAPPER}} .add-to-cart-product',
		]);
		$this->add_control('text_colors', [
			'label'     => 'رنگ متون',
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .add-to-cart-product' => 'color: {{VALUE}}',
			],
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			'name'     => 'outer_border',
			'label'    => 'حاشیه',
			'selector' => '{{WRAPPER}} .add-to-cart-product',
		]);
		$this->end_controls_section();

	}

	protected function render () {
		if ( is_singular('product') ) {
			do_action('pooshe_add_to_cart_widget');
		}
	}

}