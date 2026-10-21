<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$store_user   = dokan()->vendor->get( get_query_var( 'author' ) );
$store_info   = $store_user->get_shop_info();
$map_location = $store_user->get_location();
$layout       = get_theme_mod( 'store_layout', 'left' );

get_header( 'shop' );

if ( function_exists( 'yoast_breadcrumb' ) ) {
    yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' );
}
?>
    <?php do_action( 'woocommerce_before_main_content' ); ?>
    <div class="row">

        <div id="dokan-secondary" class="col-lg-3" role="complementary">
            <div class="pooshe-myan">
                <div class="dokan-widget-area widget-collapse">
                <?php do_action( 'dokan_sidebar_store_before', $store_user->data, $store_info ); ?>
                <section class="widget ns-store-avatar wc-dashboard">
                    <header class="store-avatar-header">
                        <div class="profile-img">
                            <div class="loader back">
                            </div>
                            <div class="loader front">
                            </div>
                            <?php echo get_avatar( $store_user->get_id(), 150, '', $store_user->get_shop_name() ); ?>
                        </div>
                    </header>
                    <footer class="store-avatar-footer">
                        <?php if ( ! empty( $store_user->get_shop_name()) ) { ?>
                            <h1 class="store-name"><?php echo esc_html( $store_user->get_shop_name() ); ?></h1>
                        <?php } ?>
                        <div class="details">
                            <time class="reg-date"><?php echo __('عضویت از ','pooshe').human_time_diff( strtotime($store_user->get_register_date()) , current_time('timestamp') ) . __(' پیش','pooshe'); ?></time>

                                <div class="wallet">
                                    <?php
                                    $rate = dokan_get_seller_rating( $store_user->get_id() );
                                    $rate_percent = (is_numeric($rate['rating']) and $rate['rating']>0)?($rate['rating']/5) * 100:0;
                                    echo '<span>'.$rate_percent.' '.__('٪ رضایت خرید','pooshe').'</span>';
                                    echo ' / '.$rate['count']. __(' رای','pooshe');
                                    ?>
                                </div>
                        </div>
                    </footer>
                </section>
                <?php
                    $args = array(
                        'before_widget' => '<section class="sidebar-widget">',
                        'after_widget'  => '</section>',
                        'before_title'  => '<h5 class="widget-title">',
                        'after_title'   => '</h5>',
                    );

                    if ( class_exists( 'Dokan_Store_Location' ) ) {
                        the_widget( 'Dokan_Store_Category_Menu', array( 'title' => 'دسته بندی فروشگاه' ), $args );

                        if (  !empty( $map_location ) && dokan_get_option( 'store_map', 'dokan_general', 'on' ) === 'on' ) {
                            the_widget( 'Dokan_Store_Location', array( 'title' => 'موقعیت فروشگاه' ), $args );
                        }

                        if ( dokan_get_option( 'contact_seller', 'dokan_general', 'on' ) === 'on' ) {
                            the_widget( 'Dokan_Store_Contact_Form', array( 'title' => 'تماس' ), $args );
                        }
                    }

                ?>

                <?php do_action( 'dokan_sidebar_store_after', $store_user->data, $store_info ); ?>
            </div>
            </div>
        </div><!-- #secondary .widget-area -->

    <div id="dokan-primary" class="dokan-single-store col-lg">
        <div id="dokan-content" class="store-page-wrap woocommerce" role="main">

            <?php dokan_get_template_part( 'store-header' ); ?>

            <?php do_action( 'dokan_store_profile_frame_after', $store_user->data, $store_info ); ?>

            <?php if ( have_posts() ) { ?>

                <div class="seller-items">

                    <?php woocommerce_product_loop_start(); ?>

                    <?php
                    while ( have_posts() ) :
                        the_post();
						?>

                        <?php wc_get_template_part( 'content', 'product' ); ?>

                    <?php endwhile; // end of the loop. ?>

                    <?php woocommerce_product_loop_end(); ?>

                </div>

                <?php dokan_content_nav( 'nav-below' ); ?>

            <?php } else { ?>

                <p class="dokan-info"><?php esc_html_e( 'No products were found of this vendor!', 'dokan-lite' ); ?></p>

            <?php } ?>
        </div>

    </div><!-- .dokan-single-store -->


</div><!-- .dokan-store-wrap -->

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer( 'shop' ); ?>
