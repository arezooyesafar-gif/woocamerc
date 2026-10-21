<?php

defined( 'ABSPATH' ) || exit;

function pooshe_quick_view_button() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	printf(
		'<button type="button" class="button pooshe-quick-view-btn" data-product_id="%d" title="%s"><i class="fa-solid fa-eye"></i></button>',
		$product->get_id(),
		esc_attr__( 'مشاهده سریع', 'pooshe' )
	);
}
add_action( 'pooshe_card_addtocart', 'pooshe_quick_view_button', 5 );

function pooshe_quick_view_modal() {
	?>
	<div class="modal fade" id="poosheQuickViewModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><?php _e( 'مشاهده محصول', 'pooshe' ); ?></h5>
					<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
						<span class="btn-over"><?php _e( 'بستن پنجره', 'pooshe' ); ?></span>
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="poosheQuickViewContent">
					<div class="pooshe-loading" style="text-align:center;padding:40px">
						<i class="fa-spin far fa-spinner fa-2x"></i>
						<p><?php _e( 'در حال بارگذاری...', 'pooshe' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'pooshe_quick_view_modal' );

function pooshe_quick_view_ajax() {
	$product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
	if ( ! $product_id ) {
		wp_send_json_error( 'آی دی محصول نامعتبر است' );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( 'محصول یافت نشد' );
	}

	$gallery_html = '';
	$attachment_ids = $product->get_gallery_image_ids();
	if ( ! empty( $attachment_ids ) ) {
		$gallery_html .= '<div class="pooshe-qv-gallery-slider">';
		$gallery_html .= $product->get_image( 'woocommerce_single' );
		foreach ( $attachment_ids as $attachment_id ) {
			$gallery_html .= wp_get_attachment_image( $attachment_id, 'woocommerce_single' );
		}
		$gallery_html .= '</div>';
	} else {
		$gallery_html = $product->get_image( 'woocommerce_single' );
	}

	ob_start();
	?>
	<div class="pooshe-quick-view-product">
		<div class="row">
			<div class="col-md-6">
				<div class="pooshe-qv-gallery">
					<?php echo $gallery_html; ?>
				</div>
			</div>
			<div class="col-md-6">
				<h2 class="pooshe-qv-title"><?php echo esc_html( $product->get_name() ); ?></h2>
				<div class="pooshe-qv-price"><?php echo $product->get_price_html(); ?></div>
				<div class="pooshe-qv-rating"><?php echo wc_get_rating_html( $product->get_average_rating() ); ?></div>
				<div class="pooshe-qv-excerpt">
					<?php echo wp_kses_post( $product->get_short_description() ); ?>
				</div>
				<div class="pooshe-qv-meta">
					<?php
					if ( wc_product_sku_enabled() && $product->get_sku() ) {
						echo '<span class="pooshe-qv-sku">' . esc_html__( 'شناسه محصول: ', 'pooshe' ) . $product->get_sku() . '</span>';
					}
					echo $product->get_categories( ', ', '<span class="pooshe-qv-cats">' . _n( 'دسته:', 'دسته‌ها:', 1, 'pooshe' ) . ' ', '</span>' );
					?>
				</div>
				<div class="pooshe-qv-addtocart">
					<?php woocommerce_template_loop_add_to_cart( array( 'product_id' => $product_id ) ); ?>
				</div>
				<div class="pooshe-qv-link">
					<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="btn btn-primary">
						<?php _e( 'مشاهده کامل محصول', 'pooshe' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<?php
	$html = ob_get_clean();
	wp_send_json_success( $html );
}
add_action( 'wp_ajax_pooshe_quick_view', 'pooshe_quick_view_ajax' );
add_action( 'wp_ajax_nopriv_pooshe_quick_view', 'pooshe_quick_view_ajax' );

function pooshe_quick_view_css() {
	echo '<style>
#poosheQuickViewModal .modal-lg{max-width:800px}
.pooshe-quick-view-product{padding:10px}
.pooshe-qv-gallery{margin-bottom:20px}
.pooshe-qv-gallery img{width:100%;height:auto;border-radius:8px}
.pooshe-qv-title{font-size:22px;font-weight:700;margin:0 0 10px;line-height:1.3}
.pooshe-qv-price{font-size:24px;font-weight:700;margin-bottom:15px}
.pooshe-qv-rating{margin-bottom:15px}
.pooshe-qv-excerpt{color:#666;font-size:14px;line-height:1.7;margin-bottom:20px}
.pooshe-qv-meta{font-size:13px;color:#888;margin-bottom:20px;display:flex;flex-wrap:wrap;gap:10px}
.pooshe-qv-addtocart{margin-bottom:15px}
.pooshe-qv-addtocart .button{width:100%;justify-content:center}
.pooshe-qv-addtocart .added_to_cart{display:none}
.pooshe-qv-link a{width:100%;justify-content:center}
.pooshe-quick-view-btn{border:none;background:none;padding:0;cursor:pointer;color:inherit;display:inline-flex;align-items:center;justify-content:center}
.pooshe-quick-view-btn i{font-size:16px}
.pooshe-qv-gallery-slider{display:flex;flex-wrap:wrap;gap:10px}
.pooshe-qv-gallery-slider img{width:100%;height:auto;border-radius:8px}
.pooshe-qv-gallery-slider img:not(:first-child){width:calc(33.33% - 7px)}
</style>' . "\n";
}
add_action( 'wp_head', 'pooshe_quick_view_css', 1 );

function pooshe_quick_view_scripts() {
	$js = <<<JS
jQuery(function($){
	$(document).on('click', '.pooshe-quick-view-btn', function(e){
		e.preventDefault();
		var btn = $(this);
		var productID = btn.data('product_id');
		var modal = $('#poosheQuickViewModal');
		var content = $('#poosheQuickViewContent');

		content.html(
			'<div class="pooshe-loading" style="text-align:center;padding:40px">' +
			'<i class="fa-spin far fa-spinner fa-2x"></i>' +
			'<p>در حال بارگذاری...</p>' +
			'</div>'
		);
		var bsModal = new bootstrap.Modal(modal[0]);
		bsModal.show();

		$.ajax({
			url: WP_OBJ.ajax_url,
			type: 'POST',
			data: {
				action: 'pooshe_quick_view',
				product_id: productID
			},
			success: function(res){
				if(res.success){
					content.html(res.data);
				} else {
					content.html('<div class="alert alert-danger">' + res.data + '</div>');
				}
			},
			error: function(){
				content.html('<div class="alert alert-danger">خطا در بارگذاری اطلاعات</div>');
			}
		});
	});
});
JS;
	wp_add_inline_script( 'pooshe-app', $js );
}
add_action( 'wp_enqueue_scripts', 'pooshe_quick_view_scripts' );
