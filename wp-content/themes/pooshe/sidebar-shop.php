<?php

if ( ! is_active_sidebar( 'sidebar-shop' ) or is_product()) {
    return;
}
?>
<button class="btn btn-primary b-block d-xl-none btn-shop-responsive-sidebar" data-bs-toggle="modal" data-bs-target="#ShopSidebarModal">
    <i class="far fa-filter"></i>
    <span><?php _e('فیلتر نمایش','pooshe'); ?></span>
</button>
<!-- ShopSidebarResponsive -->
<div class="modal fade carouselWidgetTabs" id="ShopSidebarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span class="btn-over"><?php _e('بستن پنجره','pooshe'); ?></span>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php dynamic_sidebar( 'sidebar-shop' ); ?>
            </div>
        </div>
    </div>
</div>

<aside id="secondary" class="sidebar-aside shop-archive-sidebar d-none d-xl-block">
    <?php dynamic_sidebar( 'sidebar-shop' ); ?>
</aside><!-- #secondary -->
