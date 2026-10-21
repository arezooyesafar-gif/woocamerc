<?php
add_action( 'wp_ajax_pooshe_ajax_search', 'pooshe_ajax_search' );
add_action( 'wp_ajax_nopriv_pooshe_ajax_search', 'pooshe_ajax_search' );
function pooshe_ajax_search() {
    $json_array = array(
        'status'    =>  false,
        'data'      =>  ''
    );
    $query_s = (isset($_POST['s']) and !empty($_POST['s']))? $_POST['s'] : false;
    $query_cat = (isset($_POST['pcat']) and !empty($_POST['pcat']))? (int)$_POST['pcat'] : false;
    $query_stuck = (isset($_POST['stuck']) and !empty($_POST['stuck']))? $_POST['stuck'] : false;
    $post_type = 'product';
    if ($query_s !== false) {
        $args = array(
            's' => $query_s,
            'posts_per_page' => 10,
            'post_type' => $post_type
        );
        if ($query_cat !== false) {
            if($post_type == "product") {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => array($query_cat),
                    ),
                );
            }else{
                $args['cat'] = $query_cat;
            }
        }
        if ($query_stuck !== false and $post_type == "product") {
            if ($query_stuck == "true") {
                $args['meta_query']['relation'] = 'AND';
                $args['meta_query'][] = [
                    'key'     => '_stock_status',
                    'value'   => 'instock',
                    'compare' => '=',
                ];
            }
        }


        $the_query = new WP_Query($args);
        if($post_type == "product") {
            if (!$the_query->have_posts()) {
                if (is_numeric($query_s)) {
                    unset($args['s']);
                    $args['p'] = $query_s;

                    $the_query = new WP_Query($args);
                }
                if (!$the_query->have_posts()) {
                    unset($args['s']);
                    unset($args['p']);
                    $args['meta_query'][] = [
                        'key' => '_sku',
                        'value' => $query_s,
                        'compare' => '=',
                    ];
                    $the_query = new WP_Query($args);
                }
            }
        }
        $posts = array();
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();

                $pf = new WC_Product_Factory();
                $prd = $pf->get_product(get_the_id());

                $posts[] = array(
                    'id'	=>	get_the_id(),
                    'title'	=>	get_the_title(),
                    'price'	=>	$prd->get_price_html(),
                    'link'	=>	get_the_permalink(),
                    'img'	=>	pooshe_product_logo(get_the_id()) !== false?pooshe_product_logo(get_the_id()):woocommerce_get_product_thumbnail()
                );
            }
            wp_reset_postdata();
        }
        $json_array['status'] = true;
        $json_array['data'] = $posts;

    }

    wp_send_json($json_array);
    wp_die();
}

add_action( 'wp_ajax_pooshe_ajax_login', 'pooshe_ajax_login' );
add_action( 'wp_ajax_nopriv_pooshe_ajax_login', 'pooshe_ajax_login' );
function pooshe_ajax_login() {
    $json_array = array(    
        'status'    =>  false,
        'data'      =>  ''
    );
    if(check_ajax_referer('pooshe_user_login')) {
        $login_res = pooshe_login_user($_POST['username'], $_POST['password'], $_POST['remember']);
        if ($login_res === true) {
            $json_array['status'] = true;
            $json_array['data'] = __('وارد شدید! درحال انتقال...', 'pooshe');
            $json_array['type'] = 'success';
        } else {
            $json_array['data'] = $login_res;
            $json_array['type'] = 'error';
        }
    }
    wp_send_json($json_array);

}
function pooshe_login_user($user_name_email, $user_pass, $remember) {

    if ( is_email($user_name_email) != false ) {

        if ( !email_exists($user_name_email) ) {
            return __("ایمیل وارد شده اشتباه است!",'pooshe');
        } else {
            if ($remember == '1') { $remb = true; } else { $remb = false; }

            $userobj = get_user_by( 'email', $user_name_email );
            $login_res = wp_authenticate($userobj->user_login, $user_pass);

            if (is_wp_error($login_res)) {

                $le = pooshe_login_error(key($login_res->errors));
                return $le;

            } elseif ($login_res instanceof WP_User) {

                wp_set_auth_cookie($login_res->ID , $remb);
                return true;

            } else {
                echo __('خطا هنگام ورود، مجددا تلاش کنید!','pooshe');
            }
        }

    } else {

        if ( !username_exists($user_name_email) ) {
            return __('نام کاربری وارد شده اشتباه است.','pooshe');
        } else {
            if ($remember == '1') { $remb = true; } else { $remb = false; }

            $login_res = wp_authenticate($user_name_email, $user_pass);

            if (is_wp_error($login_res)) {

                $le = pooshe_login_error(key($login_res->errors));
                return $le;

            } elseif ($login_res instanceof WP_User) {

                wp_set_auth_cookie($login_res->ID , $remb);
                return true;

            } else {
                echo __('مشکلی هنگام ورود به وجود آمده است، لطفا مجددا سعی نمایید.','pooshe');
            }
        }

    }
}
function pooshe_login_error($err_name) {
    $err_dec = $err_name;
    if ($err_dec == 'incorrect_password') {
        $err_dec = __('رمز عبور وارد شده صحیح نمی باشد!','pooshe');
    } elseif ($err_dec == 'invalid_username') {
        $err_dec = __('کاربری با مشخصات وارد شده یافت نشد!','pooshe');
    } else {
        $err_dec = __('مشکلی هنگام ورود به وجود آمده است!','pooshe').' ' . $err_name;
    }
    return $err_dec;
}

