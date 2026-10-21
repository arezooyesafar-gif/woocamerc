<?php
/**
 * Custom functions that act independently of the theme templates.
 * Eventually, some of the functionality here could be replaced by core features.
 * @package rooten
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function rooten_body_classes( $classes ) {

    $layout_c        = get_theme_mod('rooten_header_layout', 'horizontal-left');
    $layout_m        = get_post_meta( get_the_ID(), 'rooten_header_layout', true );

    $transparent_c   = get_theme_mod( 'rooten_header_transparent');
    $transparent_m   = get_post_meta( get_the_ID(), 'rooten_header_transparent', true );
    $transparent     = (!empty($transparent_m)) ? $transparent_m : $transparent_c;

    $navbar_style = get_post_meta( get_the_ID(), 'rooten_navbar_style', true);
    $navbar_style = ( !empty($navbar_style) ) ? get_post_meta( get_the_ID(), 'rooten_navbar_style', true) : get_theme_mod('rooten_navbar_style', 'style1');


    global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

    if ( $is_lynx ) { $classes[] = 'lynx'; }
    elseif ( $is_gecko ) { $classes[] = 'gecko'; }
    elseif ( $is_opera ) { $classes[] = 'opera'; }
    elseif ( $is_NS4 ) { $classes[] = 'ns4'; }
    elseif ( $is_safari ) { $classes[] = 'safari'; }
    elseif ( $is_chrome ) { $classes[] = 'chrome'; }
    elseif ( $is_IE ) { $classes[] = 'ie'; }
    if($is_iphone) $classes[] = 'iphone-safari';

    
    // Adds a class of group-blog to blogs with more than 1 published author.
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }

    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    $classes[] = 'layout-' . get_theme_mod('rooten_global_layout', 'default');

    //$classes[] = $page_layout;
    $classes[] = 'navbar-' . $navbar_style;
    
    if (!empty($layout_m) and $layout_m != 'default') {
        $classes[] = 'tm-custom-header header-mode-'. $layout_m;
    } else {
        $classes[] = 'header-mode-'. $layout_c;

    }

    $classes[] = ($transparent != false and $transparent != 'no') ? 'tm-header-transparent' : '';

	return $classes;
}
add_filter( 'body_class', 'rooten_body_classes' );



add_filter( 'the_password_form', 'rooten_password_form' );
function rooten_password_form() {
    global $post;
    $label = ( empty( $post->ID ) ? uniqid('pf') : $post->ID );
    $output = '<div class="bdt-alert bdt-alert-warning">' . esc_html__( "This content is password protected. To view it please enter your password below:", "rooten" ) . '</div>
                <form action="' . esc_url( site_url( '/wp-login.php?action=postpass', 'login_post' ) ).'" method="post" class="bdt-grid-small bdt-margin-bottom" bdt-grid>
                    <div class="bdt-width-1-2@s">
                        <input name="post_password" id="' . $label . '" type="password" class="bdt-input bdt-border-rounded" />
                    </div>
                    <div class="bdt-width-1-2@s">
                        <input type="submit" name="Submit" class="bdt-button bdt-button-primary bdt-contrast bdt-border-rounded" value="' . esc_attr__( "Unlock", "rooten" ) . '" />
                    </div>
                </form>';
    return $output;
}


/**
 * Converts a HEX value to RGB.
 *
 * @since Rooten 1.0
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function rooten_hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}



add_action('wp_ajax_rooten_search', 'rooten_ajax_search');
add_action('wp_ajax_nopriv_rooten_search', 'rooten_ajax_search');

function rooten_ajax_search() {
    global $wp_query;

    $result = array('results' => array());
    $query  = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

    if (strlen($query) >= 3) {

        $wp_query->query_vars['posts_per_page'] = 5;
        $wp_query->query_vars['post_status'] = 'publish';
        $wp_query->query_vars['s'] = $query;
        $wp_query->is_search = true;

        foreach ($wp_query->get_posts() as $post) {

            $content = !empty($post->post_excerpt) ? strip_tags(strip_shortcodes($post->post_excerpt)) : strip_tags(strip_shortcodes($post->post_content));

            if (strlen($content) > 180) {
                $content = substr($content, 0, 179).'...';
            }

            $result['results'][] = array(
                'title' => $post->post_title,
                'text'  => $content,
                'url'   => get_permalink($post->ID)
            );
        }
    }

    die(json_encode($result));
}


// Wp override

function rooten_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'rooten_login_logo_url' );

$rooten_logo = get_theme_mod('rooten_logo_default');

if ($rooten_logo) {
    function rooten_login_logo() { 
        $rooten_logo = get_theme_mod('rooten_logo_default'); ?>
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url(<?php echo esc_url($rooten_logo); ?>);
                height:36px;
                width:320px;
                background-size: auto 36px;
                background-repeat: no-repeat;
                background-position: center center;
                padding-bottom: 30px;
            }
        </style>
    <?php }
    add_action( 'login_enqueue_scripts', 'rooten_login_logo' );
}


function rooten_comment($comment, $args, $depth) { ?>
    <li <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
    
        <article id="div-comment-<?php comment_ID() ?>" class="comment even thread-even bdt-comment bdt-visible-toggle depth-<?php echo esc_attr($depth); ?>">

            <header class="bdt-comment-header bdt-position-relative">
                <div class="bdt-grid-medium bdt-flex-middle bdt-grid" bdt-grid="">
                    <?php if ( $args['avatar_size'] != 0 ) : ?>
                        <div class="bdt-width-auto bdt-first-column">
                            <?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
                        </div>
                    <?php endif; ?>
                    <div class="bdt-width-expand">
                        <h3 class="bdt-comment-title bdt-margin-remove bdt-text-left">
                            <span class="bdt-link-reset"><?php comment_author_link(); ?></span>
                        </h3>

                        <ul class="bdt-comment-meta bdt-subnav bdt-subnav-divider bdt-margin-remove-top">
                            <li><a class="bdt-link-reset" href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
                                <?php /* translators: 1: date, 2: time */
                                    printf( __('%1$s at %2$s', 'rooten'), get_comment_date(),  get_comment_time() ); ?></a>
                            </li>

                            <?php if (get_edit_comment_link()) : ?>
                                <li class="bdt-visible@s"><?php edit_comment_link( esc_html__( 'Edit Comment', 'rooten' ), '  ', '' ); ?></li>
                            <?php endif; ?>

                            <?php if ( $comment->comment_approved == '0' ) : ?>
                                <li><em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'rooten' ); ?></em></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="bdt-position-top-right bdt-hidden-hover bdt-visible@s">
                     <?php comment_reply_link( array_merge( $args, array( 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>           
                </div>
            </header>            

            <div class="bdt-comment-body">
            <?php comment_text(); ?>
            
            </div>

            <ul class="bdt-comment-meta bdt-hidden@s bdt-subnav">
                <li><?php comment_reply_link( array_merge( $args, array( 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                </li>

                <?php if (get_edit_comment_link()) : ?>
                    <li><?php edit_comment_link( esc_html__( 'Edit Comment', 'rooten' ), '  ', '' ); ?></li>
                <?php endif; ?>
            </ul>

        
        </article>
        
    <?php
}

/**
 * Avoid any unexpected error when metabox not there
 */
if ( ! function_exists( 'rwmb_meta' ) ) {
    function rwmb_meta( $key, $args = '', $post_id = null ) {
        return false;
    }
}