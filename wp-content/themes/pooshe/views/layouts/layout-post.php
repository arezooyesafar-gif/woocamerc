<?php
    $style = pooshe_opt(BLOG_OPTS,'style','grid');
?>
<article <?php post_class('post-card style-'.$style) ?>>
    <div class="entry-header">
        <?php if(has_post_thumbnail()){?>
            <figure class="thumbnail">
                <?php

                if($style == "list") {
                    the_post_thumbnail();
                }else{
                    the_post_thumbnail('medium');
                }

                ?>
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
        <?php the_excerpt();
        ?>
    </div>

</article>