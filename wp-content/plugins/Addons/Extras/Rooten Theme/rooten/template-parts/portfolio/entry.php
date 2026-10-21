<?php
$rooten_author_desc     = get_the_author_meta('description');
$rooten_company_name    = get_post_meta( get_the_ID(), 'bdthemes_company_name', true );
$rooten_complition_date = get_post_meta( get_the_ID(), 'bdthemes_complition_date', true );
$rooten_project_link    = get_post_meta( get_the_ID(), 'bdthemes_project_link', true );


?>

<article id="post-<?php the_ID() ?>" <?php post_class('bdt-article') ?> data-permalink="<?php the_permalink() ?>" typeof="Article">

    <?php get_template_part( 'template-parts/portfolio/schema-meta' ); ?>
    
    <?php if (!is_single()) : ?>
        <?php get_template_part( 'template-parts/portfolio/media' ); ?>    
    <?php endif; ?>

    <div class="">
        <div class="bdt-card bdt-padding-large bdt-grid" bdt-grid>

            <div class="bdt-width-3-5@m bdt-hidden@m">
                <?php get_template_part( 'template-parts/portfolio/title' ); ?>
            </div>

            <ul class="bdt-list bdt-width-2-5@m">

                <?php if ( ! empty( $rooten_company_name ) ) : ?>
                    <li><span class="bdt-width-1-2 bdt-display-inline-block bdt-text-bold"><?php echo __('Company/Owner Name', 'rooten'); ?></span><span class="bdt-width-1-2 bdt-display-inline-block"><span class="bdt-margin-medium-right">:</span><?php echo esc_attr( $rooten_company_name ); ?></span></li>
                <?php endif; ?>

                <li><span class="bdt-width-1-2 bdt-display-inline-block bdt-text-bold"><?php echo __('Starting Date', 'rooten'); ?></span><span class="bdt-width-1-2 bdt-display-inline-block"><span class="bdt-margin-medium-right">:</span><?php echo esc_attr(get_the_date('M d, Y')); ?></span></li>

                <?php if ( ! empty( $rooten_complition_date ) ) : ?>
                    <li><span class="bdt-width-1-2 bdt-display-inline-block bdt-text-bold"><?php echo __('Complition Date', 'rooten'); ?></span><span class="bdt-width-1-2 bdt-display-inline-block"><span class="bdt-margin-medium-right">:</span><?php echo esc_attr( $rooten_complition_date ); ?></span></li>
                <?php endif; ?>

                <?php if ( ! empty( $rooten_project_link ) ) : ?>
                    <li><span class="bdt-width-1-2 bdt-display-inline-block bdt-text-bold"><?php echo __('Project Link', 'rooten'); ?></span><span class="bdt-width-1-2 bdt-display-inline-block"><a href="<?php echo esc_url( $rooten_project_link ); ?>" class="bdt-button-link bdt-link-reset" bdt-icon="arrow-right"><span class="bdt-margin-medium-right">:</span><?php echo __('Click Here ', 'rooten'); ?></a></li>
                <?php endif; ?>

            </ul>

            <div class="bdt-width-3-5@m bdt-text-right bdt-visible@m">
                <?php get_template_part( 'template-parts/portfolio/title' ); ?>
            </div>

        </div>
        <?php

        $rooten_images = rwmb_meta( 'bdthemes_gallery', 'type=image_advanced&size=parlour_blog' );

        if ( ! empty( $rooten_images ) ) {

            ?>
            <div class="bdt-margin-medium-bottom" bdt-slideshow>
                <ul class="bdt-slideshow-items">
                    <?php 
                    foreach ( $rooten_images as $rooten_image) {
                        echo '<li><img src="" alt="" bdt-cover><img src="'.esc_url($rooten_image['url']).'" alt="'.esc_attr($rooten_image['alt']).'" bdt-cover></li>';
                    } ?>
                </ul>
            </div>
            <?php

        } else {
            get_template_part( 'template-parts/portfolio/media' );
        }

        ?>
        <div class="bdt-padding">

            <?php get_template_part( 'template-parts/portfolio/content' ); ?>
            
            <div>
                <?php edit_post_link(esc_html__('Edit this post', 'rooten'), '<div class="bdt-button-text">','</div>'); ?>               
                <?php get_template_part( 'template-parts/portfolio/read-more' ); ?>
            </div>
        </div>

    </div>
</article>

<?php if(is_single() and empty($rooten_author_desc)) : ?>
    <hr class="bdt-margin-remove-top bdt-margin-medium-bottom">
<?php endif ?>