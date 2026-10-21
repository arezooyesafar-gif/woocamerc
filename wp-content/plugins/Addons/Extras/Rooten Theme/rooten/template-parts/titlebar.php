<?php

$rooten_id             = 'tm-titlebar';
$rooten_titlebar_show  = rwmb_meta('rooten_titlebar');
$rooten_class          = '';
$rooten_section_media  = [];
$rooten_section_image  = '';
$rooten_layout         = get_post_meta( get_the_ID(), 'rooten_titlebar_layout', true );
$rooten_metabox_layout = (!empty($rooten_layout) and $rooten_layout != 'default') ? true : false;
$rooten_position       = (get_post_meta( get_the_ID(), 'rooten_page_layout', true )) ? get_post_meta( get_the_ID(), 'rooten_page_layout', true ) : get_theme_mod( 'rooten_page_layout', 'sidebar-right' );

if ($rooten_metabox_layout) {
    $rooten_bg_style = get_post_meta( get_the_ID(), 'rooten_titlebar_bg_style', true );
    $rooten_bg_style = ( !empty($rooten_bg_style) ) ? $rooten_bg_style : get_theme_mod( 'rooten_titlebar_bg_style' );
    $rooten_width    = get_post_meta( get_the_ID(), 'rooten_titlebar_width', true );
    $rooten_padding  = get_post_meta( get_the_ID(), 'rooten_titlebar_padding', true );
    $rooten_text     = get_post_meta( get_the_ID(), 'rooten_titlebar_txt_style', true );
} else {
    $rooten_bg_style = get_theme_mod( 'rooten_titlebar_bg_style', 'muted' );
    $rooten_width    = get_theme_mod( 'rooten_titlebar_width', 'default' );
    $rooten_padding  = get_theme_mod( 'rooten_titlebar_padding', 'medium' );
    $rooten_text     = get_theme_mod( 'rooten_titlebar_txt_style' );
}

if (is_array($rooten_class)) {
	$rooten_class = implode(' ', array_filter($rooten_class));
}     

if ($rooten_metabox_layout) {
    $rooten_section_images = rwmb_meta( 'rooten_titlebar_bg_img', "type=image_advanced&size=standard" );
    if ( is_array($rooten_section_images) ) {
	    foreach ( $rooten_section_images as $rooten_image ) { 
	        $rooten_section_image = esc_url($rooten_image["url"]);
	    }
	}
} else {
    $rooten_section_image         = get_theme_mod( 'rooten_titlebar_bg_img' );
}

// Image
if ($rooten_section_image &&  $rooten_bg_style == 'media') {
    $rooten_section_media['style'][] = "background-image: url('{$rooten_section_image}');";
    $rooten_section_media['class'][] = 'bdt-background-norepeat';
}


$rooten_class   = ['tm-titlebar', 'bdt-section', $rooten_class];

$rooten_class[] = ($rooten_bg_style) ? 'bdt-section-'.$rooten_bg_style : '';
$rooten_class[] = ($rooten_text) ? 'bdt-'.$rooten_text : '';
if ($rooten_padding != 'none') {
    $rooten_class[]       = ($rooten_padding) ? 'bdt-section-'.$rooten_padding : '';
} elseif ($rooten_padding == 'none') {
    $rooten_class[]       = ($rooten_padding) ? 'bdt-padding-remove-vertical' : '';
}



