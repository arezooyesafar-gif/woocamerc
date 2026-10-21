<?php
/**
 * The template for displaying product search form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/product-searchform.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form role="search" method="get" class="woocommerce-product-search sidebar-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <div class="input-group">
        <div class="input-group-prepend">
            <button class="btn" type="submit"><?php echo esc_attr_x( 'Search', 'submit button', 'woocommerce' ); ?></button>
        </div>
        <input type="search" id="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="form-control search-field" placeholder="<?php echo esc_attr__( 'Search products&hellip;', 'woocommerce' ); ?>" value="<?php echo get_search_query(); ?>" name="s" >

    </div>
    <p class="mb-0 sub-text"><?php _e('جستجو در محصولات','pooshe'); ?></p>
    <input type="hidden" name="post_type" value="product" />
</form>
