<?php
$rooten_logo                   = get_theme_mod('rooten_logo_mobile');
$rooten_logo_width             = get_theme_mod('rooten_logo_width_mobile');
$rooten_width                  = ($rooten_logo_width) ? $rooten_logo_width : '';
$rooten_img_atts               = [];
$rooten_img_atts['class'][]    = 'bdt-responsive-height';
$rooten_img_atts['style'][]    = 'width:'.esc_attr($rooten_width);
$rooten_img_atts['src'][]      = esc_url($rooten_logo);
$rooten_img_atts['itemprop'][] = 'logo';
$rooten_img_atts['alt'][]      = get_bloginfo( 'name' );

?>

<a href="<?php echo esc_url(home_url('/')); ?>"<?php rooten_helper::attrs(['class' => 'bdt-logo bdt-navbar-item']) ?> itemprop="url">
    <?php if ($rooten_logo) : ?>
        <img<?php rooten_helper::attrs($rooten_img_atts) ?>>
    <?php else : ?>
        <?php bloginfo( 'name' );?>
    <?php endif; ?>
</a>