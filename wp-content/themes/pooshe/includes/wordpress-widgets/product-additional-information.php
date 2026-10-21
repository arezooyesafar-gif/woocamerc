<?php
namespace pooshe;
class POOSHE_PAI extends \WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'classname' => 'pooshe-pai',
            'description' => 'توضیحات تکمیلی محصولات قالب پوشه',
        );
        parent::__construct( 'POOSHE_PAI', 'پوشه - توضیحات تکمیلی محصول', $widget_ops );
    }

    public function widget( $args, $instance ) {
        global $product;
        if($product == null or !$product){return;}
        if(empty($product->get_attributes())){return;}
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        wc_get_template( 'single-product/tabs/additional-information.php' );
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
        register_widget('pooshe\POOSHE_PAI');
    }
} );

