<?php
$rooten_description = get_bloginfo( 'description', 'display' );
if ( $rooten_description || is_customize_preview() ) : ?>
	<p class="site-description"><?php echo esc_html($rooten_description); ?></p>
<?php endif; ?>