<?php if(is_home() and get_theme_mod('rooten_blog_readmore', 1)) :?>

<p class="bdt-text-center bdt-margin-medium">
	<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="bdt-button bdt-button-text"><?php esc_html_e('Read More...', 'rooten'); ?></a>
</p>

<?php endif; ?>