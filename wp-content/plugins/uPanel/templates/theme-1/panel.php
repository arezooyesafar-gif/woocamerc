<?php

global $wpdb;
$current_user = wp_get_current_user();
$user_meta = get_user_meta($current_user->data->ID);
$current_user->data->first_name = $user_meta['first_name'][0];
$current_user->data->last_name = $user_meta['last_name'][0];
$current_user->data->nickname = $user_meta['nickname'][0];
$current_user->data->role = $current_user->roles[0];
// $new_user = get_userdata($current_user->data->ID);

$plugin_url = plugin_dir_url(__FILE__);

$log_out_link = wp_logout_url(get_site_url() . '/my-account');
$table_prefix = $wpdb->prefix;

$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
$result = $wpdb->get_results("SELECT * FROM $table_name LIMIT 1")[0];
$current_user->data->profile = '';


$table_tickets = $wpdb->prefix . 'hd_upanel_tickets';
$user_id = $current_user->data->ID;
$usermeta_table = $wpdb->prefix . 'usermeta';
$result_mobile = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$user_id' AND meta_key='hd_upanel_mobile'");
$result_digits = $wpdb->get_results("SELECT * FROM $usermeta_table WHERE user_id='$user_id' AND meta_key='digits_phone_no' ");
if (count($result_mobile) > 0)
    $current_user->data->mobile = $result_mobile[0]->meta_value;
if (count($result_digits) > 0)
    $current_user->data->mobile = $result_digits[0]->meta_value;

$tickets = $wpdb->get_results("SELECT * FROM $table_tickets WHERE user_id=$user_id AND parent= 0 ORDER BY id DESC");
foreach ($tickets as $key => $value) {
    $tickets[$key]->created_at = date_i18n('j F Y', $tickets[$key]->created_at);
    $tickets[$key]->ticket_text = preg_replace("/[\n\r]/", "<br />", $tickets[$key]->ticket_text);
    $parent = $value->id;
    $tickets[$key]->childs = $wpdb->get_results("SELECT * FROM $table_tickets WHERE parent=$parent ORDER BY id ASC");
    foreach ($tickets[$key]->childs as $key01 => $value01) {
        $tickets[$key]->childs[$key01]->ticket_text = preg_replace("/[\n\r]/", "<br />", $tickets[$key]->childs[$key01]->ticket_text);
        $tickets[$key]->childs[$key01]->created_at = date_i18n('j F Y', $tickets[$key]->childs[$key01]->created_at);
    }
}
foreach ($tickets as $key => $value) {
    // if($notifications[$key]->user_type == 1 && $notifications[$key]->user != '' && $notifications[$key]->user != 'all'){
    //     $notifications[$key]->user = explode(",",$notifications[$key]->user);
    //     if(!in_array($current_user->data->ID, $notifications[$key]->user)){
    //         unset($notifications[$key]);
    //     }
    // }
}



$table_notifcations = $wpdb->prefix . 'hd_upanel_notifications';
$notifications = $wpdb->get_results("SELECT id,title,image,created_at,user,user_type FROM $table_notifcations ORDER BY id DESC");

foreach ($notifications as $key => $value) {
    if ($notifications[$key]->user_type == 1 && $notifications[$key]->user != '' && $notifications[$key]->user != 'all') {
        $notifications[$key]->user = explode(",", $notifications[$key]->user);
        if (!in_array($current_user->data->ID, $notifications[$key]->user)) {
            unset($notifications[$key]);
        }
    }
}
$notifications = array_values($notifications);
foreach ($notifications as $key => $value) {

    $notifications[$key]->created_at = date_i18n('j F Y', strtotime($notifications[$key]->created_at));
}
$table_notifcations_read = $wpdb->prefix . 'hd_upanel_notifications_read';
$upanel_notifications_readed = '[]';
$user_notifications_readed = $wpdb->get_results("SELECT * FROM $table_notifcations_read where user_id=$current_user->ID ");
if (count($user_notifications_readed) == 0) {
    $wpdb->insert(
        $table_notifcations_read,
        array(
            'user_id' => $current_user->ID,
        )
    );
} else {
    $current_user->data->profile = $user_notifications_readed[0]->image;
    $upanel_notifications_readed = $user_notifications_readed[0]->notification_id;
}
if ($upanel_notifications_readed == '') {
    $upanel_notifications_readed = '[]';
}

$items = json_decode(stripslashes($result->items));
$settings = json_decode($result->settings);
$all_settings = json_decode($result->all_settings);


