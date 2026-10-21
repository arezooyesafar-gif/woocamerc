<?php 
/* Template Name: Blank Page */

get_header();

$rooten_bg_style         = get_theme_mod( 'rooten_body_bg_style');
//$mainbody_width = get_theme_mod( 'rooten_body_width');
$rooten_text             = get_theme_mod( 'rooten_body_txt_style' );


$rooten_class            = ['bdt-section', 'bdt-padding-remove-vertical'];
$rooten_class[]          = ($rooten_bg_style) ? 'bdt-section-'.$rooten_bg_style : '';
$rooten_class[]          = ($rooten_text) ? 'bdt-'.$rooten_text : '';


?>



<div<?php rooten_helper::attrs(['id' => $id, 'class' => $rooten_class]); ?>>
	<div class="">
		<main class="tm-home">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<?php the_content(); ?>

			<?php endwhile; endif; ?>
		</main> <!-- end main -->

	</div> <!-- end container -->
</div> <!-- end tm main -->
	
<?php get_footer(); ?>