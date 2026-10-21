<?php

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'rooten_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function rooten_register_required_plugins() {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        array(
            'name'               => esc_html_x('BdThemes FAQ', 'backend', 'rooten'),
            'slug'               => 'bdthemes-faq',
            'source'             => 'https://bdthemes.com/secure/rooten/bdthemes-faq.zip?key=4x6e7s3ed405dy9xz99qw19947742e90',
            'version'            => '1.0.0',
        ),
        array(
            'name'               => esc_html_x('BdThemes Testimonials', 'backend', 'rooten'),
            'slug'               => 'bdthemes-testimonials',
            'source'             => 'https://bdthemes.com/secure/rooten/bdthemes-testimonials.zip?key=4x6e7s3ed405dy9xz99qw19947742e90',
            'version'            => '1.1.1',
        ),
        array(
            'name'               => esc_html_x('BdThemes Portfolio', 'backend', 'rooten'),
            'slug'               => 'bdthemes-portfolio',
            'source'             => 'https://bdthemes.com/secure/rooten/bdthemes-portfolio.zip?key=4x6e7s3ed405dy9xz99qw19947742e90',
            'version'            => '1.0.1',
        ),
        array(
            'name'               => esc_html_x('Elementor Page Builder', 'backend', 'rooten'),
            'slug'               => 'elementor',
            'required'           => true,
            'version'            => '2.5.0',
        ),
        array(
            'name'               => esc_html_x('MetaBox', 'backend', 'rooten'),
            'slug'               => 'meta-box',
            'required'           => true,
            'version'            => '4.18.1',
        ),
         array(
            'name'               => esc_html_x('Element Pack', 'backend', 'rooten'),
            'slug'               => 'bdthemes-element-pack',
            'required'           => true,
            'source'             => 'https://elementpack.pro/',
            'version'            => '3.1.0',
        ),
        array(
            'name'               => esc_html_x('Booked', 'backend', 'rooten'),
            'slug'               => 'booked',
            'source'             => 'https://bdthemes.com/secure/rooten/booked.zip?key=4x6e7s3ed405dy9xz99qw19947742e90',
            'version'            => '2.2.3',
        ),
        array(
            'name' => esc_html_x('SVG Support', 'backend', 'rooten'),
            'slug' => 'svg-support',
        ),
    );

    /*
     * Array of configuration settings. Amend each line as needed.
     *
     * TGMPA will start providing localized text strings soon. If you already have translations of our standard
     * strings available, please help us make TGMPA even better by giving us access to these translations or by
     * sending in a pull-request with .po file(s) with the translations.
     *
     * Only uncomment the strings in the config array if you want to customize the strings.
     */
    $config = array(
        'id'           => 'rooten',                // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => true,                    // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
    );

    tgmpa( $plugins, $config );
}