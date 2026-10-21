<?php
/**
 * Dokan Seller Single product tab Template
 *
 * @since   2.4
 *
 * @package dokan
 */
?>

<div class="ns-store-avatar">
    <?php do_action( 'dokan_product_seller_tab_start', $author, $store_info ); ?>
        <div class="info">
            <div class="profile-img">
                <?php echo get_avatar( $author->ID, 150, '', $author->display_name ); ?>
            </div>
            <?php if ( !empty( $store_info['store_name'] ) ) { ?>
            <h3 class="store-name">
                <?php printf( '<a href="%1$s">%2$s</a>', esc_url( dokan_get_store_url( $author->ID ) ), esc_html( $store_info['store_name'] ) ); ?>
            </h3>
        </div>
           <div class="rate">
               <?php if ( ( dokan()->is_pro_exists() && dokan_pro()->module->is_active( 'store_reviews' ) ) || ( 'yes' === get_option( 'woocommerce_enable_reviews' ) ) ) : ?>
                   <?php echo wp_kses_post( dokan_get_readable_seller_rating( $author->ID ) ); ?>
               <?php endif; ?>
           </div>
        <?php } ?>
    <?php do_action( 'dokan_product_seller_tab_end', $author, $store_info ); ?>
</div>
