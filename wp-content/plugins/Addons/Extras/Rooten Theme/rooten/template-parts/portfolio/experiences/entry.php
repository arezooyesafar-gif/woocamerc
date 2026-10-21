<?php
    $rooten_title       = 'yes';  // TODO
    $rooten_meta        = 'yes';  // TODO
    $rooten_excerpt     = 'no';  // TODO
    $rooten_align       = 'center';  // TODO
    $rooten_social_link = 'yes'; // TODO

?>

<div class="bdt-portfolio-content-wrapper bdt-box-shadow-small bdt-portfolio-align-<?php echo esc_attr($rooten_align); ?>">

    <?php if (has_post_thumbnail()) : ?>
        <div class="portfolio-thumbnail bdt-position-relative bdt-overflow-hidden">
            <div class="portfolio-thumbnail-design">
                <?php get_template_part( 'template-parts/portfolio/media' ); ?>
                <div class="bdt-portfolio-overlay bdt-position-cover bdt-overlay bdt-overlay-gradient bdt-position-z-index"></div>
            </div>  
        </div>
    <?php endif; ?>

    <?php if(( $rooten_title==='yes') or ( $rooten_meta==='yes') or ( $rooten_excerpt==='yes')) { ?>
        <div class="bdt-portfolio-desc bdt-padding bdt-position-relative bdt-position-z-index">

            <?php if( $rooten_title==='yes') { ?>
                <?php get_template_part( 'template-parts/portfolio/title' ); ?>
            <?php }; 

            if( $rooten_meta==='yes') {

                echo get_the_term_list(get_the_ID(),'experiences', '<ul class="bdt-portfolio-meta bdt-flex-'.$rooten_align.' bdt-margin-small-top bdt-margin-remove"><li>', '</li><li>', '</li></ul>' );
            }; 

            if( $rooten_social_link === 'yes') { 
                get_template_part( 'template-parts/portfolio/social-link' );
            }; ?>


            <?php if( $rooten_excerpt==='yes') { ?>
                <div class="bdt-container bdt-text-<?php echo esc_attr($rooten_align); ?> bdt-container-small">
                        <?php get_template_part( 'template-parts/portfolio/content' ); ?>
                </div>
            <?php }; ?>
        </div>
    <?php }; ?>
</div>