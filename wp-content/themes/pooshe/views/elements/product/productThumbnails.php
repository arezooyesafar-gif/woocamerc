<?php

namespace PoosheElementor\Widgets;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class productThumbnails extends Widget_Base {

	public function get_name () : string {
		return 'poosheProductThumbnails';
	}

	public function get_title () : string {
		return 'تصویر شاخص';
	}

	public function get_icon () : string {
		return 'eicon-slider-full-screen';
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
			'selector' => '{{WRAPPER}} .pooshe-pai',
		]);
		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'outer_background',
			'label'    => 'رنگ زمینه',
			'types'    => ['classic', 'gradient', 'video'],
			'selector' => '{{WRAPPER}} .pooshe-pai',
		]);
		$this->add_control('text_colors', [
			'label'     => 'رنگ متون',
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .pooshe-pai .woocommerce-product-attributes .woocommerce-product-attributes-item__label' => 'color: {{VALUE}}',
				'{{WRAPPER}} .pooshe-pai .woocommerce-product-attributes .woocommerce-product-attributes-item__value' => 'color: {{VALUE}}',
			],
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			'name'     => 'outer_border',
			'label'    => 'حاشیه',
			'selector' => '{{WRAPPER}} .pooshe-pai',
		]);


		$this->end_controls_section();
	}

	protected function render () {
		if ( is_singular('product') ) {
			wc_get_template('single-product/product-image.php');
		}
	}

}