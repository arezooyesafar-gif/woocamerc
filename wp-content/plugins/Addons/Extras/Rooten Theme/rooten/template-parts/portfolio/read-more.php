<?php if(is_home() and get_theme_mod('rooten_blog_readmore', 1)) : ?>
	<p class="bdt-margin-remove">
		<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="bdt-button bdt-button-secondary"><?php esc_html_e('Read More...', 'rooten'); ?></a>
	</p>
<?php endif; ?>