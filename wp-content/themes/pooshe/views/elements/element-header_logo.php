<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_header_logo_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_logo';
    }
    public function get_title() {
        return 'لوگو پیشرفته';
    }
    public function get_icon() {
        return 'eicon-site-logo';
    }
    public function get_categories() {
        return [ 'pooshe_header' ];
    }
    protected function _register_controls() {

        $this->start_controls_section(
            'logo_content_section',
            [
                'label' => 'لوگو',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => 'تصویر لوگو',
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => get_theme_file_uri('/dist/img/logo.png'),
                ],
            ]
        );
        $this->add_control(
            'desc',
            [
                'label' => 'توضیحات لوگو',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => get_bloginfo('name'),
            ]
        );

        $this->add_control(
            'url',
            [
                'label' => 'لینک مقصد',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => site_url()
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'socials_content_section',
            [
                'label' => 'شبکه های اجتماعی',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
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
            'url',
            [
                'label' => 'لینک',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => 'https://example.com/username'
            ]
        );
        $this->add_control(
            'socials',
            [
                'label' => 'آیتم ها',
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'prevent_empty' =>  false
            ]
        );


        $this->end_controls_section();


    }
    protected function render() {

        $settings = $this->get_settings_for_display();

        ?>

        <div class="pooshe-elementor-widget">
            <div class="header-logo">
                <div class="inner">
                    <?php echo !cb_is_empty($settings['url'])?'<a href="'.$settings['url'].'">':''; ?>
                        <?php if(!cb_is_empty($settings['image']['url'])){
                            echo '<figure>'.cb_img($settings['image']['url'],$settings['desc']).'</figure>';
                        }?>
                    <?php echo !cb_is_empty($settings['url'])?'</a>':''; ?>

                    <?php echo !cb_is_empty($settings['desc'])?'<h2 class="desc">'.$settings['desc'].'</h2>':''; ?>
                </div>
                <?php if(!cb_is_empty($settings['socials'])){?>
                <div class="socials">
                    <ul>
                    <?php foreach ($settings['socials'] as $item){?>
                        <li>
                            <a href="<?php echo $item['url']; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $item['title']; ?>">
                                <i class="<?php echo pooshe_fontawesome_425($item['icon']); ?>"></i>
                            </a>
                        </li>
                    <?php } ?>
                    </ul>
                </div>
                <?php }?>
            </div>
        </div>
<?php

    }

}
