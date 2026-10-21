<?php
namespace pooshe;
class POOSHE_PRW extends \WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'classname' => 'pooshe-prw',
            'description' => 'امتیاز محصولات قالب پوشه',
        );
        parent::__construct( 'POOSHE_PRW', 'پوشه - امتیاز محصول', $widget_ops );
    }

    public function widget( $args, $instance ) {
        global $product;
        if($product == null or !$product){return;}
        echo $args['before_widget'];

        if ( ! wc_review_ratings_enabled() ) {
            return;
        }

        $rating_count = $product->get_rating_count();
        $review_count = $product->get_review_count();
        $average      = $product->get_average_rating();

 ?>

            <div class="woocommerce-product-rating">

                    <?php
                    if ( ! empty( $instance['title'] ) ) {
                        echo '<b class="reviews-title">' . $instance['title'] .'</b>';
                    }
                    ?>

                <?php echo cb_get_rating_html( $average, $rating_count ); // WPCS: XSS ok. ?>
                <div class="desc"><b><?php echo $average;?></b>
                    <?php _e('امتیاز بر اساس ','pooshe'); ?>
                    <b><?php echo $review_count;?></b>
                    <?php _e('نظر','pooshe'); ?></div>
            </div>

        <?php

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">عنوان:</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

        return $instance;
    }
}


add_action( 'widgets_init', function () {
    if(function_exists('WC')) {
        register_widget('pooshe\POOSHE_PRW');
    }
} );

