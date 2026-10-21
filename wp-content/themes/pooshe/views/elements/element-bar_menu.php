<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_bar_menu_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_bar_menu';
    }
    public function get_title() {
        return 'نوار منو';
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
        $this->add_control(
            'menu_align',
            [
                'label' => 'سمت منو',
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'to-right' => [
                        'title' => 'به راست',
                        'icon' => 'fa fa-align-left',
                    ],
                    'to-left' => [
                        'title' => 'به چپ',
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'to-right',
                'toggle' => true,
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
                <nav class="bar-menu <?php echo $settings['menu_align']; ?>">
                    <?php wp_nav_menu($menu_args); ?>
                </nav>
            <?php } ?>
        </div>
<?php
    }

}