<?php

namespace ElementorPro\Modules\AparatVideo\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use ElementorPro\Base\Base_Widget;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor video widget.
 *
 * Elementor widget that displays a video player.
 *
 * @since 1.0.0
 */
class AparatVideo extends Base_Widget
{

    /**
     * Get widget name.
     *
     * Retrieve aparat widget name.
     *
     * @return string Widget name.
     * @since 3.8.2
     * @access public
     *
     */
    public function get_name()
    {
        return 'aparat';
    }

    /**
     * Get widget title.
     *
     * Retrieve aparat widget title.
     *
     * @return string Widget title.
     * @since 3.8.2
     * @access public
     *
     */
    public function get_title()
    {
        return esc_html__('Aparat', 'elementor-pro');
    }

    /**
     * Get widget icon.
     *
     * Retrieve aparat widget icon.
     *
     * @return string Widget icon.
     * @since 3.8.2
     * @access public
     *
     */
    public function get_icon()
    {
        return 'eicon-video-camera';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the video widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @return array Widget categories.
     * @since 2.0.0
     * @access public
     *
     */
    public function get_categories()
    {
        return ['pro-elements'];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @return array Widget keywords.
     * @since 2.1.0
     * @access public
     *
     */
    public function get_keywords()
    {
        return ['video', 'player', 'embed', 'aparat'];
    }

    /**
     * Register video widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 3.1.0
     * @access protected
     */
    protected function register_controls()
    {
        $this->start_controls_section(
            'section_aparat',
            [
                'label' => esc_html__('Aparat', 'elementor-pro'),
            ]
        );

        $this->add_control(
            'aparat_id',
            [
                'label' => esc_html__('Aparat ID', 'elementor-pro'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__('Enter Aparat Video ID', 'elementor-pro'),
                'default' => 'UoeGa',
                'label_block' => true,
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'separator1',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );
        $this->add_control(
            'mute_video', [
                'label' => esc_html__('Mute Video', 'elementor-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'elementor-pro'),
                'label_off' => __('Off', 'elementor-pro'),
                'return_value' => 'true',
                'default' => 'block',
                'selectors' => [
                    '{{WRAPPER}} .selectors' => 'display: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'separator2',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'show_title', [
                'label' => esc_html__('Show Title', 'elementor-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'elementor-pro'),
                'label_off' => __('Off', 'elementor-pro'),
                'return_value' => 'true',
                'default' => 'block',
                'selectors' => [
                    '{{WRAPPER}} .selectors' => 'display: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'separator3',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );
        $this->add_control(
            'auto_play', [
                'label' => esc_html__('Auto Play', 'elementor-pro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'elementor-pro'),
                'label_off' => __('Off', 'elementor-pro'),
                'return_value' => 'true',
                'default' => 'block',
                'selectors' => [
                    '{{WRAPPER}} .selectors' => 'display: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'separator4',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );
        $this->add_control(
            'start_hour', [
                'label' => esc_html__('Start Hour', 'elementor-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
            ]
        );
        $this->add_control(
            'start_minute', [
                'label' => esc_html__('Start Minute', 'elementor-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
            ]
        );
        $this->add_control(
            'start_second', [
                'label' => esc_html__('Start Second', 'elementor-pro'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
            ]
        );
        $this->end_controls_section();
    }


    /**
     * Render video widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {

        /*
         * The main goal of this render section is to create embeded video as aparat says.
         * Aparat video embed link without any control
         * https://www.aparat.com/embed/JYs9I?data[rnddiv]=58817447201&data[responsive]=yes
         * Aparat video embed link with all controls
         *  https://www.aparat.com/embed/JYs9I?data[rnddiv]=58817447201&data[responsive]=yes&muted=true&titleShow=true&startTime=60&autoplay=true
        */

        $settings = $this->get_settings_for_display();


        /*Eeasy access for aprat video hash*/
        $video_id = $settings['aparat_id'];
        /*The container id is a unique integer to identify parent element*/
        $container_id = time()+mt_rand(1, 1000);
        /*Create video address based on aparat url design*/
        $http_type = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $http_type = 'https';
        }
        $video_address = "{$http_type}://www.aparat.com/embed/{$video_id}";

        $parameters = [
            'data[rnddiv]' => $container_id,
            'data[responsive]' => 'yes'
        ];
        if(!empty($settings['mute_video']) ){
            $parameters = array_merge($parameters, [
                'muted' => $settings['mute_video'],
                ]);
        }
        if(!empty($settings['show_title'])){
            $parameters = array_merge($parameters, [
                'titleShow' => $settings['show_title'],
            ]);
        }
        if(!empty($settings['auto_play'])){
            $parameters = array_merge($parameters, [
                'autoplay' => $settings['auto_play'],
            ]);
        }

        /*Calculate start time of video*/
        $start_time = 0;
        if(!empty($settings['start_hour'])){
            $start_time = (int) $settings['start_hour'] * 60 * 60;
        }
        if(!empty($settings['start_minute'])){
            $start_time += (int) $settings['start_minute'] * 60;
        }
        if(!empty($settings['start_second'])){
            $start_time += (int) $settings['start_second'];
        }

        if(!empty($start_time)){
            $parameters = array_merge($parameters, [
                'startTime' => $start_time,
            ]);
        }
        /*Append parameters as url query to aparat link*/
        $video_address = add_query_arg($parameters, $video_address);

        ?>
        <div id="<?php echo $container_id; ?>"
             class="aparat-video-container"
        >
            <script type="text/javascript"
                    src="<?php echo $video_address; ?>"
            ></script>
        </div>

        <?php
    }

}
