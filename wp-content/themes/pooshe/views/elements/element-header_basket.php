<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_header_basket_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_header_basket';
    }
    public function get_title() {
        return 'سبد خرید';
    }
    public function get_icon() {
        return 'eicon-cart';
    }
    public function get_categories() {
        return [ 'pooshe_header' ];
    }
    protected function _register_controls() {
        $this->start_controls_section(
            'basket_content_section',
            [
                'label' => 'محتوای سبد خرید',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'text',
            [
                'label' => 'متن سبد',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'سبد خرید',
            ]
        );
        $this->add_control(
            'list',
            [
                'label' => 'نمایش محتوای سبد خرید',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );


        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_color',
            [
                'label' => 'رنگ ها',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'color',
            [
                'label' => 'رنگ کلی',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .header-cart a.cart-contents' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .header-cart .widget_shopping_cart_content .woocommerce-mini-cart-item .remove:hover' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .header-cart:hover a.cart-contents .detail .count' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .header-cart a.cart-contents i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .header-cart:not(:hover) a.cart-contents .detail .count' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .header-cart:hover a.cart-contents' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'btn_color',
            [
                'label' => 'رنگ دکمه سبد خرید',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .header-cart .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.checkout' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'btn_color_hover',
            [
                'label' => 'رنگ دکمه سبد خرید حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .header-cart .widget_shopping_cart_content .woocommerce-mini-cart__buttons a.checkout:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'cart_design_section',
            [
                'label' => 'طرح ها',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'cart_design',
            [
                'label' => 'انتخاب طرح',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => 'پیشفرض',
                    'solid'  => 'ساده و گرد',
                    'bordered'  => 'حالت حاشیه ای',
                ],
            ]
        );
        $this->end_controls_section();

    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        if(function_exists('WC')) {
            ?>
            <div class="pooshe-elementor-widget">
                <div class="header-cart <?php echo $settings['cart_design'] == "solid"?"design_basic":""; echo $settings['cart_design'] == "bordered"?"design_bordered":""; ?>">
                    <?php echo do_shortcode('[pooshe_mini_cart text="'.$settings['text'].'"]'); ?>
                    <?php if($settings['list'] == 'yes'){ ?>
                        <div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
    }


}
