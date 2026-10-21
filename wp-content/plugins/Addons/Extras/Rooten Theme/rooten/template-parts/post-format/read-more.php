<?php if(is_home() and get_theme_mod('rooten_blog_readmore', 1)) :?>

<p class="bdt-margin-medium">
	<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="bdt-button bdt-button-text tm-read-more-button"><?php esc_html_e('Read More', 'rooten'); ?> <i bdt-icon="arrow-right"></i> </a>
</p>

<?php endif; ?>