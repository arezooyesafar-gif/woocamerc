<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="wrapper pooshe-app <?php if (is_account_page() && !is_user_logged_in()){ echo 'on-login-page'; }?>">

    <header class="pooshe-header" <?php if (is_account_page() && !is_user_logged_in()){ echo 'style="display: none;"'; }?>>
		<?php
		if ( !function_exists('elementor_theme_do_location') || !elementor_theme_do_location('header') ) {
			get_template_part('views/layouts/layout', 'header');
		}
		?>
    </header>

	<?php if ( function_exists('elementor_theme_do_location') ) { ?>
        <header class="responsive-header" >
			<?php elementor_theme_do_location('responsive-header'); ?>
        </header>
	<?php } ?>