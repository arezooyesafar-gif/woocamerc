<?php

namespace PoosheElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Query;
use PoosheElementor\Pooshe_Current_Query_Renderer;
use PoosheElementor\Pooshe_Products_Renderer;

class Elementor_Categories_Widget extends \Elementor\Widget_Base
{

    public function get_script_depends()
    {
        return ['script-pooshe_shop_small-product'];
    }

    public function get_name()
    {
        return 'pooshe_categories';
    }

    public function get_title()
    {
        return 'دسته بندی ها';
    }

    public function get_icon()
    {
        return 'eicon-bullet-list';
    }

    public function get_categories()
    {
        return ['pooshe_main'];
    }

    protected function _register_controls()
    {

        $this->start_controls_section(
            'carousel_section',
            [
                'label' => 'استایل',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'titles',
            [
                'label' => 'عنوان ابزارک',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'colors',
            [
                'label' => 'رنگ اصلی',
                'type' => \Elementor\Controls_Manager::COLOR,

                'default' => '#29275F',
                'selectors' => [
                    '{{WRAPPER}} .carusel-category .section-title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .carusel-category .card-category .title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'content_section',
            [
                'label' => 'محتوا',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control('termsToShow', [
            'label' => __('Query', 'pooshe'),
            'type' => Controls_Manager::SELECT,
            'default' => 'latest',
            'options' => [
                'latest' => __('Latest', 'pooshe'),
                'parents' => __('Parents', 'pooshe'),
                'top' => __('Top Categories', 'pooshe'),
                'query' => __('Current Query', 'pooshe'),
                'custom' => __('Custom', 'pooshe'),
            ],
        ]);

        $this->add_control('termsSelect', [
            'type' => 'query',
            'multiple' => true,
            'autocomplete' => [
                'object' => 'tax',
                'display' => 'detailed',
                'by_field' => 'term_taxonomy_id',
                'query' => [
                    'taxonomy' => 'product_cat'
                ],
            ],
            'condition' => [
                'termsToShow' => 'custom'
            ]
        ]);

        $this->add_control('termsCount', [
            'label' => __('Count', 'pooshe'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 100,
            'step' => 1,
            'default' => 8
        ]);


        $this->end_controls_section();


    }

    protected function get_shortcode_object($settings)
    {
        $type = 'products';
        return new Pooshe_Products_Renderer($settings, $type);
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $termArgs = [
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'number' => $settings['termsCount']
        ];

        if ($settings['termsToShow'] === 'latest') {
            $termArgs['orderby'] = 'id';
            $termArgs['order'] = 'DESC';
        }

        if ($settings['termsToShow'] === 'top') {
            $termArgs['orderby'] = 'count';
            $termArgs['order'] = 'DESC';
        }

        if ($settings['termsToShow'] === 'parents') {
            $termArgs['parent'] = 0;
            $termArgs['orderby'] = 'id';
            $termArgs['order'] = 'DESC';
        }

        if ($settings['termsToShow'] === 'custom' && !empty($settings['termsSelect'])) {
            $termArgs['include'] = $settings['termsSelect'];
        }

        if ($settings['termsToShow'] === 'query' && is_tax()) {
            $term = get_queried_object();
            $termArgs['parent'] = $term->term_id;
            $termArgs['orderby'] = 'id';
            $termArgs['order'] = 'DESC';
        }

        $settings['categories'] = get_terms($termArgs);
        if (empty($settings['categories'])) {
            return;
        }
        ?>
        <div class="carusel-category">
            <?php if ($settings['titles'] == "yes") { ?>
                <div class="header">
                    <h6 class="section-title">دسته بندی محصولات</h6>
                </div>
            <?php } ?>
            <div class="cards">
                <?php
                foreach ($settings['categories'] as $category) {
                    $thumbnailID = get_term_meta($category->term_id, 'thumbnail_id', true);
                    $meta_title = get_term_meta($category->term_id, 'pooshe_meta_title', true);
                    $image = wp_get_attachment_url($thumbnailID);
                    $link = get_term_link((int)$category->term_id, 'product_cat');
                    ?>
                    <a href="<?php echo $link ?>" class="card-category">
                        <div class="background-image">
                            <?php if (!empty($image)) { ?>
                                <?php echo "<img src='{$image}' alt='' width='48' height='48' />"; ?>
                            <?php } else { ?>
                                <span></span>
                            <?php } ?>
                        </div>
                        <h6 class="title">
                            <?php echo $category->name; ?>
                        </h6>
                    </a>
                <?php } ?>
            </div>
        </div>
        <?php


    }

}
