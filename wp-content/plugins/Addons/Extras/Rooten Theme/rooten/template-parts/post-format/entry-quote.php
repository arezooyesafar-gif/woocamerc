<article id="post-<?php the_ID() ?>" <?php post_class('bdt-article post-format-quote bdt-text-'.get_theme_mod('rooten_blog_align', 'center')) ?> data-permalink="<?php the_permalink() ?>" typeof="Article">

    <?php get_template_part( 'template-parts/post-format/schema-meta' ); ?>
    
    <?php 
    $rooten_quote_text = get_post_meta( get_the_ID(), 'rooten_blog_quote', true );
    $rooten_quote_src = get_post_meta( get_the_ID(), 'rooten_blog_quotesrc', true );

    if (!empty($rooten_quote_text)) : ?>
    <div class="post-quote<?php echo (is_single()) ? ' bdt-margin-large-bottom' : ' bdt-margin-bottom'; ?>">
        <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'rooten'), the_title_attribute('echo=0') ); ?>" class="quote-text"><?php echo esc_html($rooten_quote_text); ?>
        <span class="quote-source"><?php echo esc_html($rooten_quote_src); ?></span></a>
    </div>

    <?php else : ?>

        <?php echo 'Please insert a Quote'; ?>

    <?php endif ?>
    
    <?php if(is_single()) : ?>
        <div class="bdt-margin-medium-bottom bdt-container bdt-container-small">
            <?php get_template_part( 'template-parts/post-format/title' ); ?>

            <?php if(get_theme_mod('rooten_blog_meta', 1)) :?>
            <?php get_template_part( 'template-parts/post-format/meta' ); ?>
            <?php endif; ?>
        </div>
        
        <div class="bdt-container bdt-container-small">
            <?php get_template_part( 'template-parts/post-format/content' ); ?>

            <?php get_template_part( 'template-parts/post-format/read-more' ); ?>
        </div>
    <?php endif ?>

</article>