if (isset($all_settings->redirect_url) && $all_settings->redirect_url != '') {

    // Decode the URL if it’s encoded
    $decoded = urldecode($all_settings->redirect_url);

    // Separate main URL and extra query string (like _wpnonce)
    $parts = explode('&', $decoded, 2);
    $url = $parts[0];          // e.g. http://localhost/panel/sdcsdcsdc
    $extra = $parts[1] ?? '';    // e.g. _wpnonce=92ab3f94e0

    // Safely extract path + query from the main URL
    $path = parse_url($url, PHP_URL_PATH);
    $query = parse_url($url, PHP_URL_QUERY);

    // Rebuild relative URL
    $result = $path;
    if ($query)
        $result .= '?' . $query;
    if ($extra)
        $result .= '&' . $extra;

    // Build logout link
    $log_out_link = wp_logout_url(get_site_url() . $result);

} else {
    $log_out_link = wp_logout_url(get_site_url() . '/my-account');
}


if (!isset($all_settings->login_by_mobile)) {
    $all_settings->login_by_mobile = 'on';
    $all_settings->login_by_email = 'on';
    $all_settings->login_by_username = 'on';
    $all_settings->register_by_mobile = 'on';
    $all_settings->register_by_email = 'on';
    $all_settings->register_by_username = 'on';
    $all_settings->login_sms = 'on';
    $all_settings->register_sms = 'on';
    $all_settings->register_firstname = 'on';
    $all_settings->register_lastname = 'on';
    $all_settings->register_username = 'on';
    $all_settings->register_email = 'on';
    $all_settings->register_mobile = 'on';



}

$admin_url = admin_url('admin-ajax.php');


function order_products($orders)
{
    $products = [];

    foreach ($orders as $item_id => $item) {

        $products[] = [
            "product_id" => $item->get_product_id(),
            "variation_id" => $item->get_variation_id(),
            // "product" => $product,
            "name" => $item->get_name(),
            "quantity" => $item->get_quantity(),
            // $subtotal = $item->get_subtotal();
            "totall" => $item->get_total(),

            // $tax = $item->get_subtotal_tax();
            // $taxclass = $item->get_tax_class();
            // $taxstat = $item->get_tax_status();
            // $allmeta = $item->get_meta_data();
            // $somemeta = $item->get_meta( '_whatever', true );
            // $product_type = $item->get_type();
        ];
    }
    return $products;
}

$downloads = array();

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

