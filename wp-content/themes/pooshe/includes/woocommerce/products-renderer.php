<?php
namespace PoosheElementor;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pooshe_Products_Renderer extends \WC_Shortcode_Products {

	private $settings = [];
	private $is_added_product_filter = false;
	const QUERY_CONTROL_NAME = 'query'; //Constraint: the class that uses the renderer, must use the same name
	const DEFAULT_COLUMNS_AND_ROWS = 4;

	public function __construct( $settings = [], $type = 'products' ) {
		$this->settings = $settings;
		$this->type = $type;
		$this->attributes = $this->parse_attributes( [
			'columns' => 1,
			'rows' => 1,
			'paginate' => false,
			'cache' => false,
		] );
		$this->query_args = $this->parse_query_args();
	}

	/**
	 * Override the original `get_query_results`
	 * with modifications that:
	 * 1. Remove `pre_get_posts` action if `is_added_product_filter`.
	 *
	 * @return bool|mixed|object
	 */

	protected function get_query_results() {
		$results = parent::get_query_results();
		// Start edit.
		if ( $this->is_added_product_filter ) {
			remove_action( 'pre_get_posts', [ wc()->query, 'product_query' ] );
		}
		// End edit.

		return $results;
	}

	protected function parse_query_args() {
		$prefix = self::QUERY_CONTROL_NAME . '_';
		$settings = &$this->settings;

		$query_args = [
			'post_type' => 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $settings[ $prefix . 'ppp' ],
			'orderby' => $settings[ $prefix . 'orderby' ],
			'order' => strtoupper( $settings[ $prefix . 'order' ] ),
		];

		$query_args['meta_query'] = WC()->query->get_meta_query();
		$query_args['tax_query'] = [];

		$front_page = is_front_page();
        $ordering_args = WC()->query->get_catalog_ordering_args( $query_args['orderby'], $query_args['order'] );

		$query_args['orderby'] = $ordering_args['orderby'];
		$query_args['order'] = $ordering_args['order'];
		if ( $ordering_args['meta_key'] ) {
			$query_args['meta_key'] = $ordering_args['meta_key'];
		}

		// Visibility.
		$this->set_visibility_query_args( $query_args );

		//Featured.
		$this->set_featured_query_args( $query_args );

		//Sale.
		$this->set_sale_products_query_args( $query_args );

		// IDs.
		$this->set_ids_query_args( $query_args );

		// Set specific types query args.
		if ( method_exists( $this, "set_{$this->type}_query_args" ) ) {
			$this->{"set_{$this->type}_query_args"}( $query_args );
		}

		// Categories & Tags
		$this->set_terms_query_args( $query_args );

		//Exclude.
		$this->set_exclude_query_args( $query_args );

		$query_args = apply_filters( 'woocommerce_shortcode_products_query', $query_args, $this->attributes, $this->type );

		// Always query only IDs.
		$query_args['fields'] = 'ids';

		return $query_args;
	}

	protected function set_ids_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME . '_';

		switch ( $this->settings[ $prefix . 'post_type' ] ) {
			case 'by_id':
				$post__in = $this->settings[ $prefix . 'posts_ids' ];
				break;
			case 'sale':
				$post__in = wc_get_product_ids_on_sale();
				break;
		}

