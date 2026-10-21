<?php 
global $wpdb;
global  $woocommerce;
$table_prefix= $wpdb->prefix;
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
$result = $wpdb->get_results ( "SELECT * FROM $table_name LIMIT 1" )[0];
$all_settings = json_decode($result->all_settings);

?>
<!DOCTYPE html>
<html lang="fa">
<head>
<base href="<?php  echo (isset($all_settings->panel_url) && $all_settings->panel_url !=''?get_site_url().'/'.$all_settings->panel_url:get_site_url().'/panel'); ?>/">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php if(isset($all_settings->panel_title)){ echo $all_settings->panel_title;}else{ echo 'پنل کاربری'; } ?> | <?php echo get_bloginfo( 'name' ); ?></title>
    <?php if(isset($all_settings->no_index) && $all_settings->no_index=='on'){ ?>
      <meta name="robots" content="noindex,nofollow">
   <?php }   ?>
    <?php wp_site_icon(); ?>
    <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/css/hd-upanel-public.css?v='.HD_UPANEL_VERSION; ?>">
   <link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/css/select2.min.css?v='.HD_UPANEL_VERSION; ?>">
    <script  src="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/js/jquery.js?v='.HD_UPANEL_VERSION; ?>"></script>
    <script  src="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/js/angular.js?v='.HD_UPANEL_VERSION; ?>"></script>
    <script  src="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/js/angular-route.js?v='.HD_UPANEL_VERSION; ?>"></script>
    <script  src="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/js/angular-sanitize.js?v='.HD_UPANEL_VERSION; ?>"></script>
    <script  src="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/js/select2.min.js?v='.HD_UPANEL_VERSION; ?>"></script>


    <style>
      :root { 
        --color: <?php echo $all_settings->color; ?>;
        --color_o: <?php echo $all_settings->color.'1a'; ?>;
        --color_dark: <?php echo $all_settings->color_dark; ?>;
        --color_dark_o: <?php echo $all_settings->color_dark.'1a'; ?>;
      }
    </style>
       

</head>
<body>
    
<?php




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

<?php if(isset($_COOKIE['upanel-dark-mode']) && $_COOKIE['upanel-dark-mode'] == 'true') {  ?>
   <div id="hd-upanel-loading" class="dark">
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

</div>
 <?php } else {  ?>
    
   <div id="hd-upanel-loading" class="light">
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

</div>
 <?php } ?>

 <?php
if ( is_user_logged_in() ) {
    include('panel.php');
 } else {
      include('login.php');
 }

//  include('login.php');


 ?>

<script src="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/js/notyf.min.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/js/scripts.js'; ?>"></script>
<script  src="<?php echo plugin_dir_url( __FILE__ ) . 'upanel/assets/js/hd-upanel-public.js?v='.HD_UPANEL_VERSION; ?>"></script>



</body>
</html>







