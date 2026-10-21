<?php
namespace pooshe;
class POOSHE_PATC extends \WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'classname' => 'pooshe-patc',
            'description' => 'افزودن به سبد خرید محصولات قالب پوشه',
        );
        parent::__construct( 'POOSHE_PATC', 'پوشه - افزودن به سبد خرید محصول', $widget_ops );
    }

    public function widget( $args, $instance ) {
        global $product;
        if($product == null or !$product){return;}
        echo $args['before_widget'];
        do_action('pooshe_add_to_cart_widget',$instance);
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'افزودن به سبد خرید';
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
        register_widget('pooshe\POOSHE_PATC');
    }
} );

