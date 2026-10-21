<?php get_header();

?>

<div class="tm-section bdt-section bdt-padding-remove-vertical">
			
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<?php the_content(); ?>

	<?php endwhile; endif; ?>
		
</div> <!-- end tm main -->
	
<?php get_footer(); ?>