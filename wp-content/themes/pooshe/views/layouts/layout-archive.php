<?php

$archive_title    = '';
$archive_subtitle = '';

if ( is_search() ) {
    global $wp_query;

    $archive_title = sprintf(
        '%1$s %2$s',
        '<span class="color-accent">جستجو</span>',
        '&ldquo;' . get_search_query() . '&rdquo;'
    );

    if ( $wp_query->found_posts ) {
        $archive_subtitle = $wp_query->found_posts . 'مطلب پیدا شد! ';
    } else {
        $archive_subtitle = 'نتیجه ای یافت نشد!';
    }
} elseif ( ! is_home() ) {
    $archive_title    = get_the_archive_title();
    $archive_subtitle = get_the_archive_description();
}

if ( $archive_title || $archive_subtitle ) {
    ?>

    <header class="archive-header">

        <div class="archive-header-inner">

            <?php if ( $archive_title ) { ?>
                <h1 class="archive-title"><?php echo wp_kses_post( $archive_title ); ?></h1>
            <?php } ?>

            <?php if ( $archive_subtitle ) { ?>
                <div class="archive-subtitle"><?php echo wp_kses_post( wpautop( $archive_subtitle ) ); ?></div>
            <?php } ?>

        </div><!-- .archive-header-inner -->

    </header><!-- .archive-header -->

    <?php
}

if ( have_posts() ) {

    while ( have_posts() ) {
        the_post();

        get_template_part( 'views/layouts/layout','post' );

    }
} elseif ( is_search() ) {
    ?>

    <div class="no-search-results-form">

        <?php
        get_search_form(
            array(
                'label' => 'جستجوی دوباره',
            )
        );
        ?>

    </div><!-- .no-search-results -->

    <?php
}
?>

<?php get_template_part( 'views/layouts/layout','pagination' ); ?>
