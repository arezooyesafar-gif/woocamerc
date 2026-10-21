<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_header_info_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_header_info';
    }
    public function get_title() {
        return 'اطلاعات';
    }
    public function get_icon() {
        return 'eicon-bullet-list';
    }
    public function get_categories() {
        return [ 'pooshe_header' ];
    }
    protected function _register_controls() {

        $this->start_controls_section(
            'info_content_section',
            [
                'label' => 'اطلاعات',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'style',
            [
                'label' => 'طرح',
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'grid' => [
                        'title' => 'گرید',
                        'icon' => 'eicon-gallery-grid',
                    ],
                    'list' => [
                        'title' => 'لیست',
                        'icon' => 'eicon-post-list',
                    ],
                ],
                'default' => 'grid',
                'toggle' => true,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'title', [
                'label' => 'عنوان',
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control(
            'icon',
            [
                'label' => 'آیکن',
                'type' => \Elementor\Controls_Manager::ICON,
                'default' => 'fa fa-facebook',
            ]
        );
        $repeater->add_control(
            'value', [
                'label' => 'مقدار',
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control(
            'url',
            [
                'label' => 'لینک',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => 'https://example.com/username'
            ]
        );
        $this->add_control(
            'informations',
            [
                'label' => 'آیتم ها',
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'prevent_empty' =>  false
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => 'استایل و رنگ',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'align',
            [
                'label' => 'چینش',
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => 'چپ',
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => 'وسط',
                        'icon' => 'eicon-text-align-center',
                    ],
                    'end' => [
                        'title' => 'راست',
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'start',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .header-information ul' => 'align-items: {{VALUE}}; justify-content: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'text',
            [
                'label' => 'رنگ متن',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#aaa',
                'selectors' => [
                    '{{WRAPPER}} .header-information ul li .value' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'text_hover',
            [
                'label' => 'رنگ متن حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#888',
                'selectors' => [
                    '{{WRAPPER}} .header-information ul li a:hover .value' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'icon',
            [
                'label' => 'رنگ آیکن',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#aaa',
                'selectors' => [
                    '{{WRAPPER}} .header-information ul li .icon i' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'icon_hover',
            [
                'label' => 'رنگ آیکن حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .header-information ul li a:hover .icon i' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'icon_bg',
            [
                'label' => 'رنگ زمینه آیکن',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#eee',
                'selectors' => [
                    '{{WRAPPER}} .header-information ul li .icon::after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $colors = pooshe_get_colors();
        extract($colors);
        $this->add_control(
            'icon_bg_hover',
            [
                'label' => 'رنگ زمینه آیکن حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => $color_main,
                'selectors' => [
                    '{{WRAPPER}} .header-information ul li a:hover .icon::after' => 'background-color: {{VALUE}}',
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
            <?php
            if(!cb_is_empty($settings['informations'])){
                $items = $settings['informations'];
                ?>
                <div class="header-information <?php echo $settings['style']; ?>">
                    <ul>
                        <?php foreach ($items as $item){?>
                            <li title="<?php echo $item['title']; ?>">
                                <?php echo !cb_is_empty($item['url'])?'<a href="'.$item['url'].'">':''; ?>
                                <?php echo !cb_is_empty($item['icon'])?'<span class="icon"><i class="'.pooshe_fontawesome_425($item['icon']).'"></i></span>':''; ?>
                                <?php echo !cb_is_empty($item['value'])?'<span class="value">'.$item['value'].'</span>':''; ?>
                                <?php echo !cb_is_empty($item['url'])?'</a>':''; ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
        </div>  
<?php
    }

}
