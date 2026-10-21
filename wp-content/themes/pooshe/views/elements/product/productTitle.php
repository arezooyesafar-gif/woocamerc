<?php

namespace PoosheElementor\Widgets;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class productTitle extends Widget_Base {

	public function get_name () : string {
		return 'poosheProductTitle';
	}

	public function get_title () : string {
		return 'عنوان محصول';
	}

	public function get_icon () : string {
		return 'eicon-product-title';
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
			'selector' => '{{WRAPPER}} .product-features-outer',
		]);
		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'outer_background',
			'label'    => 'رنگ زمینه',
			'types'    => ['classic', 'gradient', 'video'],
			'selector' => '{{WRAPPER}} .product-features-outer',
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			'name'     => 'outer_border',
			'label'    => 'حاشیه',
			'selector' => '{{WRAPPER}} .product-features-outer',
		]);


		$this->end_controls_section();
	}

	protected function render () {
		if ( is_singular('product') ) {
			echo '<div class="title-box">';
			echo '<div class="title-outer">';
			echo '<h1 class="product_title entry-title">' . get_the_title() . '</h1>';

			global $product;
			$tmp = get_post_meta($product->get_id(), 'pooshe_product_subtitle', true);
			if ( !empty($tmp) ) {
				echo '<h3 class="pooshe-subtitle">' . $tmp . '</h3>';
			}

			echo '</div>';
			echo '</div>';
		}
	}

}