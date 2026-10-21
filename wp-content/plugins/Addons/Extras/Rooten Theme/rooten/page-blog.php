<?php
/* Template Name: Blog */

	get_header(); 

	// Layout
	$rooten_position = (get_post_meta( get_the_ID(), 'rooten_page_layout', true )) ? get_post_meta( get_the_ID(), 'rooten_page_layout', true ) : get_theme_mod( 'rooten_page_layout', 'sidebar-right' );

	$rooten_grid_class = ['bdt-grid'];


	$rooten_large        = rwmb_meta( 'rooten_blog_columns' );
	$rooten_medium       = rwmb_meta( 'rooten_blog_columns_medium' );
	$rooten_small        = rwmb_meta( 'rooten_blog_columns_small' );
	
	$rooten_grid_class[] = ($rooten_large != null) ? 'bdt-child-width-1-'.$large.'@l' : 'bdt-child-width-1-3@l' ;
	$rooten_grid_class[] = ($rooten_medium != null) ? 'bdt-child-width-1-'.$rooten_medium.'@m' : 'bdt-child-width-1-2@m';
	$rooten_grid_class[] = ($rooten_small != null) ? 'bdt-child-width-1-'.$rooten_small : 'bdt-child-width-1-1';
	$rooten_column_gap   = rwmb_meta( 'rooten_blog_columns_gap');
	$rooten_limit        = rwmb_meta( 'rooten_blog_limit');
	$rooten_grid_class[] = ($rooten_column_gap) ? 'bdt-grid-'.$rooten_column_gap : '';


	global $rooten_wp_query;
	// Pagination fix to work when set as Front Page
	// $rooten_paged = get_query_var('paged') ? get_query_var('paged') : 1;
	if ( get_query_var('paged') ) { $rooten_paged = get_query_var('paged'); } elseif ( get_query_var('page') ) { $rooten_paged = get_query_var('page'); } else { $rooten_paged = 1; }	

	// Get Categories
	$rooten_categories = rwmb_meta( 'rooten_blog_categories');

	$rooten_args = array(
		'posts_per_page' => intval($rooten_limit),
		'post_status' => 'publish',
		'orderby'     => 'date',
		'order'       => 'DESC',
		'cat'         => $rooten_categories,
		'paged'       => $rooten_paged
	);
	$rooten_wp_query = new WP_Query($rooten_args);

?>

<div<?php rooten_helper::section('main'); ?>>
	<div<?php rooten_helper::container(); ?>>
		<div<?php rooten_helper::grid(); ?>>
			
			<div class="bdt-width-expand">
				<main class="tm-content">
					<?php if ($rooten_large != 1) : ?>
						<div<?php rooten_helper::attrs(['class' => $rooten_grid_class]) ?> bdt-grid>
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
								<div>
									<?php get_template_part( 'template-parts/post-format/entry', get_post_format() ); ?>
								</div>
							<?php endwhile; endif; ?>
						</div>
					<?php else : ?>
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							<?php get_template_part( 'template-parts/post-format/entry', get_post_format() ); ?>
						<?php endwhile; endif; ?>
					<?php endif; ?>

					<?php get_template_part( 'template-parts/pagination' ); ?>
				</main> <!-- end main -->
			</div> <!-- end content -->

			<?php if($rooten_position == 'sidebar-left' || $rooten_position == 'sidebar-right') : ?>
				<aside<?php echo rooten_helper::sidebar($rooten_position); ?>>
				    <?php get_sidebar(); ?>
				</aside> <!-- end aside -->
			<?php endif; ?>
			
		</div> <!-- end grid -->
	</div> <!-- end container -->
</div> <!-- end tm main -->
	
<?php get_footer(); ?>