		if ( ! empty( $post__in ) ) {
			$query_args['post__in'] = $post__in;
			remove_action( 'pre_get_posts', [ wc()->query, 'product_query' ] );
		}
	}

	private function set_terms_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME . '_';

		$query_type = $this->settings[ $prefix . 'post_type' ];

		if ( 'by_id' === $query_type || 'current_query' === $query_type ) {
			return;
		}

		if ( empty( $this->settings[ $prefix . 'include' ] ) || empty( $this->settings[ $prefix . 'include_term_ids' ] ) || ! in_array( 'terms', $this->settings[ $prefix . 'include' ], true ) ) {
			return;
		}

		$terms = [];
		foreach ( $this->settings[ $prefix . 'include_term_ids' ] as $id ) {
			$term_data = get_term_by( 'term_taxonomy_id', $id );
			$taxonomy = $term_data->taxonomy;
			$terms[ $taxonomy ][] = $id;
		}
		$tax_query = [];
		foreach ( $terms as $taxonomy => $ids ) {
			$query = [
				'taxonomy' => $taxonomy,
				'field' => 'term_taxonomy_id',
				'terms' => $ids,
			];

			$tax_query[] = $query;
		}

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = array_merge( $query_args['tax_query'], $tax_query );
		}
	}

	protected function set_featured_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME . '_';
		if ( 'featured' === $this->settings[ $prefix . 'post_type' ] ) {
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			$query_args['tax_query'][] = [
				'taxonomy' => 'product_visibility',
				'field' => 'term_taxonomy_id',
				'terms' => [ $product_visibility_term_ids['featured'] ],
			];
		}
	}

	protected function set_sale_products_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME . '_';
		if ( 'sale' === $this->settings[ $prefix . 'post_type' ] ) {
			parent::set_sale_products_query_args( $query_args );
		}
	}

	protected function set_exclude_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME . '_';

		if ( empty( $this->settings[ $prefix . 'exclude' ] ) ) {
			return;
		}
		$post__not_in = [];
		if ( in_array( 'current_post', $this->settings[ $prefix . 'exclude' ] ) ) {
			if ( is_singular() ) {
				$post__not_in[] = get_queried_object_id();
			}
		}

		if ( in_array( 'manual_selection', $this->settings[ $prefix . 'exclude' ] ) && ! empty( $this->settings[ $prefix . 'exclude_ids' ] ) ) {
			$post__not_in = array_merge( $post__not_in, $this->settings[ $prefix . 'exclude_ids' ] );
		}

		$query_args['post__not_in'] = empty( $query_args['post__not_in'] ) ? $post__not_in : array_merge( $query_args['post__not_in'], $post__not_in );

		/**
		 * WC populates `post__in` with the ids of the products that are on sale.
		 * Since WP_Query ignores `post__not_in` once `post__in` exists, the ids are filtered manually, using `array_diff`.
		 */
		if ( 'sale' === $this->settings[ $prefix . 'post_type' ] ) {
			$query_args['post__in'] = array_diff( $query_args['post__in'], $query_args['post__not_in'] );
		}

		if ( in_array( 'terms', $this->settings[ $prefix . 'exclude' ] ) && ! empty( $this->settings[ $prefix . 'exclude_term_ids' ] ) ) {
			$terms = [];
			foreach ( $this->settings[ $prefix . 'exclude_term_ids' ] as $to_exclude ) {
				$term_data = get_term_by( 'term_taxonomy_id', $to_exclude );
				$terms[ $term_data->taxonomy ][] = $to_exclude;
			}
			$tax_query = [];
			foreach ( $terms as $taxonomy => $ids ) {
				$tax_query[] = [
					'taxonomy' => $taxonomy,
					'field' => 'term_id',
					'terms' => $ids,
					'operator' => 'NOT IN',
				];
			}
			if ( empty( $query_args['tax_query'] ) ) {
				$query_args['tax_query'] = $tax_query;
			} else {
				$query_args['tax_query']['relation'] = 'AND';
				$query_args['tax_query'][] = $tax_query;
			}
		}
	}


    protected function product_loop($before = '',$after='',$style = 'default') {
        $columns  = absint( $this->attributes['columns'] );
        $products = $this->get_query_results();

        ob_start();

        if ( $products && $products->ids ) {
            // Prime caches to reduce future queries.
            if ( is_callable( '_prime_post_caches' ) ) {
                _prime_post_caches( $products->ids );
            }

            // Setup the loop.
            wc_setup_loop(
                array(
                    'name'         => $this->type,
                    'is_shortcode' => true,
                    'is_search'    => false,
                    'total'        => $products->total,
                    'total_pages'  => $products->total_pages,
                    'per_page'     => $products->per_page,
                    'current_page' => $products->current_page,
                )
            );

            $original_post = $GLOBALS['post'];

            do_action( "woocommerce_shortcode_before_{$this->type}_loop", $this->attributes );


            if ( wc_get_loop_prop( 'total' ) ) {
                foreach ( $products->ids as $product_id ) {
                    $GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
                    setup_postdata( $GLOBALS['post'] );

                    // Set custom product visibility when quering hidden products.
                    add_action( 'woocommerce_product_is_visible', array( $this, 'set_product_as_visible' ) );
                    echo $before;
                    // Render product template.
                    wc_get_template( 'content-product.php',[
                            'style' =>  $style
                    ] );
                    echo $after;
                    // Restore product visibility.
                    remove_action( 'woocommerce_product_is_visible', array( $this, 'set_product_as_visible' ) );
                }
            }

            $GLOBALS['post'] = $original_post; // WPCS: override ok.


            do_action( "woocommerce_shortcode_after_{$this->type}_loop", $this->attributes );

            wp_reset_postdata();
            wc_reset_loop();
        } else {
            do_action( "woocommerce_shortcode_{$this->type}_loop_no_results", $this->attributes );
        }

        return ob_get_clean();
    }
    protected function custom_product_loop($before = '',$after='') {
        $columns  = absint( $this->attributes['columns'] );
        $products = $this->get_query_results();

        ob_start();

        if ( $products && $products->ids ) {
            // Prime caches to reduce future queries.
            if ( is_callable( '_prime_post_caches' ) ) {
                _prime_post_caches( $products->ids );
            }

            // Setup the loop.
            wc_setup_loop(
                array(
                    'name'         => $this->type,
                    'is_shortcode' => true,
                    'is_search'    => false,
                    'total'        => $products->total,
                    'total_pages'  => $products->total_pages,
                    'per_page'     => $products->per_page,
                    'current_page' => $products->current_page,
                )
            );

            $original_post = $GLOBALS['post'];

            do_action( "woocommerce_shortcode_before_{$this->type}_loop", $this->attributes );


            if ( wc_get_loop_prop( 'total' ) ) {
                foreach ( $products->ids as $product_id ) {
                    $GLOBALS['post'] = get_post( $product_id ); // WPCS: override ok.
                    setup_postdata( $GLOBALS['post'] );

                    // Set custom product visibility when quering hidden products.
                    add_action( 'woocommerce_product_is_visible', array( $this, 'set_product_as_visible' ) );
                    echo $before;
                    $product = new \WC_Product($product_id);

                    ?>
                    <div class="post-item small-post">
                        <a href="<?php the_permalink(); ?>">
                            <figure class="thumbnail"><?php echo pooshe_product_logo(); ?></figure>
                        </a>
                        <div class="post-details smart-hover">
                            <a href="<?php the_permalink(); ?>">
                            <figure class="large">
                                <?php echo woocommerce_get_product_thumbnail(); ?>
                            </figure>
                            <?php the_title("<h4 class=\"title\">","</h4>") ?>
                            <div class="price pooshe-price">
                                <?php echo $product->get_price_html(); ?>
                            </div>
                            </a>
                        </div>
                    </div>
                    <?php
                    echo $after;
                    // Restore product visibility.
                    remove_action( 'woocommerce_product_is_visible', array( $this, 'set_product_as_visible' ) );
                }
            }

            $GLOBALS['post'] = $original_post; // WPCS: override ok.


            do_action( "woocommerce_shortcode_after_{$this->type}_loop", $this->attributes );

            wp_reset_postdata();
            wc_reset_loop();
        } else {
            do_action( "woocommerce_shortcode_{$this->type}_loop_no_results", $this->attributes );
        }

        return ob_get_clean();
    }
    public function get_content($before = '',$after='',$item_before = '',$item_after = '', $style='default') {
        return $before . $this->product_loop($item_before,$item_after,$style) . $after;
    }
    public function get_custom_content($before = '',$after='',$item_before = '',$item_after = '') {
        return $before . $this->custom_product_loop($item_before,$item_after) . $after;
    }
}
