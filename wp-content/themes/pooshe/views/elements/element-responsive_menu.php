<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_responsive_menu_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_responsive_menu';
    }
    public function get_title() {
        return 'منوی همبرگری';
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
            'menu',
            [
                'label' => 'انتخاب منو',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $menus,
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'menu_style_section',
            [
                'label' => 'محتوای منو',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'to-right',
            [
                'label' => 'چپ به راست',
                'type' => \Elementor\Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'btn-color',
            [
                'label' => 'رنگ دکمه',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#555',
                'selectors' => [
                    '{{WRAPPER}} .responsive-menu .toggle-menu' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'icon',
            [
                'label' => __( 'Icon', 'text-domain' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'far fa-bars',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'btn-size',
            [
                'label' => 'سایز دکمه',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 16,
                'selectors' => [
                    '{{WRAPPER}} .responsive-menu .toggle-menu' => 'font-size: {{VALUE}}px',
                ],
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
            <div class="responsive-menu">
                <button class="btn btn-transparent toggle-menu"><?php echo '<i class="'.pooshe_fontawesome_425($settings['icon']['value']).'"></i>'; ?></button>
                <div class="menu-popup<?php echo wc_string_to_bool($settings['to-right'])? ' to-right':''; ?>">
                <?php
                if(!cb_is_empty($settings['menu'])){
                    $menu_args['menu'] = (int) $settings['menu'];
                ?>
                <nav class="responsive-navbar">
                    <?php wp_nav_menu($menu_args); ?>
                </nav>
                <?php } ?>
                </div>
                <div class="overlay"></div>
            </div>
        </div>
<?php
    }

}