function order_details($order_status, $user)
{
    global $downloads;


    if (is_plugin_active('dokan-pro/dokan-pro.php')) {
        global $wpdb;
        global $woocommerce;
        $post_meta_table_name = $wpdb->prefix . 'postmeta';
        $dokan_orders_table_name = $wpdb->prefix . 'dokan_orders';
        $woocommerce_order_items = $wpdb->prefix . 'woocommerce_order_items';
        $result = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE meta_key='_customer_user' AND meta_value = $user ");
        foreach ($result as $key => $value) {
            $post_id = $value->post_id;
            $result[$key]->ID = $post_id;
            $order_status = $wpdb->get_results("SELECT * FROM $dokan_orders_table_name WHERE order_id=$post_id ");
            if (count($order_status) > 0) {
                $result[$key]->value = (int) $order_status[0]->order_total;
                $result[$key]->status = $order_status[0]->order_status;
            }

            // $order = new WC_Order($post_id);
            // $order_item = $order->get_items();
            // print_r($order);
            // die();  
            $order_status = $wpdb->get_results("SELECT * FROM $woocommerce_order_items WHERE order_id=$post_id ");


            $order_item = $value->get_items();

            // $result[$key]['status'] =
        }
    } else {

        $customer_orders = get_posts([
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_value' => $user,
            'post_type' => wc_get_order_types(),
            'post_status' => $order_status,
        ]);
        if (count($customer_orders) == 0) {
            $args = array(
                'customer_id' => $user,
                'limit' => -1,
                'post_type' => wc_get_order_types(),
                'post_status' => $order_status,
            );
            $customer_orders = wc_get_orders($args);
        }


        $Order_Array = [];
        foreach ($customer_orders as $customer_order) {
            $orderq = wc_get_order($customer_order);

            global $wpdb;

            $order_id = $orderq->get_id();
            $products = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d", $order_id));
            $products = array_filter($products, function ($obj) {
                if (isset($obj->order_item_type) && $obj->order_item_type == 'line_item') {
                    return true;
                }
                return false;
            });
            $order_products = [];

            foreach ($products as $order_item) {
                $order_item_id = $order_item->order_item_id;
                $order_item_meta = $wpdb->get_results($wpdb->prepare(
                    "SELECT meta_key, meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d",
                    $order_item_id
                ));

                // Initialize item data array
                $item_data = [
                    'product_id' => '',
                    'variation_id' => '',
                    'name' => $order_item->order_item_name, // Use the order_item_name for the product name
                    'quantity' => '',
                    'totall' => ''
                ];

                // Map the meta data to the array
                foreach ($order_item_meta as $meta) {

                    switch ($meta->meta_key) {
                        case '_product_id':
                            $item_data['product_id'] = $meta->meta_value;
                            break;
                        case '_variation_id':
                            $item_data['variation_id'] = $meta->meta_value;
                            break;
                        case '_qty':
                            $item_data['quantity'] = $meta->meta_value;
                            break;
                        case '_line_total':
                            $item_data['totall'] = $meta->meta_value;
                            break;
                    }
                }

                // Add item data to items array
                $order_products[] = $item_data;
            }


            $notes = wc_get_order_notes(['order_id' => $orderq->get_id(), 'type' => 'customer']);
            foreach ($notes as $key => $value) {
                $notes[$key]->date_created = date_i18n('j F Y', strtotime($notes[$key]->date_created));
            }

            foreach ($order_products as $key => $value) {
                global $woocommerce;
                $product_id = $value['product_id'];
                if ($product_id && get_post_status($product_id)) {
                    $product = new WC_Product($product_id);
                    if ($product && is_a($product, 'WC_Product')) {
                        // Continue with your logic
                        $order_products[$key]['img'] = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'single-post-thumbnail');
                        $order_products[$key]['price'] = htmlentities($product->get_price_html());
                        $order_products[$key]['link'] = get_permalink($product_id);
                    } else {
                        // Handle invalid product
                        $order_products[$key]['img'] = '';
                        $order_products[$key]['price'] = '';
                        $order_products[$key]['link'] = '';
                    }
                } else {
                    // Handle invalid product ID
                    $order_products[$key]['img'] = '';
                    $order_products[$key]['price'] = '';
                    $order_products[$key]['link'] = '';
                }

            }

            $factoria_link = '';

            if (is_plugin_active('faktor-yar/faktor-yar.php')) {
                $factoria_link = '?action_type=faktoryar_doc&type=invoice&order_id=' . $orderq->get_id() . '&_wpnonce=133ae08472';
            }

            $address_components = [
                'first_name' => $orderq->get_billing_first_name(),
                'last_name' => $orderq->get_billing_last_name(),
                'company' => $orderq->get_billing_company(),
                'address_1' => $orderq->get_billing_address_1(),
                'address_2' => $orderq->get_billing_address_2(),
                'city' => $orderq->get_billing_city(),
                'state' => $orderq->get_billing_state(),
                'postcode' => $orderq->get_billing_postcode(),
                'country' => $orderq->get_billing_country(),
                'phone' => $orderq->get_billing_phone(),
                'email' => $orderq->get_billing_email(),
            ];

            $address_lines = [];
            if ($address_components['first_name'] || $address_components['last_name']) {
                $address_lines[] = trim($address_components['first_name'] . ' ' . $address_components['last_name']);
            }
            if ($address_components['company'])
                $address_lines[] = $address_components['company'];
            if ($address_components['address_1'])
                $address_lines[] = $address_components['address_1'];
            if ($address_components['address_2'])
                $address_lines[] = $address_components['address_2'];

            // city/state/postcode line
            $city_line = implode(' ', array_filter([$address_components['city'], $address_components['state'], $address_components['postcode']]));
            if ($city_line)
                $address_lines[] = $city_line;

            if ($address_components['country'])
                $address_lines[] = $address_components['country'];
            if ($address_components['phone'])
                $address_lines[] = 'Phone: ' . $address_components['phone'];
            if ($address_components['email'])
                $address_lines[] = 'Email: ' . $address_components['email'];

            $address_text = implode("\n", $address_lines);

            // HTML variant (use nl2br to render line breaks)
            $address_html = nl2br(esc_html($address_text));

            // Fallback: if fields empty, use formatted address but normalize escaped \n
            $formatted_raw = $orderq->get_formatted_billing_address();
            if (empty(trim($address_text))) {
                // Replace literal "\n" with newline and strip tags if needed
                $maybe = str_replace('\\n', "\n", $formatted_raw);
                $address_text = wp_strip_all_tags($maybe);
                $address_html = nl2br(esc_html($address_text));
            }

            $Order_Array[] = [
                "ID" => $orderq->get_id(),
                "Value" => $orderq->get_total(),
                "status" => $orderq->get_status(),
                "payment" => $orderq->get_payment_method_title(),
                "payment_url" => $orderq->get_checkout_payment_url(),
                "notes" => $notes,
                "factoria" => $factoria_link,
                "Value_html" => htmlentities($orderq->get_formatted_order_total()),
                // "downloads" => get_downloads($orderq->get_downloadable_items()),
                "items" => $order_products,
                "address" => [
                    "components" => $address_components,  // structured fields
                    "text" => $address_text,        // plain text with \n
                    "html" => $address_html,        // safe html with <br>
                ],
                "Date" => $orderq->get_date_created()->date_i18n('j F Y'),
            ];
            $dd = $orderq->get_downloadable_items();
            if (count($dd) > 0 && $order_status == 'wc-completed') {
                foreach ($dd as $key => $value) {
                    $value['access_expires'] = wc_format_datetime($value['access_expires'], 'j F Y');
                    $ele = array(
                        "download_url" => stripslashes($value['download_url']),
                        "product_name" => str_replace("'", '', $value['product_name']),
                        "downloads_remaining" => $value['downloads_remaining'],
                        "access_expires" => $value['access_expires'],
                    );
                    array_push($downloads, $value);
                }
            }
        }
    }
    return $Order_Array;
}

