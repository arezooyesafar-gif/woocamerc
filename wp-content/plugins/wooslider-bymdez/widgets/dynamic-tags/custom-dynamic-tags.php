<?php 

namespace Woosbymdz\Helpers\DynamicTags;

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

class dyna_tags 
{

    //PROPS
	private static $instance;

    public function __construct()
    {

        add_action( 'elementor/dynamic_tags/register', [$this, 'register_cust_dyna_tags'] );

    }

    //METHODS
    public static function get_instance() 
	{
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


    public function register_cust_dyna_tags($dynamic_tags_manager)
    {

        //Comments Avg Rating (Number)
        require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'dynamic-tags'.DIRECTORY_SEPARATOR.'comments-avg-rank.php';
        $dynamic_tags_manager->register( new \Woosbymdz_ComentsAvg() );
        /***/

        //Posts Links (Global)
        require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'dynamic-tags'.DIRECTORY_SEPARATOR.'posts-url-every-where.php';
        $dynamic_tags_manager->register( new \Woosbymdz_PostsUrl_EveryWhere() );
        /***/

        //Products Advanced Prices
        require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'dynamic-tags'.DIRECTORY_SEPARATOR.'procuts-advanced-prices.php';
        $dynamic_tags_manager->register( new \Woosbymdz_Adv_Prices() );
        /***/

        //Products Offer Percent
        require_once WOOSBYMDZ_PATH.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'dynamic-tags'.DIRECTORY_SEPARATOR.'products-sale-perc.php';
        $dynamic_tags_manager->register( new \Woosbymdz_Pros_SalePercent() );
        /***/
    }

}//close the PHP tag to reduce the blank spaces ?>