<?php if(is_single()) :?>
    <?php 
        $rooten_email            = get_post_meta( get_the_ID(), 'bdthemes_portfolio_email', true );
        $rooten_phone            = get_post_meta( get_the_ID(), 'bdthemes_portfolio_phone', true );
        $rooten_appointment_link = get_post_meta( get_the_ID(), 'bdthemes_portfolio_appointment_link', true );
        $rooten_link_title       = get_post_meta( get_the_ID(), 'bdthemes_portfolio_appointment_link_title', true );
        $rooten_badge            = get_post_meta( get_the_ID(), 'bdthemes_portfolio_badge', true );
        $rooten_social_link      = get_post_meta( get_the_ID(), 'bdthemes_portfolio_social_link', true );
    ?>
    <div class="bdt-card bdt-card-default">
        
        <div class="bdt-position-relative">
            <?php get_template_part( 'template-parts/portfolio/media' ); ?>
            <div class="bdt-position-cover bdt-overlay bdt-overlay-gradient bdt-position-z-index"></div>

            <?php if($rooten_social_link != null) : ?>
                <ul class="bdt-list bdt-position-medium bdt-position-bottom-left bdt-position-z-index bdt-margin-remove-bottom">
                <?php foreach ($rooten_social_link as $rooten_link) : ?>
                    <?php $rooten_tooltip = ucfirst(rooten_helper::icon($rooten_link)); ?>
                    <li class="bdt-display-inline-block">
                        <a<?php rooten_helper::attrs(['href' => $rooten_link, 'class' => 'bdt-margin-small-right']); ?> bdt-icon="icon: <?php echo rooten_helper::icon($rooten_link); ?>" title="<?php echo esc_attr($rooten_tooltip); ?>" bdt-tooltip></a>
                    </li>
                <?php endforeach ?>
                </ul>
            <?php endif; ?>

            
        </div>

        <div class="bdt-card-header">
            <h3 class="bdt-card-title"><?php echo get_the_title( ) . ' '; esc_html_e( 'Info', 'rooten' ); ?></h3>
        </div>

        <div class="bdt-card-body">    
            <?php if($rooten_badge != null) : ?>
                <div class="bdt-card-badge bdt-label"><?php echo esc_attr($rooten_badge); ?></div>
            <?php endif; ?>

            <ul class="bdt-list bdt-list-divider bdt-margin-small-bottom bdt-padding-remove">

                <?php if($rooten_email != null) : ?>
                    <li class="">
                        <div class="bdt-grid-small bdt-flex-bottom" bdt-grid>
                            <div class="bdt-width-expand" bdt-leader><?php echo esc_html_e ('Email: ', 'rooten'); ?></div>
                            <div class="bdt-width-auto bdt-text-bold"><?php echo esc_html($rooten_email); ?></div>
                        </div>
                       
                    </li>
                <?php endif; ?>

                <?php if($rooten_phone != null) : ?>
                    <li class="">
                        <div class="bdt-grid-small bdt-flex-bottom" bdt-grid>
                            <div class="bdt-width-expand" bdt-leader><?php echo esc_html_e ('Phone Number: ', 'rooten'); ?></div>
                            <div class="bdt-width-auto bdt-text-bold"><?php echo esc_html($rooten_phone); ?></div>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <?php if($rooten_appointment_link != null) : ?>
            <div class="bdt-card-footer">
                <a href="<?php echo esc_url($rooten_appointment_link); ?>" class="bdt-button bdt-button-primary bdt-border-rounded bdt-text-bold bdt-width-1-1"><?php echo ($rooten_link_title) ? $rooten_link_title : esc_html__( 'Appointment Now', 'rooten' ); ?></a>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>