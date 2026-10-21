<?php

namespace PoosheElementor\Widgets;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class productBoughtAlert extends Widget_Base {

	public function get_name () : string {
		return 'poosheProductBoughtAlert';
	}

	public function get_title () : string {
		return 'پیام محصول خریداری شده';
	}

	public function get_icon () : string {
		return 'eicon-file-download';
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
			'selector' => '{{WRAPPER}} .product-bought',
		]);
		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'outer_background',
			'label'    => 'رنگ زمینه',
			'types'    => ['classic', 'gradient', 'video'],
			'selector' => '{{WRAPPER}} .product-bought',
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			'name'     => 'outer_border',
			'label'    => 'حاشیه',
			'selector' => '{{WRAPPER}} .product-bought',
		]);


		$this->end_controls_section();
	}

	protected function render () {
		if ( is_singular('product') ) {
			pooshe_product_bought_alert();
		}
	}

}