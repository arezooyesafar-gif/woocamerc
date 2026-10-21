<?php


if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_Shortcodes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('user_subscriptions', array($this, 'user_subscriptions_shortcode'));
        add_shortcode('subscription_downloads', array($this, 'subscription_downloads_shortcode'));
        add_shortcode('subscription_days_left', array($this, 'subscription_days_left_shortcode'));
        add_shortcode('subscription_expired', array($this, 'subscription_expired_shortcode'));
        add_shortcode('product_download_status', array($this, 'product_download_status_shortcode'));
        add_shortcode('eshterakyar_product_cta', array($this, 'product_cta_shortcode'));
        add_shortcode('subscription_info', array($this, 'subscription_info_shortcode'));
        add_shortcode('subscription_badge', array($this, 'subscription_badge_shortcode'));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function shortcode_flag_enabled($value): bool {
        return in_array(strtolower((string) $value), array('yes', 'true', '1', 'on'), true);
    }

    /**
     * Ensure frontend CSS/JS load even when shortcode is inside Elementor/widgets.
     */
    private function ensure_frontend_assets(): void {
        if (class_exists('Eshterakyar')) {
            Eshterakyar::enqueue_frontend_assets(true);
        }
    }

    /**
     * Build tooltip detail rows for subscription badge.
     *
     * @param object $subscription
     * @param array<string, mixed> $atts
     * @return array<int, array{label: string, value: string}>
     */
    private function get_subscription_badge_tooltip_rows($subscription, array $atts): array {
        $rows = array();
        $days_left = Eshterakyar_Subscription::get_days_left($subscription->id);

        if ($this->shortcode_flag_enabled($atts['show_plan'])) {
            $rows[] = array(
                'label' => __('پلن:', 'eshterakyar'),
                'value' => (string) $subscription->plan_name,
            );
        }

        if ($this->shortcode_flag_enabled($atts['show_days'])) {
            $rows[] = array(
                'label' => __('روزهای باقی‌مانده:', 'eshterakyar'),
                'value' => $days_left > 0
                    ? sprintf(__('%d روز', 'eshterakyar'), $days_left)
                    : __('منقضی شده', 'eshterakyar'),
            );
        }

        if ($this->shortcode_flag_enabled($atts['show_expiry'])) {
            $rows[] = array(
                'label' => __('تاریخ انقضا:', 'eshterakyar'),
                'value' => date_i18n(get_option('date_format'), strtotime($subscription->end_date)),
            );
        }

        if ($this->shortcode_flag_enabled($atts['show_downloads'])) {
            if ($subscription->total_downloads > 0) {
                $remaining = max(0, (int) $subscription->total_downloads - (int) $subscription->downloads_used);
                $rows[] = array(
                    'label' => __('دانلود باقی‌مانده:', 'eshterakyar'),
                    'value' => absint($remaining) . ' / ' . absint($subscription->total_downloads),
                );
            } else {
                $rows[] = array(
                    'label' => __('دانلود باقی‌مانده:', 'eshterakyar'),
                    'value' => __('نامحدود', 'eshterakyar'),
                );
            }
        }

        if ($this->shortcode_flag_enabled($atts['show_daily']) && (int) $subscription->daily_limit > 0) {
            global $wpdb;
            $today = current_time('Y-m-d');
            $today_start = $today . ' 00:00:00';
            $today_end = $today . ' 23:59:59';
            $downloads_table = $wpdb->prefix . 'subscription_downloads';
            $daily_count = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(DISTINCT product_id) FROM `{$downloads_table}`
                 WHERE subscription_id = %d AND download_date >= %s AND download_date <= %s",
                $subscription->id,
                $today_start,
                $today_end
            ));
            $remaining_daily = max(0, (int) $subscription->daily_limit - $daily_count);
            $rows[] = array(
                'label' => __('دانلود امروز:', 'eshterakyar'),
                'value' => absint($remaining_daily) . ' / ' . absint($subscription->daily_limit),
            );
        }

        return $rows;
    }

    /**
     * Hide subscription widgets/shortcodes for out-of-stock products.
     *
     * @param int $product_id
     * @return bool
     */
    private function should_hide_for_out_of_stock_product(int $product_id): bool {
        if ($product_id <= 0 || !function_exists('wc_get_product')) {
            return false;
        }
        $product = wc_get_product($product_id);
        if (!$product) {
            return false;
        }
        return !$product->is_in_stock();
    }

    /**
     * Out-of-stock message for subscription widgets.
     *
     * @return string
     */
    private function get_out_of_stock_subscription_message_html(): string {
        return '<div class="eshterakyar-no-subscription-alert"><p>' .
            esc_html(eshterakyar_get_label('product_out_of_stock_subscription_disabled', __('این محصول ناموجود است و امکان اشتراک برای آن فعال نیست.', 'eshterakyar'))) .
            '</p></div>';
    }

    /**
     * Daily/total download quota snapshot for a subscription row.
     *
     * @param object $subscription
     * @return array{daily_limit:int,daily_used:int,remaining_daily:int,total_limit:int,total_used:int,remaining_total:int,daily_unlimited:bool,total_unlimited:bool}
     */
    private function get_subscription_quota_snapshot($subscription): array {
        global $wpdb;

        $daily_limit = isset($subscription->daily_limit) ? (int) $subscription->daily_limit : 0;
        $total_limit = isset($subscription->total_downloads) ? (int) $subscription->total_downloads : 0;
        $total_used = isset($subscription->downloads_used) ? (int) $subscription->downloads_used : 0;
        $daily_used = 0;

        if ($daily_limit > 0 && !empty($subscription->id)) {
            $today = current_time('Y-m-d');
            $downloads_table = $wpdb->prefix . 'subscription_downloads';
            $daily_used = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(DISTINCT product_id) FROM `{$downloads_table}`
                 WHERE subscription_id = %d AND download_date >= %s AND download_date <= %s",
                (int) $subscription->id,
                $today . ' 00:00:00',
                $today . ' 23:59:59'
            ));
        }

        return array(
            'daily_limit' => $daily_limit,
            'daily_used' => $daily_used,
            'remaining_daily' => $daily_limit > 0 ? max(0, $daily_limit - $daily_used) : 0,
            'total_limit' => $total_limit,
            'total_used' => $total_used,
            'remaining_total' => $total_limit > 0 ? max(0, $total_limit - $total_used) : 0,
            'daily_unlimited' => $daily_limit <= 0,
            'total_unlimited' => $total_limit <= 0,
        );
    }

    /**
     * Resolve product ID for product CTA shortcode.
     *
     * @param array $atts
     * @return int
     */
    private function resolve_product_cta_product_id(array $atts): int {
        $product_id = isset($atts['product_id']) ? absint($atts['product_id']) : 0;
        if ($product_id > 0) {
            return $product_id;
        }
        if (function_exists('is_product') && is_product()) {
            return (int) get_the_ID();
        }
        global $product;
        if ($product && is_object($product) && method_exists($product, 'get_id')) {
            return (int) $product->get_id();
        }
        return 0;
    }

    /**
     * Whether product CTA shortcode should render for this product (respects plugin settings).
     *
     * @param int $product_id
     * @return bool
     */
    private function product_cta_is_allowed_for_product(int $product_id): bool {
        $options = get_option('eshterakyar_options', array());
        $show_for_non_subscription = isset($options['show_download_box_for_non_subscription_products'])
            ? (int) $options['show_download_box_for_non_subscription_products']
            : 1;
        if ($show_for_non_subscription) {
            return true;
        }
        return Eshterakyar_Subscription::is_product_subscription_downloadable($product_id);
    }

    /**
     * Render WooCommerce price + add-to-cart block for a product.
     *
     * @param int   $product_id
     * @param array $atts
     * @return string
     */
    private function render_product_cta_cart_block(int $product_id, array $atts): string {
        if (!function_exists('wc_get_product')) {
            return '';
        }

        $product = wc_get_product($product_id);
        if (!$product) {
            return '';
        }

        if (!$product->is_purchasable()) {
            return '<p class="eshterakyar-cta-cart-unavailable">' .
                esc_html__('این محصول در حال حاضر قابل خرید نیست.', 'eshterakyar') .
                '</p>';
        }

        if (!$product->is_in_stock()) {
            return '<p class="eshterakyar-cta-cart-unavailable">' .
                esc_html__('این محصول ناموجود است.', 'eshterakyar') .
                '</p>';
        }

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_script('wc-add-to-cart');
            if ($product->is_type('variable')) {
                wp_enqueue_script('wc-add-to-cart-variation');
            }
        }

        $previous_product = isset($GLOBALS['product']) ? $GLOBALS['product'] : null;
        $GLOBALS['product'] = $product;

        $show_price = $this->shortcode_flag_enabled($atts['show_price']);
        $show_quantity = $this->shortcode_flag_enabled($atts['show_quantity']);
        $cart_text = trim((string) $atts['cart_text']);

        $button_text_filter = null;
        if ($cart_text !== '') {
            $button_text_filter = static function () use ($cart_text) {
                return $cart_text;
            };
            add_filter('woocommerce_product_single_add_to_cart_text', $button_text_filter, 99);
            add_filter('woocommerce_product_add_to_cart_text', $button_text_filter, 99);
        }

        if (!$show_quantity && $product->is_type('simple')) {
            add_filter('woocommerce_is_sold_individually', '__return_true', 99);
        }

        ob_start();
        echo '<div class="eshterakyar-cta-cart-block">';
        if ($show_price && function_exists('woocommerce_template_single_price')) {
            echo '<div class="eshterakyar-cta-price">';
            woocommerce_template_single_price();
            echo '</div>';
        }
        if (function_exists('woocommerce_template_single_add_to_cart')) {
            echo '<div class="eshterakyar-cta-add-to-cart">';
            woocommerce_template_single_add_to_cart();
            echo '</div>';
        }
        echo '</div>';
        $html = ob_get_clean();

        if (!$show_quantity && $product->is_type('simple')) {
            remove_filter('woocommerce_is_sold_individually', '__return_true', 99);
        }
        if ($button_text_filter) {
            remove_filter('woocommerce_product_single_add_to_cart_text', $button_text_filter, 99);
            remove_filter('woocommerce_product_add_to_cart_text', $button_text_filter, 99);
        }

        $GLOBALS['product'] = $previous_product;

        return $html;
    }

    /**
     * Product purchase / free-download CTA shortcode.
     *
     * no_sub_mode:
     * - cart  (الف): قیمت + افزودن به سبد خرید محصول
     * - plans (ب): دکمه خرید اشتراک (صفحه پلن‌ها)
     * - both  (ج): هر دو
     *
     * @param array|string $atts
     * @return string
     */
    public function product_cta_shortcode($atts) {
        $this->ensure_frontend_assets();
        $atts = shortcode_atts(array(
            'product_id' => 0,
            'no_sub_mode' => 'both',
            'show_price' => 'yes',
            'show_quantity' => 'no',
            'show_note' => 'yes',
            'show_daily_bar' => 'yes',
            'show_total_bar' => 'no',
            'show_download_url' => 'yes',
            'show_plan_name' => 'no',
            'plans_url' => '',
            'download_text' => '',
            'plans_text' => '',
            'cart_text' => '',
            'note_text' => '',
            'class' => '',
            'align' => 'stretch',
        ), $atts, 'eshterakyar_product_cta');

        $product_id = $this->resolve_product_cta_product_id($atts);
        if ($product_id <= 0) {
            return '';
        }

        if (!$this->product_cta_is_allowed_for_product($product_id)) {
            return '';
        }

        $modes = array('cart', 'plans', 'both');
        $no_sub_mode = strtolower(trim((string) $atts['no_sub_mode']));
        if (!in_array($no_sub_mode, $modes, true)) {
            $no_sub_mode = 'both';
        }
        $atts['no_sub_mode'] = $no_sub_mode;

        $alignments = array('stretch', 'left', 'center', 'right');
        $align = in_array($atts['align'], $alignments, true) ? $atts['align'] : 'stretch';

        $options = get_option('eshterakyar_options', array());
        $frontend_style = isset($options['frontend_style']) ? $options['frontend_style'] : 'style1';
        $style_class = $frontend_style === 'style2' ? 'eshterakyar-style-2' : 'eshterakyar-style-1';

        $plans_url = !empty($atts['plans_url']) ? esc_url_raw($atts['plans_url']) : Eshterakyar_Subscription_Page::get_page_url();
        if (!$plans_url) {
            $plans_url = home_url('/');
        }

        $plans_text = $atts['plans_text'] !== ''
            ? $atts['plans_text']
            : eshterakyar_get_label('buy_subscription_free_download', __('خرید اشتراک و دانلود رایگان', 'eshterakyar'));
        $download_text = $atts['download_text'] !== ''
            ? $atts['download_text']
            : eshterakyar_get_label('download_with_subscription', __('دانلود رایگان با اشتراک', 'eshterakyar'));
        $note_text = $atts['note_text'] !== ''
            ? $atts['note_text']
            : __('برای دانلود رایگان این محصول نیاز به اشتراک فعال دارید. می‌توانید محصول را جداگانه بخرید یا اشتراک تهیه کنید.', 'eshterakyar');

        $user_id = get_current_user_id();
        $subscription = $user_id ? Eshterakyar_Subscription::get_active_subscription($user_id) : null;
        $is_downloaded_any = $user_id ? Eshterakyar_Download::is_downloaded_any_subscription($user_id, $product_id) : false;
        $is_downloaded = false;
        if ($user_id && $subscription) {
            $is_downloaded = Eshterakyar_Download::is_downloaded($user_id, (int) $subscription->id, $product_id);
        }

        $can_download = array('can' => false, 'message' => '');
        if ($user_id) {
            $can_download = Eshterakyar_Subscription::can_download(
                $user_id,
                $product_id,
                ($is_downloaded || $is_downloaded_any)
            );
        }

        $has_download_access = !empty($can_download['can']);
        $lifetime_access = $has_download_access && !empty($can_download['lifetime_access']);
        $active_subscription = $has_download_access && !empty($can_download['subscription'])
            ? $can_download['subscription']
            : $subscription;

        $state = 'inactive';
        if ($has_download_access) {
            $state = 'download';
        } elseif ($user_id && $subscription && empty($can_download['can'])) {
            $state = 'blocked';
        }

        $download_url = '';
        $multipart_html = '';
        $quota = null;
        $redownload_uses_quota = false;

        if ($state === 'download') {
            $subscription_id_for_url = $active_subscription ? (int) $active_subscription->id : null;
            $download_url = Eshterakyar_Download::get_tokenized_download_url($user_id, $product_id, $subscription_id_for_url);
            if (!$download_url) {
                $download_url = '';
            }
            $redownload_uses_quota = !empty($can_download['redownload_uses_quota']);
            $multipart_html = Eshterakyar_Download::render_multipart_download_list_html(
                $product_id,
                $redownload_uses_quota,
                array('link_class' => 'button eshterakyar-download-part-link')
            );
            if ($active_subscription && !$lifetime_access) {
                $quota = $this->get_subscription_quota_snapshot($active_subscription);
            }

            if ($is_downloaded || $is_downloaded_any) {
                if ($redownload_uses_quota) {
                    $download_text = eshterakyar_get_label('redownload_with_quota', __('دانلود مجدد با کسر اعتبار', 'eshterakyar'));
                } elseif ($atts['download_text'] === '') {
                    $download_text = eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar'));
                }
            }
        }

        $wrap_classes = array(
            'eshterakyar-product-cta',
            'eshterakyar-product-cta-state-' . sanitize_html_class($state),
            'eshterakyar-product-cta-align-' . sanitize_html_class($align),
            'eshterakyar-style-wrapper',
            $style_class,
        );
        if ($atts['class'] !== '') {
            foreach (preg_split('/\s+/', trim((string) $atts['class'])) as $class_token) {
                $safe_class = sanitize_html_class($class_token);
                if ($safe_class !== '') {
                    $wrap_classes[] = $safe_class;
                }
            }
        }

        ob_start();
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/shortcodes/product-cta.php';
        return ob_get_clean();
    }

    public function user_subscriptions_shortcode($atts) {
        $this->ensure_frontend_assets();
        if (!is_user_logged_in()) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            ob_start();
            echo '<div class="eshterakyar-no-subscription">';
            echo '<p>' . esc_html__('برای مشاهده اشتراک‌های خود باید وارد حساب کاربری شوید.', 'eshterakyar') . '</p>';
            if ($subscription_page_url) {
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                echo esc_html(eshterakyar_get_label('buy_subscription', __('خرید اشتراک', 'eshterakyar')));
                echo '</a>';
            }
            echo '</div>';
            return ob_get_clean();
        }
        
        $atts = shortcode_atts(array(
            'status' => 'all',
            'show_expired' => 'yes'
        ), $atts);
        
        $user_id = get_current_user_id();
        $subscriptions = Eshterakyar_Subscription::get_user_subscriptions($user_id);
        
        if ($atts['status'] != 'all') {
            $subscriptions = array_filter($subscriptions, function($sub) use ($atts) {
                return $sub->status == $atts['status'];
            });
        }
        
        if ($atts['show_expired'] == 'no') {
            $subscriptions = array_filter($subscriptions, function($sub) {
                return $sub->status != 'expired';
            });
        }
        
        ob_start();
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/shortcodes/user-subscriptions.php';
        return ob_get_clean();
    }
    
    public function subscription_downloads_shortcode($atts) {
        $this->ensure_frontend_assets();
        if (!is_user_logged_in()) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            ob_start();
            echo '<div class="eshterakyar-no-subscription">';
            echo '<p>' . esc_html__('برای مشاهده دانلودهای خود باید وارد حساب کاربری شوید.', 'eshterakyar') . '</p>';
            if ($subscription_page_url) {
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                echo esc_html(eshterakyar_get_label('buy_subscription', __('خرید اشتراک', 'eshterakyar')));
                echo '</a>';
            }
            echo '</div>';
            return ob_get_clean();
        }
        
        $atts = shortcode_atts(array(
            'limit' => 20
        ), $atts);
        
        $user_id = get_current_user_id();
        $active_subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        
        if (!$active_subscription) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            ob_start();
            echo '<div class="eshterakyar-no-subscription">';
            echo '<p>' . esc_html__('شما اشتراک فعالی ندارید.', 'eshterakyar') . '</p>';
            if ($subscription_page_url) {
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                echo esc_html(eshterakyar_get_label('buy_subscription', __('خرید اشتراک', 'eshterakyar')));
                echo '</a>';
            }
            echo '</div>';
            return ob_get_clean();
        }
        
        $downloads = Eshterakyar_Download::get_user_downloads($user_id, $active_subscription->id);
        $downloads = array_slice($downloads, 0, intval($atts['limit']));
        
        ob_start();
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/shortcodes/downloads.php';
        return ob_get_clean();
    }
    
    public function subscription_days_left_shortcode($atts) {
        $this->ensure_frontend_assets();
        $atts = shortcode_atts(array(
            'format' => 'days' 
        ), $atts);
        

        if (!is_user_logged_in()) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            if ($subscription_page_url) {
                return '<div class="eshterakyar-no-subscription">' . 
                       '<p>' . __('برای مشاهده روزهای باقی‌مانده اشتراک، باید وارد حساب کاربری شوید.', 'eshterakyar') . '</p>' .
                       '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">' . 
                       esc_html(eshterakyar_get_label('buy_subscription', __('خرید اشتراک', 'eshterakyar'))) . '</a>' .
                       '</div>';
            }
            return '<span class="eshterakyar-no-subscription">' . __('برای مشاهده روزهای باقی‌مانده اشتراک، باید وارد حساب کاربری شوید.', 'eshterakyar') . '</span>';
        }
        
        $user_id = get_current_user_id();
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        
        if (!$subscription) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            if ($subscription_page_url) {
                return '<div class="eshterakyar-no-subscription">' . 
                       '<span>' . __('اشتراک فعالی ندارید', 'eshterakyar') . '</span>' .
                       '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">' . 
                       esc_html(eshterakyar_get_label('buy_subscription', __('خرید اشتراک', 'eshterakyar'))) . '</a>' .
                       '</div>';
            }
            return '<span class="eshterakyar-no-subscription">' . __('اشتراک فعالی ندارید', 'eshterakyar') . '</span>';
        }
        
        $days_left = Eshterakyar_Subscription::get_days_left($subscription->id);
        
        if ($days_left <= 0) {
            return '<span class="eshterakyar-expired">' . __('اشتراک منقضی شده', 'eshterakyar') . '</span>';
        }
        
        if ($atts['format'] == 'full') {
            $end_date = date_i18n(get_option('date_format'), strtotime($subscription->end_date));
            /* translators: %1$d: Number of days left, %2$s: End date */
            return sprintf(__('%1$d روز باقی مانده (تا %2$s)', 'eshterakyar'), $days_left, $end_date);
        }
        
        /* translators: %d: Number of days left */
        return '<span class="eshterakyar-days-left">' . sprintf(__('%d روز', 'eshterakyar'), $days_left) . '</span>';
    }
    
    public function subscription_expired_shortcode($atts) {
        $this->ensure_frontend_assets();
        $atts = shortcode_atts(array(
            'message' => __('اشتراک شما منقضی شده است. لطفاً برای ادامه استفاده، اشتراک جدید خریداری کنید.', 'eshterakyar')
        ), $atts);
        
        if (!is_user_logged_in()) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            ob_start();
            echo '<div class="eshterakyar-expired-message">';
            echo '<p>' . esc_html__('برای استفاده از این بخش، باید وارد حساب کاربری شوید.', 'eshterakyar') . '</p>';
            if ($subscription_page_url) {
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                echo esc_html(eshterakyar_get_label('buy_subscription', __('خرید اشتراک', 'eshterakyar')));
                echo '</a>';
            }
            echo '</div>';
            return ob_get_clean();
        }
        
        $user_id = get_current_user_id();
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        
        if (!$subscription) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            ob_start();
            echo '<div class="eshterakyar-expired-message">';
            echo '<p>' . esc_html($atts['message']) . '</p>';
            if ($subscription_page_url) {
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                echo esc_html(eshterakyar_get_label('buy_subscription', __('خرید اشتراک', 'eshterakyar')));
                echo '</a>';
            }
            echo '</div>';
            return ob_get_clean();
        }
        
        return '';
    }
    
    public function product_download_status_shortcode($atts) {
        $this->ensure_frontend_assets();
        global $product;
        
        $atts = shortcode_atts(array(
            'product_id' => 0
        ), $atts);
        
        $product_id = $atts['product_id'] ? intval($atts['product_id']) : (is_product() ? get_the_ID() : 0);
        
        if (!$product_id) {
            return '';
        }
        if ($this->should_hide_for_out_of_stock_product($product_id)) {
            return $this->get_out_of_stock_subscription_message_html();
        }
        
        $options = get_option('eshterakyar_options', array());
        $show_for_non_subscription = isset($options['show_download_box_for_non_subscription_products']) ? $options['show_download_box_for_non_subscription_products'] : 1;
        
        if (!$show_for_non_subscription && !Eshterakyar_Subscription::is_product_subscription_downloadable($product_id)) {
            return '';
        }
        
        if (!is_user_logged_in()) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            ob_start();
            echo '<div class="eshterakyar-no-subscription-alert">';
            echo '<p>' . esc_html__('برای دانلود رایگان این محصول نیاز به اشتراک فعال دارید.', 'eshterakyar') . '</p>';
            if ($subscription_page_url) {
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                echo esc_html(eshterakyar_get_label('buy_subscription_free_download', __('خرید اشتراک و دانلود رایگان', 'eshterakyar')));
                echo '</a>';
            }
            echo '</div>';
            return ob_get_clean();
        }
        
        $user_id = get_current_user_id();
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        
        if (!$subscription) {
            $is_downloaded_any = Eshterakyar_Download::is_downloaded_any_subscription($user_id, $product_id);
            $can_download = Eshterakyar_Subscription::can_download($user_id, $product_id, $is_downloaded_any);

            if (!empty($can_download['can']) && !empty($can_download['lifetime_access'])) {
                $download_url = Eshterakyar_Download::get_tokenized_download_url($user_id, $product_id, null);
                if (!$download_url) {
                    $download_url = '#';
                }

                ob_start();
                echo '<div class="eshterakyar-lifetime-download-section">';
                echo '<p class="eshterakyar-lifetime-access-note description" style="margin: 0 0 15px; color: #22c55e;">';
                echo esc_html(eshterakyar_get_label('lifetime_access_note', __('دسترسی مادام‌العمر: شما می‌توانید این فایل را در هر زمان، حتی بدون اشتراک فعال، دانلود کنید.', 'eshterakyar')));
                echo '</p>';
                echo '<div class="eshterakyar-download-button-wrapper">';
                echo '<a href="' . esc_url($download_url) . '" class="button eshterakyar-download-button" data-product-id="' . esc_attr($product_id) . '" target="_blank" rel="noopener noreferrer">';
                echo esc_html(eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar')));
                echo '</a>';
                echo '</div>';
                echo '</div>';
                return ob_get_clean();
            }

            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            ob_start();
            echo '<div class="eshterakyar-no-subscription-alert">';
            echo '<p>' . esc_html__('برای دانلود رایگان این محصول نیاز به اشتراک فعال دارید.', 'eshterakyar') . '</p>';
            if ($subscription_page_url) {
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                echo esc_html(eshterakyar_get_label('buy_subscription_free_download', __('خرید اشتراک و دانلود رایگان', 'eshterakyar')));
                echo '</a>';
            }
            echo '</div>';
            return ob_get_clean();
        }
        
        $is_downloaded = Eshterakyar_Download::is_downloaded($user_id, $subscription->id, $product_id);
        $can_download = Eshterakyar_Subscription::can_download($user_id, $product_id, $is_downloaded);
        
        $options = get_option('eshterakyar_options', array());
        $frontend_style = isset($options['frontend_style']) ? $options['frontend_style'] : 'style1';
        $style_class = $frontend_style === 'style2' ? 'eshterakyar-style-2' : 'eshterakyar-style-1';

        ob_start();
        echo '<div class="eshterakyar-style-wrapper ' . esc_attr($style_class) . '">';
        include ESHTERAKYAR_PLUGIN_DIR . 'templates/shortcodes/product-download-status.php';
        echo '</div>';
        return ob_get_clean();
    }
    
    public function subscription_info_shortcode($atts) {
        $this->ensure_frontend_assets();
        $atts = shortcode_atts(array(
            'show_plan' => 'yes',
            'show_days' => 'yes',
            'show_downloads' => 'yes',
            'show_download_button' => 'yes' 
        ), $atts);
        
        if (is_product()) {
            global $product;
            if ($product) {
                $product_id = $product->get_id();
                if ($this->should_hide_for_out_of_stock_product($product_id)) {
                    return $this->get_out_of_stock_subscription_message_html();
                }
                
                $options = get_option('eshterakyar_options', array());
                $show_for_non_subscription = isset($options['show_download_box_for_non_subscription_products']) ? $options['show_download_box_for_non_subscription_products'] : 1;
                
                if (!$show_for_non_subscription && !Eshterakyar_Subscription::is_product_subscription_downloadable($product_id)) {
                    return '';
                }
            }
        }
        
        $options = get_option('eshterakyar_options', array());
        $frontend_style = isset($options['frontend_style']) ? $options['frontend_style'] : 'style1';
        $style_class = $frontend_style === 'style2' ? 'eshterakyar-style-2' : 'eshterakyar-style-1';
        $is_product_page = is_product();
        $use_style2_simple = ($style_class === 'eshterakyar-style-2' && $is_product_page);
        // Always use card layout so labels/values don't collide in Elementor narrow columns.
        $account_panel_class = ' eshterakyar-panel-context eshterakyar-subscription-info-card';
        
        if (!is_user_logged_in()) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            ob_start();
            echo '<div class="eshterakyar-style-wrapper ' . esc_attr($style_class) . esc_attr($account_panel_class) . '">';
            
            if ($use_style2_simple) {
                if ($subscription_page_url) {
                    echo '<div class="eshterakyar-download-button-wrapper">';
                    echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                    echo esc_html(eshterakyar_get_label('buy_subscription_free_download', __('خرید اشتراک و دانلود رایگان', 'eshterakyar')));
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                echo '<div class="eshterakyar-subscription-status">';
                echo '<h4 class="eshterakyar-subscription-title">' . esc_html(eshterakyar_get_label('buy_subscription', __('خرید اشتراک', 'eshterakyar'))) . '</h4>';
                echo '<p class="eshterakyar-subscription-note">' . esc_html__('برای مشاهده اطلاعات اشتراک و دانلود رایگان محصولات، باید وارد حساب کاربری شوید.', 'eshterakyar') . '</p>';
                if ($subscription_page_url) {
                    echo '<div class="eshterakyar-download-button-wrapper">';
                    echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                    echo esc_html(eshterakyar_get_label('buy_subscription_free_download', __('خرید اشتراک و دانلود رایگان', 'eshterakyar')));
                    echo '</a>';
                    echo '</div>';
                }
                echo '</div>';
            }
            
            echo '</div>';
            return ob_get_clean();
        }
        
        $user_id = get_current_user_id();
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        
        if (!$subscription && $is_product_page) {
            global $product;
            if ($product) {
                $lifetime_product_id = $product->get_id();
                $is_downloaded_any = Eshterakyar_Download::is_downloaded_any_subscription($user_id, $lifetime_product_id);
                $can_download = Eshterakyar_Subscription::can_download($user_id, $lifetime_product_id, $is_downloaded_any);

                if (!empty($can_download['can']) && !empty($can_download['lifetime_access'])) {
                    $download_url = Eshterakyar_Download::get_tokenized_download_url($user_id, $lifetime_product_id, null);
                    if (!$download_url) {
                        $download_url = '#';
                    }

                    ob_start();
                    echo '<div class="eshterakyar-style-wrapper ' . esc_attr($style_class) . esc_attr($account_panel_class) . '">';
                    echo '<div class="eshterakyar-subscription-status eshterakyar-lifetime-download-section">';
                    echo '<h4 class="eshterakyar-subscription-title">' . esc_html(eshterakyar_get_label('subscription_info', __('اطلاعات اشتراک', 'eshterakyar'))) . '</h4>';
                    echo '<p class="eshterakyar-lifetime-access-note description" style="margin: 0 0 15px; color: #22c55e;">';
                    echo esc_html(eshterakyar_get_label('lifetime_access_note', __('دسترسی مادام‌العمر: شما می‌توانید این فایل را در هر زمان، حتی بدون اشتراک فعال، دانلود کنید.', 'eshterakyar')));
                    echo '</p>';

                    if ($atts['show_download_button'] == 'yes') {
                        echo '<div class="eshterakyar-download-button-wrapper">';
                        echo '<a href="' . esc_url($download_url) . '" class="button eshterakyar-download-button" data-product-id="' . esc_attr($lifetime_product_id) . '" target="_blank" rel="noopener noreferrer">';
                        echo esc_html(eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar')));
                        echo '</a>';
                        echo '</div>';
                    }

                    echo '</div>';
                    echo '</div>';
                    return ob_get_clean();
                }
            }
        }

        if (!$subscription) {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            ob_start();
            echo '<div class="eshterakyar-style-wrapper ' . esc_attr($style_class) . esc_attr($account_panel_class) . '">';
            
            if ($use_style2_simple) {
                if ($subscription_page_url) {
                    echo '<div class="eshterakyar-download-button-wrapper">';
                    echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                    echo esc_html(eshterakyar_get_label('buy_subscription_free_download', __('خرید اشتراک و دانلود رایگان', 'eshterakyar')));
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                echo '<div class="eshterakyar-subscription-status">';
                echo '<h4 class="eshterakyar-subscription-title">' . esc_html(eshterakyar_get_label('subscription_info', __('اطلاعات اشتراک', 'eshterakyar'))) . '</h4>';
                echo '<p class="eshterakyar-subscription-note">' . esc_html__('شما اشتراک فعالی ندارید. برای دانلود رایگان محصولات، اشتراک خریداری کنید.', 'eshterakyar') . '</p>';
                if ($subscription_page_url) {
                    echo '<div class="eshterakyar-download-button-wrapper">';
                    echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                    echo esc_html(eshterakyar_get_label('buy_subscription_free_download', __('خرید اشتراک و دانلود رایگان', 'eshterakyar')));
                    echo '</a>';
                    echo '</div>';
                }
                echo '</div>';
            }
            
            echo '</div>';
            return ob_get_clean();
        }

        ob_start();
        echo '<div class="eshterakyar-style-wrapper ' . esc_attr($style_class) . esc_attr($account_panel_class) . '">';
        
        $use_collapsible = ($style_class === 'eshterakyar-style-2' && $is_product_page);

        if ($use_collapsible) {
            echo '<button type="button" class="button eshterakyar-subscription-toggle-button">';
            echo esc_html(eshterakyar_get_label('show_subscription_download_info', __('نمایش اطلاعات اشتراک و دانلود', 'eshterakyar')));
            echo '</button>';
            echo '<div class="eshterakyar-subscription-collapsible" style="display:none;">';
        }

        $product_id = 0;
        $can_download_product = true;
        $product_access_message = '';
        
        if ($is_product_page) {
            global $product;
            if ($product) {
                $product_id = $product->get_id();
                
                $options = get_option('eshterakyar_options', array());
                $show_for_non_subscription = isset($options['show_download_box_for_non_subscription_products']) ? $options['show_download_box_for_non_subscription_products'] : 1;
                
                $can_download_product = Eshterakyar_Subscription::is_product_subscription_downloadable($product_id, $user_id);
                
                if (!$show_for_non_subscription && !$can_download_product) {
                    echo '<div class="eshterakyar-subscription-status">';
                    echo '<h4 class="eshterakyar-subscription-title">' . esc_html(eshterakyar_get_label('subscription_info', __('اطلاعات اشتراک', 'eshterakyar'))) . '</h4>';
                    echo '<div class="eshterakyar-download-error">';
                    echo '<p class="eshterakyar-alert">' . esc_html__('این محصول در دسترس پلن شما نیست', 'eshterakyar') . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    return ob_get_clean();
                }
                
                $plan = Eshterakyar_Plan::get_by_id($subscription->plan_id);
                if ($plan && !Eshterakyar_Subscription::check_product_access($product_id, $plan)) {
                    $can_download_product = false;
                    $product_access_message = __('این محصول در دسترس پلن شما نیست', 'eshterakyar');
                }
            }
        }
        
        $days_left = Eshterakyar_Subscription::get_days_left($subscription->id);
        
        echo '<div class="eshterakyar-subscription-status">';
        echo '<h4 class="eshterakyar-subscription-title">' . esc_html(eshterakyar_get_label('subscription_info', __('اطلاعات اشتراک', 'eshterakyar'))) . '</h4>';
        
        echo '<div class="eshterakyar-subscription-details">';
        
        if ($atts['show_plan'] == 'yes') {
            echo '<div class="eshterakyar-subscription-detail-item">';
            echo '<span class="eshterakyar-detail-label">' . esc_html__('اشتراک:', 'eshterakyar') . '</span>';
            echo '<span class="eshterakyar-detail-value">' . esc_html($subscription->plan_name) . '</span>';
            echo '</div>';
        }
        
        if ($atts['show_days'] == 'yes') {
            echo '<div class="eshterakyar-subscription-detail-item">';
            echo '<span class="eshterakyar-detail-label">' . esc_html__('روزهای باقی‌مانده:', 'eshterakyar') . '</span>';
            echo '<span class="eshterakyar-detail-value">' . absint($days_left) . ' ' . esc_html__('روز', 'eshterakyar') . '</span>';
            echo '</div>';
        }
        
        $is_total_exhausted = false;
        $is_daily_exhausted = false;
        
        if ($atts['show_downloads'] == 'yes') {
            if ($subscription->total_downloads > 0) {
                $remaining_downloads = $subscription->total_downloads - $subscription->downloads_used;
                if ($remaining_downloads <= 0) {
                    $remaining_downloads = 0;
                    $is_total_exhausted = true;
                }
                echo '<div class="eshterakyar-subscription-detail-item">';
                echo '<span class="eshterakyar-detail-label">' . esc_html__('دانلود باقی‌مانده:', 'eshterakyar') . '</span>';
                echo '<span class="eshterakyar-detail-value">' . absint($remaining_downloads) . ' / ' . absint($subscription->total_downloads) . '</span>';
                echo '</div>';
            } else {
                echo '<div class="eshterakyar-subscription-detail-item">';
                echo '<span class="eshterakyar-detail-label">' . esc_html__('دانلود باقی‌مانده:', 'eshterakyar') . '</span>';
                echo '<span class="eshterakyar-detail-value">' . esc_html__('نامحدود', 'eshterakyar') . '</span>';
                echo '</div>';
            }
            
            if ($subscription->daily_limit > 0) {
                global $wpdb;
                $today = current_time('Y-m-d');
                $today_start = $today . ' 00:00:00';
                $today_end = $today . ' 23:59:59';
                $downloads_table = $wpdb->prefix . 'subscription_downloads';
                $daily_count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT product_id) FROM `{$downloads_table}` 
                     WHERE subscription_id = %d AND download_date >= %s AND download_date <= %s",
                    $subscription->id, $today_start, $today_end
                ));
                $remaining_daily = $subscription->daily_limit - intval($daily_count);
                if ($remaining_daily <= 0) {
                    $remaining_daily = 0;
                    $is_daily_exhausted = true;
                }
                echo '<div class="eshterakyar-subscription-detail-item">';
                echo '<span class="eshterakyar-detail-label">' . esc_html__('دانلود امروز:', 'eshterakyar') . '</span>';
                echo '<span class="eshterakyar-detail-value">' . absint($remaining_daily) . ' / ' . absint($subscription->daily_limit) . '</span>';
                echo '</div>';
            }
        }
        
        echo '</div>';
        
        if ($is_product_page && $product_id > 0 && !empty($product_access_message)) {
            echo '<div class="eshterakyar-download-error">';
            echo '<p class="eshterakyar-alert">' . esc_html($product_access_message) . '</p>';
            echo '</div>';
        } else {
            $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
            if ($subscription_page_url && ($is_total_exhausted || $is_daily_exhausted)) {
                echo '<div class="eshterakyar-renew-subscription-inline">';
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-renew-subscription-button">';
                echo esc_html(eshterakyar_get_label('renew_subscription', __('تمدید اشتراک', 'eshterakyar')));
                echo '</a>';
                echo '<p class="eshterakyar-renew-note">' . esc_html(eshterakyar_get_label('renew_note', __('با تمدید اشتراک، محدودیت‌های دانلود شما بازنشانی می‌شود.', 'eshterakyar'))) . '</p>';
                echo '</div>';
            }
        }
        
        if ($atts['show_download_button'] == 'yes' && is_product()) {
            global $product;
            if ($product) {
                $product_id = $product->get_id();
                
                $options = get_option('eshterakyar_options', array());
                $show_for_non_subscription = isset($options['show_download_box_for_non_subscription_products']) ? $options['show_download_box_for_non_subscription_products'] : 1;
                
                if (!$show_for_non_subscription && !Eshterakyar_Subscription::is_product_subscription_downloadable($product_id)) {
                } else {
                    $is_downloaded = Eshterakyar_Download::is_downloaded($user_id, $subscription->id, $product_id);
                    $can_download = Eshterakyar_Subscription::can_download($user_id, $product_id, $is_downloaded);
                
                if ($can_download['can']) {
                    $download_url = Eshterakyar_Download::get_tokenized_download_url($user_id, $product_id, $subscription->id);
                    if (!$download_url) {
                        $download_url = '#';
                    }
                    
                    $is_downloaded_any = Eshterakyar_Download::is_downloaded_any_subscription($user_id, $product_id);
                    
                    if ($is_downloaded || $is_downloaded_any) {
                        echo '<div class="eshterakyar-downloaded-badge">';
                        echo '<span class="dashicons dashicons-yes-alt"></span> ' . esc_html(eshterakyar_get_label('already_downloaded', __('این محصول قبلاً دانلود شده است', 'eshterakyar')));
                        if ($is_downloaded_any && !$is_downloaded) {
                            echo '<span class="eshterakyar-previous-subscription-note">' . esc_html__('(در دوره اشتراک قبلی)', 'eshterakyar') . '</span>';
                        }
                        echo '</div>';
                    }

                    $redownload_uses_quota = !empty($can_download['redownload_uses_quota']);
                    if ($redownload_uses_quota) {
                        echo '<p class="eshterakyar-redownload-quota-note">' . esc_html(eshterakyar_get_label('redownload_with_quota_note', __('با کلیک، یک بار از سهمیه روزانه/کلی شما کسر می‌شود.', 'eshterakyar'))) . '</p>';
                    }

                    $multipart_sc = Eshterakyar_Download::render_multipart_download_list_html($product_id, $redownload_uses_quota);
                    echo '<div class="eshterakyar-download-button-wrapper">';
                    if ($multipart_sc !== '') {
                        echo $multipart_sc;
                    } else {
                        echo '<a href="' . esc_url($download_url) . '" class="button eshterakyar-download-button' . ($redownload_uses_quota ? ' eshterakyar-redownload-with-quota' : '') . '" data-product-id="' . esc_attr($product_id) . '" target="_blank" rel="noopener noreferrer">';
                        if ($redownload_uses_quota) {
                            echo esc_html(eshterakyar_get_label('redownload_with_quota', __('دانلود مجدد با کسر اعتبار', 'eshterakyar')));
                        } else {
                            echo ($is_downloaded || $is_downloaded_any) ? esc_html(eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar'))) : esc_html(eshterakyar_get_label('download_with_subscription', __('دانلود رایگان با اشتراک', 'eshterakyar')));
                        }
                        echo '</a>';
                    }
                    echo '</div>';
                } else {
                    $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
                    echo '<div class="eshterakyar-download-error">';
                    echo '<p class="eshterakyar-alert">' . esc_html($can_download['message']) . '</p>';
                    
                    if ($subscription_page_url) {
                        echo '<div class="eshterakyar-renew-subscription-action">';
                        echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-renew-subscription-button">';
                        echo esc_html(eshterakyar_get_label('renew_subscription', __('تمدید اشتراک', 'eshterakyar')));
                        echo '</a>';
                        echo '<p class="eshterakyar-renew-note">' . esc_html(eshterakyar_get_label('renew_note', __('با تمدید اشتراک، محدودیت‌های دانلود شما بازنشانی می‌شود.', 'eshterakyar'))) . '</p>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                }
                }
            }
        }
        
        echo '</div>';

        if ($use_collapsible) {
            echo '</div>';
        }

        echo '</div>';
        
        return ob_get_clean();
    }

    /**
     * Compact subscription badge button with hover tooltip.
     *
     * @param array|string $atts
     * @return string
     */
    public function subscription_badge_shortcode($atts) {
        $this->ensure_frontend_assets();
        $atts = shortcode_atts(array(
            'variant' => 'default',
            'align' => 'inline',
            'tooltip_position' => 'bottom',
            'show_plan' => 'yes',
            'show_days' => 'yes',
            'show_downloads' => 'yes',
            'show_daily' => 'no',
            'show_expiry' => 'no',
            'show_tooltip_title' => 'yes',
            'active_text' => '',
            'inactive_text' => '',
            'login_text' => '',
            'tooltip_title' => '',
            'plans_url' => '',
            'account_url' => '',
        ), $atts, 'subscription_badge');

        $variants = array('default', 'pill', 'outline', 'minimal', 'dark');
        $variant = in_array($atts['variant'], $variants, true) ? $atts['variant'] : 'default';

        $alignments = array('inline', 'left', 'center', 'right');
        $align = in_array($atts['align'], $alignments, true) ? $atts['align'] : 'inline';

        $tooltip_positions = array('top', 'bottom', 'left', 'right');
        $tooltip_position = in_array($atts['tooltip_position'], $tooltip_positions, true) ? $atts['tooltip_position'] : 'bottom';

        $plans_url = !empty($atts['plans_url']) ? esc_url($atts['plans_url']) : Eshterakyar_Subscription_Page::get_page_url();
        if (!$plans_url) {
            $plans_url = home_url('/');
        }

        $account_url = !empty($atts['account_url']) ? esc_url($atts['account_url']) : '';
        if (!$account_url && function_exists('wc_get_page_permalink')) {
            $account_url = wc_get_page_permalink('myaccount');
        }
        if (!$account_url) {
            $account_url = wp_login_url();
        }

        $active_text = $atts['active_text'] !== ''
            ? $atts['active_text']
            : eshterakyar_get_label('subscription_badge_active', __('شما اشتراک دارید!', 'eshterakyar'));
        $inactive_text = $atts['inactive_text'] !== ''
            ? $atts['inactive_text']
            : eshterakyar_get_label('subscription_badge_inactive', __('خرید اشتراک', 'eshterakyar'));
        $login_text = $atts['login_text'] !== ''
            ? $atts['login_text']
            : eshterakyar_get_label('subscription_badge_login', __('ورود / خرید اشتراک', 'eshterakyar'));
        $tooltip_title = $atts['tooltip_title'] !== ''
            ? $atts['tooltip_title']
            : eshterakyar_get_label('subscription_info', __('اطلاعات اشتراک', 'eshterakyar'));

        $wrap_classes = array(
            'eshterakyar-subscription-badge-wrap',
            'eshterakyar-badge-variant-' . sanitize_html_class($variant),
            'eshterakyar-badge-align-' . sanitize_html_class($align),
        );

        ob_start();
        echo '<div class="' . esc_attr(implode(' ', $wrap_classes)) . '">';

        if (!is_user_logged_in()) {
            echo '<a href="' . esc_url($plans_url) . '" class="eshterakyar-subscription-badge eshterakyar-badge-state-guest">';
            echo '<span class="eshterakyar-badge-button-text">' . esc_html($login_text) . '</span>';
            echo '</a>';
            echo '</div>';
            return ob_get_clean();
        }

        $user_id = get_current_user_id();
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);

        if (!$subscription) {
            echo '<a href="' . esc_url($plans_url) . '" class="eshterakyar-subscription-badge eshterakyar-badge-state-inactive">';
            echo '<span class="eshterakyar-badge-button-text">' . esc_html($inactive_text) . '</span>';
            echo '</a>';
            echo '</div>';
            return ob_get_clean();
        }

        $tooltip_rows = $this->get_subscription_badge_tooltip_rows($subscription, $atts);
        $badge_id = wp_unique_id('eshterakyar-badge-');

        echo '<div id="' . esc_attr($badge_id) . '" class="eshterakyar-subscription-badge eshterakyar-badge-state-active eshterakyar-badge-has-tooltip" tabindex="0" role="button" aria-expanded="false" aria-controls="' . esc_attr($badge_id) . '-tooltip">';
        echo '<span class="eshterakyar-badge-button">';
        echo '<span class="eshterakyar-badge-icon" aria-hidden="true"></span>';
        echo '<span class="eshterakyar-badge-button-text">' . esc_html($active_text) . '</span>';
        echo '</span>';

        if (!empty($tooltip_rows)) {
            echo '<div id="' . esc_attr($badge_id) . '-tooltip" class="eshterakyar-badge-tooltip eshterakyar-tooltip-pos-' . esc_attr($tooltip_position) . '" role="tooltip">';
            if ($this->shortcode_flag_enabled($atts['show_tooltip_title'])) {
                echo '<div class="eshterakyar-badge-tooltip-title">' . esc_html($tooltip_title) . '</div>';
            }
            echo '<ul class="eshterakyar-badge-tooltip-list">';
            foreach ($tooltip_rows as $row) {
                echo '<li>';
                echo '<span class="eshterakyar-badge-tooltip-label">' . esc_html($row['label']) . '</span> ';
                echo '<span class="eshterakyar-badge-tooltip-value">' . esc_html($row['value']) . '</span>';
                echo '</li>';
            }
            echo '</ul>';
            if ($account_url) {
                echo '<a class="eshterakyar-badge-tooltip-link" href="' . esc_url($account_url) . '">';
                echo esc_html(eshterakyar_get_label('subscription_badge_account_link', __('مشاهده حساب کاربری', 'eshterakyar')));
                echo '</a>';
            }
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';

        return ob_get_clean();
    }
}

