<article id="post-<?php the_ID() ?>" <?php post_class('bdt-article') ?> data-permalink="<?php the_permalink() ?>" typeof="Article">

    <?php get_template_part( 'template-parts/post-loop/schema-meta' ); ?>

    <?php if (has_post_thumbnail()) : ?>
        <div class="bdt-margin-bottom tm-blog-thumbnail">
            <?php if(is_single()) : ?>
                <?php echo  the_post_thumbnail('rooten_blog', array('class' => 'bdt-border-rounded'));  ?>
            <?php else : ?>
                <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
                    <?php echo  the_post_thumbnail('rooten_blog', array('class' => 'bdt-border-rounded'));  ?>
                </a>
            <?php endif; ?> 
            <img class="tm-blog-entry-overlay" src="<?php echo get_template_directory_uri(); ?>/images/blog-entry-overlay.svg" alt="">          
        </div>
    <?php endif; ?>


    <div class="bdt-margin-medium-bottom bdt-container bdt-container-small bdt-text-center">
        <?php get_template_part( 'template-parts/post-loop/title' ); ?>

        <?php if(get_theme_mod('rooten_blog_meta', 1)) :?>
        <?php get_template_part( 'template-parts/post-loop/meta' ); ?>
        <?php endif; ?>
    </div>
    
    
    <div class="bdt-container bdt-container-small">
        <?php get_template_part( 'template-parts/post-loop/content' ); ?>

        <?php get_template_part( 'template-parts/post-loop/read-more' ); ?>
    </div>

</article>