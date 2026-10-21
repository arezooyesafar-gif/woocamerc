<?php
defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<div class="product-v2-hero">
		<div class="product-v2-gallery">
			<?php woocommerce_show_product_images(); ?>
		</div>

		<div class="product-v2-info">
			<?php
			woocommerce_template_single_title();

			wc_get_subtitle();
			?>

			<div class="product-v2-stats">
				<?php if ( pooshe_opt( PRODUCT_OPTS, 'product-sale', true ) ) : ?>
				<div class="stats-item">
					<i class="fal fa-shopping-bag"></i>
					<div class="stats-inner">
						<span class="stats-value"><?php echo (int) $product->get_total_sales(); ?></span>
						<span class="stats-label"><?php _e( 'فروش', 'pooshe' ); ?></span>
					</div>
				</div>
				<?php endif; ?>

				<div class="stats-divider"></div>

				<div class="stats-item">
					<i class="fal fa-star" style="color:#f5a623"></i>
					<div class="stats-inner">
						<span class="stats-value"><?php echo (int) $product->get_rating_count(); ?></span>
						<span class="stats-label"><?php _e( 'رأی', 'pooshe' ); ?></span>
					</div>
				</div>

				<div class="stats-divider"></div>

				<?php if ( pooshe_opt( PRODUCT_OPTS, 'product-comment', true ) ) : ?>
				<div class="stats-item">
					<i class="fal fa-comment"></i>
					<div class="stats-inner">
						<span class="stats-value"><?php echo (int) $product->get_review_count(); ?></span>
						<span class="stats-label"><?php _e( 'دیدگاه', 'pooshe' ); ?></span>
					</div>
				</div>
				<?php endif; ?>
			</div>

			<?php
			pooshe_featured_attrs();

			woocommerce_template_single_price();

			woocommerce_template_single_excerpt();

			do_action( 'pooshe_v2_before_add_to_cart' );

			woocommerce_template_single_add_to_cart();

			do_action( 'pooshe_v2_after_add_to_cart' );
			?>

			<div class="product-share">
				<?php if ( pooshe_opt( PRODUCT_OPTS, 'product-bookmark', true ) ) : ?>
					<?php echo pooshe_product_favourite(); ?>
				<?php endif; ?>
				<?php if ( pooshe_opt( PRODUCT_OPTS, 'product-share', true ) ) : ?>
					<button type="button" data-bs-toggle="modal" data-bs-target="#productShareModal" class="button"><i class="fal fa-share-alt"></i></button>
				<?php endif; ?>
				<?php if ( pooshe_opt( PRODUCT_OPTS, 'product-link', true ) ) : ?>
					<button type="button" class="button pooshe-copy-link" data-link="<?php echo esc_url( wp_get_shortlink() ); ?>"><i class="fal fa-copy"></i> <?php _e( 'کپی لینک', 'pooshe' ); ?></button>
				<?php endif; ?>
			</div>

			<?php
			?>
		</div>
	</div>

	<?php if ( pooshe_opt( PRODUCT_OPTS, 'product-share', true ) ) : ?>
	<!-- productShareModal -->
	<div class="modal fade" id="productShareModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><?php _e( 'اشتراک گذاری محصول', 'pooshe' ); ?></h5>
					<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
						<span class="btn-over"><?php _e( 'بستن پنجره', 'pooshe' ); ?></span>
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<?php echo pooshe_social_sharing_buttons(); ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php $has_description = $product->get_description(); ?>
	<div class="product-v2-accordion">
		<?php if ($has_description) : ?>
		<div class="v2-accordion-item">
			<button class="v2-accordion-trigger active" type="button">
				<?php _e( 'توضیحات', 'pooshe' ); ?>
				<i class="fal fa-chevron-down"></i>
			</button>
			<div class="v2-accordion-body" style="display:block">
				<?php the_content(); ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( comments_open() ) : ?>
		<div class="v2-accordion-item">
			<button class="v2-accordion-trigger<?php echo !$has_description ? ' active' : ''; ?>" type="button">
				<?php _e( 'دیدگاه‌ها', 'pooshe' ); ?>
				<i class="fal fa-chevron-down"></i>
			</button>
			<div class="v2-accordion-body"<?php echo !$has_description ? ' style="display:block"' : ''; ?>>
				<?php comments_template(); ?>
			</div>
		</div>
		<?php endif; ?>
	</div>

	<?php woocommerce_output_related_products(); ?>

</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
