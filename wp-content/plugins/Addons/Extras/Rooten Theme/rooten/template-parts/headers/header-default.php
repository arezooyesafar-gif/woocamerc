<?php
/**
* @package   rooten
* @author    bdthemes http://www.bdthemes.com
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

	$rooten_container_media = [];
	$rooten_container_image = '';
	// Options
	$rooten_layout_c        = get_theme_mod( 'rooten_header_layout', 'horizontal-left' );
	$rooten_layout_m        = get_post_meta( get_the_ID(), 'rooten_header_layout', true );
	$rooten_layout          = (!empty($rooten_layout_m) and $rooten_layout_m != 'default') ? $rooten_layout_m : $rooten_layout_c;
	
	$rooten_fullwidth       = get_theme_mod( 'rooten_header_fullwidth' );
	$rooten_logo            = get_theme_mod( 'rooten_logo_default' );
	$rooten_class           = array_merge(['tm-header', 'bdt-visible@' . get_theme_mod( 'rooten_mobile_break_point', 'm')]);
	$rooten_search          = get_theme_mod( 'rooten_search_position', 'header' );
	
	$rooten_transparent     = get_theme_mod( 'rooten_header_transparent' );
	
	$rooten_sticky          = get_theme_mod( 'rooten_header_sticky' );
	$rooten_cart            = get_theme_mod( 'rooten_woocommerce_cart' );
	$rooten_menu_text       = get_theme_mod( 'rooten_mobile_menu_text' );
	$rooten_offcanvas_mode  = get_theme_mod( 'rooten_mobile_offcanvas_mode', 'push' );
	$rooten_shadow          = get_theme_mod( 'rooten_header_shadow', 'small' );

    $rooten_bg_style = get_theme_mod( 'rooten_header_bg_style' );
    $rooten_width    = get_theme_mod( 'rooten_header_width' );
    $rooten_padding  = get_theme_mod( 'rooten_header_padding' );
    $rooten_text     = get_theme_mod( 'rooten_header_txt_style' );
	
    $rooten_container_image         = get_theme_mod( 'rooten_header_bg_img' );
    $rooten_container_bg_img_pos    = get_theme_mod( 'rooten_header_bg_img_position' );
	

	// Image
	if ($rooten_container_image &&  $rooten_bg_style == 'media') {
	    $rooten_container_media['style'][] = "background-image: url( '{$rooten_container_image}' );";
	    // Settings
	    $rooten_container_media['class'][] = 'bdt-background-norepeat';
	    $rooten_container_media['class'][] = $rooten_container_bg_img_pos ? "bdt-background-{$rooten_container_bg_img_pos}" : '';
	}

	// Container
	$rooten_container            = ['class' => ['bdt-navbar-container', 'tm-primary-navbar']];
	$rooten_container['class'][] = ($rooten_bg_style && ($rooten_transparent == false or $rooten_transparent == 'no' ) ) ? 'navbar-color-'.$rooten_bg_style : '';
	$rooten_class[]              = ($rooten_text) ? 'bdt-'.$rooten_text : '';
	$rooten_class[]              = ($rooten_shadow) ? 'bdt-box-shadow-'.$rooten_shadow : '';


	// Transparent
	if ($rooten_transparent != false and $rooten_transparent != 'no') {
	    $rooten_class[] = 'tm-header-transparent';
	    $rooten_container['class'][] = "bdt-navbar-transparent bdt-{$rooten_transparent}";
	}

	$rooten_navbar_attrs = [ 'class' => 'bdt-navbar' ];

	
	if (is_admin_bar_showing()) {
		$rooten_offset = '32';
	} else {
		$rooten_offset = 0;
	}

	// Sticky
	if ($rooten_sticky != false and $rooten_sticky != 'no') {
	    $rooten_container['bdt-sticky'] = json_encode(array_filter([
			'media'       => 768,
			'show-on-up'  => $rooten_sticky == 'smart',
			'animation'   => $rooten_transparent || $rooten_sticky == 'smart' ? 'bdt-animation-slide-top' : '',
			'top'         => $rooten_transparent ? '!.js-sticky' : 1,
			'offset' 	  => $rooten_offset,
			'clsActive'   => 'bdt-active bdt-navbar-sticky',
			'clsInactive' => $rooten_transparent ? "bdt-navbar-transparent bdt-{$rooten_transparent}" : false,
	    ]));
	}
?>

<?php if ($rooten_transparent) : ?>
<div<?php rooten_helper::attrs(['class' => 'js-sticky']) ?>>
<?php endif; ?>
	<div<?php rooten_helper::attrs(['class' => $rooten_class]) ?>>
		<?php if ($rooten_layout == 'horizontal-left' or $rooten_layout == 'horizontal-center' or $rooten_layout == 'horizontal-right') : ?>
		    <div<?php rooten_helper::attrs($rooten_container, $rooten_container_media) ?>>
		        <div class="bdt-container <?php echo ($rooten_fullwidth) ? 'bdt-container-expand' : '' ?>">
		            <nav<?php rooten_helper::attrs($rooten_navbar_attrs) ?>>

		                <div class="bdt-navbar-left">
		                    <?php get_template_part( 'template-parts/logo-default' ); ?>
		                    <?php if ($rooten_layout == 'horizontal-left' and has_nav_menu( 'primary' ) ) : ?>
		                        <?php get_template_part( 'template-parts/menu-primary' ); ?>
		                        <?php if ($rooten_search == 'menu' ) : ?>
		                        	<div class="bdt-navbar-item">
		                            	<?php get_template_part( 'template-parts/search' ); ?>
		                            </div>
		                        <?php endif ?>
		                    <?php endif ?>
		                </div>

		                <?php if ($rooten_layout == 'horizontal-center' && has_nav_menu( 'primary' ) ) : ?>
		                <div class="bdt-navbar-center">
		                    <?php get_template_part( 'template-parts/menu-primary' ); ?>
		                    <?php if ($rooten_search == 'menu' ) : ?>
		                    	<div class="bdt-navbar-item">
		                        	<?php get_template_part( 'template-parts/search' ); ?>
		                        </div>
		                    <?php endif ?>
		                </div>
		                <?php endif ?>

		                <?php if (is_active_sidebar( 'headerbar') || $rooten_layout == 'horizontal-right' || $rooten_search == 'header' || has_nav_menu( 'primary') || $rooten_cart == 'header') : ?>
		                <div class="bdt-navbar-right">
		                    <?php if ($rooten_layout == 'horizontal-right' && has_nav_menu( 'primary' ) ) : ?>
		                        <?php get_template_part( 'template-parts/menu-primary' ); ?>
		                    <?php endif ?> 
							
		                    <?php if ($rooten_layout == 'horizontal-right' && $rooten_search == 'menu' ) : ?>
		                    	<div class="bdt-navbar-item">
		                        	<?php get_template_part( 'template-parts/search' ); ?>
		                        </div>
		                    <?php endif ?>

							<?php if (is_active_sidebar( 'headerbar' ) ) : ?>
								<div class="bdt-navbar-item">
			                    	<?php dynamic_sidebar( 'headerbar') ?>
								</div>
							<?php endif; ?>

							<?php if (($rooten_layout == 'horizontal-left' || $rooten_layout == 'horizontal-center' || $rooten_layout == 'horizontal-right') && $rooten_search == 'header' ) : ?>
								<div class="bdt-navbar-item">
							    	<?php get_template_part( 'template-parts/search' ); ?>
							    </div>
							<?php endif ?>

							<?php if (($rooten_layout == 'horizontal-left' || $rooten_layout == 'horizontal-center' || $rooten_layout == 'horizontal-right') && $rooten_cart == 'header' ) : ?>
								<div class="bdt-navbar-item">
							    	<?php get_template_part( 'template-parts/woocommerce-cart' ); ?>
							    </div>
							<?php endif ?>
		                </div>
		                <?php endif ?>
		            </nav>
		        </div>
		    </div>
			<?php //endif ?>		
		<?php elseif (in_array($rooten_layout, ['stacked-center-a', 'stacked-center-b', 'stacked-center-split'])) : ?>
		    <?php if ($rooten_layout != 'stacked-center-split' || $rooten_layout == 'stacked-center-a' && is_active_sidebar( 'headerbar' ) ) : ?>
		    <div class="tm-headerbar-top">
		        <div class="bdt-container<?php echo ($rooten_fullwidth) ? ' bdt-container-expand' : '' ?>">

		            <div class="bdt-text-center bdt-position-relative">
		                <?php get_template_part( 'template-parts/logo-default' ); ?>
		               <?php if ($rooten_layout == 'stacked-center-a') : ?> 
		                <div>
		                	<img class="center-logo-art" src="<?php echo get_template_directory_uri(); ?>/images/header-art-01.svg" width="250">
		                </div>
		            	<?php endif; ?>
		            </div>

		            <?php if ($rooten_layout == 'stacked-center-a' && is_active_sidebar( 'headerbar' ) ) : ?>
		            <div class="tm-headerbar-stacked bdt-grid-medium bdt-child-width-auto bdt-flex-center bdt-flex-middle bdt-margin-medium-top" bdt-grid>
		                <?php dynamic_sidebar( 'headerbar') ?>
		            </div>
		            <?php endif ?>

		        </div>
		    </div>
		    <?php endif ?>

		    <?php if (has_nav_menu( 'primary' ) ) : ?>
		    <div<?php rooten_helper::attrs($rooten_container) ?>>

		        <div class="bdt-container <?php echo ($rooten_fullwidth) ? 'bdt-container-expand' : '' ?>">
		            <nav<?php rooten_helper::attrs($rooten_navbar_attrs) ?>>

		                <div class="bdt-navbar-center">
		                    <?php get_template_part( 'template-parts/menu-primary' ); ?>
		                </div>

		            </nav>
		        </div>

		    </div>
		    <?php endif ?>

		    <?php if (in_array($rooten_layout, ['stacked-center-b', 'stacked-center-split']) && is_active_sidebar( 'headerbar' ) ) : ?>
		    <div class="tm-headerbar-bottom">
		        <div class="bdt-container <?php echo ($rooten_fullwidth) ? 'bdt-container-expand' : '' ?>">
		            <div class="bdt-grid-medium bdt-child-width-auto bdt-flex-center bdt-flex-middle" bdt-grid>
		                <?php dynamic_sidebar( 'headerbar') ?>
		            </div>
		        </div>
		    </div>
		    <?php endif ?>
		<?php elseif ($rooten_layout == 'stacked-left-a' || $rooten_layout == 'stacked-left-b') : ?>
		    <?php if ($rooten_logo || is_active_sidebar( 'headerbar' ) ) : ?>
		    <div class="tm-headerbar-top">
		        <div class="bdt-container <?php echo ($rooten_fullwidth) ? 'bdt-container-expand' : '' ?> bdt-flex bdt-flex-middle">

		            <?php get_template_part( 'template-parts/logo-default' ); ?>

		            <?php if (is_active_sidebar( 'headerbar') or $rooten_search) : ?>
		            <div class="bdt-margin-auto-left">
		                <div class="bdt-grid-medium bdt-child-width-auto bdt-flex-middle" bdt-grid>
		                    
							<?php if ($rooten_layout == 'stacked-left-a') : ?>
		                    	<?php dynamic_sidebar( 'headerbar') ?>
		                    <?php endif ?>
		                    

		                    <?php if ($rooten_search == 'header' ) : ?>
		                    	<div>
		                        	<?php get_template_part( 'template-parts/search' ); ?>
		                        </div>
		                    <?php endif ?>

	                    	<?php if ($rooten_cart == 'header' ) : ?>
								<div>
							    	<?php get_template_part( 'template-parts/woocommerce-cart' ); ?>
							    </div>
							<?php endif ?>
		                </div>
		            </div>
		            <?php endif ?>

		        </div>
		    </div>
		    <?php endif ?>

		    <?php if (has_nav_menu( 'primary' ) ) : ?>
			    <div<?php rooten_helper::attrs($rooten_container) ?>>
			        <div class="bdt-container <?php echo ($rooten_fullwidth) ? 'bdt-container-expand' : '' ?>">
			            <nav<?php rooten_helper::attrs($rooten_navbar_attrs) ?>>

			                <?php if ($rooten_layout == 'stacked-left-a') : ?>
			                <div class="bdt-navbar-left">
			                    <?php get_template_part( 'template-parts/menu-primary' ); ?>

			                    <?php if ($rooten_search == 'menu' ) : ?>
			                    	<div class="bdt-navbar-item">
			                        	<?php get_template_part( 'template-parts/search' ); ?>
			                        </div>
			                    <?php endif ?>
			                </div>
			                <?php endif ?>

			                <?php if ($rooten_layout == 'stacked-left-b') : ?>
			                <div class="bdt-navbar-left bdt-flex-auto">
			                    <?php get_template_part( 'template-parts/menu-primary' ); ?>

            					<?php if ($rooten_layout == 'stacked-left-b') : ?>
            						<div class="bdt-margin-auto-left bdt-navbar-item">
                                		<?php dynamic_sidebar( 'headerbar') ?>
                                	</div>
                                <?php endif ?>

			                    <?php if ($rooten_search == 'menu' ) : ?>
			                    	<div class="bdt-margin-auto-left bdt-navbar-item">
			                        	<?php get_template_part( 'template-parts/search' ); ?>
			                        </div>
			                    <?php endif ?>
			                </div>
			                <?php endif ?>

			            </nav>
			        </div>
			    </div>
		    <?php endif ?>
		<?php elseif ($rooten_layout == 'toggle-offcanvas' || $rooten_layout == 'toggle-modal') : ?>
		    <div<?php rooten_helper::attrs($rooten_container) ?>>
		        <div class="bdt-container <?php echo ($rooten_fullwidth) ? 'bdt-container-expand' : '' ?>">
		            <nav<?php rooten_helper::attrs($rooten_navbar_attrs) ?>>

		            <?php if ($rooten_logo) : ?>
		            <div class="bdt-navbar-left">
		                <?php get_template_part( 'template-parts/logo-default' ); ?>
		            </div>
		            <?php endif ?>

		            <?php if (has_nav_menu( 'primary' ) ) : ?>
		            <div class="bdt-navbar-right">
		                <a class="bdt-navbar-toggle" href="#" bdt-toggle="target: !.bdt-navbar-container + [bdt-offcanvas], [bdt-modal]">
		                    <?php if ($rooten_menu_text) : ?>
		                    <span class="bdt-margin-small-right"><?php esc_html_e( 'Menu', 'rooten') ?></span>
		                    <?php endif ?>
		                    <div bdt-navbar-toggle-icon></div>
		                </a>
		            </div>
		            <?php endif ?>

		            </nav>
		        </div>
		    </div>
			<?php if ($rooten_layout == 'toggle-offcanvas' && (has_nav_menu( 'primary') || is_active_sidebar( 'headerbar' ) )) : ?>
			    <div bdt-offcanvas="flip: true" mode="<?php echo esc_html($rooten_offcanvas_mode); ?>" overlay>
			        <div class="bdt-offcanvas-bar">

			            <?php
			            	if(has_nav_menu( 'primary' ) ) {
			            		wp_nav_menu( array(
			            			'theme_location' => 'primary',
			            			'container'      => false,
			            			'items_wrap'     => '<ul id="%1$rooten_s" class="%2$rooten_s" bdt-nav>%3$rooten_s</ul>',
			            			'menu_id'        => 'nav-offcanvas',
			            			'menu_class'     => 'bdt-nav bdt-nav-default bdt-nav-parent-icon',
			            			'echo'           => true,
			            			'before'         => '',
			            			'after'          => '',
			            			'link_before'    => '',
			            			'link_after'     => '',
			            			'depth'          => 0,
			            			)
			            		); 
			            	}
			            ?>

    		            <?php if ($rooten_search == 'menu' ) : ?>
    		            	<div class="bdt-margin-auto-left bdt-navbar-item">
    		                	<?php get_template_part( 'template-parts/search' ); ?>
    		                </div>
    		            <?php endif ?>

	                    <?php if (is_active_sidebar( 'headerbar' ) ) : ?>
	                    <div class="bdt-margin-large-top">
	                        <?php dynamic_sidebar( 'headerbar') ?>
	                    </div>
	                    <?php endif ?>

	                    <?php if ($rooten_search == 'header' ) : ?>
	                    	<div class="bdt-margin-auto-left bdt-navbar-item">
	                        	<?php get_template_part( 'template-parts/search' ); ?>
	                        </div>
	                    <?php endif ?>

			        </div>
			    </div>
		    <?php elseif ($rooten_layout == 'toggle-modal' && (has_nav_menu( 'primary') || is_active_sidebar( 'headerbar' ) )) : ?>
			    <div class="bdt-modal-full" bdt-modal>
			        <div class="bdt-modal-dialog bdt-modal-body">
			            <button class="bdt-modal-close-full" type="button" bdt-close></button>
			            <div class="bdt-flex bdt-flex-center bdt-flex-middle bdt-text-center" bdt-height-viewport>
			                <div>

			                    <?php
	        		            	if(has_nav_menu( 'primary' ) ) {
	        		            		wp_nav_menu( array(
	        		            			'theme_location' => 'primary',
	        		            			'container'      => false,
	        		            			'items_wrap'     => '<ul id="%1$rooten_s" class="%2$rooten_s" bdt-nav>%3$rooten_s</ul>',
	        		            			'menu_id'        => 'nav-offcanvas',
	        		            			'menu_class'     => 'bdt-nav bdt-nav-primary bdt-nav-center bdt-nav-parent-icon',
	        		            			'echo'           => true,
	        		            			'before'         => '',
	        		            			'after'          => '',
	        		            			'link_before'    => '',
	        		            			'link_after'     => '',
	        		            			'depth'          => 0,
	        		            			)
	        		            		); 
	        		            	}
	        		            ?>

	        		            <?php if ($rooten_search == 'menu' ) : ?>
	        		            	<div class="bdt-margin-auto-left bdt-navbar-item">
	        		                	<?php get_template_part( 'template-parts/search' ); ?>
	        		                </div>
	        		            <?php endif ?>

			                    <?php if (is_active_sidebar( 'headerbar' ) ) : ?>
			                    <div class="bdt-margin-large-top">
			                        <?php dynamic_sidebar( 'headerbar') ?>
			                    </div>
			                    <?php endif ?>

			                    <?php if ($rooten_search == 'header' ) : ?>
			                    	<div class="bdt-margin-auto-left bdt-navbar-item">
			                        	<?php get_template_part( 'template-parts/search' ); ?>
			                        </div>
			                    <?php endif ?>

			                </div>
			            </div>
			        </div>
			    </div>
			<?php endif ?>
		
		<?php elseif ($rooten_layout == 'side-left' || $rooten_layout == 'side-right') : ?>
			<?php 
				$rooten_sidebar_position = ($rooten_layout == 'side-left') ? ' bdt-position-left' : ' bdt-position-right';
				//$rooten_sidebar_class = [];
				$rooten_sidebar_class = ['class' => ['bdt-position-fixed', 'bdt-position-z-index', 'bdt-padding', 'bdt-width-medium']];
				$rooten_sidebar_class['class'][] = $rooten_sidebar_position;
				$rooten_sidebar_class['class'][] = ($rooten_bg_style) ? 'bdt-background-'.$rooten_bg_style : '';
				$rooten_sidebar_class['class'][] = ($rooten_shadow) ? 'bdt-box-shadow-'.$rooten_shadow : '';
			?>
		    <div<?php rooten_helper::attrs($rooten_sidebar_class, $rooten_container_media) ?>>
		        <div class="">
					
					<div class="">
			        	<?php if ($rooten_logo) : ?>
			        	<div class="bdt-text-center">
			        	    <?php get_template_part( 'template-parts/logo-default' ); ?>
			        	</div>
			        	<?php endif ?>

			            <?php
			            	if(has_nav_menu( 'primary' ) ) {
			            		wp_nav_menu( array(
			            			'theme_location' => 'primary',
			            			'container'      => false,
			            			'items_wrap'     => '<ul id="%1$rooten_s" class="%2$rooten_s" bdt-nav>%3$rooten_s</ul>',
			            			'menu_id'        => 'nav-offcanvas',
			            			'menu_class'     => 'bdt-nav bdt-nav-default bdt-nav-parent-icon bdt-margin-medium-top',
			            			'echo'           => true,
			            			'before'         => '',
			            			'after'          => '',
			            			'link_before'    => '',
			            			'link_after'     => '',
			            			'depth'          => 0,
			            			)
			            		); 
			            	}
			            ?>

			            <?php if ($rooten_search == 'menu' ) : ?>
			            	<div class="bdt-margin-auto-left bdt-margin-medium-top">
			                	<?php get_template_part( 'template-parts/search' ); ?>
			                </div>
			            <?php endif ?>

	                </div>



                    <?php //if ($rooten_search == 'header' ) : ?>
                    	<div class="tm-side-bottom bdt-text-uppercase bdt-text-small bdt-margin-large-top">

                    		<?php if (is_active_sidebar( 'headerbar' ) ) : ?>
                    		<div class="bdt-margin-medium-bottom">
                    		    <?php dynamic_sidebar( 'headerbar') ?>
                    		</div>
                    		<?php endif ?>
							
							<div class="bdt-margin-small-bottom bdt-grid-divider bdt-grid-small" bdt-grid>
	                    		<?php if ($rooten_cart == 'header' ) : ?>
	                    			<div class="tm-wpml">
	                    		    	<?php get_template_part( 'template-parts/toolbars/wpml' ); ?>
	                    		    </div>
	                    		<?php endif ?>

	                    		<?php if ($rooten_cart == 'header' ) : ?>
	                    			<div class="">
	                    		    	<?php get_template_part( 'template-parts/woocommerce-cart' ); ?>
	                    		    </div>
	                    		<?php endif ?>

			                    <?php if ($rooten_search == 'header' ) : ?>
			                    	<div class="">
			                        	<?php get_template_part( 'template-parts/search' ); ?>
			                        </div>
			                    <?php endif ?>
							</div>

                        	<?php if( get_theme_mod( 'rooten_copyright_text_custom_show' ) ) : ?>
								<div class="copyright-txt"><?php echo wp_kses_post( get_theme_mod( 'rooten_copyright_text_custom' ) ); ?></div>
							<?php else : ?>								
								<div class="copyright-txt">&copy; <?php esc_html_e( 'Copyright', 'rooten') ?> <?php echo esc_html(date("Y ")); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' );?>"> <?php echo esc_html( bloginfo( 'name' ) ); ?> </a></div>
							<?php endif; ?>
                        </div>
                    <?php //endif ?>



		        </div>
		    </div>
		<?php endif ?>

		<?php if ($rooten_shadow == 'special') : ?>
			<div class="tm-header-shadow">
				<div></div>
			</div>
		<?php endif; ?>
	</div>
<?php if ($rooten_transparent) : ?>
</div>
<?php endif; ?>