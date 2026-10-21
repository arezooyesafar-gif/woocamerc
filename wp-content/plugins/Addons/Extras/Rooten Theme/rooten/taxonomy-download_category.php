<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package rooten
 */

get_header();

// Layout
$rooten_position = (get_post_meta( get_the_ID(), 'rooten_page_layout', true )) ? get_post_meta( get_the_ID(), 'rooten_page_layout', true ) : get_theme_mod( 'rooten_page_layout', 'sidebar-right' );
?>



<div<?php rooten_helper::section(); ?>>
	<div<?php rooten_helper::container(); ?>>
		<div<?php rooten_helper::grid(); ?>>
			
			<div class="bdt-width-expand">
				<main class="tm-content">

					<div class="bdt-grid bdt-child-width-1-3 tm-download-category" bdt-grid bdt-height-match="target: > div > div > .tm-edd-item-bottom-part;">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						
						<?php get_template_part( 'template-parts/download/download-grid' ); ?>
						
					<?php endwhile; endif; ?>

					</div>
					
					<?php get_template_part( 'template-parts/pagination' ); ?>
				</main> <!-- end main -->
			</div> <!-- end content -->
			
			
		</div> <!-- end grid -->
	</div> <!-- end container -->
</div> <!-- end tm main -->
	
<?php get_footer();
