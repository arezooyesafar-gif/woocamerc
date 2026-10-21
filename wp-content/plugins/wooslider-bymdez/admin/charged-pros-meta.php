<?php
if (!function_exists('add_action'))
{
    echo '<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>';
    exit();
}
else if (!defined('ABSPATH'))
{
    echo '<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>';
    exit();
}
/**/

$has_used_ever = get_option("woosbymdz_charged_pros_everrunned");

if(!$has_used_ever)
{

//Add meta on all products
global $table_prefix, $wpdb;
$tb_posts = $table_prefix.'posts';

$sql = "SELECT * FROM `$tb_posts` WHERE `post_type` = 'product'";
$res = $wpdb->get_results($sql);

foreach($res as $re)
{

    $p_id = $re->ID;
    $product = wc_get_product($p_id);
    $p_type = $product->get_type();

    switch($p_type){
        case 'simple':
            $has_meta = metadata_exists('post', $p_id, 'woosbymdz_pro_quan');
            if(!$has_meta){
                $stck = $product->stock_quantity;
                update_post_meta($p_id, 'woosbymdz_pro_quan', $stck);
            }
            break;
        case 'variable':
            $variations = $product->get_available_variations();
            foreach($variations as $this_var){

                $var_id = $this_var['variation_id'];
                $var_pro = get_product($var_id);

                $has_meta = metadata_exists('post', $var_id, 'woosbymdz_pro_quan');
                if(!$has_meta){
                    $stck = $var_pro->stock_quantity;
                    update_post_meta($var_id, 'woosbymdz_pro_quan', $stck);
                }
            }
            break;
    }
}

$tmp_arr = [
    1,
    time(),
];
update_option('woosbymdz_charged_pros_everrunned', $tmp_arr, false);


$mssg = esc_html__('Meta information has been successfully set on all products!', 'wooslider-bymdez');
$mssg2 = esc_html__('\nWooslider: We sincerely thank you for your cooperation', 'wooslider-bymdez');
$mssg = $mssg.$mssg2;
echo "<script> alert('".$mssg."'); </script>";

}
//close the PHP tag to reduce the blank spaces ?>