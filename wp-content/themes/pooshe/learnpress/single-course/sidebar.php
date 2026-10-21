<?php
/**
 * Template for displaying course sidebar.
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hide sidebar if there is no content
 */
if ( ! is_active_sidebar( 'course-sidebar' ) && ! LearnPress::instance()->template( 'course' )->has_sidebar() ) {
	return;
}
$course = learn_press_get_course();
$level = learn_press_get_post_level( get_the_ID() );

if ( ! $level ) {
    return;
}
$lessons    = $course->count_items( LP_LESSON_CPT );
$quizzes    = $course->count_items( LP_QUIZ_CPT );
$instructor = $course->get_instructor();
?>

<aside class="course-summary-sidebar">
	<div class="course-summary-sidebar__inner">
		<div class="course-sidebar-top widget-area">
			<?php
			/**
			 * LP Hook
			 *
			 * @since 4.0.0
			 */
			do_action( 'learn-press/before-course-summary-sidebar' );

			/**
			 * LP Hook
			 *
			 * @since 4.0.0
			 *
			 * @see   LP_Template_Course::course_sidebar_preview() - 10
			 * @see   LP_Template_Course::course_featured_review() - 20
			 */
			do_action( 'learn-press/course-summary-sidebar' );

			/**
			 * LP Hook
			 *
			 * @since 4.0.0
			 */
			do_action( 'learn-press/after-course-summary-sidebar' );

			?>

		</div>
        <div class="sidebar-widget pooshe-pai">
            <table class="woocommerce-product-attributes shop_attributes">
                <tbody>
                        <tr>
                            <th class="woocommerce-product-attributes-item__label">دسته بندی </th>
                            <td class="woocommerce-product-attributes-item__value">
                                <?php
                                if ( ! get_the_terms( get_the_ID(), 'course_category' ) ) {
                                    esc_html_e( 'Uncategorized', 'learnpress' );
                                } else {
                                    echo get_the_term_list( get_the_ID(), 'course_category', '', '<span>|</span>' );
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="woocommerce-product-attributes-item__label">مدت زمان دسترسی</th>
                            <td class="woocommerce-product-attributes-item__value"><?php echo learn_press_get_post_translated_duration( get_the_ID(), esc_html__( 'نامحدود' ) ); ?></td>
                        </tr>
                        <tr>
                            <th class="woocommerce-product-attributes-item__label">سطح دوره</th>
                            <td class="woocommerce-product-attributes-item__value"><?php echo esc_html( $level ); ?></td>
                        </tr>
                        <tr>
                            <th class="woocommerce-product-attributes-item__label">تعداد درس</th>
                            <td class="woocommerce-product-attributes-item__value"><?php echo $lessons ?></td>
                        </tr>
                        <tr>
                            <th class="woocommerce-product-attributes-item__label">تعداد آزمون</th>
                            <td class="woocommerce-product-attributes-item__value"><?php echo $quizzes ?></td>
                        </tr>
                        <tr>
                            <th class="woocommerce-product-attributes-item__label">تعداد دانش آموزان</th>
                            <td class="woocommerce-product-attributes-item__value"><?php echo $students = $course->count_students(); ?></td>
                        </tr>
                </tbody>
            </table>
        </div>
        <div class="sidebar-widget pooshe-instructor">
            <div class="avatar">
                <?php echo wp_kses_post( $instructor->get_profile_picture() ); ?>
            </div>
            <div class="author-title"><?php echo wp_kses_post( $course->get_instructor_html() ); ?></div>
            <div class="author-description ">

                <?php
                /**
                 * LP Hook
                 *
                 * @since 4.0.0
                 */
                do_action( 'learn-press/begin-course-instructor-description', $instructor );

                echo wp_kses_post( $instructor->get_description() );

                /**
                 * LP Hook
                 *
                 * @since 4.0.0
                 */
                do_action( 'learn-press/end-course-instructor-description', $instructor );

                ?>
            </div>
        </div>

		<?php if ( is_active_sidebar( 'course-sidebar' ) ) : ?>
			<div class="course-sidebar-secondary">
				<?php dynamic_sidebar( 'course-sidebar' ); ?>
			</div>
		<?php endif; ?>
	</div>

</aside>
