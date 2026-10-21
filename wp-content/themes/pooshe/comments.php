<?php
if ( post_password_required() ) {
    return;
}
?>
<div class="post-comments post-wg">
<div id="comments" class="comments-area">

    <?php
    if ( have_comments() ) : ?>
        <div class="comments-header">
            <h2 class="comments-title">
                <i class="fal fa-comment"></i>
                <?php
                $comments_number = get_comments_number();
                if ( '1' === $comments_number ) {
                    printf( _x( 'یک دیدگاه برای &ldquo;%s&rdquo;', 'comments title','negarshop'), get_the_title() );
                } else {
                    printf(
                        _nx(
                            '%1$s دیدگاه برای &ldquo;%2$s&rdquo;',
                            '%1$s دیدگاه برای &ldquo;%2$s&rdquo;',
                            $comments_number,
                            'comments title',
                            'negarshop'
                        ),
                        number_format_i18n( $comments_number ),
                        get_the_title()
                    );
                }
                ?>
            </h2>
            <div class="comment-sort">
                <span class="sort-label"><?php _e('مرتب‌سازی:', 'pooshe'); ?></span>
                <select class="comment-sort-select" name="comment_sort" id="comment-sort">
                    <option value="newest" <?php selected(isset($_GET['comment_sort']) && $_GET['comment_sort'] === 'newest'); ?>><?php _e('جدیدترین', 'pooshe'); ?></option>
                    <option value="oldest" <?php selected(isset($_GET['comment_sort']) && $_GET['comment_sort'] === 'oldest'); ?>><?php _e('قدیمی‌ترین', 'pooshe'); ?></option>
                    <option value="most_liked" <?php selected(isset($_GET['comment_sort']) && $_GET['comment_sort'] === 'most_liked'); ?>><?php _e('پرامتیازترین', 'pooshe'); ?></option>
                </select>
            </div>
        </div>

        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'walker'      => new Pooshe_Walker_Comment(),
                'avatar_size' => 100,
                'style'       => 'ul',
                'short_ping'  => true,
                'reply_text'    =>  __("پاسخ دادن",'negarshop'),
            ) );
            ?>
        </ol>

        <?php the_comments_pagination();

    endif;

    if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>

        <p class="no-comments"><?php _e( 'Comments are closed.' ); ?></p>
        <?php
    endif;

    comment_form(array('class_submit'   =>  'btn btn-primary'));
    ?>

</div>
</div>