add_action( 'wp_ajax_pooshe_ajax_add_to_cart', 'pooshe_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_pooshe_ajax_add_to_cart', 'pooshe_ajax_add_to_cart' );
function pooshe_ajax_add_to_cart() {
    $product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    $quantity     = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;
    $variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
    $variation    = isset( $_POST['variation'] ) ? (array) $_POST['variation'] : array();

    if ( ! $product_id ) {
        wp_send_json_error( array( 'message' => __( 'محصول نامعتبر است.', 'pooshe' ) ) );
    }

    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        wp_send_json_error( array( 'message' => __( 'محصول یافت نشد.', 'pooshe' ) ) );
    }

    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );
    if ( ! $passed_validation ) {
        wp_send_json_error( array( 'message' => __( 'امکان افزودن محصول به سبد خرید وجود ندارد.', 'pooshe' ) ) );
    }

    $cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );
    if ( $cart_item_key ) {
        do_action( 'woocommerce_ajax_added_to_cart', $product_id );

        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();

        $data = array(
            'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
            ) ),
            'cart_hash'  => WC()->cart->get_cart_hash(),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total(),
            'message'    => sprintf( __( '"%s" به سبد خرید اضافه شد.', 'pooshe' ), $product->get_name() ),
        );
        wp_send_json_success( $data );
    } else {
        wp_send_json_error( array( 'message' => __( 'خطا در افزودن محصول به سبد خرید.', 'pooshe' ) ) );
    }

    wp_die();
}

add_action( 'wp_ajax_pooshe_ajax_carousel', 'pooshe_ajax_carousel' );
add_action( 'wp_ajax_nopriv_pooshe_ajax_carousel', 'pooshe_ajax_carousel' );
function pooshe_ajax_carousel() {
    $json_array = array(
        'status'    =>  false,
        'data'      =>  []
    );
    $options = (isset($_POST['options']) and !empty($_POST['options']))? json_decode(string_compress_decode(stripslashes($_POST['options'])),true) : false;
    $options = ($options !== null)?$options:false;
    if($options !== false){
        $json_array['status'] = true;

        $query = new PoosheElementor\Pooshe_Products_Renderer($options,'products');
        $content = '';
        if($options['is_carousel']) {
            $content = $query->get_content('', '', '<div class="swiper-slide"><ul>', '</ul></div>');
        }else{
            $content = $query->get_content('', '', '<div class="'.$options['columns'].'"><ul>', '</ul></div>');
        }
        if(isset($options['archive'])){
            $json_array['data']['archive'] = $options['archive'];
        }
        $json_array['data']['content'] = $content;

    }


    wp_send_json($json_array);
    wp_die();
}

add_action( 'wp_ajax_pooshe_ajax_comment_vote', 'pooshe_ajax_comment_vote' );
add_action( 'wp_ajax_nopriv_pooshe_ajax_comment_vote', 'pooshe_ajax_comment_vote' );
function pooshe_ajax_comment_vote() {
    $comment_id = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : 0;
    $vote_type  = isset($_POST['vote_type']) ? $_POST['vote_type'] : '';
    $nonce      = isset($_POST['nonce']) ? $_POST['nonce'] : '';

    if (!$comment_id || !in_array($vote_type, array('like', 'dislike'), true)) {
        wp_send_json_error(array('message' => __('درخواست نامعتبر', 'pooshe')));
    }

    if (!wp_verify_nonce($nonce, 'pooshe_comment_vote_' . $comment_id)) {
        wp_send_json_error(array('message' => __('خطای امنیتی', 'pooshe')));
    }

    $comment = get_comment($comment_id);
    if (!$comment) {
        wp_send_json_error(array('message' => __('دیدگاه یافت نشد', 'pooshe')));
    }

    $vote_key = pooshe_get_comment_vote_key();
    $votes = get_comment_meta($comment_id, 'cb_votes', true);
    $votes = is_array($votes) ? $votes : array();
    $like_count = (int) get_comment_meta($comment_id, 'cb_like', true);
    $dislike_count = (int) get_comment_meta($comment_id, 'cb_dislike', true);
    $existing = isset($votes[$vote_key]) ? $votes[$vote_key] : '';

    if ($existing === $vote_type) {
        unset($votes[$vote_key]);
        if ($vote_type === 'like') {
            $like_count = max(0, $like_count - 1);
        } else {
            $dislike_count = max(0, $dislike_count - 1);
        }
        $new_vote = '';
    } else {
        if ($existing === 'like') {
            $like_count = max(0, $like_count - 1);
        } elseif ($existing === 'dislike') {
            $dislike_count = max(0, $dislike_count - 1);
        }
        $votes[$vote_key] = $vote_type;
        if ($vote_type === 'like') {
            $like_count++;
        } else {
            $dislike_count++;
        }
        $new_vote = $vote_type;
    }

    update_comment_meta($comment_id, 'cb_votes', $votes);
    update_comment_meta($comment_id, 'cb_like', $like_count);
    update_comment_meta($comment_id, 'cb_dislike', $dislike_count);

    wp_send_json_success(array(
        'like_count'    => $like_count,
        'dislike_count' => $dislike_count,
        'user_vote'     => $new_vote,
    ));
}

