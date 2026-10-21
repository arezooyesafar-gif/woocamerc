<article id="post-<?php the_ID() ?>" <?php post_class('bdt-article post-format-gallery') ?> data-permalink="<?php the_permalink() ?>" typeof="Article">

    <?php get_template_part( 'template-parts/post-format/schema-meta' ); ?>
    
    <?php 
    //$rooten_images = get_post_meta( get_the_ID(), 'rooten_blog_gallery', true );
    $rooten_images = rwmb_meta( 'rooten_blog_gallery', 'type=image_advanced&size=rooten_blog' );
    if (!empty($rooten_images)) : ?>

    <div class="post-image-gallery<?php echo (is_single()) ? ' bdt-margin-large-bottom' : ' bdt-margin-bottom'; ?>">
        <div class="image-lightbox owl-carousel owl-theme" data-owl-carousel='{"margin": 10, "items": 1, "nav": true, "navText": "", "loop": true}'>
            <?php 
            foreach ( $rooten_images as $rooten_image) {
                echo '<div class="carousel-cell"><a href="'.esc_url($rooten_image['full_url']).'" title="'.esc_attr($rooten_image['title']).'"><img src="'.esc_url($rooten_image['url']).'" alt="'.esc_attr($rooten_image['alt']).'" width="'.esc_attr($rooten_image['width']).'" height="'.esc_attr($rooten_image['height']).'" class="bdt-border-rounded" /></a></div>';
            } ?>
        </div>
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