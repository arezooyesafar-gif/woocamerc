<?php

namespace PoosheElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_header_account_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'pooshe_header_account';
    }

    public function get_title()
    {
        return 'حساب کاربری';
    }

    public function get_icon()
    {
        return 'eicon-lock-user';
    }

    public function get_categories()
    {
        return ['pooshe_header'];
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'account_content_section',
            [
                'label' => 'زیر منو ها',
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
            'link',
            [
                'label' => 'لینک',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => 'https://example.com/username'
            ]
        );
        $this->add_control(
            'menus',
            [
                'label' => 'آیتم ها',
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'prevent_empty' => false
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'login_content_section',
            [
                'label' => 'ورود به حساب',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'popup_login',
            [
                'label' => 'ورود پاپ آپ',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'other_login_link',
            [
                'label' => 'لینک صفحه ورود دیگر',
                'type' => \Elementor\Controls_Manager::URL,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'label' => 'تصویر صفحه ورود',
                'types' => ['classic', 'gradient', 'video'],
                'selector' => '#header-popup-login .bg-col',
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'section_style',
            [
                'label' => 'طرح حساب کاربری',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );



        $this->add_control(
            'btn_color',
            [
                'label' => 'رنگ زمینه دکمه',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#aaa',
                'selectors' => [
                    '{{WRAPPER}} .header-account figure' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'btn_hover_color',
            [
                'label' => 'رنگ زمینه دکمه در حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#bbb',
                'selectors' => [
                    '{{WRAPPER}} .header-account figure:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'btn_txt_color',
            [
                'label' => 'رنگ آیکن دکمه',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .header-account figure i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'badge_color',
            [
                'label' => 'رنگ بدج',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FF5959',
                'selectors' => [
                    '{{WRAPPER}} .header-account figure .badge' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'logout_color',
            [
                'label' => 'رنگ متن دکمه خروج',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FF5959',
                'selectors' => [
                    '{{WRAPPER}} .header-account .sub-items li.logout a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'logout_bg_color',
            [
                'label' => 'رنگ زمینه حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(255, 89, 89, 0.1)',
                'selectors' => [
                    '{{WRAPPER}} .header-account .sub-items li.logout a:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'logout_bg_txt_color',
            [
                'label' => 'رنگ متن حالت هاور',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FF5959',
                'selectors' => [
                    '{{WRAPPER}} .header-account .sub-items li.logout a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'account_design_section',
            [
                'label' => 'طرح ها',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'account_design',
            [
                'label' => 'انتخاب طرح',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => 'پیشفرض',
                    'solid' => 'طرح دوم',
                    'bordered' => 'طرح حاشیه ای',
                ],
            ]
        );
        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (function_exists('WC')) {
            $my_account = wc_get_account_endpoint_url('');
            $reset = wc_get_account_endpoint_url('lost-password');
            $user = false;
            $menu_items = [];
            if (is_user_logged_in()) {
                $user = wp_get_current_user();
                $profile = [
                    'image' => get_avatar_url($user->ID),
                    'title' => $user->display_name,
                    'link' => $my_account
                ];
                if (!empty(do_shortcode('[get-account-funds]'))) {
                    $funds = do_shortcode('[get-account-funds]') !== '[get-account-funds]' ? do_shortcode('[get-account-funds]') : '';
                    if (!empty($funds)) {
                        $profile['sub_title'] = __('کیف پول: ', 'pooshe') . do_shortcode('[get-account-funds]');
                    } else {
                        $profile['sub_title'] = __('عزیز، خوش آمدید!', 'pooshe');
                    }
                }
                $menu_items[] = $profile;

                if (!empty($settings['menus'])) {
                    $menu_items = array_merge($menu_items, $settings['menus']);
                }
                if (function_exists('rcp_process_registration')) {
                    $menu_items[] = [
                        'title' => __('خرید اشتراک', 'pooshe'),
                        'class' => 'vip-account',
                        'link' => get_bloginfo("url") . "/ثبت-نام",
                    ];
                }
                if (\pooshe_opt(STYLE_OPTS, 'dark_mode', true)) {
                    $menu_items[] = [
                        'type' => 'dark_mode_toggle',
                        'class' => 'dark-mode-item',
                    ];
                }
                $menu_items[] = [
                    'title' => __('خروج', 'pooshe'),
                    'class' => 'logout',
                    'link' => wc_logout_url()
                ];
            }
            if (is_user_logged_in()) {
                $user = wp_get_current_user();
            }
            ?>

            <div class="pooshe-elementor-widget">
                <div class="header-account <?php echo $settings['account_design'] == "solid" ? "design_basic" : "";
                echo $settings['account_design'] == "bordered" ? "design_bordered" : ""; ?>">
                    <a href="<?php if (!empty($settings['other_login_link']["url"])){echo $settings['other_login_link']["url"] ;}else{ echo esc_attr($my_account);}  ?>" <?php if (!is_user_logged_in() and $settings['popup_login'] == "yes") {
                        echo 'data-bs-toggle="modal" data-bs-target="#header-popup-login"';
                    } ?>>
                        <?php if ($settings['account_design'] != 'bordered') { ?>
                            <figure>
                                <?php if (!is_user_logged_in()) { ?>
                                    <i class="fal fa-user"></i>
                                    <span class="badge">
                            <?php _e('لطفا وارد شوید!', 'pooshe'); ?>
                            </span>
                                <?php } else { ?>
                                    <img src="<?php echo get_avatar_url($user->ID); ?>"
                                         alt="<?php echo esc_attr($user->display_name); ?>">
                                <?php } ?>
                            </figure>
                        <?php }else { ?>
                            <span class="user-avatar"> <i class="fal fa-user"></i></span>
                            <div class="user-info">
                                <span class="user-name">
                                    <?php
                                    if (is_user_logged_in()) {
                                        echo esc_attr($user->display_name);
                                    }else{
                                        _e('لطفا وارد شوید!', 'pooshe');
                                    }
                                    ?>
                                </span>
                                <?php  if (is_user_logged_in() && !empty($menu_items)) { ?>
                                <i class="fas fa-angle-down"></i>
                                <?php } ?>
                            </div>
                         <?php } ?>
                        <?php if ($settings['account_design'] == "solid") { ?>
                            <div class="user-info">
                                <span class="user-name"><?php echo esc_attr($user->display_name); ?></span>
                                <span class="user-num">
                                        <?php if (!empty($menu_items)) {
                                            foreach ($menu_items as $item) {
                                                echo !empty($item['sub_title']) ? '<span class="sub-title">' . $item['sub_title'] . '</span>' : '';
                                            }
                                        } else {
                                            echo esc_attr($user->user_email);
                                        } ?>
                                </span>
                            </div>
                        <?php } ?>
                    </a>
                    <?php if (!empty($menu_items)) { ?>
                        <ul class="sub-items">
                            <?php foreach ($menu_items as $item) { ?>
                                <?php if (isset($item['type']) && $item['type'] === 'dark_mode_toggle') : ?>
                                <li class="dark-mode-item">
                                    <button class="pooshe-dark-mode-toggle header-account-dark-toggle" type="button" aria-label="حالت تاریک/روشن">
                                        <span class="toggle-track">
                                            <span class="toggle-sun"><i class="fas fa-sun"></i></span>
                                            <span class="toggle-thumb"></span>
                                            <span class="toggle-moon"><i class="fas fa-moon"></i></span>
                                        </span>
                                        <span class="title"><?php esc_html_e('تاریک/روشن', 'pooshe'); ?></span>
                                    </button>
                                </li>
                                <?php else : ?>
                                <li class="<?php echo isset($item['class']) ? $item['class'] : ''; ?>">
                                    <a href="<?php echo $item['link']; ?>">
                                        <?php echo !empty($item['image']) ? '<img src="' . $item['image'] . '" alt="' . $item['title'] . '">' : ''; ?>
                                        <?php echo !empty($item['title']) ? '<span class="title">' . $item['title'] . '</span>' : ''; ?>
                                        <?php echo !empty($item['sub_title']) ? '<span class="sub-title">' . $item['sub_title'] . '</span>' : ''; ?>
                                    </a>
                                </li>
                                <?php endif; ?>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
                <?php if (!is_user_logged_in() and $settings['popup_login'] == "yes") { ?>
                <?php } ?>

            </div>
            <?php

        }
    }


}
