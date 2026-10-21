
<?php
if (!function_exists("add_action"))
{
    echo "<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>";
    exit();
}
else if (!defined("ABSPATH"))
{
    echo "<h3>an error occured! You may not be able to access this plugin via direct URL...</h3>";
    exit();
}
/**/
$theme_prod_title = "h2.woocommerce-loop-product__title"; 
$theme_prod_cats = ".woosbymdz-null"; 
$theme_prod_price = "span.price";
$theme_prod_price_normal = "span.price .amount"; 
$theme_prod_price_del = "span.price del .amount"; 
$theme_prod_label_con = ".onsale"; 
$theme_prod_label_txt = ".onsale"; 
$theme_prod_add_btn = "a.button";
if(!function_exists("woosbymdz_ret_themeSwiperClasses")){
function woosbymdz_ret_themeSwiperClasses(){
    return "";
}
}
//close the PHP tag to reduce the blank spaces ?>