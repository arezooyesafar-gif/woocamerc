<?php

defined('ABSPATH') || exit;

function pooshe_order_sms_is_enabled()
{
    return function_exists('pooshe_opt') && defined('SMS_OPTS') && pooshe_opt(SMS_OPTS, 'order_sms_enabled', false);
}

function pooshe_order_sms_get_customer_mobile($order)
{
    if (!$order) return '';
    $user_id = $order->get_user_id();
    if ($user_id) {
        $mobile = get_user_meta($user_id, 'ns_user_mobile', true);
        if (!empty($mobile)) return $mobile;
    }
    return $order->get_billing_phone();
}

function pooshe_order_sms_parse_template($template, $order)
{
    if (empty($template) || !$order) return '';

    $replacements = [
        '{order_id}' => $order->get_id(),
        '{total}' => $order->get_formatted_order_total(),
        '{customer_name}' => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
        '{customer_mobile}' => pooshe_order_sms_get_customer_mobile($order),
        '{items_count}' => $order->get_item_count(),
        '{payment_method}' => $order->get_payment_method_title(),
        '{status}' => wc_get_order_status_name($order->get_status()),
    ];

    return str_replace(array_keys($replacements), array_values($replacements), $template);
}

add_action('woocommerce_new_order', 'pooshe_order_sms_admin_new_order', 20, 1);
function pooshe_order_sms_admin_new_order($order_id)
{
    if (!pooshe_order_sms_is_enabled()) return;
    if (!pooshe_opt(SMS_OPTS, 'order_sms_admin_new_order', false)) return;

    $admin_mobile = pooshe_opt(SMS_OPTS, 'order_sms_admin_mobile', '');
    if (empty($admin_mobile)) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    $template = pooshe_opt(SMS_OPTS, 'order_sms_admin_template', '');
    $message = pooshe_order_sms_parse_template($template, $order);
    if (empty($message)) {
        $message = sprintf(
            __('سفارش جدید %s - مبلغ: %s - خریدار: %s', 'pooshe'),
            $order_id,
            $order->get_formatted_order_total(),
            trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name())
        );
    }

    $sender = new Pooshe_Sender();
    $sender->send([
        'mobile' => $admin_mobile,
        'message' => $message,
    ]);
}

add_action('woocommerce_order_status_completed', 'pooshe_order_sms_customer_completed', 20, 1);
function pooshe_order_sms_customer_completed($order_id)
{
    if (!pooshe_order_sms_is_enabled()) return;
    if (!pooshe_opt(SMS_OPTS, 'order_sms_customer_completed', false)) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    $mobile = pooshe_order_sms_get_customer_mobile($order);
    if (empty($mobile)) return;

    $template = pooshe_opt(SMS_OPTS, 'order_sms_customer_template', '');
    $message = pooshe_order_sms_parse_template($template, $order);
    if (empty($message)) {
        $message = sprintf(
            __('سفارش شما با شماره %s با موفقیت تکمیل شد. مبلغ پرداختی: %s - %s', 'pooshe'),
            $order_id,
            $order->get_formatted_order_total(),
            get_bloginfo('name')
        );
    }

    $sender = new Pooshe_Sender();
    $sender->send([
        'mobile' => $mobile,
        'message' => $message,
    ]);
}
