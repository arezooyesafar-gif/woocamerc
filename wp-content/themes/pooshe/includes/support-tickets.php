<?php

defined('ABSPATH') || exit;

define('CHECKOUT_OPTS', 'pooshe_theme_settings_checkout');

add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('pooshe_section_checkout', [
        'title' => 'تسویه حساب',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'panel' => 'pooshe_theme_settings',
    ]);
    $wp_customize->add_setting(CHECKOUT_OPTS . '[hide_extra]', [
        'default' => true,
        'capability' => 'edit_theme_options',
    ]);
    $wp_customize->add_control('checkout-hide-extra', [
        'settings' => CHECKOUT_OPTS . '[hide_extra]',
        'label' => 'حذف فیلدهای اضافی',
        'section' => 'pooshe_section_checkout',
        'description' => 'فیلدهای غیرضروری مانند آدرس، استان و شهر را حذف کن (مناسب فروش فایل)',
        'type' => 'checkbox',
    ]);
});

add_filter('woocommerce_checkout_fields', function ($fields) {
    if (pooshe_opt(CHECKOUT_OPTS, 'hide_extra', true)) {
        $extra = ['billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_postcode', 'billing_country', 'billing_state'];
        foreach ($extra as $field) {
            unset($fields['billing'][$field]);
        }
        unset($fields['order']['order_comments']);
    }
    return $fields;
}, 100);

add_filter('woocommerce_billing_fields', function ($fields) {
    if (!pooshe_opt(CHECKOUT_OPTS, 'hide_extra', true)) {
        return $fields;
    }
    $extra = ['billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_postcode', 'billing_country', 'billing_state'];
    foreach ($extra as $key) {
        unset($fields[$key]);
    }
    return $fields;
}, 100);

class Pooshe_Support_Tickets {

    const POST_TYPE = 'pooshe_ticket';
    const STATUS_META = '_ticket_status';
    const PRIORITY_META = '_ticket_priority';

    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('init', [$this, 'register_endpoint']);
        add_filter('woocommerce_account_menu_items', [$this, 'add_account_menu_item'], 50);
        add_action('woocommerce_account_support-tickets_endpoint', [$this, 'account_content']);
        add_action('wp_ajax_pooshe_create_ticket', [$this, 'ajax_create_ticket']);
        add_action('wp_ajax_pooshe_ticket_reply', [$this, 'ajax_add_reply']);
        add_action('wp_ajax_pooshe_admin_ticket_action', [$this, 'ajax_admin_action']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save_meta'], 10, 2);
        add_filter('manage_' . self::POST_TYPE . '_posts_columns', [$this, 'admin_columns']);
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', [$this, 'admin_column_content'], 10, 2);
        add_filter('post_row_actions', [$this, 'remove_quick_edit'], 10, 2);
    }

