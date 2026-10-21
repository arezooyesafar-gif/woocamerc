<article id="post-<?php the_ID() ?>" <?php post_class('bdt-article post-format-gallery bdt-text-'.get_theme_mod('rooten_blog_align', 'center')) ?> data-permalink="<?php the_permalink() ?>" typeof="Article">

    <?php get_template_part( 'template-parts/post-format/schema-meta' ); ?>
    
    <?php 
    //$rooten_images = get_post_meta( get_the_ID(), 'rooten_blog_gallery', true );
    $rooten_images = rwmb_meta( 'rooten_blog_gallery', 'type=image_advanced&size=rooten_blog' );
    if (!empty($rooten_images)) : ?>

    <div class="post-image-gallery bdt-position-relative bdt-overflow-hidden tm-blog-thumbnail bdt-margin-large-bottom">
        <div class="swiper-wrapper" bdt-lightbox>
            <?php if (has_post_thumbnail()) : ?>
                <div class="swiper-slide">
                    <?php echo  the_post_thumbnail('rooten_blog', array('class' => 'bdt-width-1-1'));  ?>
                </div>
            <?php endif; ?>
            
            <?php foreach ( $rooten_images as $rooten_image) : ?> 
                <div class="swiper-slide">
                <a href="<?php echo esc_url($rooten_image['full_url']); ?>" title="<?php echo esc_attr($rooten_image['title']); ?>">
                    <img src="<?php echo esc_url($rooten_image['url']); ?>" alt="<?php echo esc_attr($rooten_image['alt']); ?>" width="<?php echo esc_attr($rooten_image['width']); ?>" height="<?php echo esc_attr($rooten_image['height']); ?>" class="" />
                </a>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Arrows -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>

        <img class="tm-blog-entry-overlay" src="<?php echo get_template_directory_uri(); ?>/images/blog-entry-overlay.svg" alt="">
    </div>

    <?php endif ?>
    
    


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

</article>