<article id="post-<?php the_ID() ?>" <?php post_class('bdt-article post-format-link') ?> data-permalink="<?php the_permalink() ?>" typeof="Article">

    <?php get_template_part( 'template-parts/post-format/schema-meta' ); ?>

    <?php 
    $rooten_link_url = get_post_meta( get_the_ID(), 'rooten_blog_link', true );

    if (!empty($rooten_link_url)) : ?>

    <div class="post-link bdt-border-rounded<?php echo (is_single()) ? ' bdt-margin-large-bottom' : ' bdt-margin-bottom'; ?>">
        <a href="<?php echo esc_url($rooten_link_url) ?>" title="<?php printf( esc_attr__('Link to %s', 'rooten'), the_title_attribute('echo=0') ); ?>" target="_blank"><?php the_title(); ?><span><?php echo esc_html($rooten_link_url) ?></span></a>
    </div>
    <?php endif ?>
    
    <?php if(is_single()) : ?>
        <div class="bdt-margin-medium-bottom bdt-container bdt-container-small bdt-text-center">
            <?php if(get_theme_mod('rooten_blog_meta', 1)) :?>
            <?php get_template_part( 'template-parts/post-format/meta' ); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</article>