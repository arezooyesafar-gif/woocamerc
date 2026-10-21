<?php

if (!defined('ABSPATH')) {
    exit;
}

class Eshterakyar_WooCommerce_Integration {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('woocommerce_single_product_summary', array($this, 'add_subscription_download_button'), 25);
        
        add_action('woocommerce_before_single_product_summary', array($this, 'add_downloaded_badge'), 5);
        
        add_action('woocommerce_order_status_completed', array($this, 'process_subscription_purchase'), 10, 1);
        
        add_action('woocommerce_order_status_changed', array($this, 'process_subscription_purchase_on_status_change'), 10, 3);
        
        add_action('woocommerce_payment_complete', array($this, 'process_subscription_purchase'), 10, 1);
        
        add_action('add_meta_boxes', array($this, 'add_product_meta_box'));
        add_action('save_post', array($this, 'save_product_meta_box'));
        
        add_filter('woocommerce_is_purchasable', array($this, 'check_subscription_required'), 10, 2);
        
        add_filter('woocommerce_add_to_cart_redirect', array($this, 'redirect_to_checkout_from_plans'), 10, 1);
    }
    

    public function process_subscription_purchase_on_status_change($order_id, $old_status, $new_status) {
        if ($new_status === 'completed' && $old_status !== 'completed') {
            $order = wc_get_order($order_id);
            if ($order) {
                $already_processed = $order->get_meta('_eshterakyar_subscription_processed', true);
                if (!$already_processed) {
                    $this->process_subscription_purchase($order_id);
                }
            }
        }
    }
    public function add_subscription_download_button() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        
        $options = get_option('eshterakyar_options', array());
        $show_for_non_subscription = isset($options['show_download_box_for_non_subscription_products']) ? $options['show_download_box_for_non_subscription_products'] : 1;
        
        if (!$show_for_non_subscription && !Eshterakyar_Subscription::is_product_subscription_downloadable($product_id)) {
            return;
        }
        
        $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
        
        if (!is_user_logged_in()) {
            echo '<div class="eshterakyar-subscription-actions">';
            echo '<div class="eshterakyar-action-buttons">';
            
            if ($subscription_page_url) {
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                echo esc_html(eshterakyar_get_label('buy_subscription_free_download', __('خرید اشتراک و دانلود رایگان', 'eshterakyar')));
                echo '</a>';
            }
            
            echo '</div>';
            echo '<p class="eshterakyar-subscription-note">' . esc_html__('برای دانلود رایگان این محصول نیاز به اشتراک فعال دارید.', 'eshterakyar') . '</p>';
            echo '</div>';
            return;
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

                echo '<div class="eshterakyar-download-section eshterakyar-lifetime-download-section">';
                echo '<div class="eshterakyar-subscription-status">';
                echo '<h4 class="eshterakyar-subscription-title">' . esc_html(eshterakyar_get_label('download_with_subscription', __('دانلود رایگان با اشتراک', 'eshterakyar'))) . '</h4>';
                echo '<p class="eshterakyar-lifetime-access-note description" style="margin: 0 0 15px; color: #22c55e;">';
                echo esc_html(eshterakyar_get_label('lifetime_access_note', __('دسترسی مادام‌العمر: شما می‌توانید این فایل را در هر زمان، حتی بدون اشتراک فعال، دانلود کنید.', 'eshterakyar')));
                echo '</p>';

                if ($is_downloaded_any) {
                    echo '<div class="eshterakyar-downloaded-badge">';
                    echo '<span class="dashicons dashicons-yes-alt"></span> ' . esc_html(eshterakyar_get_label('already_downloaded', __('این محصول قبلاً دانلود شده است', 'eshterakyar')));
                    echo '</div>';
                }

                $multipart_html = Eshterakyar_Download::render_multipart_download_list_html($product_id, false, array(
                    'link_class' => 'button eshterakyar-download-part-link',
                ));
                if ($multipart_html !== '') {
                    echo '<div class="eshterakyar-download-button-wrapper eshterakyar-download-multipart-wrap">';
                    echo $multipart_html;
                    echo '</div>';
                } else {
                    echo '<div class="eshterakyar-download-button-wrapper">';
                    echo '<a href="' . esc_url($download_url) . '" class="button eshterakyar-download-button" data-product-id="' . esc_attr($product_id) . '" target="_blank" rel="noopener noreferrer">';
                    echo esc_html(eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar')));
                    echo '</a>';
                    echo '</div>';
                }

                echo '</div>';
                echo '</div>';
                return;
            }

            echo '<div class="eshterakyar-subscription-actions">';
            echo '<div class="eshterakyar-action-buttons">';
            
            if ($subscription_page_url) {
                echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-buy-subscription-button">';
                echo esc_html(eshterakyar_get_label('buy_subscription_free_download', __('خرید اشتراک و دانلود رایگان', 'eshterakyar')));
                echo '</a>';
            }
            
            echo '</div>';
            echo '<p class="eshterakyar-subscription-note">' . esc_html__('برای دانلود رایگان این محصول نیاز به اشتراک فعال دارید.', 'eshterakyar') . '</p>';
            echo '</div>';
            return;
        }
        
        $is_downloaded = Eshterakyar_Download::is_downloaded($user_id, $subscription->id, $product_id);
        $can_download = Eshterakyar_Subscription::can_download($user_id, $product_id, $is_downloaded);
        
        $is_downloaded_any = Eshterakyar_Download::is_downloaded_any_subscription($user_id, $product_id);
        
        if (!$can_download['can']) {
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
            return;
        }
        
        $download_url = Eshterakyar_Download::get_tokenized_download_url($user_id, $product_id, $subscription->id);
        if (!$download_url) {
            $download_url = '#';
        }
        
        echo '<div class="eshterakyar-download-section">';
        
        $subscription_page_url = Eshterakyar_Subscription_Page::get_page_url();
        $days_left = Eshterakyar_Subscription::get_days_left($subscription->id);
        $plan = Eshterakyar_Plan::get_by_id($subscription->plan_id);
        
        echo '<div class="eshterakyar-subscription-status">';
        echo '<h4 class="eshterakyar-subscription-title">' . esc_html(eshterakyar_get_label('download_with_subscription', __('دانلود رایگان با اشتراک', 'eshterakyar'))) . '</h4>';
        
        echo '<div class="eshterakyar-subscription-details">';
        echo '<div class="eshterakyar-subscription-detail-item">';
        echo '<span class="eshterakyar-detail-label">' . esc_html__('اشتراک:', 'eshterakyar') . '</span>';
        echo '<span class="eshterakyar-detail-value">' . esc_html($subscription->plan_name) . '</span>';
        echo '</div>';
        
        echo '<div class="eshterakyar-subscription-detail-item">';
        echo '<span class="eshterakyar-detail-label">' . esc_html__('روزهای باقی‌مانده:', 'eshterakyar') . '</span>';
        echo '<span class="eshterakyar-detail-value">' . absint($days_left) . ' ' . esc_html__('روز', 'eshterakyar') . '</span>';
        echo '</div>';
        
        $is_total_exhausted = false;
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
        
        $is_daily_exhausted = false;
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
        
        
        if ($subscription_page_url && ($is_total_exhausted || $is_daily_exhausted)) {
            echo '<div class="eshterakyar-renew-subscription-inline">';
            echo '<a href="' . esc_url($subscription_page_url) . '" class="button eshterakyar-renew-subscription-button">';
            echo esc_html(eshterakyar_get_label('renew_subscription', __('تمدید اشتراک', 'eshterakyar')));
            echo '</a>';
            echo '<p class="eshterakyar-renew-note">' . esc_html(eshterakyar_get_label('renew_note', __('با تمدید اشتراک، محدودیت‌های دانلود شما بازنشانی می‌شود.', 'eshterakyar'))) . '</p>';
            echo '</div>';
        }
        
        echo '</div>';
        
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
        
        $multipart_html = Eshterakyar_Download::render_multipart_download_list_html($product_id, $redownload_uses_quota, array(
            'link_class' => 'button eshterakyar-download-part-link',
        ));
        if ($multipart_html !== '') {
            echo '<div class="eshterakyar-download-button-wrapper eshterakyar-download-multipart-wrap">';
            echo $multipart_html;
            echo '</div>';
        } else {
            echo '<div class="eshterakyar-download-button-wrapper">';
            echo '<a href="' . esc_url($download_url) . '" class="button eshterakyar-download-button' . ($redownload_uses_quota ? ' eshterakyar-redownload-with-quota' : '') . '" data-product-id="' . esc_attr($product_id) . '" target="_blank" rel="noopener noreferrer">';
            if ($redownload_uses_quota) {
                echo esc_html(eshterakyar_get_label('redownload_with_quota', __('دانلود مجدد با کسر اعتبار', 'eshterakyar')));
            } else {
                echo ($is_downloaded || $is_downloaded_any) ? esc_html(eshterakyar_get_label('download_again', __('دانلود مجدد', 'eshterakyar'))) : esc_html(eshterakyar_get_label('download_with_subscription', __('دانلود رایگان با اشتراک', 'eshterakyar')));
            }
            echo '</a>';
            echo '</div>';
        }
        
        echo '</div>'; 
        echo '</div>'; 
    }
    

    public function add_downloaded_badge() {
        global $product;
        
        if (!is_user_logged_in() || !$product) {
            return;
        }
        
        $user_id = get_current_user_id();
        $subscription = Eshterakyar_Subscription::get_active_subscription($user_id);
        
        $product_id = $product->get_id();
        
        $is_downloaded = false;
        if ($subscription) {
            $is_downloaded = Eshterakyar_Download::is_downloaded($user_id, $subscription->id, $product_id);
        }
        
        $is_downloaded_any = Eshterakyar_Download::is_downloaded_any_subscription($user_id, $product_id);
        
        if ($is_downloaded || $is_downloaded_any) {
            echo '<div class="eshterakyar-product-downloaded-overlay" data-elementor-type="widget">';
            echo '<span class="eshterakyar-downloaded-label">' . esc_html__('دانلود شده', 'eshterakyar') . '</span>';
            echo '</div>';
        }
    }
    
    public function process_subscription_purchase($order_id) {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Eshterakyar: Order not found: ' . $order_id);
            }
            return;
        }
        
        $already_processed = $order->get_meta('_eshterakyar_subscription_processed', true);
        if ($already_processed) {
            return;
        }
        
        $user_id = $order->get_user_id();
        
        if (!$user_id || $user_id <= 0) {
            $billing_email = $order->get_billing_email();
            if ($billing_email) {
                $user = get_user_by('email', $billing_email);
                if ($user) {
                    $user_id = $user->ID;
                } else {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('Eshterakyar: No user found for order: ' . $order_id . ', Email: ' . $billing_email);
                    }
                    return;
                }
            } else {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Eshterakyar: Invalid user_id and no billing email for order: ' . $order_id);
                }
                return;
            }
        }
        
        $subscriptions_created = 0;
        
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            
            if (!$product_id || $product_id <= 0) {
                continue;
            }
            
            $plan = Eshterakyar_Plan::get_by_product($product_id);
            
            if ($plan) {
                $is_subscription_product = get_post_meta($product_id, '_eshterakyar_is_subscription', true);
                $product_plan_id = get_post_meta($product_id, '_eshterakyar_plan_id', true);
                
                if ($is_subscription_product && $product_plan_id == $plan->id) {
                    $has_access = true;
                } else {
                    $has_access = Eshterakyar_Subscription::check_product_access($product_id, $plan);
                }
                
                if (!$has_access) {
                    continue; 
                }
                
                $subscription_id = Eshterakyar_Subscription::create($user_id, $plan->id, $order_id);
                
                if ($subscription_id) {
                    $subscriptions_created++;
                    Eshterakyar_Notifications::send_purchase_notification($user_id, $subscription_id);
                } else {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('Eshterakyar: Failed to create subscription for order: ' . $order_id . ', Plan: ' . $plan->id . ', User: ' . $user_id);
                    }
                }
            }
        }
        
        if ($subscriptions_created > 0) {
            $order->update_meta_data('_eshterakyar_subscription_processed', true);
            $order->update_meta_data('_eshterakyar_subscriptions_count', $subscriptions_created);
            $order->save();
        }
    }

    public function add_product_meta_box() {
        add_meta_box(
            'eshterakyar_product_meta',
            __('تنظیمات اشتراک یار', 'eshterakyar'),
            array($this, 'product_meta_box_callback'),
            'product',
            'side',
            'default'
        );
    }

    public function product_meta_box_callback($post) {
        wp_nonce_field('eshterakyar_product_meta', 'eshterakyar_product_meta_nonce');
        
        $is_subscription_product = get_post_meta($post->ID, '_eshterakyar_is_subscription', true);
        $plan_id = get_post_meta($post->ID, '_eshterakyar_plan_id', true);
        

        $is_subscription_downloadable = get_post_meta($post->ID, '_eshterakyar_subscription_downloadable', true);
        $subscription_download_all_plans = get_post_meta($post->ID, '_eshterakyar_subscription_download_all_plans', true);
        $subscription_download_plans = get_post_meta($post->ID, '_eshterakyar_subscription_download_plans', true);
        
        if (!is_array($subscription_download_plans)) {
            $subscription_download_plans = array();
        }
        
        $plans = Eshterakyar_Plan::get_all('all');
        
        echo '<div class="eshterakyar-product-meta-box">';
        
        echo '<div class="eshterakyar-meta-section">';
        echo '<h4>' . esc_html__('پلن اشتراک', 'eshterakyar') . '</h4>';
        echo '<p>';
        echo '<label>';
        echo '<input type="checkbox" name="_eshterakyar_is_subscription" value="1" ' . checked(1, $is_subscription_product, false) . ' />';
        echo ' ' . esc_html__('این محصول یک پلن اشتراک است', 'eshterakyar');
        echo '</label>';
        echo '</p>';
        
        echo '<p>';
        echo '<label for="_eshterakyar_plan_id">' . esc_html__('پلن مرتبط:', 'eshterakyar') . '</label><br>';
        echo '<select name="_eshterakyar_plan_id" id="_eshterakyar_plan_id" class="eshterakyar-plan-select">';
        echo '<option value="">' . esc_html__('-- انتخاب کنید --', 'eshterakyar') . '</option>';
        foreach ($plans as $plan) {
            echo '<option value="' . esc_attr($plan->id) . '" ' . selected($plan_id, $plan->id, false) . '>' . esc_html($plan->name) . '</option>';
        }
        echo '</select>';
        echo '</p>';
        echo '</div>';
        
        echo '<div class="eshterakyar-meta-section" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">';
        echo '<h4>' . esc_html__('دانلود با اشتراک', 'eshterakyar') . '</h4>';
        
        echo '<p>';
        echo '<label>';
        echo '<input type="checkbox" name="_eshterakyar_subscription_downloadable" id="_eshterakyar_subscription_downloadable" value="1" ' . checked(1, $is_subscription_downloadable, false) . ' />';
        echo ' ' . esc_html__('این محصول با اشتراک قابل دانلود است', 'eshterakyar');
        echo '</label>';
        echo '</p>';
        
        echo '<div id="eshterakyar-subscription-download-options" style="' . ($is_subscription_downloadable ? '' : 'display: none;') . ' margin-top: 15px; padding: 15px; background: #f9f9f9; border-radius: 4px;">';
        
        $download_type_value = $subscription_download_all_plans ? 'all' : 'specific';
        
        echo '<p>';
        echo '<label>';
        echo '<input type="radio" name="_eshterakyar_subscription_download_type" value="all" ' . checked('all', $download_type_value, false) . ' />';
        echo ' ' . esc_html__('همه پلن‌ها', 'eshterakyar');
        echo '</label>';
        echo '</p>';
        
        echo '<p>';
        echo '<label>';
        echo '<input type="radio" name="_eshterakyar_subscription_download_type" value="specific" ' . checked('specific', $download_type_value, false) . ' />';
        echo ' ' . esc_html__('پلن‌های خاص', 'eshterakyar');
        echo '</label>';
        echo '</p>';
        
        echo '<div id="eshterakyar-specific-plans-selection" style="' . ($subscription_download_all_plans ? 'display: none;' : '') . ' margin-top: 10px;">';
        echo '<p style="margin-bottom: 10px;"><strong>' . esc_html__('انتخاب پلن‌ها:', 'eshterakyar') . '</strong></p>';
        echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">';
        
        if (!empty($plans)) {
            foreach ($plans as $plan) {
                if ($plan->status === 'deleted') {
                    continue;
                }
                $checked = in_array($plan->id, $subscription_download_plans) ? 'checked' : '';
                echo '<label style="display: block; margin-bottom: 8px;">';
                echo '<input type="checkbox" name="_eshterakyar_subscription_download_plans[]" value="' . esc_attr($plan->id) . '" ' . $checked . ' />';
                echo ' ' . esc_html($plan->name);
                if ($plan->status === 'inactive') {
                    echo ' <span style="color: #999;">(' . esc_html__('غیرفعال', 'eshterakyar') . ')</span>';
                }
                echo '</label>';
            }
        } else {
            echo '<p style="color: #999;">' . esc_html__('هیچ پلنی یافت نشد', 'eshterakyar') . '</p>';
        }
        
        echo '</div>';
        echo '</div>';

        if (Eshterakyar_Subscription::is_cumulative_plan_download_access_enabled()) {
            echo '<p class="description" style="margin-top: 10px;">';
            echo esc_html__('با فعال بودن «دسترسی جمع‌شونده پلن‌ها» در تنظیمات، انتخاب بالاترین پلن، پلن‌های پایین‌تر (طبق ترتیب در صفحه پلن‌ها) را هم شامل می‌شود.', 'eshterakyar');
            echo '</p>';
        }
        
        echo '</div>';
        echo '</div>'; 
        echo '</div>'; 
        

        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#_eshterakyar_subscription_downloadable').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#eshterakyar-subscription-download-options').slideDown();
                } else {
                    $('#eshterakyar-subscription-download-options').slideUp();
                }
            });
            
            $('input[name="_eshterakyar_subscription_download_type"]').on('change', function() {
                if ($(this).val() === 'all') {
                    $('#eshterakyar-specific-plans-selection').slideUp();
                } else {
                    $('#eshterakyar-specific-plans-selection').slideDown();
                }
            });
        });
        </script>
        <style>
        .eshterakyar-product-meta-box .eshterakyar-meta-section {
            margin-bottom: 15px;
        }
        .eshterakyar-product-meta-box h4 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
        }
        </style>
        <?php
    }
    

    public function save_product_meta_box($post_id) {
        if (!isset($_POST['eshterakyar_product_meta_nonce']) || !wp_verify_nonce($_POST['eshterakyar_product_meta_nonce'], 'eshterakyar_product_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        

        $is_subscription = isset($_POST['_eshterakyar_is_subscription']) ? 1 : 0;
        $plan_id = isset($_POST['_eshterakyar_plan_id']) ? intval($_POST['_eshterakyar_plan_id']) : 0;
        
        update_post_meta($post_id, '_eshterakyar_is_subscription', $is_subscription);
        
        if ($plan_id > 0) {
            update_post_meta($post_id, '_eshterakyar_plan_id', $plan_id);
            Eshterakyar_Plan::update($plan_id, array('product_id' => $post_id));
            $this->clear_product_id_from_other_plans($post_id, $plan_id);
        } else {
            delete_post_meta($post_id, '_eshterakyar_plan_id');
            $this->clear_product_id_from_other_plans($post_id, null);
        }
        

        $is_subscription_downloadable = isset($_POST['_eshterakyar_subscription_downloadable']) ? 1 : 0;
        $download_type = isset($_POST['_eshterakyar_subscription_download_type']) ? sanitize_text_field($_POST['_eshterakyar_subscription_download_type']) : 'all';
        $download_all_plans = ($download_type === 'all') ? 1 : 0;
        $download_plans = isset($_POST['_eshterakyar_subscription_download_plans']) && is_array($_POST['_eshterakyar_subscription_download_plans']) 
            ? array_map('intval', $_POST['_eshterakyar_subscription_download_plans']) 
            : array();
        
        update_post_meta($post_id, '_eshterakyar_subscription_downloadable', $is_subscription_downloadable);
        update_post_meta($post_id, '_eshterakyar_subscription_download_all_plans', $download_all_plans);
        
        if ($is_subscription_downloadable) {
            if ($download_all_plans) {
                delete_post_meta($post_id, '_eshterakyar_subscription_download_plans');
            } else {
                update_post_meta($post_id, '_eshterakyar_subscription_download_plans', $download_plans);
            }
        } else {
            delete_post_meta($post_id, '_eshterakyar_subscription_download_all_plans');
            delete_post_meta($post_id, '_eshterakyar_subscription_download_plans');
        }
    }

    /**
     * وقتی در صفحه ویرایش محصول پلن را عوض می‌کنیم، محصول از پلن قبلی جدا شود
     * (فقط یک پلن می‌تواند این محصول را به عنوان محصول مرتبط داشته باشد).
     *
     * @param int      $product_id شناسه محصول
     * @param int|null $keep_plan_id پلنی که الان به این محصول وصل است؛ بقیه پلن‌ها product_id را خالی می‌کنیم
     */
    private function clear_product_id_from_other_plans($product_id, $keep_plan_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'subscription_plans';
        $product_id = (int) $product_id;
        if ($keep_plan_id !== null) {
            $keep_plan_id = (int) $keep_plan_id;
            $wpdb->query($wpdb->prepare(
                "UPDATE `{$table}` SET product_id = NULL WHERE product_id = %d AND id != %d",
                $product_id,
                $keep_plan_id
            ));
        } else {
            $wpdb->query($wpdb->prepare(
                "UPDATE `{$table}` SET product_id = NULL WHERE product_id = %d",
                $product_id
            ));
        }
        if (class_exists('Eshterakyar_Cache')) {
            Eshterakyar_Cache::delete('plans_all');
            Eshterakyar_Cache::delete('plans_active');
            Eshterakyar_Cache::delete('plans_inactive');
        }
        wp_cache_delete('plans_all', 'eshterakyar');
        wp_cache_delete('plans_active', 'eshterakyar');
        wp_cache_delete('plans_inactive', 'eshterakyar');
    }

    public function check_subscription_required($purchasable, $product) {
        return $purchasable;
    }
    
    public function redirect_to_checkout_from_plans($url) {
        $referer = wp_get_referer();
        if (!$referer) {
            return $url;
        }

        $plans_url = Eshterakyar_Subscription_Page::get_page_url();
        $plans_path = $plans_url ? (string) wp_parse_url($plans_url, PHP_URL_PATH) : '';
        $referer_path = (string) wp_parse_url($referer, PHP_URL_PATH);

        $from_plans_page = false;
        if ($plans_path !== '' && $plans_path !== '/' && $referer_path !== '' && strpos($referer_path, untrailingslashit($plans_path)) !== false) {
            $from_plans_page = true;
        }
        if (!$from_plans_page && (
            strpos($referer, 'subscription-plans') !== false ||
            strpos($referer, 'eshterakyar_subscription_plans') !== false
        )) {
            $from_plans_page = true;
        }

        if ($from_plans_page) {
            return wc_get_checkout_url();
        }

        return $url;
    }
}

