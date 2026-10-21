<?php
/**
 * helper functions class
 */
class rooten_helper {

    static $selfClosing = ['input'];


    /**
     * Renders a tag.
     *
     * @param  string $name
     * @param  array  $attrs
     * @param  string $text
     * @return string
     */
    public static function tag($name, array $attrs = [], $text = null) {
        $attrs = self::attrs($attrs);
        return "<{$name}{ $attrs }" . (in_array($name, self::$selfClosing) ? '/>' : ">$text</{$name}>");
    }

    /**
     * Renders a form tag.
     *
     * @param  array $tags
     * @param  array $attrs
     * @return string
     */
    public static function form($tags, array $attrs = []) {
        $attrs = self::attrs($attrs);
        return "<form{$attrs}>\n" . implode("\n", array_map(function($tag) {
            $output = self::tag($tag['tag'], array_diff_key($tag, ['tag' => null]));
            return $output;
        }, $tags)) . "\n</form>";
    }

    /**
     * Renders an image tag.
     *
     * @param  array|string $url
     * @param  array        $attrs
     * @return string
     */
    public static function image($url, array $attrs = []) {
        $url = (array) $url;
        $path = array_shift($url);
        $params = $url ? '?'.http_build_query(array_map(function ($value) {
            return is_array($value) ? implode(',', $value) : $value;
        }, $url)) : '';

        if (!isset($attrs['alt']) || empty($attrs['alt'])) {
            $attrs['alt'] = true;
        }

        $output = self::attrs(['src' => $path.$params], $attrs);

        return "<img{$output}>";
    }
    
