<?php if(!is_single() or is_single()) :?>
	<?php if(!is_single()) :?>
	<h3 class="bdt-portfolio-title bdt-margin-remove-top bdt-margin-small-bottom">
	    <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="bdt-link-reset"><?php the_title(); ?></a>
	    <?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
	            <?php printf( '<span class="sticky-post featured">%s</span>', esc_html__( 'Featured', 'rooten') ); ?>
	    <?php endif; ?>
	</h3>
	
	<?php elseif(is_single()) :?>
		<div class="bdt-article-title">
		    <h1 class="bdt-margin-remove-top bdt-heading-bullet">
		    	<?php the_title(); ?>
		    </h1>
		</div>
	<?php endif ?>
<?php endif ?>