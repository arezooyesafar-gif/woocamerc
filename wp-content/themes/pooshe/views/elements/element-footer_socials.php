<?php
namespace PoosheElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_footer_socials extends \Elementor\Widget_Base {
    public function get_name() {
        return 'pooshe_footer_socials';
    }
    public function get_title() {
        return 'شبکه های اجتماعی';
    }
    public function get_icon() {
        return 'eicon-social-icons';
    }
    public function get_categories() {
        return [ 'pooshe_footer' ];
    }
    protected function _register_controls() {

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
            <?php if(!cb_is_empty($settings['socials'])){?>
                <div class="footer-socials">
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
<?php
    }

}