    /**
     * Renders tag attributes.
     * @param  array $attrs
     * @return string
     */
    public static function attrs(array $attrs) {
        $output = [];

        if (count($args = func_get_args()) > 1) {
            $attrs = call_user_func_array('array_merge_recursive', $args);
        }

        foreach ($attrs as $key => $value) {

            if (is_array($value)) { $value = implode(' ', array_filter($value)); }
            if (empty($value) && !is_numeric($value)) { continue; }

            if (is_numeric($key)) {
               $output[] = $value;
            } elseif ($value === true) {
               $output[] = $key;
            } elseif ($value !== '') {
               $output[] = sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_COMPAT, 'UTF-8', false));
            }
        }

        $output = ($output) ? ' '.implode(' ', $output) : '';

        echo wp_kses_post( $output );
    }

    /**
     * automated section class, id, attributes generator based on var.
     * @param  string $name  section name here
     * @param  string $id    section id here
     * @param  string $class section extra class here
     * @return [type]        [description]
     */
    public static function section($name = '', $id = '', $class='') {
        $id             = ($id) ? 'tm-'.$id : false;
        $prefix         = 'rooten_';
        $section        = ($name) ? $prefix.$name : $prefix.'section';
        $name           = ($name) ? 'tm-'.$name : 'tm-section';
        $section_media  = [];
        $section_image  = '';
        $layout         = get_post_meta( get_the_ID(), $section.'_layout', true );
        $metabox_layout = (!empty($layout) and $layout != 'default') ? true : false;
        $position       = (get_post_meta( get_the_ID(), 'rooten_page_layout', true )) ? get_post_meta( get_the_ID(), 'rooten_page_layout', true ) : get_theme_mod( 'rooten_page_layout', 'sidebar-right' );

        if ($metabox_layout) {
            $bg_style = get_post_meta( get_the_ID(), $section.'_bg_style', true );
            $bg_style = ( !empty($bg_style) ) ? $bg_style : get_theme_mod( $section.'_bg_style' );
            $width    = get_post_meta( get_the_ID(), $section.'_width', true );
            $padding  = get_post_meta( get_the_ID(), $section.'_padding', true );
            $text     = get_post_meta( get_the_ID(), $section.'_txt_style', true );
        } else {
            $bg_style = get_theme_mod( $section.'_bg_style' );
            $width    = get_theme_mod( $section.'_width', 'default' );
            $padding  = get_theme_mod( $section.'_padding', 'medium' );
            $text     = get_theme_mod( $section.'_txt_style' );
        }

        if (is_array($class)) {
            $class = implode(' ', array_filter($class));
        }

            
        if ($metabox_layout) {
            $section_images = rwmb_meta( $section.'_bg_img', "type=image_advanced&size=standard" );
            foreach ( $section_images as $image ) { 
                $section_image = esc_url($image["url"]);
            }
            $section_bg_img_pos    = get_post_meta( get_the_ID(), $section.'_bg_img_position', true );
            $section_bg_img_attach = get_post_meta( get_the_ID(), $section.'_bg_img_fixed', true );
            $section_bg_img_vis    = get_post_meta( get_the_ID(), $section.'_bg_img_visibility', true );
        } else {
            $section_image         = get_theme_mod( $section.'_bg_img' );
            $section_bg_img_pos    = get_theme_mod( $section.'_bg_img_position' );
            $section_bg_img_attach = get_theme_mod( $section.'_bg_img_fixed' );
            $section_bg_img_vis    = get_theme_mod( $section.'_bg_img_visibility' );
        }

        // Image
        if ($section_image &&  $bg_style == 'media') {
            $section_media['style'][] = "background-image: url('{$section_image}');";
            // Settings
            $section_media['class'][] = 'bdt-background-norepeat';
            $section_media['class'][] = $section_bg_img_pos ? "bdt-background-{$section_bg_img_pos}" : '';
            $section_media['class'][] = $section_bg_img_attach ? "bdt-background-fixed" : '';
            $section_media['class'][] = $section_bg_img_vis ? "bdt-background-image@{$section_bg_img_vis}" : '';
        }

        $class   = [$name, 'bdt-section', $class];
        $class[] = ($position == 'full' and $name == 'tm-main') ? 'bdt-padding-remove-vertical' : ''; // section spacific override
        

        $class[] = ($bg_style) ? 'bdt-section-'.$bg_style : '';
        $class[] = ($text) ? 'bdt-'.$text : '';
        if ($padding != 'none') {
            $class[]       = ($padding) ? 'bdt-section-'.$padding : '';
        } elseif ($padding == 'none') {
            $class[]       = ($padding) ? 'bdt-padding-remove-vertical' : '';
        }

        $output = self::attrs(['id' => $id, 'class' => $class], $section_media);

        echo esc_attr($output);
    }

    /**
     * Auto container class
     * @param  string $class [description]
     * @return [type]        [description]
     */
    public static function container($class='') {
        
        $container_class    = ['bdt-container', $class];
        
        $output = self::attrs(['class' => $container_class]);

        echo  esc_attr($output);
    }

    /**
     * Auto div grid class system
     * @param  string $class [description]
     * @return [type]        [description]
     */
    public static function grid($class='') {
        
        $column_divider = get_theme_mod( 'rooten_sidebar_divider' );
        $gutter         = get_theme_mod( 'rooten_sidebar_gutter', 'large' );
        
        $grid_class     = ['bdt-grid', $class];
        $grid_class[]   = ($gutter) ? 'bdt-grid-'.$gutter : '';
        $grid_class[]   = ($column_divider && $gutter != 'collapse') ? 'bdt-grid-divider' : '';

        $data_grid = '';
        
        $output = self::attrs(['class' => $grid_class, 'bdt-grid' => true]);

        echo  esc_attr($output);
    }

    /**
     * Sidebar class automization
     * @param  string $position [description]
     * @param  string $class    [description]
     * @return [type]           [description]
     */
    public static function sidebar($position = 'sidebar-right', $class = '', $width = '') {
        
        $position = ($position) ? $position : 'sidebar-right';

        $width      = ($width) ? $width : get_theme_mod( 'rooten_sidebar_width', '1-3' );
        $breakpoint = get_theme_mod( 'rooten_sidebar_breakpoint', 'm' );

        $class = ['tm-sidebar', $class];
        $class[] = ($width) ? 'bdt-width-'.$width.'@'.$breakpoint.' bdt-first-column' : 'bdt-width-1-3@'.$breakpoint.' bdt-first-column';
        $class[] = ($position == 'sidebar-left') ? 'bdt-flex-first@'.$breakpoint.' bdt-first-column' : '';
        
        $output = self::attrs(['class' => $class]);

        return $output;
    }

    /**
     * social icon generator from link
     * @param  [type] $link [description]
     * @return [type]       [description]
     */
    public static function icon($link) {
        static $icons;
        $icons = self::social_icons();

        if (strpos($link, 'mailto:') === 0) {
            return 'mail';
        } elseif (strpos($link, 'tel:') === 0) {
            return 'phone';
        }

        $icon = parse_url($link, PHP_URL_HOST);
        $icon = preg_replace('/.*?(plus\.google|[^\.]+)\.[^\.]+$/i', '$1', $icon);
        $icon = str_replace('plus.google', 'google-plus', $icon);

        if (!in_array($icon, $icons)) {
            $icon = 'social';
        }

        return $icon;
    }

    public static function social_icons() {
        $icons = ['behance', 'dribbble', 'facebook', 'flickr', 'foursquare', 'github', 'github-alt', 'google', 'google-plus', 'instagram', 'joomla', 'linkedin', 'pagekit', 'pinterest', 'soundcloud', 'tripadvisor', 'tumblr', 'twitter', 'uikit', 'vimeo', 'whatsapp', 'wordpress', 'xing', 'yelp', 'youtube'];

        return $icons;
    }

    /**
     * Returns url of no image
     * @param string $size
     * @return string
     */
    public static function no_image_url($size = "") {
        switch ($size) {
            case "150":
                return get_template_directory_uri() . '/images/no-image-150x150.jpg';
                break;
            case "300":
                return get_template_directory_uri() . '/images/no-image-300x300.jpg';
                break;
            default:
                return get_template_directory_uri() . '/images/no-image-450x450.jpg';
                break;
        }
    }

}

