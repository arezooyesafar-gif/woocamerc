<?php
/**
 * The template for displaying product search form
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<form role="search" method="get" class="bdt-search bdt-search-default bdt-width-1-1" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
	<span bdt-search-icon></span>
	<input type="search" id="woocommerce-product-search-field" class="search-field bdt-search-input" placeholder="<?php echo esc_attr_x( 'Search Products&hellip;', 'placeholder', 'rooten' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'rooten' ); ?>" />
	<input type="hidden" name="post_type" value="product" />
</form>
