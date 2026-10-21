<?php
if(function_exists('WC') and (is_cart() or is_checkout())){return;}
$sidebarName = 'sidebar-page';
$dokan = get_option('dokan_pages');
if(get_option('woocommerce_myaccount_page_id') == get_the_ID()){
    $sidebarName = 'sidebar-myaccount';
}else if(($dokan and !empty($dokan)) and (isset($dokan['dashboard']) and get_the_ID() == $dokan['dashboard'])){
    $sidebarName = 'sidebar-dokan-dashboard';
}
if ($sidebarName != 'sidebar-dokan-dashboard' and ! is_active_sidebar( $sidebarName ) ) {
    return;
}

?>
<aside id="<?php echo esc_attr($sidebarName); ?>" class="sidebar-aside <?php echo esc_attr($sidebarName); ?>">
    <?php
    if($sidebarName !== "sidebar-dokan-dashboard") {
        if (is_active_sidebar($sidebarName)) : ?>
            <div class="widget-area">
                <?php dynamic_sidebar($sidebarName); ?>
            </div><!-- .widget-area -->
        <?php endif;
    }else{
        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        echo '<div class="woocommerce-MyAccount-navigation">';
        do_action( 'dokan_dashboard_content_before' );
        echo '</div>';
    }
    ?>
</aside>