add_action( 'wp_ajax_pooshe_ajax_submit_comment', 'pooshe_ajax_submit_comment' );
add_action( 'wp_ajax_nopriv_pooshe_ajax_submit_comment', 'pooshe_ajax_submit_comment' );
function pooshe_ajax_submit_comment() {
    $comment_data = isset($_POST['comment_data']) ? $_POST['comment_data'] : array();

    if (empty($comment_data) || empty($comment_data['comment'])) {
        wp_send_json_error(array('message' => __('متن دیدگاه نمی‌تواند خالی باشد', 'pooshe')));
    }

    $post_id = isset($comment_data['comment_post_ID']) ? (int) $comment_data['comment_post_ID'] : 0;
    $post = get_post($post_id);
    if (!$post || !comments_open($post_id)) {
        wp_send_json_error(array('message' => __('امکان ثبت دیدگاه وجود ندارد', 'pooshe')));
    }

    $author = isset($comment_data['author']) ? sanitize_text_field($comment_data['author']) : '';
    $email  = isset($comment_data['email']) ? sanitize_email($comment_data['email']) : '';

    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $author = $user->display_name;
        $email  = $user->user_email;
        $user_id = $user->ID;
    } else {
        $user_id = 0;
        if (empty($author) || empty($email)) {
            wp_send_json_error(array('message' => __('نام و ایمیل الزامی است', 'pooshe')));
        }
        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('ایمیل وارد شده معتبر نیست', 'pooshe')));
        }
    }

    $data = array(
        'comment_post_ID'      => $post_id,
        'comment_author'       => $author,
        'comment_author_email' => $email,
        'comment_author_url'   => isset($comment_data['url']) ? esc_url_raw($comment_data['url']) : '',
        'comment_content'      => wp_kses_post($comment_data['comment']),
        'comment_type'         => isset($comment_data['comment_type']) ? $comment_data['comment_type'] : 'comment',
        'comment_parent'       => isset($comment_data['comment_parent']) ? (int) $comment_data['comment_parent'] : 0,
        'user_id'              => $user_id,
    );

    if (!empty($comment_data['rating']) && $post->post_type === 'product') {
        $rating = absint($comment_data['rating']);
        if ($rating >= 1 && $rating <= 5) {
            $data['comment_meta']['rating'] = $rating;
        }
    }

    add_filter('wp_new_comment_allow_duplicates', '__return_true');

    $comment_id = wp_new_comment($data, true);
    if (is_wp_error($comment_id)) {
        wp_send_json_error(array('message' => $comment_id->get_error_message()));
    }

    if (isset($data['comment_meta']['rating'])) {
        update_comment_meta($comment_id, 'rating', $data['comment_meta']['rating']);
    }

    if (isset($comment_data['plus-rate']) && is_array($comment_data['plus-rate'])) {
        add_comment_meta($comment_id, 'plus-rate', array_filter($comment_data['plus-rate']));
    }
    if (isset($comment_data['negative-rate']) && is_array($comment_data['negative-rate'])) {
        add_comment_meta($comment_id, 'negative-rate', array_filter($comment_data['negative-rate']));
    }

    add_comment_meta($comment_id, 'cb_like', 0);
    add_comment_meta($comment_id, 'cb_dislike', 0);

    if ($post->post_type === 'product' && $user_id !== 0) {
        $customer_email = get_user_meta($user_id, 'billing_email', true);
        $is_bought      = wc_customer_bought_product($customer_email, $user_id, $post_id);
        if ($is_bought) {
            add_comment_meta($comment_id, 'is_buyer', '1');
            add_comment_meta($comment_id, 'verified', '1');
        }
    }

    $comment = get_comment($comment_id);
    ob_start();
    wp_list_comments(array(
        'walker'      => new Pooshe_Walker_Comment(),
        'avatar_size' => 100,
        'style'       => 'ul',
        'short_ping'  => true,
        'max_depth'   => get_option('thread_comments_depth'),
    ), array($comment));
    $html = ob_get_clean();

    wp_send_json_success(array(
        'comment_id'  => $comment_id,
        'comment_html'=> $html,
        'message'     => __('دیدگاه شما با موفقیت ثبت شد و پس از تایید نمایش داده خواهد شد.', 'pooshe'),
    ));
}
