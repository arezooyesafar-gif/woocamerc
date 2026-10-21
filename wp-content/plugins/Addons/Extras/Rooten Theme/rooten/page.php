<?php get_header();

// Layout
$rooten_layout = get_post_meta( get_the_ID(), 'rooten_page_layout', true );
$rooten_position = (!empty($rooten_layout)) ? $rooten_layout : get_theme_mod( 'rooten_page_layout', 'sidebar-right' );

$rooten_class[] = ($rooten_layout !== 'full') ? 'bdt-container' : ''; 

?>



<div<?php rooten_helper::section('main'); ?>>
	<div<?php rooten_helper::attrs(['class' => $rooten_class]) ?>>
		<div<?php rooten_helper::grid(); ?>>
			<div class="bdt-width-expand">
				<main class="tm-content">
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

						<?php the_content(); ?>

						<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>

						<?php if(get_theme_mod('rooten_comment_show', 1) == 1 and comments_open()) { ?>
							<hr class="bdt-margin-large-top bdt-margin-large-bottom">

							<?php comments_template(); ?>
						<?php } ?>

					<?php endwhile; endif; ?>
				</main> <!-- end main -->
			</div> <!-- end content -->

			<?php if($rooten_position == 'sidebar-left' or $rooten_position == 'sidebar-right') : ?>
				<aside<?php echo rooten_helper::sidebar($rooten_position); ?>>
				    <?php get_sidebar(); ?>
				</aside> <!-- end aside -->
			<?php endif; ?>

		</div> <!-- end grid -->
	</div> <!-- end container -->
</div> <!-- end tm main -->
	
<?php get_footer(); ?>
