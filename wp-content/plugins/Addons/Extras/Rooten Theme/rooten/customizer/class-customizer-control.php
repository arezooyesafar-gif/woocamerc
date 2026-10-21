<?php

function rooten_customize_register( $wp_customize ) {
    if( class_exists( 'WP_Customize_Control' ) ):

        // alert custom control
        class rooten_Customize_Alert_Control extends WP_Customize_Control {
            public $type = 'alert';
            public $text = '';
            public $alert_type = '';
            public function render_content() {
            ?>
            <label>
                <span class="rooten-alert <?php echo esc_html( $this->alert_type ); ?>"><?php echo esc_html( $this->text ); ?></span>
            </label>
            <?php
            }
        } 

        // Textarea custom control
        class rooten_Customize_Textarea_Control extends WP_Customize_Control {
            public $type = 'textarea';
     
            public function render_content() {
                ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                <textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            </label>
            <?php
            }
        }

        // Select custom control with default option
        class rooten_Customize_Select_Control extends WP_Customize_Control {
            public $type = 'select';

            public function render_content() {
                ?>
                    <label>
                      <span><?php echo esc_html( $this->label ); ?></span>
                      <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                      <select>
                        <option value="0" <?php if(!$this->value): ?>selected="selected"<?php endif; ?>><?php esc_attr_e('Default', 'rooten'); ?></option>
                      </select>
                    </label>
                <?php
            }
        }

        // Layout custom control for select sidebar
        class rooten_Customize_Layout_Control extends WP_Customize_Control {

            public $type = 'layout';
            public function render_content() { ?>

                <div class="rooten-layout-select">

                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>

                <ul>
                <?php 
                    $name = '_customize-radio-' . $this->id; 

                    foreach ( $this->choices as $value => $label ) : ?>
                        <li>
                            <label for="<?php echo esc_attr($this->id); ?>[<?php echo esc_attr( $value ); ?>]" title="<?php echo esc_attr( $label ); ?>">
                                <input type="radio" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($this->id); ?>[<?php echo esc_attr( $value ); ?>]" value="<?php echo esc_attr( $value ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
                                <img src="<?php echo get_template_directory_uri() . '/admin/images/'.esc_attr( $value ).'.png';  ?>" alt="<?php echo esc_attr( $label ); ?>" />
                            </label>
                        </li>
                   
                    <?php endforeach; ?>

                    </ul>
                </div>
                <?php
            }
        }

        // Header custom control for select sidebar
        class rooten_Customize_Header_Layout_Control extends WP_Customize_Control {

            public $type = 'layout_header';
            public function render_content() { ?>

                <div class="rooten-layout-select">

                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>

                <ul>
                <?php 
                    $name = '_customize-radio-' . $this->id; 

                    foreach ( $this->choices as $value => $label ) : ?>
                        <li>
                            <label for="<?php echo esc_attr($this->id); ?>[<?php echo esc_attr( $value ); ?>]" title="<?php echo esc_attr( $label ); ?>">
                                <input type="radio" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($this->id); ?>[<?php echo esc_attr( $value ); ?>]" value="<?php echo esc_attr( $value ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
                                <img src="<?php echo get_template_directory_uri() . '/admin/images/header-'.esc_attr( $value ).'.png';  ?>" alt="<?php echo esc_attr( $label ); ?>" />
                            </label>
                        </li>
                   
                    <?php endforeach; ?>

                    </ul>
                </div>
                <?php
            }
        }


        // Social custom control for select sidebar
        class rooten_Customize_Social_Control extends WP_Customize_Control {
            private static $firstLoad = true;
            public $type = 'social';
            public function render_content() { 
                // the saved value is an array. convert it to csv
                if ( is_array( $this->value() ) ) {
                    $savedValueCSV = implode( ',', $this->value() );
                    $values = $this->value();
                } else {
                    $savedValueCSV = $this->value();
                    $values = explode( ',', $this->value() );
                }
                if ( self::$firstLoad ) {
                    self::$firstLoad = false;
                    ?>
                    <script>
                    jQuery(document).ready( function($) {
                        "use strict";
                        $( 'input.bdt-social-link' ).change( function(event) {
                            event.preventDefault();
                            var csv = '';
                            $( this ).parents( 'li:eq(0)' ).find( 'input[type=text]' ).each( function() {
                                if ($( this ).val()) {
                                    csv += $( this ).attr( 'value' ) + ',';
                                }
                            } );
                            csv = csv.replace(/,+$/, "");
                            $( this ).parents( 'li:eq(0)' ).find( 'input[type=hidden]' ).val(csv)
                            // we need to trigger the field afterwards to enable the save button
                            .trigger( 'change' );
                            return true;
                        } );
                    } );
                    </script>
                    <?php
                } ?>


                <div class="rooten-social-link">
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>              
                        <?php
                        for ( $links = 0; $links <= 4; $links++ ) : ?>
                            <label for="<?php echo esc_html($this->id.$links); ?>">
                                <input type="text" class="bdt-social-link" id="<?php echo esc_html($this->id.$links); ?>" value="<?php echo isset($values[$links]) ? esc_html($values[$links]) : ''; ?>" placeholder="<?php echo esc_html_x('http://', 'backend', 'rooten') ?>" />
                            </label>
                        <?php endfor; ?>
                    
                    <input type="hidden" value="<?php echo esc_html( $savedValueCSV ); ?>" <?php $this->link(); ?> />
                </div><?php
            }
        }

        /**
         * Multiple check customize control class.
         * @since 1.0.0
         */
        class rooten_Customize_Multicheck_Control extends WP_Customize_Control {
            public $description = '';
            public $subtitle = '';
            private static $firstLoad = true;
            // Since theme_mod cannot handle multichecks, we will do it with some JS
            public function render_content() {
                // the saved value is an array. convert it to csv
                if ( is_array( $this->value() ) ) {
                    $savedValueCSV = implode( ',', $this->value() );
                    $values = $this->value();
                } else {
                    $savedValueCSV = $this->value();
                    $values = explode( ',', $this->value() );
                }
                if ( self::$firstLoad ) {
                    self::$firstLoad = false;
                    ?>
                    <script>
                    jQuery(document).ready( function($) {
                        "use strict";
                        $( 'input.bdt-multicheck' ).change( function(event) {
                            event.preventDefault();
                            var csv = '';
                            $( this ).parents( 'li:eq(0)' ).find( 'input[type=checkbox]' ).each( function() {
                                if ($( this ).is( ':checked' )) {
                                    csv += $( this ).attr( 'value' ) + ',';
                                }
                            } );
                            csv = csv.replace(/,+$/, "");
                            $( this ).parents( 'li:eq(0)' ).find( 'input[type=hidden]' ).val(csv)
                            // we need to trigger the field afterwards to enable the save button
                            .trigger( 'change' );
                            return true;
                        } );
                    } );
                    </script>
                    <?php
                } ?>
                <label class='bdt-multicheck-container'>
                    <span class="customize-control-title">
                        <?php echo esc_html( $this->label ); ?>
                        <?php if ( isset( $this->description ) && '' != $this->description ) { ?>
                            <a href="#" class="button tooltip" title="<?php echo strip_tags( esc_html( $this->description ) ); ?>">?</a>
                        <?php } ?>
                    </span>
                    <?php if ( '' != $this->subtitle ) : ?>
                        <div class="customizer-subtitle"><?php echo esc_html($this->subtitle); ?></div>
                    <?php endif; ?>
                    <?php
                    foreach ( $this->choices as $value => $label ) {
                        printf( '<label for="%s"><input class="bdt-multicheck" id="%s" type="checkbox" value="%s" %s/> %s</label><br>',
                            $this->id . $value,
                            $this->id . $value,
                            esc_attr( $value ),
                            checked( in_array( $value, $values ), true, false ),
                            $label
                        );
                    }
                    ?>
                    <input type="hidden" value="<?php echo esc_attr( $savedValueCSV ); ?>" <?php $this->link(); ?> />
                </label>
                <?php
            }
        }

    endif;

}
add_action( 'customize_register', 'rooten_customize_register' );

/* custom sanitization */
function rooten_sanitize_textarea($string) {
    return htmlspecialchars_decode(esc_textarea( $string));
}

// global_layout activate check for customizer option visible 
function rooten_global_layout_check() {

    if ( get_theme_mod('rooten_global_layout' == 'boxed') ) {
        return true;
    } else {
        return false;
    }
}


// toolbar activate check for customizer option visible 
function rooten_toolbar_check() {

    if ( get_theme_mod('rooten_toolbar') ) {
        return true;
    } else {
        return false;
    }
}

// toolbar activate check for customizer option visible 
function rooten_toolbar_left_custom_check() {

    if ( get_theme_mod('rooten_toolbar') == 1 and get_theme_mod('rooten_toolbar_left') == 'custom-left') {
        return true;
    } else {
        return false;
    }
}

function rooten_toolbar_right_custom_check() {

    if ( get_theme_mod('rooten_toolbar') == 1 and get_theme_mod('rooten_toolbar_right') == 'custom-right' ) {
        return true;
    } else {
        return false;
    }
}

// custom sanitize
function rooten_sanitize_choices( $input, $setting ) {
    global $wp_customize;
 
    $control = $wp_customize->get_control( $setting->id );
 
    if ( array_key_exists( $input, $control->choices ) ) {
        return $input;
    } else {
        return $setting->default;
    }
}

function rooten_sanitize_checkbox( $input ) {
    if ( $input == 1 ) {
        return 1;
    } else {
        return 0;
    }
}

if ( ! function_exists( 'rooten_sanitize_color_scheme' ) ) :

function rooten_sanitize_color_scheme( $value ) {
    $color_schemes = rooten_get_color_scheme_choices();

    if ( ! array_key_exists( $value, $color_schemes ) ) {
        return 'default';
    }
    return $value;
}
endif; // rooten_sanitize_color_scheme

function rooten_toolbar_social_check() {
    if ( get_theme_mod('rooten_toolbar_left') == 'social' || get_theme_mod('rooten_toolbar_right') == 'social' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_header_layout_check() {
    if ( 'custom' == get_theme_mod('rooten_header_layout')) {
        return false;
    }
    if ( get_theme_mod('rooten_header_layout') == 'side-left' || get_theme_mod('rooten_header_layout') == 'side-right' ) {
        return false;
    } else {
        return true;
    }
}




function rooten_bottom_gutter_collapse_check() {
    if ( get_theme_mod('rooten_bottom_gutter') != 'collapse' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_copyright_text_custom_show_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return false;
    }
    if ( get_theme_mod('rooten_copyright_text_custom_show') ) {
        return true;
    } else {
        return false;
    }
}

function rooten_titlebar_bg_check() {
    if ( get_theme_mod('rooten_titlebar_bg_style') == 'media' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_main_bg_check() {
    if ( get_theme_mod('rooten_main_bg_style') == 'media' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_header_transparent_check() {
    if ( 'custom' == get_theme_mod('rooten_header_layout')) {
        return false;
    }
    if ( get_theme_mod('rooten_header_transparent') == false ) {
        return true;
    } else {
        return false;
    }
}

function rooten_header_bg_style_check() {
    if ( 'custom' == get_theme_mod('rooten_header_layout')) {
        return false;
    }
    if ( get_theme_mod('rooten_header_bg_style') == 'media' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_header_bg_img_check() {
    if ( 'custom' == get_theme_mod('rooten_header_layout')) {
        return false;
    }
    if ( get_theme_mod('rooten_header_bg_img')) {
        return true;
    } else {
        return false;
    }
}

function rooten_bottom_bg_custom_color_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return false;
    }
    if ( get_theme_mod('rooten_bottom_bg_style') == 'custom' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_bottom_txt_custom_color_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return false;
    }
    if ( get_theme_mod('rooten_bottom_txt_style') == 'custom' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_bottom_bg_style_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return false;
    }
    if ( get_theme_mod('rooten_bottom_bg_style') == 'media' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_bottom_bg_img_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return false;
    }
    if ( get_theme_mod('rooten_bottom_bg_img')) {
        return true;
    } else {
        return false;
    }
}

function rooten_header_fixed_check() {
    if ( 'custom' == get_theme_mod('rooten_header_layout')) {
        return false;
    }
    if ( get_theme_mod('rooten_header_sticky') == 'fixed' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_offcanvas_mode_check() {
    if ( 'custom' == get_theme_mod('rooten_header_layout')) {
        return false;
    }
    if ( get_theme_mod('rooten_mobile_offcanvas_style') == 'offcanvas' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_homepage_check() {
    if ( is_front_page() || is_home() || is_single()) {
        return false;
    } else {
        return true;
    }
}

function rooten_titlebar_check() {
    if ( is_front_page()) {
        return false;
    } else {
        return true;
    }
}

function rooten_cookie_policy_button_check() {
    if ( get_theme_mod('rooten_cookie_policy_button') ) {
        return true;
    } else {
        return false;
    }
}

// Preloader callback checking

function rooten_preloader_logo_check() {
    if (get_theme_mod('rooten_preloader_logo') == 'custom') {
        return true;
    } else {
        return false;
    }
}

function rooten_preloader_text_check() {
    if (get_theme_mod('rooten_preloader_text') == 'custom') {
        return true;
    } else {
        return false;
    }
}

function rooten_preloader_animation_check() {
    if (get_theme_mod('rooten_preloader_animation')) {
        return true;
    } else {
        return false;
    }
}
function rooten_copyright_bg_custom_color_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return false;
    }
    if ( get_theme_mod('rooten_copyright_bg_style') == 'custom' ) {
        return true;
    } else {
        return false;
    }
}
function rooten_copyright_bg_style_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return false;
    }
    if ( get_theme_mod('rooten_copyright_bg_style') == 'media' ) {
        return true;
    } else {
        return false;
    }
}

function rooten_copyright_bg_img_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return false;
    }
    if ( get_theme_mod('rooten_copyright_bg_img')) {
        return true;
    } else {
        return false;
    }
}

function rooten_totop_check() {
    if ( get_theme_mod('rooten_totop_show')) {
        return true;
    } else {
        return false;
    }
}

// Header condition 
function rooten_custom_header_yes_check() {
    if ( 'custom' == get_theme_mod('rooten_header_layout')) {
        return true;
    } else {
        return false;
    }
}
function rooten_custom_header_no_check() {
    if ( 'custom' == get_theme_mod('rooten_header_layout')) {
        return false;
    } else {
        return true;
    }
}

// Footer condition 
function rooten_custom_footer_no_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return false;
    } else {
        return true;
    }
}

function rooten_custom_footer_yes_check() {
    if ( 'custom' == get_theme_mod('rooten_footer_widgets')) {
        return true;
    } else {
        return false;
    }
}