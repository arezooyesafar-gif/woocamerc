<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_main_menu_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_main_menu';
    }
    public function get_title() {
        return 'منوی اصلی';
    }
    public function get_icon() {
        return 'eicon-nav-menu';
    }
    public function get_categories() {
        return [ 'pooshe_header' ];
    }
    protected function _register_controls() {

        $menus = wd_get_nav_menus_array();
        $this->start_controls_section(
            'menu_content_section',
            [
                'label' => 'محتوای منو',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
	    $this->add_control(
		    'ubermenu',
		    [
			    'label' => 'حالت اوبر منو',
			    'type' => \Elementor\Controls_Manager::SWITCHER,
			    'label_on' => 'فعال',
			    'label_off' => 'غیرفعال',
			    'return_value' => 'yes',
			    'default' => 'no',
		    ]
	    );

        $this->add_control(
            'menu',
            [
                'label' => 'انتخاب منو',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $menus,
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => 'رنگ ها',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'text',
            [
                'label' => 'رنگ متن',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#909398',
                'selectors' => [
                    '{{WRAPPER}} .main-navbar ul.categories-menu>li > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .main-navbar ul.categories-menu>li.menu-item-has-children > a::after' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'text_hover',
            [
                'label' => 'رنگ متن حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666',
                'selectors' => [
                    '{{WRAPPER}} .main-navbar ul.categories-menu > li:hover > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .main-navbar ul.categories-menu>li.menu-item-has-children:hover > a::after' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'icon',
            [
                'label' => 'رنگ آیکن',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D0D0D3',
                'selectors' => [
                    '{{WRAPPER}} .main-navbar ul.categories-menu > li > a > i' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'icon_hover',
            [
                'label' => 'رنگ آیکن حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D0D0D3',
                'selectors' => [
                    '{{WRAPPER}} .main-navbar ul.categories-menu > li:hover > a > i' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'menu_design_section',
            [
                'label' => 'طرح ها',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'menu_design',
            [
                'label' => 'انتخاب طرح',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => 'پیشفرض',
                    'solid'  => 'ساده',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'menu_font',
            [
                'label' => 'فونت',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .main-navbar ul.categories-menu li > a',
            ]
        );
        $this->end_controls_section();

    }
    protected function render() {
        $settings = $this->get_settings_for_display();

        $menu_args = array(
            'menu_class'     => 'categories-menu',
            'container'      => false
        );
        ?>
        <div class="pooshe-elementor-widget">
        <?php
            if(!cb_is_empty($settings['menu'])){
                $menu_args['menu'] = (int) $settings['menu'];
            ?>
            <nav class="main-navbar<?= $settings['ubermenu'] === "yes" ? '-uber' : '' ?> <?php echo $settings['menu_design'] == "solid"?"design_basic":""; ?>">
                <?php wp_nav_menu($menu_args); ?>
            </nav>
            <?php } ?>
        </div>
<?php
    }

}