if ( $rooten_titlebar_show !== 'hide') : ?>

	<?php 
		global $post;
		$rooten_blog_title        = get_theme_mod('rooten_blog_title', esc_html__('Blog', 'rooten'));
		$rooten_woocommerce_title = get_theme_mod('rooten_woocommerce_title', esc_html__('Shop', 'rooten'));
		$rooten_titlebar_global   = get_theme_mod('rooten_titlebar_layout', 'left');
		$rooten_titlebar_metabox  = get_post_meta( get_the_ID(), 'rooten_titlebar_layout', true);
		$rooten_title             = get_the_title();

	?>

	<?php if( is_object($post) && !is_archive() &&!is_search() && !is_404() && !is_author() && !is_home() && !is_page() ) { ?>

		<?php if($rooten_titlebar_metabox != 'default' && !empty($rooten_titlebar_metabox)) { ?>

			<?php  if ($rooten_titlebar_metabox == 'left' or $rooten_titlebar_metabox == 'center' or $rooten_titlebar_metabox == 'right') { ?>
				<div<?php rooten_helper::attrs(['id' => $rooten_id, 'class' => $rooten_class], $rooten_section_media); ?>>
					<div<?php rooten_helper::container(); ?>>
						<div<?php rooten_helper::grid(); ?>>
							<div id="title" class="bdt-width-expand<?php echo ($rooten_titlebar_metabox == 'center')?' bdt-text-center':''; ?>">
								<h1 class="bdt-margin-small-bottom"><?php echo wp_kses_post( $rooten_title ); ?></h1>
								<?php rooten_breadcrumbs($rooten_titlebar_global); ?>
							</div>
							<?php if ($rooten_titlebar_metabox != 'center') : ?>
							<div class="bdt-margin-auto-left bdt-position-relative bdt-width-small bdt-visible@s">
								<div class="bdt-position-center-right">
									<a class="bdt-button-text bdt-link-reset" onclick="history.back()"><span class="bdt-margin-small-right" bdt-icon="icon: arrow-left"></span> <?php esc_html_e('Back', 'rooten'); ?></a>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php } elseif (rwmb_meta('rooten_titlebar') == 'notitle') { ?>
				<div id="notitlebar" class="titlebar-no"></div>
			<?php } ?>

		<?php } else { ?>
				<?php
					// Define the Title for different Pages
					if ( is_home() ) { $rooten_title = $rooten_blog_title; }
					elseif( is_search() ) { 	
						$rooten_allsearch = new WP_Query("s=$s&showposts=-1"); 
						$rooten_count = $rooten_allsearch->post_count; 
						wp_reset_postdata();
						$rooten_title = $rooten_count . ' '; 
						$rooten_title .= esc_html__('Search results for:', 'rooten');
						$rooten_title .= ' ' . get_search_query();
					}
					elseif( class_exists('Woocommerce') && is_woocommerce() ) { $rooten_title = $rooten_woocommerce_title; }
					elseif( is_archive() ) { 
						if (is_category()) { 	$rooten_title = single_cat_title('',false); }
						elseif( is_tag() ) { 	$rooten_title = esc_html__('Posts Tagged:', 'rooten') . ' ' . single_tag_title('',false); }
						elseif (is_day()) { 	$rooten_title = esc_html__('Archive for', 'rooten') . ' ' . get_the_time('F jS, Y'); }
						elseif (is_month()) { 	$rooten_title = esc_html__('Archive for', 'rooten') . ' ' . get_the_time('F Y'); }
						elseif (is_year()) { 	$rooten_title = esc_html__('Archive for', 'rooten') . ' ' . get_the_time('Y'); }
						elseif (is_author()) { 	$rooten_title = esc_html__('Author Archive for', 'rooten') . ' ' . get_the_author(); }
						elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { $rooten_title = esc_html__('Blog Archives', 'rooten'); }
						else{
							$rooten_title = single_term_title( "", false );
							if ( $rooten_title == '' ) { // Fix for templates that are archives
								$rooten_post_id = $post->ID;
								$rooten_title = get_the_title($rooten_post_id);
							} 
						}
					}
					elseif( is_404() ) { $rooten_title = esc_html__('Oops, this Page could not be found.', 'rooten'); }
					elseif( get_post_type() == 'post' ) { $rooten_title = $rooten_blog_title; }
					else { $rooten_title = get_the_title(); }
				?>

				<div<?php rooten_helper::attrs(['id' => $rooten_id, 'class' => $rooten_class], $rooten_section_media); ?>>
					<div<?php rooten_helper::container(); ?>>
						<div<?php rooten_helper::grid(); ?>>
							<div id="title" class="<?php echo ($rooten_titlebar_metabox == 'center')?'bdt-text-center':''; ?>">
								<h1 class="bdt-margin-small-bottom"><?php echo wp_kses_post($rooten_title); ?></h1>
								<?php rooten_breadcrumbs($rooten_titlebar_global); ?>
							</div>
							<?php if ($rooten_titlebar_metabox != 'center') :?>
							<div class="bdt-margin-auto-left bdt-position-relative bdt-width-small bdt-visible@s">
								<div class="bdt-position-center-right">
									<a class="bdt-button-text bdt-link-reset" onclick="history.back()"><span class="bdt-margin-small-right" bdt-icon="icon: arrow-left"></span> <?php esc_html_e('Back', 'rooten'); ?></a>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>

		<?php } // End Else ?>

	<?php } else { // If no post page ?>
		
		<?php if($rooten_titlebar_metabox != 'default' && !empty($rooten_titlebar_metabox)) { ?>

			<?php  if ($rooten_titlebar_metabox == 'left' or $rooten_titlebar_metabox == 'center' or $rooten_titlebar_metabox == 'right') { ?>
				<div<?php rooten_helper::attrs(['id' => $rooten_id, 'class' => $rooten_class], $rooten_section_media); ?>>
					<div<?php rooten_helper::container(); ?>>
						<div<?php rooten_helper::grid(); ?>>
							<div id="title" class="bdt-width-expand<?php echo ($rooten_titlebar_metabox == 'center')?' bdt-text-center':''; ?>">
								<h1 class="bdt-margin-small-bottom"><?php echo wp_kses_post($rooten_title); ?></h1>
								<?php rooten_breadcrumbs($rooten_titlebar_global); ?>
							</div>
							<?php if ($rooten_titlebar_metabox != 'center') : ?>
							<div class="bdt-margin-auto-left bdt-position-relative bdt-width-small bdt-visible@s">
								<div class="bdt-position-center-right">
									<a class="bdt-button-text bdt-link-reset" onclick="history.back()"><span class="bdt-margin-small-right" bdt-icon="icon: arrow-left"></span> <?php esc_html_e('Back', 'rooten'); ?></a>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php } elseif (rwmb_meta('rooten_titlebar') == 'notitle') { ?>
				<div id="notitlebar" class="titlebar-no"></div>
			<?php } ?>

		<?php } else { ?>

			<?php
				// Define the Title for different Pages
				if ( is_home() ) { $rooten_title = $rooten_blog_title; }
				elseif( is_search() ) { 	
					$rooten_allsearch = new WP_Query("s=$s&showposts=-1"); 
					$rooten_count = $rooten_allsearch->post_count; 
					wp_reset_postdata();
					$rooten_title = $rooten_count . ' '; 
					$rooten_title .= esc_html__('Search results for:', 'rooten');
					$rooten_title .= ' ' . get_search_query();
				}
				elseif( class_exists('Woocommerce') && is_woocommerce() ) { $rooten_title = $rooten_woocommerce_title; }
				elseif( is_archive() ) { 
					if (is_category()) { 	$rooten_title = single_cat_title('',false); }
					elseif( is_tag() ) { 	$rooten_title = esc_html__('Posts Tagged:', 'rooten') . ' ' . single_tag_title('',false); }
					elseif (is_day()) { 	$rooten_title = esc_html__('Archive for', 'rooten') . ' ' . get_the_time('F jS, Y'); }
					elseif (is_month()) { 	$rooten_title = esc_html__('Archive for', 'rooten') . ' ' . get_the_time('F Y'); }
					elseif (is_year()) { 	$rooten_title = esc_html__('Archive for', 'rooten') . ' ' . get_the_time('Y'); }
					elseif (is_author()) { 	$rooten_title = esc_html__('Author Archive for', 'rooten') . ' ' . get_the_author(); }
					elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { $rooten_title = esc_html__('Blog Archives', 'rooten'); }
					else{
						$rooten_title = single_term_title( "", false );
						if ( $rooten_title == '' ) { // Fix for templates that are archives
							$rooten_post_id = $post->ID;
							$rooten_title = get_the_title($rooten_post_id);
						} 
					}
				}
				elseif( is_404() ) { $rooten_title = esc_html__('Oops, this Page could not be found.', 'rooten'); }
				elseif( get_post_type() == 'post' ) { $rooten_title = $rooten_blog_title; }
				else { $rooten_title = get_the_title(); }
			?>
			
			<?php if($rooten_titlebar_global == 'left' or $rooten_titlebar_global == 'center' or $rooten_titlebar_global == 'right') { ?>
				<div<?php rooten_helper::attrs(['id' => $rooten_id, 'class' => $rooten_class], $rooten_section_media); ?>>
					<div<?php rooten_helper::container(); ?>>
						<div<?php rooten_helper::grid(); ?>>
							<div id="title" class="bdt-width-expand<?php echo ($rooten_titlebar_global == 'center')?' bdt-text-center':''; ?>">
								<h1 class="bdt-margin-small-bottom"><?php echo wp_kses_post($rooten_title); ?></h1>
								<?php rooten_breadcrumbs($rooten_titlebar_global); ?>
							</div>
							<?php if ($rooten_titlebar_global != 'center') : ?>
							<div class="bdt-margin-auto-left bdt-position-relative bdt-width-small bdt-visible@s">
								<div class="bdt-position-center-right">
									<a class="bdt-button-text bdt-link-reset" onclick="history.back()"><span class="bdt-margin-small-right" bdt-icon="icon: arrow-left"></span> <?php esc_html_e('Back', 'rooten'); ?></a>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php } elseif($rooten_titlebar_global == 'notitle') { ?>
				<div id="notitlebar" class="titlebar-no"></div>
			<?php } ?>	
		<?php } ?>

	<?php } // End Else ?>

<?php endif;