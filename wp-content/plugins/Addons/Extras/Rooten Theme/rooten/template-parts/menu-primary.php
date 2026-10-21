<?php
$rooten_main_menu = get_theme_mod('rooten_menu_show', true);

if ($rooten_main_menu) {

	if(has_nav_menu('primary')) {
		$rooten_navbar = wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_id'        => 'nav',
			'menu_class'     => 'bdt-navbar-nav',
			'echo'           => false,
			'before'         => '',
			'after'          => '',
			'link_before'    => '',
			'link_after'     => '',
			'depth'          => 0,
			'parent_id'      => 'tmMainMenu',
			)
		);

		$rooten_primary_menu = new rooten_nav_dom($rooten_navbar);
		echo 	$rooten_primary_menu->proccess();
	} else {
		echo '<ul class="no-menu bdt-hidden-small"><li><a href="'.admin_url('/nav-menus.php').'"><strong>NO MENU ASSIGNED</strong> <span>Go To Appearance > Menus and create a Menu</span></a></li></ul>';
	} 
}