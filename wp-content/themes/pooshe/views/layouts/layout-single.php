<article <?php post_class("post-card"); ?>>
    <div class="entry-header">
        <?php if(has_post_thumbnail()){?>
        <figure class="thumbnail">
            <?php the_post_thumbnail(); ?>
        </figure>
        <?php } ?>
        <div class="post-meta">
            <?php echo sprintf(the_title('<h1 class="title"><a href="%s">','</a></h1>',false),get_the_permalink()); ?>
            <ul class="meta-items">
                <li><i class="far fa-archive"></i><span class="item-title">دسته بندی:</span>
                    <?php the_category(); ?>
                </li>
                <li><i class="far fa-calendar"></i><span class="item-title">تاریخ انتشار:</span>
                    <span class="item-value"><?php echo get_the_date(); ?></span>
                </li>
            </ul>
        </div>
    </div>
    <div class="entry-body">
        <?php the_content();
        ?>

        <?php
        edit_post_link(
            sprintf(
                'ویرایش <span class="screen-reader-text"> "%s"</span>',
                get_the_title()
            ),
            '<span class="edit-link">',
            '</span>'
        );
        ?>
        <?php
        $related_posts = pooshe_related_posts(get_the_ID(), 3);
        if($related_posts) {
            ?>
            <div class="related-posts">
                <h5 class="title">مطالب مرتبط</h5>
                <ul>
                    <?php foreach ($related_posts as $related_post){ ?>
                        <li><a href="<?php echo $related_post['permalink']; ?>"><?php echo $related_post['title']; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <?php
        }
        $posttags = get_the_tags();
        if ($posttags) {
        ?>
        <div class="tags">
            <i class="far fa-tags"></i>
            <span class="title">برچسب ها:</span>
            <?php the_tags( '<ul><li>', '</li><li>', '</li></ul>' ); ?>
        </div>
        <?php } ?>
    </div>

</article>
<?php
the_post_navigation(
    array(
        'next_text' => '<span class="meta-nav" aria-hidden="true">نوشته بعدی</span> <br/>' .
            '<span class="post-title">%title</span>',
        'prev_text' => '<span class="meta-nav" aria-hidden="true">نوشته قبلی</span> <br/>' .
            '<span class="post-title">%title</span>',
    )
);

if ( comments_open() || get_comments_number() ) {
    comments_template();
}
