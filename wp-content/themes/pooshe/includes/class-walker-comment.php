<?php

class Pooshe_Walker_Comment extends Walker_Comment {

    protected function html5_comment($comment, $depth, $args) {
        $tag = ('div' === $args['style']) ? 'div' : 'li';
        $comment_badge = pooshe_get_comment_badge($comment);
        $comment_author_badge = pooshe_get_comment_author_badge($comment);
        $likes = (int) get_comment_meta($comment->comment_ID, 'cb_like', true);
        $dislikes = (int) get_comment_meta($comment->comment_ID, 'cb_dislike', true);
        $user_vote = pooshe_get_user_comment_vote($comment->comment_ID);
        ?>
<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
    <div class="comment-body">
        <div class="comment-meta">
            <div class="comment-author vcard">
                <?php if (0 != $args['avatar_size']) {
                    echo get_avatar($comment, $args['avatar_size']);
                } ?>
                <cite class="fn"><?php echo get_comment_author_link($comment); ?></cite>
                <span class="says">:</span>
                <?php echo $comment_author_badge; ?>
            </div>
            <div class="comment-metadata">
                <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                    <time datetime="<?php comment_time('c'); ?>">
                        <?php printf(__('%s قبل', 'pooshe'), human_time_diff(get_comment_time('U'), current_time('timestamp'))); ?>
                    </time>
                </a>
                <?php edit_comment_link(__('Edit'), '<span class="edit-link">', '</span>'); ?>
            </div>
        </div>
        <?php if ($comment->comment_type === 'review' && function_exists('wc_get_rating_html')) :
            $rating = (int) get_comment_meta($comment->comment_ID, 'rating', true);
            if ($rating) : ?>
                <div class="woocommerce-review-rating"><?php echo wc_get_rating_html($rating); ?></div>
            <?php endif;
        endif; ?>
        <?php if ($comment_badge) : ?>
            <div class="comment-badge-wrapper"><?php echo $comment_badge; ?></div>
        <?php endif; ?>
        <div class="comment-content">
            <?php comment_text(); ?>
        </div>
        <div class="comment-likes">
            <button type="button"
                    class="btn-comment-like<?php echo $user_vote === 'like' ? ' voted' : ''; ?>"
                    data-comment-id="<?php echo $comment->comment_ID; ?>"
                    data-action="like"
                    data-nonce="<?php echo wp_create_nonce('pooshe_comment_vote_' . $comment->comment_ID); ?>">
                <i class="far fa-thumbs-up"></i>
                <span class="like-count"><?php echo $likes; ?></span>
            </button>
            <button type="button"
                    class="btn-comment-dislike<?php echo $user_vote === 'dislike' ? ' voted' : ''; ?>"
                    data-comment-id="<?php echo $comment->comment_ID; ?>"
                    data-action="dislike"
                    data-nonce="<?php echo wp_create_nonce('pooshe_comment_vote_' . $comment->comment_ID); ?>">
                <i class="far fa-thumbs-down"></i>
                <span class="dislike-count"><?php echo $dislikes; ?></span>
            </button>
        </div>
        <?php
        $reply_text = $args['reply_text']
            ? sprintf($args['reply_text'], '')
            : __('پاسخ', 'pooshe');
        comment_reply_link(array_merge($args, array(
            'add_below' => 'div-comment',
            'depth'     => $depth,
            'max_depth' => $args['max_depth'],
            'before'    => '<div class="reply">',
            'after'     => '</div>',
            'reply_text'=> $reply_text,
        )), $comment);
        ?>
    </div>
<?php
    }
}
