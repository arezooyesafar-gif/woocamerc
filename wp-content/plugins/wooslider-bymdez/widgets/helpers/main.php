<?php 

namespace Woosbymdz\Helpers\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class main {

	//PROPS
	private static $instance;

	//METHODS
	public static function get_instance() 
	{
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @since 6.0.00
	 * gets all the available wordpress terms from the specified `taxonomy` and return as array
	 */
	public function get_terms_mdz( $taxonomy, $top_level_only = false, $with_empty = true ) 
    {

		$args = array(
			'taxonomy'      => $taxonomy,
			'hide_empty'    =>	false,
			'include' 		=> 'all',
		);
		
		if ( $top_level_only )
			$args['parent'] = 0;

		$the_query = new \WP_Term_Query($args);
		$list = array();
		if ( $with_empty ) 
			$list[] = esc_html__( 'Choose', 'wooslider-bymdez' );

		foreach( $the_query->get_terms() as $term ) { 
			$id = $term->term_id;
			$list[$id] = $term->name . ' (id - ' . $id . ')';
		}

		return $list;
	}

	/**
	 * @since 6.0.00
	 * adds all the css-deps files for the slider widgets (all the 3 widgets!)
	 */
	public function print_slider_css_deps()
	{
		$is_global_assts = get_option('woosbymdz_globalimport');
        if($is_global_assts != '1'){

            $is_browsernocache = get_option('woosbymdz_isbrowsernocache');
            $dont_import_swiper = get_option('woosbymdz_donimport_swiper');

            switch ($is_browsernocache) {
                case '1':
                    $dynamic_ver = get_option('woosbymdz_tmpversion');
                    break;
                default:
                    $dynamic_ver = '';
                    break;
            }

            if($dont_import_swiper != '1'){//if Dont add Swiper.js is not activated

                wp_register_style( 'woosbymdz-swipercss', WOOSBYMDZ_URL.'front/css/swiper-min.css', array(), WOOSBYMDZ_SW_VER);

            }
            

            $final_version = WOOSBYMDZ_VER.'.'.$dynamic_ver; 
            wp_register_style( 'woosbymdz-frontcss', WOOSBYMDZ_URL.'front/css/frontcss.css', array(), $final_version);

            if($dont_import_swiper != '1'){

                return [
                    'woosbymdz-swipercss',
                    'woosbymdz-frontcss',
                ];

            }else if($dont_import_swiper == '1'){

                return [
                    'woosbymdz-frontcss',
                ];

            }
            
        }else return [];
        

	}

	/**
	 * @since 6.0.00
	 * adds all the js-deps files for the slider widgets (all the 3 widgets!)
	 */
	public function print_slider_js_deps()
	{

		$is_global_assts = get_option('woosbymdz_globalimport');
        if($is_global_assts != '1'){

            $is_browsernocache = get_option('woosbymdz_isbrowsernocache');
            $dont_import_swiper = get_option('woosbymdz_donimport_swiper');

            switch ($is_browsernocache) {
                case '1':
                    $dynamic_ver = get_option('woosbymdz_tmpversion');
                    break;
                default:
                    $dynamic_ver = '';
                    break;
            }

            if($dont_import_swiper != '1'){//if Dont add Swiper.js is not activated

                wp_register_script( 'woosbymdz-swiperjs', WOOSBYMDZ_URL.'front/js/swiper-min.js', array(), WOOSBYMDZ_SW_VER);

            }
            
    
            $final_version = WOOSBYMDZ_VER.'.'.$dynamic_ver;
            $is_old_theme = get_option('woosbymdz_oldthemesdebuger');
            if($is_old_theme == '1'){
                wp_register_script( 'woosbymdz-frontjs', WOOSBYMDZ_URL.'front/js/frontjs.js', array('woosbymdz-swiperjs'), $final_version);
            }else wp_register_script( 'woosbymdz-frontjs', WOOSBYMDZ_URL.'front/js/frontjs.js', array('jquery', 'woosbymdz-swiperjs'), $final_version);

            wp_localize_script(
                'woosbymdz-frontjs',
                'woosbymdz_frnt_ajx_obj',
                [
                    'wwkxssjk'  => wp_create_nonce( 'woosbymdz-uio2899nb-w144wkxs' ),
                ],
            );//For Ajax-call Security
            
            
            if($dont_import_swiper != '1'){//if Dont add Swiper.js is not activated

                return [
                    'woosbymdz-swiperjs',
                    'woosbymdz-frontjs',
                ];

            }else if($dont_import_swiper == '1') {

                return [
                    'woosbymdz-frontjs',
                ];

            }

        }else return [];

	}

    /**
	 * @since 6.1.00
	 * Calculates the Average-Rating of a post or product
	 */
    public function calculate_average_post_rating_from_meta( $post_id, $meta_key='', $comment_meta_key='rating', $calc_unverfs='no') {

        if ( ! $post_id ) {
            return false;
        }

        //if is a Woocommerce Product
        if($meta_key == '_wc_average_rating' || !empty($meta_key)){
            return get_post_meta($post_id, $meta_key, true);
        }
        /***/

        $args = [
            'post_id' => $post_id,
        ];
        $post_comments = get_comments($args);

        $total_avg_rating = 0;
        $toal_ratings_count = 0;
        foreach($post_comments as $comment){
            //var_dump($comment);
            $com_id = $comment->comment_ID;
            $is_verified = get_comment_meta($com_id, 'verified', true);

            if($calc_unverfs == 'yes'){
                $is_verified = 1;
            }else if(!$is_verified){
                $is_verified = 1;
            }

            if($is_verified == 1 || $is_verified == 'yes' || $is_verified == 'ok' || $is_verified == 'on'){

                $has_rating_meta = get_comment_meta($com_id, $comment_meta_key, true);

                if($has_rating_meta && !empty($has_rating_meta)){

                    $has_rating_meta = $this->float_or_int($has_rating_meta);
                    $total_avg_rating = $total_avg_rating+$has_rating_meta;

                    $toal_ratings_count++;
                }
            }
            

        }


        if($toal_ratings_count == 0){
            $toal_ratings_count = 1;//Ignore Divide be the Zero!
        }

        return $total_avg_rating/$toal_ratings_count;
    }

    //Misc
    public function float_or_int($number)
    {
        if(is_string($number) && is_numeric($number)){
            if(strpos($number, '.') !== false){
                return floatval($number);
            }else return intval($number);
        }

        if(is_float($number)){
            return floatval($number);
        }else if(is_integer($number)){
            return intval($number);
        }
    }
    /***/
	
}//close the PHP tag to reduce the blank spaces ?>