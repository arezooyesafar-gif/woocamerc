<article id="post-<?php the_ID() ?>" <?php post_class('bdt-article bdt-text-'.get_theme_mod('rooten_blog_align', 'left')) ?> data-permalink="<?php the_permalink() ?>" typeof="Article">

    <?php get_template_part( 'template-parts/post-format/schema-meta' ); ?>

    <?php if (has_post_thumbnail()) : ?>
        <div class="bdt-margin-large-bottom tm-blog-thumbnail">
            <?php if(is_single()) : ?>
                <?php echo  the_post_thumbnail('rooten_blog', array('class' => 'bdt-border-rounded'));  ?>
            <?php else : ?>
                <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
                    <?php echo  the_post_thumbnail('rooten_blog', array('class' => 'bdt-border-rounded'));  ?>
                </a>
            <?php endif; ?>           
        </div>
    <?php endif; ?>


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