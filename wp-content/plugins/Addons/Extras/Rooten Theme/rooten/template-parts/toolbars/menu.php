<?php
if (has_nav_menu('toolbar')) {
	echo wp_nav_menu( array( 'theme_location' => 'toolbar', 'container_class' => 'tm-toolbar-menu', 'menu_class' => 'bdt-subnav bdt-subnav-divider', 'depth' => 1 ) );  
} else {
	echo '<ul class="no-menu bdt-hidden-small"><li><a href="'.admin_url('/nav-menus.php').'"><strong>NO MENU ASSIGNED</strong></a></li></ul>';
}