/**
 * Rooten Saved Header Template list maker
 * @return template type with array
 */
function rooten_custom_template_list($type = 'header') {

    if ( !defined( 'ELEMENTOR_VERSION' ) ) {
        return ['0' => esc_html__( 'Install Elementor First', 'rooten' ) ];
    }

    $args = array(
      'numberposts' => 20,
      'post_type'   => 'bdt-custom-template',
      'meta_query'   => array(
        'relation' => 'OR',
        array(
          'key'     => 'rooten_template_type',
          'value'   => $type,
          'compare' => '==',
          'type'    => 'post',
        ),
      ),
    );

    $post_list = get_posts($args);

    $post_list_options = [0 => sprintf( __( 'Select %s', 'rooten' ), ucfirst($type) ) ];

    foreach ( $post_list as $list ) :
     $post_list_options[ $list->ID ] = $list->post_title;
    endforeach;   

    return $post_list_options;
}

/* 
 * Custom Excerpts control
 */

// Set new Default Excerpt Length
function rooten_new_excerpt_length($length) {
    return 200;
}
add_filter('excerpt_length', 'rooten_new_excerpt_length');

// Custom Excerpt Length
function rooten_custom_excerpt($limit=50) {
    return strip_shortcodes(wp_trim_words(get_the_content(), $limit, '...'));
}

// Word Limiter
function rooten_limit_words($string, $word_limit) {
    $words = explode(' ', $string);
    return implode(' ', array_slice($words, 0, $word_limit));
}

// Remove Shortcodes from Search Results Excerpt
function rooten_remove_shortcode_from_excerpt($excerpt) {
  if ( is_search() ) {
    $excerpt = strip_shortcodes( $excerpt );
  }
  return $excerpt;
}
add_filter('the_excerpt', 'rooten_remove_shortcode_from_excerpt');

/* custom sanitization */
function rooten_stripslashes($string) {
    if(get_magic_quotes_gpc()) {
        return stripslashes($string);
    } else {
        return $string;
    }
}

/**
 * sanitize text for customizer
 */
function rooten_sanitize_text($string) {
    return rooten_stripslashes(htmlspecialchars($string));
}

/**
 * sanitize text for html control
 */
function rooten_sanitize_text_decode($string) {
    return rooten_stripslashes(htmlspecialchars_decode($string));
}



/**
 * Add pre-connect for Google Fonts.
 *
 * @since 1.2.0
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function rooten_resource_hints( $urls, $relation_type ) {
    if ( wp_style_is( 'theme-fonts-google', 'queue' ) && 'preconnect' === $relation_type ) {
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }

    return $urls;
}
add_filter( 'wp_resource_hints', 'rooten_resource_hints', 10, 2 );

/*
 * Helper - expand allowed tags()
*/
function rooten_allowed_tags( $tag = null ) {
    $tag_allowed = wp_kses_allowed_html('post');

    $tag_allowed['input'] = [
        'class'   => [],
        'id'      => [],
        'name'    => [],
        'value'   => [],
        'checked' => [],
        'type'    => [],
    ];
    $tag_allowed['select'] = [
        'class'    => [],
        'id'       => [],
        'name'     => [],
        'value'    => [],
        'multiple' => [],
        'type'     => [],
    ];
    $tag_allowed['option'] = [
        'value'    => [],
        'selected' => [],
    ];

    $tag_allowed['title'] = [
        'a'      => [
            'href'  => [],
            'title' => [],
            'class' => [],
        ],
        'br'     => [],
        'em'     => [],
        'strong' => [],
        'hr' => [],
    ];

    $tag_allowed['text'] = [
        'a'      => [
            'href'  => [],
            'title' => [],
            'class' => [],
        ],
        'br'     => [],
        'em'     => [],
        'strong' => [],
        'hr'     => [],
        'i'      => [
            'class' => [],
        ],
        'span'   => [
            'class' => [],
        ],
    ];

    $allowed_tag['iframe'] = [
        'src'             => [],
        'height'          => [],
        'width'           => [],
        'frameborder'     => [],
        'allowfullscreen' => [],
    ];

    if( $tag == null ){
        return $tag_allowed;
    } elseif( is_array($tag) ){
        $new_tag_allow = [];
        
        foreach ( $tag as $_tag ){
            $new_tag_allow[$_tag] = $tag_allowed[$_tag];
        }

        return $new_tag_allow;
    } else {
        return isset($tag_allowed[$tag]) ? $tag_allowed[$tag] : [];
    }
}


// set custom menu walker for get facility for megamenu style and all others benefit from here.
add_filter('wp_nav_menu_args', function($args) {
    if (empty($args['walker'])) {
        $args['walker'] = new rooten_menu_walker;
    }
    return $args; }
);