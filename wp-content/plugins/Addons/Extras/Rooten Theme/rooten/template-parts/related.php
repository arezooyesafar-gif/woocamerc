<?php if(get_theme_mod('rooten_related_post')) { 
	//for use in the loop, list 5 post titles related to first tag on current post
	$rooten_tags = wp_get_post_tags($post->ID);
	if($rooten_tags) { ?>	
	<hr class="bdt-divider-icon">
	<div id="related-posts">
		<h3><?php esc_html_e('Related Posts', 'rooten'); ?></h3>
		<ul class="bdt-list bdt-list-line">
			<?php  $rooten_first_tag = $rooten_tags[0]->term_id;
			  $rooten_args=array(
			    'tag__in' => array($rooten_first_tag),
			    'post__not_in' => array($post->ID),
			    'showposts'=>4
			   );
			  $rooten_my_query = new WP_Query($rooten_args);
			  if( $rooten_my_query->have_posts() ) {
			    while ($rooten_my_query->have_posts()) : $rooten_my_query->the_post(); ?>
			      <li><a href="<?php the_permalink() ?>" rel="bookmark" title="Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a> <span class="bdt-article-meta"><?php the_time(get_option('date_format')); ?></span></li>
			      <?php
			    endwhile;
			    wp_reset_postdata();
			  } ?>
		</ul>
	</div>
	
	<?php } // end if $rooten_tags 
} ?>