$dashboard = [
    "user" => [
        "id" => $current_user->ID,
        "count_register_date" => (new DateTime())->diff(new DateTime($current_user->user_registered))->format("%a"),
        "register_date" => (date_i18n("j F Y", strtotime($current_user->user_registered))),
        "billing" => (new WC_Customer($current_user->ID))->get_billing(),
        "shipping" => (new WC_Customer($current_user->ID))->get_shipping(),
    ],
    "date" => [
        "persian_date" => ["day" => date_i18n("l"), "month" => date_i18n("j F"), "year" => date_i18n("Y")],
        "gregorian_date" => ["day" => date("l"), "month" => date("j F"), "year" => date("Y")]
    ],
    "user_comments" => [
        "all" => $wpdb->get_row("SELECT count(comment_ID) as counts FROM " . $table_prefix . "comments where  user_id=" . $current_user->ID),
        "not_accepted" => $wpdb->get_row("SELECT count(comment_ID) as counts FROM " . $table_prefix . "comments where `comment_approved`=0 and  user_id=" . $current_user->ID)
    ],
    "user_orders" => [
        "pending" => order_details("wc-pending", $current_user->ID),
        "processing" => order_details("wc-processing", $current_user->ID),
        "on-hold" => order_details("wc-on-hold", $current_user->ID),
        "completed" => order_details("wc-completed", $current_user->ID),
        "cancelled" => order_details("wc-cancelled", $current_user->ID),
        "refunded" => order_details("wc-refunded", $current_user->ID),
        "failed" => order_details("wc-failed", $current_user->ID),
        "all" => order_details(["wc-pending", "wc-processing", "wc-on-hold", "wc-completed", "wc-cancelled", "wc-refunded", "wc-failed"], $current_user->ID),
        "totall_sale" => htmlentities(wc_price((new WC_Customer($current_user->ID))->get_total_spent())),

    ],
];


$membership = false;
if (is_plugin_active('restrict-content-pro/restrict-content-pro.php')) {
    $membership = true;
    $memberships = rcp_get_user_payments(1);
    if ($memberships) {
        foreach ($memberships as $payment) {
            printf(__('ID: %s', 'rcp'), $payment->id); // Payment ID number
            printf(__('Subscription: %s', 'rcp'), $payment->subscription); // Name of the associated subscription level
            printf(__('Date: %s', 'rcp'), $payment->date); // Date the payment was made, in MySQL format
            printf(__('Amount: %s', 'rcp'), $payment->amount); // Amount the payment was for
            printf(__('User ID: %s', 'rcp'), $payment->user_id); // ID of the associated user
            printf(__('Payment Type: %s', 'rcp'), $payment->payment_type); // How the payment was made
            printf(__('Subscription Key: %s', 'rcp'), $payment->subscription_key); // Subscription key
            printf(__('Transaction ID: %s', 'rcp'), $payment->transaction_id); // Transaction ID
            printf(__('Status: %s', 'rcp'), $payment->status); // Status of the payment - 'complete', 'refunded'
        }
    }
}


require('upanel/index.php');
