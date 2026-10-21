




<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد  <?php echo get_bloginfo( 'name' ); ?></title>
    <?php wp_site_icon(); ?>

   <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/css/select2.min.css?v='.HD_UPANEL_VERSION; ?>">
    <script  src="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/js/select2.min.js?v='.HD_UPANEL_VERSION; ?>"></script>
    
    <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) . 'files/assets/css/styles.css?v='.HD_UPANEL_VERSION; ?>">
    <script src="<?php echo plugin_dir_url( __FILE__ ) . 'files/assets/js/jquery.min.js'; ?>"></script>
    <script src="<?php echo plugin_dir_url( __FILE__ ) . 'files/assets/js/scripts.js?v='.HD_UPANEL_VERSION; ?>"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
     integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
     crossorigin=""/>
    <!-- <script src="https://cdn.jsdelivr.net/npm/vue@3"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-router@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/element-plus@1.1.0-beta.9/dist/index.full.min.js"></script> -->

    <!-- <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
    <script src="https://unpkg.com/vue-router@3/dist/vue-router.js"></script> -->
    <script src="https://unpkg.com/vue@2/dist/vue.js"></script>
<script src="https://unpkg.com/vue-router@3/dist/vue-router.js"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/vuex@3.0.1/dist/vuex.js"></script>
    <!-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> -->

</head>
<body>
    
<?php

global $wpdb;
global  $woocommerce;
$table_prefix= $wpdb->prefix;
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
$result = $wpdb->get_results ( "SELECT * FROM $table_name LIMIT 1" )[0];
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

$currency_symbol = get_woocommerce_currency_symbol();

?>

<?php if(isset($_COOKIE['cr-dashboard-dark-mode']) && $_COOKIE['cr-dashboard-dark-mode'] == 'true') {  ?>
   <!-- <div id="cr-dashboard-loading" class="dark">
<img src="<?php if($all_settings->main_logo_dark){ echo $all_settings->main_logo_dark; }
else{ echo plugin_dir_url( __FILE__ ).'upanel/assets/img/logo_dark.svg'; }  ?>">
  <svg ng-if="data.loading"  xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" stroke="#fff">
    <g fill="none" fill-rule="evenodd">
        <g transform="translate(1 1)" stroke-width="2">
            <circle stroke-opacity=".5" cx="18" cy="18" r="18"/>
            <path d="M36 18c0-9.94-8.06-18-18-18">
                <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"/>
            </path>
        </g>
    </g>
</svg>

</div> -->
 <?php } else {  ?>
    
   <!-- <div id="cr-dashboard-loading" class="light">
<img src="<?php if($all_settings->main_logo){ echo $all_settings->main_logo; }
else{ echo plugin_dir_url( __FILE__ ).'upanel/assets/img/logo.svg'; }  ?>">

<svg ng-if="data.loading"  xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" stroke="#fff">
    <g fill="none" fill-rule="evenodd">
        <g transform="translate(1 1)" stroke-width="2">
            <circle stroke-opacity=".5" cx="18" cy="18" r="18"/>
            <path d="M36 18c0-9.94-8.06-18-18-18">
                <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"/>
            </path>
        </g>
    </g>
</svg>

</div> -->
 <?php } ?>

 <?php
if ( is_user_logged_in() ) {
    include('panel.php');
 } else {
      include('login.php');
 }

//  include('login.php');


 ?>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
     integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
     crossorigin=""></script>
<script src="<?php echo plugin_dir_url( __FILE__ ) . 'files/assets/js/scripts.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ) . 'files/assets/js/cr-dashboard-public.js'; ?>"></script>



</body>
</html>







