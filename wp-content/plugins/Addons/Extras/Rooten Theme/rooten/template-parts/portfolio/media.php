<?php 
//$rooten_images = get_post_meta( get_the_ID(), 'medium_gallery', true );
$rooten_images = rwmb_meta( 'bdthemes_portfolio_altimg', 'type=image_advanced&size=medium' );


?>

<?php if (has_post_thumbnail() and empty($rooten_images) ) : ?>
    <div class="">
        <?php if(is_single()) : ?>
            <?php echo  the_post_thumbnail('large', array('class' => 'bdt-width-1-1'));  ?>
        <?php else : ?>
            <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
                <?php echo  the_post_thumbnail('medium', array('class' => 'bdt-width-1-1'));  ?>
            </a>
        <?php endif; ?>           
    </div>

<?php elseif (!empty($rooten_images)) : ?>
    
    <?php if(is_single()) : ?>
        <div class="">
            <?php echo  the_post_thumbnail('large', array('class' => 'bdt-width-1-1'));  ?>
        </div>
    <?php else : ?>
        <div class="portfolio-image-gallery bdt-position-relative bdt-overflow-hidden" bdt-toggle="target: > .portfolio-img-flip; mode: hover; animation: bdt-animation-fade; queued: true; duration: 300" bdt-lightbox>

                <div class="portfolio-img-flip bdt-position-absolute bdt-position-z-index">
                    <?php echo  the_post_thumbnail('medium', array('class' => 'bdt-width-1-1'));  ?>
                </div>
                
                <?php foreach ( $rooten_images as $rooten_image) : ?> 
                    <div class="portfolio-img">
                        <a href="<?php echo esc_url($rooten_image['full_url']); ?>" title="<?php echo esc_attr($rooten_image['title']); ?>">
                            <img src="<?php echo esc_url($rooten_image['url']); ?>" alt="<?php echo esc_attr($rooten_image['alt']); ?>" width="<?php echo esc_attr($rooten_image['width']); ?>" height="<?php echo esc_attr($rooten_image['height']); ?>" class="" />
                        </a>
                    </div>
                <?php endforeach; ?>
        </div>
    <?php endif; ?>           

<?php endif ?>
    
