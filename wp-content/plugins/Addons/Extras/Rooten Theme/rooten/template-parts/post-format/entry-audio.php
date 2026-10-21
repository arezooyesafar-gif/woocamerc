<article id="post-<?php the_ID() ?>" <?php post_class('bdt-article post-format-audio') ?> data-permalink="<?php the_permalink() ?>" typeof="Article">

    <?php get_template_part( 'template-parts/post-format/schema-meta' ); ?>

    <?php 
    $rooten_audio = get_post_meta( get_the_ID(), 'rooten_blog_audio', true );
    if (!empty($rooten_audio)) : ?>

    <div class="post-audio<?php echo (is_single()) ? ' bdt-margin-large-bottom' : ' bdt-margin-bottom'; ?>">

        <?php echo wp_kses($rooten_audio, rooten_allowed_tags('iframe')); ?>

    </div>

    <?php endif ?>
    
    <div class="bdt-margin-medium-bottom bdt-container bdt-container-small bdt-text-center">
        <?php get_template_part( 'template-parts/post-format/title' ); ?>

        <?php if(get_theme_mod('rooten_blog_meta', 1)) :?>
        <?php get_template_part( 'template-parts/post-format/meta' ); ?>
        <?php endif; ?>
    </div>
    
    <div class="bdt-container bdt-container-small">
        <?php get_template_part( 'template-parts/post-format/content' ); ?>

        <?php get_template_part( 'template-parts/post-format/read-more' ); ?>
    </div>

</article>