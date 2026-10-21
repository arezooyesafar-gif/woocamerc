<?php

namespace PoosheElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_shop_category_Widget extends \Elementor\Widget_Base {
	public function get_name () {
		return 'pooshe_shop_category';
	}

	public function get_title () {
		return 'کارت های سفارشی';
	}

	public function get_icon () {
		return 'eicon-bullet-list';
	}

	public function get_categories () {
		return ['pooshe_main'];
	}

	protected function _register_controls () {

		$this->start_controls_section('info_content_section', [
			'label' => 'اطلاعات',
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control('style', [
			'label'   => 'طرح',
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'small',
			'options' => [
				'small' => 'کوچک',
				'big'   => 'بزرگ',
			],
		]);

		$repeater->add_control('colorType', [
			'label'   => 'رنگ کارت',
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'default',
			'options' => [
				'default' => 'پیشفرض',
				'custom'   => 'سفارشی',
			],
		]);
		$repeater->add_group_control(
			'background',
			[
				'name' => 'background',
				'label' => 'رنگ زمینه کارت',
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .cat-card::before',
				'condition' => [
					'colorType' => 'custom',
				],
			]
		);

		$repeater->add_control('title', [
			'label' => 'عنوان',
			'type'  => \Elementor\Controls_Manager::TEXT,
		]);

		$repeater->add_control('subTitle', [
			'label' => 'عنوان فرعی',
			'type'  => \Elementor\Controls_Manager::TEXT,
		]);

		$repeater->add_control('icon', [
			'label'   => 'آیکن',
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => [
				'url' => \Elementor\Utils::get_placeholder_image_src(),
			]
		]);

		$repeater->add_control('desc', [
			'label' => 'محتوا',
			'type'  => \Elementor\Controls_Manager::WYSIWYG,
		]);

		$repeater->add_control('linkTitle', [
			'label' => 'متن لینک',
			'type'  => \Elementor\Controls_Manager::TEXT,
		]);

		$repeater->add_control('link', [
			'label'         => 'لینک',
			'type' => \Elementor\Controls_Manager::TEXT,
			'input_type' => 'url',
			'placeholder' => 'https://example.com/username'

		]);

		$this->add_control('cards', [
			'label'         => 'کارت ها',
			'type'          => \Elementor\Controls_Manager::REPEATER,
			'fields'        => $repeater->get_controls(),
			'title_field'   => '{{{ title }}}',
			'prevent_empty' => false
		]);

		$this->end_controls_section();


	}

	protected function render () {
		$settings = $this->get_settings_for_display();
		if ( empty($settings['cards']) ) {
			return;
		}
		?>
        <div class="pooshe-elementor-widget">
            <div class="pooshe-elementor-cat-items">
                <div class="cat_item cat_item--grid">
                    <?php foreach ($settings['cards'] as $item): ?>
                    <div class="elementor-repeater-item-<?= $item['_id'] ?> cat_item__item <?= $item['style'] === 'big' ? 'cat_item__item--highlight' : '' ?>">
                        <div class="cat-card">
                            <div class="cat-card__block cat-card__block--main">
                                <h3 class="cat-card__title"><?= $item['title'] ?></h3>
                                <p class="cat-card__subtitle"><?= $item['subTitle'] ?></p>
                                <p class="cat-card__text"><?= $item['desc'] ?></p>
                                <div class="cat-card__element cat-card__element--user-img">
                                    <?php if (isset($item['icon']['url'])){ ?>
                                        <img src="<?= $item['icon']['url'] ?>" alt="<?= $item['title'] ?>">
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if(!empty($item['linkTitle'])){ ?>
                            <a class="cat-button cat-button--primary trade" href="<?= $item['link'] ?>"><?= $item['linkTitle'] ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
		<?php
	}

}