    public function register_post_type() {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name' => 'تیکت‌های پشتیبانی',
                'singular_name' => 'تیکت',
                'menu_name' => 'تیکت‌ها',
                'all_items' => 'همه تیکت‌ها',
                'add_new' => 'تیکت جدید',
                'add_new_item' => 'افزودن تیکت جدید',
                'edit_item' => 'ویرایش تیکت',
                'view_item' => 'مشاهده تیکت',
                'search_items' => 'جستجوی تیکت',
                'not_found' => 'تیکتی یافت نشد',
                'not_found_in_trash' => 'تیکتی در زباله‌دان یافت نشد',
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-format-chat',
            'menu_position' => 56,
            'supports' => ['title', 'editor', 'author'],
            'capability_type' => 'post',
            'capabilities' => [
                'create_posts' => 'edit_posts',
            ],
            'map_meta_cap' => true,
        ]);
    }

    public function register_endpoint() {
        add_rewrite_endpoint('support-tickets', EP_ROOT | EP_PAGES);
    }

    public function add_account_menu_item($items) {
        $user_id = get_current_user_id();
        $open_count = $this->count_user_tickets($user_id, 'open');
        $label = 'تیکت‌های پشتیبانی';
        if ($open_count > 0) {
            $label .= ' (' . $open_count . ')';
        }
        $items = array_slice($items, 0, 3, true)
            + ['support-tickets' => $label]
            + array_slice($items, 3, null, true);
        return $items;
    }

    public function account_content() {
        $value = get_query_var('support-tickets');

        if ($value === 'new-ticket') {
            $this->render_new_form();
        } elseif (!empty($value)) {
            $this->render_single((int)$value);
        } else {
            $this->render_list();
        }
    }

    private function get_status_label($status) {
        $labels = [
            'open' => 'باز',
            'in-review' => 'در حال بررسی',
            'closed' => 'بسته شده',
        ];
        return isset($labels[$status]) ? $labels[$status] : $status;
    }

    private function get_priority_label($priority) {
        $labels = [
            'low' => 'کم',
            'normal' => 'معمولی',
            'high' => 'زیاد',
            'urgent' => 'فوری',
        ];
        return isset($labels[$priority]) ? $labels[$priority] : 'معمولی';
    }

    private function get_status_class($status) {
        $classes = [
            'open' => 'status-open',
            'in-review' => 'status-review',
            'closed' => 'status-closed',
        ];
        return isset($classes[$status]) ? $classes[$status] : '';
    }

    public function count_user_tickets($user_id, $status = null) {
        $args = [
            'post_type' => self::POST_TYPE,
            'author' => $user_id,
            'posts_per_page' => -1,
            'fields' => 'ids',
        ];
        if ($status) {
            $args['meta_key'] = self::STATUS_META;
            $args['meta_value'] = $status;
        }
        $query = new WP_Query($args);
        return $query->found_posts;
    }

    public function render_list() {
        $user_id = get_current_user_id();
        if (!$user_id) {
            echo '<p>لطفا وارد حساب کاربری خود شوید.</p>';
            return;
        }

        $tickets = get_posts([
            'post_type' => self::POST_TYPE,
            'author' => $user_id,
            'posts_per_page' => 20,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        $new_url = wc_get_account_endpoint_url('support-tickets') . 'new-ticket/';
        ?>
        <div class="pooshe-tickets-wrap">
            <div class="pooshe-tickets-header">
                <h2>تیکت‌های پشتیبانی</h2>
                <a href="<?php echo esc_url($new_url); ?>" class="pooshe-btn pooshe-btn-primary">تیکت جدید</a>
            </div>

            <?php if (empty($tickets)) : ?>
                <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info">
                    <p>تیکتی ثبت نکرده‌اید. برای شروع یک تیکت جدید ایجاد کنید.</p>
                </div>
            <?php else : ?>
                <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive">
                    <thead>
                        <tr>
                            <th>موضوع</th>
                            <th>اولویت</th>
                            <th>وضعیت</th>
                            <th>آخرین به‌روزرسانی</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket) :
                            $status = get_post_meta($ticket->ID, self::STATUS_META, true) ?: 'open';
                            $priority = get_post_meta($ticket->ID, self::PRIORITY_META, true) ?: 'normal';
                            $comment_count = wp_count_comments($ticket->ID);
                            $reply_count = $comment_count->approved;
                            $last_comment = get_comments([
                                'post_id' => $ticket->ID,
                                'number' => 1,
                                'status' => 'approve',
                            ]);
                            $last_update = !empty($last_comment) ? $last_comment[0]->comment_date : $ticket->post_modified;
                            ?>
                            <tr>
                                <td data-title="موضوع">
                                    <a href="<?php echo esc_url(wc_get_account_endpoint_url('support-tickets') . $ticket->ID . '/'); ?>">
                                        <?php echo esc_html($ticket->post_title); ?>
                                    </a>
                                    <?php if ($reply_count > 0) : ?>
                                        <span class="pooshe-ticket-replies"><?php echo $reply_count; ?> پاسخ</span>
                                    <?php endif; ?>
                                </td>
                                <td data-title="اولویت"><span class="pooshe-priority priority-<?php echo esc_attr($priority); ?>"><?php echo $this->get_priority_label($priority); ?></span></td>
                                <td data-title="وضعیت"><span class="pooshe-status <?php echo $this->get_status_class($status); ?>"><?php echo $this->get_status_label($status); ?></span></td>
                                <td data-title="آخرین به‌روزرسانی"><?php echo pooshe_jdate('Y/m/j H:i', strtotime($last_update)); ?></td>
                                <td data-title="عملیات">
                                    <a href="<?php echo esc_url(wc_get_account_endpoint_url('support-tickets') . $ticket->ID . '/'); ?>" class="woocommerce-button button">مشاهده</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }

    public function render_new_form() {
        ?>
        <div class="pooshe-tickets-wrap">
            <div class="pooshe-tickets-header">
                <h2>تیکت جدید</h2>
                <a href="<?php echo esc_url(wc_get_account_endpoint_url('support-tickets')); ?>" class="pooshe-btn">&larr; بازگشت به لیست</a>
            </div>

            <form class="pooshe-ticket-form" method="post" id="pooshe-new-ticket-form">
                <?php wp_nonce_field('pooshe_ticket_nonce', 'ticket_nonce'); ?>
                <p class="form-row">
                    <label for="ticket-title">موضوع <span class="required">*</span></label>
                    <input type="text" id="ticket-title" name="ticket_title" class="input-text" required>
                </p>
                <p class="form-row">
                    <label for="ticket-priority">اولویت</label>
                    <select id="ticket-priority" name="ticket_priority" class="input-text">
                        <option value="low">کم</option>
                        <option value="normal" selected>معمولی</option>
                        <option value="high">زیاد</option>
                        <option value="urgent">فوری</option>
                    </select>
                </p>
                <p class="form-row">
                    <label for="ticket-message">متن پیام <span class="required">*</span></label>
                    <textarea id="ticket-message" name="ticket_message" class="input-text" rows="8" required></textarea>
                </p>
                <p class="form-row">
                    <button type="submit" class="pooshe-btn pooshe-btn-primary">ارسال تیکت</button>
                </p>
                <div class="pooshe-ticket-msg"></div>
            </form>
        </div>

        <script>
        jQuery(function($) {
            $('#pooshe-new-ticket-form').on('submit', function(e) {
                e.preventDefault();
                var btn = $(this).find('button[type="submit"]');
                var msgBox = $(this).find('.pooshe-ticket-msg');
                btn.prop('disabled', true).text('در حال ارسال...');
                msgBox.empty();

                $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                    action: 'pooshe_create_ticket',
                    title: $('#ticket-title').val(),
                    priority: $('#ticket-priority').val(),
                    message: $('#ticket-message').val(),
                    nonce: $('#ticket_nonce').val()
                }, function(res) {
                    btn.prop('disabled', false).text('ارسال تیکت');
                    if (res.success) {
                        msgBox.html('<div class="woocommerce-message">' + res.data.message + '</div>');
                        setTimeout(function() {
                            window.location.href = res.data.redirect;
                        }, 1500);
                    } else {
                        msgBox.html('<div class="woocommerce-error">' + res.data.message + '</div>');
                    }
                }).fail(function() {
                    btn.prop('disabled', false).text('ارسال تیکت');
                    msgBox.html('<div class="woocommerce-error">خطا در ارسال. لطفا دوباره تلاش کنید.</div>');
                });
            });
        });
        </script>
        <?php
    }

    public function render_single($ticket_id) {
        $ticket = get_post($ticket_id);
        if (!$ticket || $ticket->post_type !== self::POST_TYPE) {
            echo '<div class="woocommerce-error">تیکت مورد نظر یافت نشد.</div>';
            return;
        }

        $user_id = get_current_user_id();
        if ((int)$ticket->post_author !== $user_id && !current_user_can('manage_options')) {
            echo '<div class="woocommerce-error">شما دسترسی به این تیکت ندارید.</div>';
            return;
        }

        $status = get_post_meta($ticket->ID, self::STATUS_META, true) ?: 'open';
        $priority = get_post_meta($ticket->ID, self::PRIORITY_META, true) ?: 'normal';
        $replies = get_comments([
            'post_id' => $ticket->ID,
            'orderby' => 'comment_date',
            'order' => 'ASC',
            'status' => 'approve',
        ]);

        $back_url = wc_get_account_endpoint_url('support-tickets');
        $author = get_userdata($ticket->post_author);
        ?>
        <div class="pooshe-tickets-wrap">
            <div class="pooshe-tickets-header">
                <h2><?php echo esc_html($ticket->post_title); ?></h2>
                <a href="<?php echo esc_url($back_url); ?>" class="pooshe-btn">&larr; بازگشت به لیست</a>
            </div>

            <div class="pooshe-ticket-meta">
                <span class="pooshe-status <?php echo $this->get_status_class($status); ?>"><?php echo $this->get_status_label($status); ?></span>
                <span class="pooshe-priority priority-<?php echo esc_attr($priority); ?>"><?php echo $this->get_priority_label($priority); ?></span>
                <span class="pooshe-ticket-date">ایجاد: <?php echo pooshe_jdate('Y/m/j H:i', strtotime($ticket->post_date)); ?></span>
            </div>

            <div class="pooshe-ticket-messages">
                <div class="pooshe-ticket-message <?php echo ((int)$ticket->post_author === $author->ID) ? 'message-user' : 'message-admin'; ?>">
                    <div class="message-header">
                        <strong><?php echo esc_html($author ? $author->display_name : 'کاربر'); ?></strong>
                        <span><?php echo pooshe_jdate('Y/m/j H:i', strtotime($ticket->post_date)); ?></span>
                    </div>
                    <div class="message-body">
                        <?php echo wpautop(wp_kses_post($ticket->post_content)); ?>
                    </div>
                </div>

                <?php if (!empty($replies)) : ?>
                    <?php foreach ($replies as $reply) :
                        $reply_user = get_userdata($reply->user_id);
                        $is_admin = user_can($reply->user_id, 'manage_options');
                        ?>
                        <div class="pooshe-ticket-message <?php echo $is_admin ? 'message-admin' : 'message-user'; ?>">
                            <div class="message-header">
                                <strong><?php echo esc_html($reply_user ? $reply_user->display_name : 'کاربر'); ?></strong>
                                <?php if ($is_admin) : ?>
                                    <span class="admin-badge">مدیریت</span>
                                <?php endif; ?>
                                <span><?php echo pooshe_jdate('Y/m/j H:i', strtotime($reply->comment_date)); ?></span>
                            </div>
                            <div class="message-body">
                                <?php echo wpautop(wp_kses_post($reply->comment_content)); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($status !== 'closed') : ?>
                <form class="pooshe-ticket-reply-form" method="post" id="pooshe-ticket-reply-form">
                    <?php wp_nonce_field('pooshe_ticket_nonce', 'ticket_nonce'); ?>
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket->ID; ?>">
                    <p class="form-row">
                        <label for="reply-message">پاسخ شما</label>
                        <textarea id="reply-message" name="reply_message" class="input-text" rows="5" required></textarea>
                    </p>
                    <p class="form-row">
                        <button type="submit" class="pooshe-btn pooshe-btn-primary">ارسال پاسخ</button>
                    </p>
                    <div class="pooshe-reply-msg"></div>
                </form>
            <?php else : ?>
                <div class="woocommerce-info">این تیکت بسته شده است. برای پیگیری مجدد یک تیکت جدید ایجاد کنید.</div>
            <?php endif; ?>
        </div>

        <script>
        jQuery(function($) {
            $('#pooshe-ticket-reply-form').on('submit', function(e) {
                e.preventDefault();
                var btn = $(this).find('button[type="submit"]');
                var msgBox = $(this).find('.pooshe-reply-msg');
                btn.prop('disabled', true).text('در حال ارسال...');
                msgBox.empty();

                $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                    action: 'pooshe_ticket_reply',
                    ticket_id: $(this).find('input[name="ticket_id"]').val(),
                    message: $('#reply-message').val(),
                    nonce: $('#ticket_nonce').val()
                }, function(res) {
                    btn.prop('disabled', false).text('ارسال پاسخ');
                    if (res.success) {
                        location.reload();
                    } else {
                        msgBox.html('<div class="woocommerce-error">' + res.data.message + '</div>');
                    }
                }).fail(function() {
                    btn.prop('disabled', false).text('ارسال پاسخ');
                    msgBox.html('<div class="woocommerce-error">خطا در ارسال. لطفا دوباره تلاش کنید.</div>');
                });
            });
        });
        </script>
        <?php
    }

    public function ajax_create_ticket() {
        if (!wp_verify_nonce($_POST['nonce'], 'pooshe_ticket_nonce')) {
            wp_send_json_error(['message' => 'خطای امنیتی. لطفا صفحه را رفرش کنید.']);
        }

        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(['message' => 'لطفا وارد حساب کاربری خود شوید.']);
        }

        $title = sanitize_text_field($_POST['title']);
        $priority = sanitize_text_field($_POST['priority']);
        $message = wp_kses_post($_POST['message']);

        if (empty($title) || empty($message)) {
            wp_send_json_error(['message' => 'لطفا موضوع و متن پیام را وارد کنید.']);
        }

        $ticket_id = wp_insert_post([
            'post_type' => self::POST_TYPE,
            'post_title' => $title,
            'post_content' => $message,
            'post_author' => $user_id,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($ticket_id)) {
            wp_send_json_error(['message' => 'خطا در ایجاد تیکت. لطفا دوباره تلاش کنید.']);
        }

        update_post_meta($ticket_id, self::STATUS_META, 'open');
        update_post_meta($ticket_id, self::PRIORITY_META, in_array($priority, ['low', 'normal', 'high', 'urgent']) ? $priority : 'normal');

        $this->notify_admin($ticket_id, $title, $message, $user_id);

        wp_send_json_success([
            'message' => 'تیکت شما با موفقیت ثبت شد.',
            'redirect' => wc_get_account_endpoint_url('support-tickets') . $ticket_id . '/',
        ]);
    }

    public function ajax_add_reply() {
        if (!wp_verify_nonce($_POST['nonce'], 'pooshe_ticket_nonce')) {
            wp_send_json_error(['message' => 'خطای امنیتی. لطفا صفحه را رفرش کنید.']);
        }

        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(['message' => 'لطفا وارد حساب کاربری خود شوید.']);
        }

        $ticket_id = (int)$_POST['ticket_id'];
        $ticket = get_post($ticket_id);

        if (!$ticket || $ticket->post_type !== self::POST_TYPE) {
            wp_send_json_error(['message' => 'تیکت مورد نظر یافت نشد.']);
        }

        if ((int)$ticket->post_author !== $user_id && !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'شما دسترسی به این تیکت ندارید.']);
        }

        $status = get_post_meta($ticket_id, self::STATUS_META, true) ?: 'open';
        if ($status === 'closed') {
            wp_send_json_error(['message' => 'این تیکت بسته شده است و نمی‌توانید پاسخ دهید.']);
        }

        $message = wp_kses_post($_POST['message']);
        if (empty($message)) {
            wp_send_json_error(['message' => 'لطفا متن پیام را وارد کنید.']);
        }

        $comment_data = [
            'comment_post_ID' => $ticket_id,
            'comment_content' => $message,
            'user_id' => $user_id,
            'comment_author' => wp_get_current_user()->display_name,
            'comment_author_email' => wp_get_current_user()->user_email,
            'comment_approved' => 1,
        ];

        $comment_id = wp_insert_comment($comment_data);

        if (!$comment_id) {
            wp_send_json_error(['message' => 'خطا در ارسال پاسخ. لطفا دوباره تلاش کنید.']);
        }

        if (!current_user_can('manage_options')) {
            update_post_meta($ticket_id, self::STATUS_META, 'open');
            $this->notify_admin_reply($ticket_id, $message, $user_id);
        }

        wp_send_json_success(['message' => 'پاسخ شما با موفقیت ثبت شد.']);
    }

    public function ajax_admin_action() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز.']);
        }

        $ticket_id = (int)$_POST['ticket_id'];
        $action = sanitize_text_field($_POST['do_action']);

        if (!in_array($action, ['open', 'in-review', 'closed'])) {
            wp_send_json_error(['message' => 'عملیات نامعتبر.']);
        }

        update_post_meta($ticket_id, self::STATUS_META, $action);

        $labels = [
            'open' => 'باز شد',
            'in-review' => 'در حال بررسی',
            'closed' => 'بسته شد',
        ];

        wp_send_json_success(['message' => 'وضعیت تیکت به "' . $labels[$action] . '" تغییر یافت.']);
    }

    public function add_meta_boxes() {
        add_meta_box('pooshe_ticket_status', 'وضعیت تیکت', [$this, 'render_status_metabox'], self::POST_TYPE, 'side', 'high');
        add_meta_box('pooshe_ticket_info', 'اطلاعات تیکت', [$this, 'render_info_metabox'], self::POST_TYPE, 'side', 'default');
    }

    public function render_status_metabox($post) {
        $status = get_post_meta($post->ID, self::STATUS_META, true) ?: 'open';
        wp_nonce_field('pooshe_ticket_status_nonce', 'status_nonce');
        ?>
        <select name="ticket_status" style="width:100%">
            <option value="open" <?php selected($status, 'open'); ?>>باز</option>
            <option value="in-review" <?php selected($status, 'in-review'); ?>>در حال بررسی</option>
            <option value="closed" <?php selected($status, 'closed'); ?>>بسته شده</option>
        </select>
        <p style="margin-top:8px">
            <button type="button" class="button pooshe-admin-reply-btn" data-ticket="<?php echo $post->ID; ?>">
                پاسخ سریع
            </button>
        </p>
        <div class="pooshe-admin-reply-box" style="display:none;margin-top:8px">
            <textarea id="pooshe-admin-reply-text" rows="4" style="width:100%" placeholder="متن پاسخ..."></textarea>
            <button type="button" class="button button-primary pooshe-admin-reply-send" style="margin-top:4px;width:100%">ارسال پاسخ</button>
            <span class="pooshe-admin-reply-msg" style="display:block;margin-top:4px"></span>
        </div>
        <script>
        jQuery(function($) {
            $('.pooshe-admin-reply-btn').on('click', function() {
                $('.pooshe-admin-reply-box').toggle();
            });
            $('.pooshe-admin-reply-send').on('click', function() {
                var btn = $(this);
                var msgBox = $('.pooshe-admin-reply-msg');
                btn.prop('disabled', true).text('در حال ارسال...');
                msgBox.html('');
                $.post(ajaxurl, {
                    action: 'pooshe_ticket_reply',
                    ticket_id: <?php echo $post->ID; ?>,
                    message: $('#pooshe-admin-reply-text').val(),
                    nonce: '<?php echo wp_create_nonce('pooshe_ticket_nonce'); ?>'
                }, function(res) {
                    btn.prop('disabled', false).text('ارسال پاسخ');
                    if (res.success) {
                        msgBox.html('<span style="color:green">پاسخ ثبت شد.</span>');
                        $('#pooshe-admin-reply-text').val('');
                    } else {
                        msgBox.html('<span style="color:red">' + res.data.message + '</span>');
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function render_info_metabox($post) {
        $priority = get_post_meta($post->ID, self::PRIORITY_META, true) ?: 'normal';
        $author = get_userdata($post->post_author);
        $comment_count = wp_count_comments($post->ID);
        ?>
        <p><strong>کاربر:</strong> <?php echo $author ? esc_html($author->display_name . ' (' . $author->user_email . ')') : '—'; ?></p>
        <p><strong>اولویت:</strong>
            <select name="ticket_priority" style="width:100%">
                <option value="low" <?php selected($priority, 'low'); ?>>کم</option>
                <option value="normal" <?php selected($priority, 'normal'); ?>>معمولی</option>
                <option value="high" <?php selected($priority, 'high'); ?>>زیاد</option>
                <option value="urgent" <?php selected($priority, 'urgent'); ?>>فوری</option>
            </select>
        </p>
        <p><strong>تاریخ ایجاد:</strong> <?php echo pooshe_jdate('Y/m/j H:i', strtotime($post->post_date)); ?></p>
        <p><strong>پاسخ‌ها:</strong> <?php echo (int)$comment_count->approved; ?></p>
        <?php
    }

    public function save_meta($post_id, $post) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!isset($_POST['status_nonce']) || !wp_verify_nonce($_POST['status_nonce'], 'pooshe_ticket_status_nonce')) return;

        if (isset($_POST['ticket_status']) && in_array($_POST['ticket_status'], ['open', 'in-review', 'closed'])) {
            update_post_meta($post_id, self::STATUS_META, $_POST['ticket_status']);
        }
        if (isset($_POST['ticket_priority']) && in_array($_POST['ticket_priority'], ['low', 'normal', 'high', 'urgent'])) {
            update_post_meta($post_id, self::PRIORITY_META, $_POST['ticket_priority']);
        }
    }

    public function admin_columns($columns) {
        $new = [];
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $new[$key] = $value;
                $new['ticket_status'] = 'وضعیت';
                $new['ticket_priority'] = 'اولویت';
                $new['ticket_author'] = 'کاربر';
            } elseif ($key === 'date') {
                $new['ticket_replies'] = 'پاسخ‌ها';
                $new[$key] = $value;
            } else {
                $new[$key] = $value;
            }
        }
        return $new;
    }

    public function admin_column_content($column, $post_id) {
        switch ($column) {
            case 'ticket_status':
                $status = get_post_meta($post_id, self::STATUS_META, true) ?: 'open';
                echo '<span class="pooshe-status ' . $this->get_status_class($status) . '" style="display:inline-block;padding:2px 8px;border-radius:4px;font-size:12px">' . $this->get_status_label($status) . '</span>';
                break;
            case 'ticket_priority':
                $priority = get_post_meta($post_id, self::PRIORITY_META, true) ?: 'normal';
                echo '<span class="pooshe-priority priority-' . esc_attr($priority) . '">' . $this->get_priority_label($priority) . '</span>';
                break;
            case 'ticket_author':
                $author = get_userdata(get_post_field('post_author', $post_id));
                echo $author ? esc_html($author->display_name) : '—';
                break;
            case 'ticket_replies':
                $count = wp_count_comments($post_id);
                echo (int)$count->approved;
                break;
        }
    }

    public function remove_quick_edit($actions, $post) {
        if ($post->post_type === self::POST_TYPE) {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    private function notify_admin($ticket_id, $title, $message, $user_id) {
        $admin_email = get_option('admin_email');
        $user = get_userdata($user_id);
        $subject = 'تیکت جدید: ' . $title;
        $body = "یک تیکت جدید ثبت شده است:\n\n";
        $body .= "کاربر: " . ($user ? $user->display_name : 'نامشخص') . " (" . ($user ? $user->user_email : '') . ")\n";
        $body .= "موضوع: " . $title . "\n\n";
        $body .= "متن پیام:\n" . $message . "\n\n";
        $body .= "لینک: " . admin_url('post.php?post=' . $ticket_id . '&action=edit') . "\n";
        wp_mail($admin_email, $subject, $body);
    }

    private function notify_admin_reply($ticket_id, $message, $user_id) {
        $admin_email = get_option('admin_email');
        $ticket = get_post($ticket_id);
        $user = get_userdata($user_id);
        $subject = 'پاسخ جدید به تیکت: ' . $ticket->post_title;
        $body = "کاربر " . ($user ? $user->display_name : '') . " به تیکت پاسخ داد:\n\n";
        $body .= $message . "\n\n";
        $body .= "لینک: " . admin_url('post.php?post=' . $ticket_id . '&action=edit') . "\n";
        wp_mail($admin_email, $subject, $body);
    }
}

new Pooshe_Support_Tickets();

add_action('wp_footer', function () {
    if (function_exists('is_account_page') && is_account_page()) {
        $main_color = pooshe_opt(STYLE_OPTS, 'main_color', '#ff9e22');
        $btn_color = pooshe_opt(STYLE_OPTS, 'main_btn', '#ff9e22');
        $btn_hover = pooshe_opt(STYLE_OPTS, 'main_btn_hover', '#ff8d00');
        ?>
        <style>
        .pooshe-tickets-wrap { max-width:100%; }
        .pooshe-tickets-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
        .pooshe-tickets-header h2 { font-size:20px; margin:0; padding-bottom:10px; border-bottom:2px solid <?php echo $main_color; ?>; }
        .pooshe-btn { display:inline-block; padding:10px 20px; border-radius:6px; text-decoration:none; font-size:14px; border:none; cursor:pointer; transition:all .2s; background:#f5f5f5; color:#333; }
        .pooshe-btn-primary { background:<?php echo $btn_color; ?>; color:#fff; }
        .pooshe-btn-primary:hover { background:<?php echo $btn_hover; ?>; color:#fff; }
        .pooshe-ticket-replies { display:inline-block; background:<?php echo $main_color; ?>; color:#fff; font-size:11px; padding:1px 7px; border-radius:10px; margin-right:5px; vertical-align:middle; line-height:18px; }
        .pooshe-status { display:inline-block; padding:3px 10px; border-radius:4px; font-size:12px; font-weight:600; }
        .status-open { background:#e8f5e9; color:#2e7d32; }
        .status-review { background:#fff3e0; color:#e65100; }
        .status-closed { background:#fce4ec; color:#c62828; }
        .pooshe-priority { display:inline-block; padding:2px 8px; border-radius:4px; font-size:12px; }
        .priority-low { background:#f5f5f5; color:#666; }
        .priority-normal { background:#e3f2fd; color:#1565c0; }
        .priority-high { background:#fff3e0; color:#e65100; }
        .priority-urgent { background:#fce4ec; color:#c62828; }
        .pooshe-ticket-meta { margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
        .pooshe-ticket-date { font-size:13px; color:#888; }
        .pooshe-ticket-messages { margin-bottom:20px; }
        .pooshe-ticket-message { padding:15px; border-radius:8px; margin-bottom:12px; border:1px solid #e0e0e0; background:#fafafa; }
        .pooshe-ticket-message.message-admin { border-right:3px solid <?php echo $main_color; ?>; background:#fff8f0; }
        .pooshe-ticket-message.message-user { border-right:3px solid #e0e0e0; }
        .message-header { display:flex; gap:10px; align-items:center; margin-bottom:8px; font-size:13px; }
        .message-header strong { font-size:14px; }
        .message-header span { color:#888; }
        .admin-badge { background:<?php echo $main_color; ?>; color:#fff; font-size:11px; padding:1px 6px; border-radius:4px; }
        .message-body { font-size:14px; line-height:1.7; }
        .message-body p:last-child { margin-bottom:0; }
        .pooshe-ticket-form .form-row,
        .pooshe-ticket-reply-form .form-row { margin-bottom:15px; }
        .pooshe-ticket-form label,
        .pooshe-ticket-reply-form label { display:block; margin-bottom:5px; font-weight:600; }
        .pooshe-ticket-form .required,
        .pooshe-ticket-reply-form .required { color:#c62828; }
        .pooshe-ticket-form input.input-text,
        .pooshe-ticket-form textarea,
        .pooshe-ticket-form select,
        .pooshe-ticket-reply-form input.input-text,
        .pooshe-ticket-reply-form textarea { width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; font-size:14px; }
        .pooshe-ticket-form textarea:focus,
        .pooshe-ticket-reply-form textarea:focus { border-color:<?php echo $main_color; ?>; outline:none; box-shadow:0 0 0 2px <?php echo $main_color; ?>33; }
        @media(max-width:768px) {
            .pooshe-tickets-header { flex-direction:column; align-items:flex-start; }
        }
        </style>
        <?php
    }
});

