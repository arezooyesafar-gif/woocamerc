<?php
$rooten_layout_c    = get_theme_mod('rooten_header_layout', 'horizontal-left');
$rooten_layout_m    = get_post_meta( get_the_ID(), 'rooten_header_layout', true );
$rooten_layout      = (!empty($rooten_layout_m) and $rooten_layout_m != 'default') ? $rooten_layout_m : $rooten_layout_c;
$rooten_logo        = get_theme_mod('rooten_logo_default');
$rooten_logo_width  = get_theme_mod('rooten_logo_width_default');


$rooten_logo_mode              = ($rooten_logo) ? 'tm-logo-img' : 'tm-logo-text';
$rooten_class                  = ['bdt-logo'];
$rooten_class[]                = (!in_array($rooten_layout, ['stacked-left-a', 'stacked-left-b', 'stacked-center-b', 'stacked-center-a', 'side-left', 'side-right']))  ? 'bdt-navbar-item' : '';
$rooten_class[]                = $rooten_logo_mode;
$rooten_width                  = ($rooten_logo_width) ? $rooten_logo_width : '';
$rooten_img_atts               = [];
$rooten_img_atts['class'][]    = 'bdt-responsive-height';
$rooten_img_atts['itemprop'][] = 'logo';
$rooten_img_atts['alt'][]      = get_bloginfo( 'name' );

$rooten_img_atts['src'][]      = esc_url($rooten_logo);	
$rooten_img_atts['style'][]    = 'width:'.esc_attr($rooten_width);


?>

<a href="<?php echo esc_url(home_url('/')); ?>"<?php rooten_helper::attrs(['class' => $rooten_class]) ?> itemprop="url">
    <?php if ($rooten_logo or !empty($rooten_custom_logo)) : ?>
        <img<?php rooten_helper::attrs($rooten_img_atts) ?>>
    <?php else : ?>
        <?php bloginfo( 'name' );?>
    <?php endif; ?>
</a>