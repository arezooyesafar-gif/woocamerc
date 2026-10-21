<?php get_header(); 

// Layout
$rooten_position = (get_post_meta( get_the_ID(), 'rooten_page_layout', true )) ? get_post_meta( get_the_ID(), 'rooten_page_layout', true ) : get_theme_mod( 'rooten_page_layout', 'sidebar-right' );
$rooten_width = '1-3';
?>

<div<?php rooten_helper::section(); ?>>
	<div<?php rooten_helper::container(); ?>>
		<div<?php rooten_helper::grid(); ?>>
			<div class="bdt-width-expand">
				<main class="tm-content">
			
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						
						<?php get_template_part( 'template-parts/testimonials/entry' ); ?>
					
						<?php if(get_theme_mod('rooten_service_next_prev', 1)) { ?>

							<hr>	

							<ul class="bdt-pagination">
							    <li>
							    	<?php
							        	$rooten_pre_btn_txt = '<span class="bdt-margin-small-right" bdt-pagination-previous></span> '. esc_html__('Previous', 'rooten'); 
							        	previous_post_link('%link', "{$rooten_pre_btn_txt}", FALSE); 
							        ?>
							        
							    </li>
							    <li class="bdt-margin-auto-left">
							    	<?php $rooten_next_btn_txt = esc_html__('Next', 'rooten') . ' <span class="bdt-margin-small-left" bdt-pagination-next></span>';
		                    			next_post_link('%link', "{$rooten_next_btn_txt}", FALSE); ?>
		                    	</li>
							</ul>
						<?php } ?>
				
					<?php endwhile; endif; ?>
				</main> <!-- end main -->
			</div> <!-- end expand -->

			<?php if($rooten_position == 'sidebar-left' || $rooten_position == 'sidebar-right' || ) : ?>
				<aside<?php echo rooten_helper::sidebar($rooten_position, $rooten_width); ?>>
					<?php get_sidebar(); ?>
				</aside> <!-- end aside -->
			<?php endif; ?>
			
		</div> <!-- end grid -->
	</div> <!-- end container -->
</div> <!-- end tm main -->
	
<?php get_footer(); ?>