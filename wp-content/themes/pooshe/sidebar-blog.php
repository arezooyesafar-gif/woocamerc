<?php
if ( ! is_active_sidebar( 'sidebar-blog' ) ) {
    return;
}
?>
<aside id="sidebar-blog" class="sidebar-aside sidebar-blog">
    <?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
        <div class="widget-area">
            <?php dynamic_sidebar( 'sidebar-blog' ); ?>
        </div><!-- .widget-area -->
    <?php endif; ?>
</aside>
