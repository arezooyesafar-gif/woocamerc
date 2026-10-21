<?php

global $wpdb;
$current_user = wp_get_current_user();
$user_meta = get_user_meta($current_user->data->ID);
$current_user->data->first_name = $user_meta['first_name'][0];
$current_user->data->last_name = $user_meta['last_name'][0];
$current_user->data->nickname = $user_meta['nickname'][0];
$current_user->data->role = $current_user->roles[0];
// $new_user = get_userdata($current_user->data->ID);

$plugin_url =plugin_dir_url( __FILE__ );

$log_out_link = wp_logout_url( get_site_url().'/my-account' );
$table_prefix= $wpdb->prefix;

$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
$result = $wpdb->get_results ( "SELECT * FROM $table_name LIMIT 1" )[0];
$current_user->data->profile = '';

$ostan_table = $wpdb->prefix . 'hd_upanel_ostan';
$ostan = $wpdb->get_results ( "SELECT * FROM $ostan_table" );

$city_table = $wpdb->prefix . 'hd_upanel_shahrestan';
$city = $wpdb->get_results ( "SELECT * FROM $city_table" );

$address_table = $wpdb->prefix . 'hd_upanel_addresses';
$addresses = $wpdb->get_results ( "SELECT * FROM $address_table" );

$table_tickets = $wpdb->prefix . 'hd_upanel_tickets';
$user_id = $current_user->data->ID;
$tickets = $wpdb->get_results("SELECT * FROM $table_tickets WHERE user_id=$user_id AND parent= 0 ORDER BY id DESC" );
foreach ($tickets as $key => $value) {
    $tickets[$key]->created_at = date_i18n('j F Y',$tickets[$key]->created_at);
    $tickets[$key]->ticket_text = preg_replace("/[\n\r]/", "<br />", $tickets[$key]->ticket_text);
    $parent = $value->id;
    $tickets[$key]->childs  = $wpdb->get_results("SELECT * FROM $table_tickets WHERE parent=$parent ORDER BY id ASC" );
    foreach ($tickets[$key]->childs as $key01 => $value01) {
        $tickets[$key]->childs[$key01]->ticket_text = preg_replace("/[\n\r]/", "<br />", $tickets[$key]->childs[$key01]->ticket_text);
        $tickets[$key]->childs[$key01]->created_at = date_i18n('j F Y',$tickets[$key]->childs[$key01]->created_at);
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
$notifications = $wpdb->get_results ( "SELECT id,title,image,created_at,user,user_type FROM $table_notifcations ORDER BY id DESC" );

foreach ($notifications as $key => $value) {
    if($notifications[$key]->user_type == 1 && $notifications[$key]->user != '' && $notifications[$key]->user != 'all'){
        $notifications[$key]->user = explode(",",$notifications[$key]->user);
        if(!in_array($current_user->data->ID, $notifications[$key]->user)){
            unset($notifications[$key]);
        }
    }
}
$notifications = array_values($notifications);
foreach ($notifications as $key => $value) {

    $notifications[$key]->created_at = date_i18n('j F Y',strtotime($notifications[$key]->created_at));
}
$table_notifcations_read = $wpdb->prefix . 'hd_upanel_notifications_read';
$upanel_notifications_readed = '[]';
$user_notifications_readed = $wpdb->get_results ( "SELECT * FROM $table_notifcations_read where user_id=$current_user->ID " );
if(count($user_notifications_readed) == 0){
    $wpdb->insert($table_notifcations_read, array(
        'user_id' => $current_user->ID,
    ));
}else{
    $current_user->data->profile = $user_notifications_readed[0]->image;
    $upanel_notifications_readed = $user_notifications_readed[0]->notification_id;
}
if($upanel_notifications_readed == ''){
    $upanel_notifications_readed = '[]';
}

$items = json_decode( stripslashes($result->items));
$settings = json_decode($result->settings);
$all_settings = json_decode($result->all_settings);
if(!isset($all_settings->login_by_mobile)){
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

$admin_url = admin_url( 'admin-ajax.php' );


function order_products($orders){
    $products = [];

foreach ( $orders as $item_id => $item ) {

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


  function order_details($order_status,$user){
    global $downloads;


    if ( is_plugin_active( 'dokan-pro/dokan-pro.php' ) ) {
        global $wpdb;  
        global $woocommerce;
        $post_meta_table_name = $wpdb->prefix . 'postmeta';  
        $dokan_orders_table_name = $wpdb->prefix . 'dokan_orders';  
        $woocommerce_order_items = $wpdb->prefix . 'woocommerce_order_items';  
        $result = $wpdb->get_results( "SELECT * FROM wp_postmeta WHERE meta_key='_customer_user' AND meta_value = $user ");
        foreach ($result as $key => $value) {
            $post_id = $value->post_id;
            $result[$key]->ID = $post_id;
            $order_status = $wpdb->get_results( "SELECT * FROM $dokan_orders_table_name WHERE order_id=$post_id ");
            if(count($order_status)>0){
                $result[$key]->value = (int)$order_status[0]->order_total;
                $result[$key]->status = $order_status[0]->order_status;
            }
            // $order = new WC_Order($post_id);
            // $order_item = $order->get_items();
            // print_r($order);
            // die();  
            $order_status = $wpdb->get_results( "SELECT * FROM $woocommerce_order_items WHERE order_id=$post_id ");


            $order_item = $value->get_items();
             
            // $result[$key]['status'] =
        }
     
   



    }else{

        $customer_orders = get_posts([
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_value' =>$user,
            'post_type' => wc_get_order_types(),
            'post_status' => $order_status,
        ]);
        $Order_Array = [];
        foreach ($customer_orders as $customer_order) {
            $orderq = wc_get_order($customer_order);
        
            $notes = wc_get_order_notes(['order_id' => $orderq->get_id(),'type' => 'customer']);
            foreach ($notes as $key => $value) {
                $notes[$key]->date_created = date_i18n('j F Y' ,strtotime($notes[$key]->date_created));
            }
            $Order_Array[] = [
                "ID" => $orderq->get_id(),
                "Value" => $orderq->get_total(),
                "status" => $orderq->get_status(),
                "payment" => $orderq->get_payment_method_title(),
                "payment_url" => $orderq->get_checkout_payment_url(),
                "notes" => $notes,
                "Value_html" => htmlentities($orderq->get_formatted_order_total()),
                // "downloads" => get_downloads($orderq->get_downloadable_items()),
                "items" =>  order_products($orderq->get_items()),
                "address" => str_replace('<br/>', '\n', $orderq->get_formatted_billing_address()),
                "Date" => $orderq->get_date_created()->date_i18n('j F Y'),
            ];
    
    $dd = $orderq->get_downloadable_items();
            if(count($dd) > 0 && $order_status == 'wc-completed'){
                foreach ($dd as $key => $value) {
                    $value['access_expires'] = wc_format_datetime($value['access_expires'], 'j F Y');
					$ele = array(
						"download_url"=>stripslashes($value['download_url']),
						"product_name"=>str_replace("'", '', $value['product_name']),				
						"downloads_remaining"=>$value['downloads_remaining'],
						"access_expires"=>$value['access_expires'],
					);
                   
                            array_push($downloads, $value);
                }
            }
    
        }

    }



    return $Order_Array;
}


if(class_exists('WOO_Wallet_Actions')){



$wallet = new Woo_Wallet_Wallet();
// $wallet_settings = new Woo_Wallet_Admin();



// $testObject->newTest();
$balance = $wallet->get_wallet_balance();
// $transactions = get_wallet_transactions( array( 'limit' => apply_filters( 'woo_wallet_transactions_count', 10 ) ) );
// print_r($transactions);

// $ty = $wallet->credit($current_user->ID,2000000, $details = '', $args = null );
// print_r($ty);

// echo $wallet->credit( $current_user->ID, 1000, 'test');
// echo $topUpAdmin;



}




$dashboard=[
    "wallet"=>[
        "balance"=>(isset($balance))?str_replace( '"', "'", stripslashes($balance) ):'',
    ],
    "user"=>[
        "id"=>$current_user->ID,
        "count_register_date"=> (new DateTime())->diff(new DateTime($current_user->user_registered))->format("%a"),
        "register_date"=> (date_i18n("j F Y",strtotime($current_user->user_registered))),
        "billing"=> (new WC_Customer( $current_user->ID ))->get_billing(),
        "shipping"=> (new WC_Customer( $current_user->ID ))->get_shipping(),
    ],
    "date"=>[
        "persian_date"=>["day"=>date_i18n("l"),"month"=>date_i18n("j F"),"year"=>date_i18n("Y")],
        "gregorian_date"=>["day"=>date("l"),"month"=>date("j F"),"year"=>date("Y")]
    ],
    "user_comments"=>[
        "all"=>$wpdb->get_row(  "SELECT count(comment_ID) as counts FROM ".$table_prefix."comments where  user_id=".$current_user->ID),
        "not_accepted"=>$wpdb->get_row(  "SELECT count(comment_ID) as counts FROM ".$table_prefix."comments where `comment_approved`=0 and  user_id=".$current_user->ID)
    ],
    "user_orders"=>[
        "pending"=> order_details("wc-pending",$current_user->ID),
        "processing"=> order_details("wc-processing",$current_user->ID),
        "on-hold"=> order_details("wc-on-hold",$current_user->ID),
        "completed"=> order_details("wc-completed",$current_user->ID),
        "cancelled"=> order_details("wc-cancelled",$current_user->ID),
        "refunded"=> order_details("wc-refunded",$current_user->ID),
        "failed"=> order_details("wc-failed",$current_user->ID),
        "all"=> order_details(wc_get_order_statuses(),$current_user->ID),
        "totall_sale"=> htmlentities(wc_price((new WC_Customer( $current_user->ID ))->get_total_spent())),

    ],
];






    $membership = false;
    if ( is_plugin_active( 'restrict-content-pro/restrict-content-pro.php' ) ) {
        $membership = true;
        $memberships = rcp_get_user_payments(1);
        if(  $memberships ) {
            foreach(  $memberships as $payment ) {
                printf( __( 'ID: %s', 'rcp' ), $payment->id ); // Payment ID number
                printf( __( 'Subscription: %s', 'rcp' ), $payment->subscription ); // Name of the associated subscription level
                printf( __( 'Date: %s', 'rcp' ), $payment->date ); // Date the payment was made, in MySQL format
                printf( __( 'Amount: %s', 'rcp' ), $payment->amount ); // Amount the payment was for
                printf( __( 'User ID: %s', 'rcp' ), $payment->user_id ); // ID of the associated user
                printf( __( 'Payment Type: %s', 'rcp' ), $payment->payment_type ); // How the payment was made
                printf( __( 'Subscription Key: %s', 'rcp' ), $payment->subscription_key ); // Subscription key
                printf( __( 'Transaction ID: %s', 'rcp' ), $payment->transaction_id ); // Transaction ID
                printf( __( 'Status: %s', 'rcp' ), $payment->status ); // Status of the payment - 'complete', 'refunded'
            }
        }
        } 


require('files/index.php');
