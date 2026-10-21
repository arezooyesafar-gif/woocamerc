<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_products ) :
	/**
	 * Ensure all images of related products are lazy loaded by increasing the
	 * current media count to WordPress's lazy loading threshold if needed.
	 * Because wp_increase_content_media_count() is a private function, we
	 * check for its existence before use.
	 */
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}
	?>

	<section class="related products pooshe-carousel">

		<h2><?php esc_html_e( 'Related products', 'woocommerce' ); ?></h2>


        <div class="swiper-container" data-options='{"slidesPerView":1,"loop":true,"spaceBetween":0,"breakpoints":{"480":{"slidesPerView":1},"768":{"slidesPerView":2},"992":{"slidesPerView":4}},"navigation":{"nextEl":".swiper-button-next","prevEl":".swiper-button-prev"},"autoplay":{"delay":5000}}'>
            <div class="swiper-wrapper">
			<?php foreach ( $related_products as $related_product ) : ?>
                <div class="swiper-slide">
				<?php
				 	$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );

					wc_get_template_part( 'content', 'product' ); ?>
                </div>
			<?php endforeach; ?>
            </div>
        </div>


	</section>

<?php endif;

wp_reset_postdata();
