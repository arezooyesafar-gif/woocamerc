<?php
	$rooten_offcanvas_style = get_theme_mod( 'rooten_mobile_offcanvas_style');
	if ( $rooten_offcanvas_style == 'modal') {
		$rooten_menu_class = 'bdt-nav bdt-nav-primary bdt-nav-center bdt-nav-parent-icon';
	} elseif ($rooten_offcanvas_style == 'offcanvas') {
		$rooten_menu_class = 'bdt-nav bdt-nav-default bdt-nav-parent-icon';
	} else {
		$rooten_menu_class = 'bdt-nav bdt-nav-parent-icon';
	}

	if (get_theme_mod('rooten_offcanvas_search', 1) and $rooten_offcanvas_style != 'modal') {
		echo '<div class="bdt-panel offcanvas-search"><div class="panel-content">';
			get_search_form();
			echo '<hr>';
		echo '</div></div>';
	}
?>

<?php 
	if(has_nav_menu('primary') and !has_nav_menu('offcanvas')) {
		$rooten_navbar = wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '<ul id="%1$s" class="%2$s" bdt-nav>%3$s</ul>',
			'menu_id'        => 'nav-offcanvas',
			'menu_class'     => $rooten_menu_class,
			'echo'           => true,
			'before'         => '',
			'after'          => '',
			'link_before'    => '',
			'link_after'     => '',
			'depth'          => 0,
			)
		);
		$rooten_primary_menu = new rooten_nav_dom($rooten_navbar);
		echo 	$rooten_primary_menu->proccess();
	}
	elseif(has_nav_menu('offcanvas')) {
		$rooten_navbar = wp_nav_menu( array(
			'theme_location' => 'offcanvas',
			'container'      => false,
			'items_wrap'     => '<ul id="%1$s" class="%2$s" bdt-nav>%3$s</ul>',
			'menu_id'        => 'nav-offcanvas',
			'menu_class'     => $rooten_menu_class,
			'echo'           => true,
			'before'         => '',
			'after'          => '',
			'link_before'    => '',
			'link_after'     => '',
			'depth'          => 0,
			)
		);
		$rooten_primary_menu = new rooten_nav_dom($rooten_navbar);
		echo 	$rooten_primary_menu->proccess();
	}
	else {
		echo '<div class="bdt-panel"><div class="panel-content"><div class="bdt-alert bdt-alert-warning bdt-margin-remove-bottom"><strong>NO MENU ASSIGNED</strong> <span>Go To Appearance > <a class="bdt-link" href="'.admin_url('/nav-menus.php').'">Menus</a> and create a Menu</span></div></div></div>';
	}



	if (is_active_sidebar('offcanvas') and $rooten_offcanvas_style != 'modal') {
		echo '<hr>';
		echo '<div class="offcanvas-widgets">';
		dynamic_sidebar('offcanvas');
		echo '</div>';
	}

?>