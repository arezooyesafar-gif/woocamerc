<article <?php post_class("post-card"); ?>>
    <div class="entry-header">
        <?php if(has_post_thumbnail()){?>
        <figure class="thumbnail">
            <?php the_post_thumbnail(); ?>
        </figure>
        <?php } ?>
        <div class="post-meta">
            <?php echo sprintf(the_title('<h1 class="title"><a href="%s">','</a></h1>',false),get_the_permalink()); ?>
        </div>
    </div>
    <div class="entry-body">
        <?php the_content(); ?>
    </div>
</article>
