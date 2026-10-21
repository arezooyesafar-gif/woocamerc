<?php

defined('ABSPATH') || exit;

/**
 * Pooshe Version Notifier
 *
 * Notifies previous buyers when a product gets a new version update.
 * hooks into save_post to detect pooshe_new_version checkbox.
 */
class Pooshe_Version_Notifier {

	const META_NOTIFIED = '_pooshe_new_version_notified_at';
	const META_VERSION_COUNT = '_pooshe_new_version_count';

	public function __construct() {
		add_action('save_post_product', [$this, 'handle_version_update'], 20, 3);
		add_filter('woocommerce_account_menu_items', [$this, 'add_account_menu_item'], 50);
		add_action('woocommerce_account_product-updates_endpoint', [$this, 'account_updates_content']);
		add_action('init', [$this, 'register_endpoint']);
	}

	public function register_endpoint() {
		add_rewrite_endpoint('product-updates', EP_ROOT | EP_PAGES);
	}

	public function add_account_menu_item($items) {
		$updates = $this->get_user_updates_count();
		$label = 'به‌روزرسانی محصولات';
		if ($updates > 0) {
			$label .= ' <span class="pooshe-updates-badge">' . $updates . '</span>';
		}

		$items = array_slice($items, 0, 1, true)
			+ ['product-updates' => $label]
			+ array_slice($items, 1, null, true);

		return $items;
	}

	public function account_updates_content() {
		$user_id = get_current_user_id();
		$product_ids = $this->get_purchased_products($user_id);
		$updated_products = [];

		if (!empty($product_ids)) {
			foreach ($product_ids as $product_id) {
				$is_new_version = get_post_meta($product_id, 'pooshe_new_version', true);
				$notified_at = get_post_meta($product_id, self::META_NOTIFIED, true);
				if ($is_new_version === 'on' && !empty($notified_at)) {
					$product = wc_get_product($product_id);
					if ($product) {
						$updated_products[] = $product;
					}
				}
			}
		}

		wc_get_template('myaccount/product-updates.php', [
			'products' => $updated_products,
		], '', get_template_directory() . '/views/woocommerce/');
	}

	private function get_user_updates_count() {
		if (!is_user_logged_in()) {
			return 0;
		}
		$user_id = get_current_user_id();
		$product_ids = $this->get_purchased_products($user_id);
		$count = 0;

		if (!empty($product_ids)) {
			foreach ($product_ids as $product_id) {
				$is_new_version = get_post_meta($product_id, 'pooshe_new_version', true);
				$notified_at = get_post_meta($product_id, self::META_NOTIFIED, true);
				if ($is_new_version === 'on' && !empty($notified_at)) {
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	 * Handle save_post to detect version updates.
	 */
	public function handle_version_update($post_id, $post, $update) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
		if (wp_is_post_revision($post_id)) {
			return;
		}

		$new_version = isset($_POST['pooshe_new_version']) ? 'on' : 'off';
		$old_version = get_post_meta($post_id, 'pooshe_new_version', true);

		if ($new_version === 'on' && $old_version !== 'on') {
			$this->notify_buyers($post_id);
		}
	}

	/**
	 * Find all customers who purchased this product and send notification.
	 */
	private function notify_buyers($product_id) {
		$customer_emails = $this->get_customer_emails_for_product($product_id);

		if (empty($customer_emails)) {
			update_post_meta($product_id, self::META_NOTIFIED, current_time('mysql'));
			return;
		}

		$product = wc_get_product($product_id);
		if (!$product) {
			return;
		}

		$version_count = (int) get_post_meta($product_id, self::META_VERSION_COUNT, true);
		$version_count++;
		update_post_meta($product_id, self::META_VERSION_COUNT, $version_count);

		$product_name = $product->get_name();
		$product_link = $product->get_permalink();

		foreach ($customer_emails as $email) {
			$this->send_notification_email($email, $product_name, $product_link, $product_id);
		}

		update_post_meta($product_id, self::META_NOTIFIED, current_time('mysql'));
	}

	/**
	 * Get unique customer emails from completed orders containing this product.
	 */
	private function get_customer_emails_for_product($product_id) {
		global $wpdb;

		$results = $wpdb->get_col($wpdb->prepare("
			SELECT DISTINCT pm.meta_value
			FROM {$wpdb->prefix}woocommerce_order_items oi
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim
				ON oi.order_item_id = oim.order_item_id AND oim.meta_key = '_product_id' AND oim.meta_value = %d
			INNER JOIN {$wpdb->posts} o
				ON oi.order_id = o.ID AND o.post_type = 'shop_order' AND o.post_status IN ('wc-completed', 'wc-processing')
			INNER JOIN {$wpdb->postmeta} pm
				ON o.ID = pm.post_id AND pm.meta_key = '_billing_email'
		", $product_id));

		return array_unique(array_filter(array_map('sanitize_email', $results)));
	}

	/**
	 * Send email notification to a customer.
	 */
	private function send_notification_email($to, $product_name, $product_link, $product_id) {
		$subject = sprintf('به‌روزرسانی جدید برای %s', $product_name);
		$heading = sprintf('نسخه جدید: %s', $product_name);

		$message = wc_get_template_html('emails/product-update-notification.php', [
			'product_name' => $product_name,
			'product_link' => $product_link,
			'product_id' => $product_id,
			'email_heading' => $heading,
		], '', get_template_directory() . '/views/woocommerce/');

		$headers = ['Content-Type: text/html; charset=UTF-8'];

		wp_mail($to, $subject, $message, $headers);
	}

	/**
	 * Get IDs of products purchased by a user.
	 */
	private function get_purchased_products($user_id) {
		global $wpdb;

		$customer = new WC_Customer($user_id);
		$email = $customer->get_email();

		if (empty($email)) {
			return [];
		}

		$results = $wpdb->get_col($wpdb->prepare("
			SELECT DISTINCT oim.meta_value
			FROM {$wpdb->prefix}woocommerce_order_items oi
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim
				ON oi.order_item_id = oim.order_item_id AND oim.meta_key = '_product_id'
			INNER JOIN {$wpdb->posts} o
				ON oi.order_id = o.ID AND o.post_type = 'shop_order' AND o.post_status IN ('wc-completed', 'wc-processing')
			INNER JOIN {$wpdb->postmeta} pm
				ON o.ID = pm.post_id AND pm.meta_key = '_billing_email' AND pm.meta_value = %s
		", $email));

		return array_map('intval', $results);
	}

}

new Pooshe_Version_Notifier();
