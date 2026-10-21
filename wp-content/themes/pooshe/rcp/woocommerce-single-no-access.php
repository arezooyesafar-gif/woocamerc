<?php
/**
 * WooCommerce - No Access Message
 *
 * This template is used to display the restriction message if an unauthorized user
 * tries to view a restricted product.
 *
 * For modifying this template, please see: http://docs.restrictcontentpro.com/article/1738-template-files
 *
 * @package     Restrict Content Pro
 * @subpackage  Templates/WooCommerce No Access
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

global $rcp_options, $product;
$message = rcp_get_restricted_content_message( true );
$subscribe_levels  = $product->get_meta( '_rcp_woo_subscription_levels_to_view',true );

?>
<div class="rcp-woocommerce-no-access">
    <?php
        if (is_array($subscribe_levels) && !empty($subscribe_levels)){
            $subscription_levels = rcp_get_subscription_levels();
            foreach ($subscription_levels as $level){
                if (!in_array($level->id, $subscribe_levels)){
                    continue;
                }
                echo '<div class="access_levels_to_view"><span>این محتوا برای کاربران  </span><b>' . $level->name . '</b> <span>قابل مشاهده میباشد</span></div>';
            }
        }
    ?>
	<?php echo rcp_format_teaser( $message ); ?>
</div>