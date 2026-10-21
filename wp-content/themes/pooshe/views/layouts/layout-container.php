<?php
get_header();
?>
    <main class="pooshe-main">
        <div class="container">
            <?php
            if(function_exists('woocommerce_breadcrumb')) {
                echo '<div class="page-breadcrumb">';
                woocommerce_breadcrumb();
                echo '</div>';
            }
            ?>
            <div class="row">
                <?php
                $columns = 'full';
                if(is_front_page() or is_singular('post')) {
                    $columns = pooshe_opt(BLOG_OPTS, 'columns', 'full');
                }
                if(is_page()){
                    $page_col = get_post_meta(get_the_ID(),'pooshe_page_column',true);
                    if($page_col == "default"){
                        $columns = pooshe_opt(PAGE_OPTS,'columns','full');
                    }else{
                        $columns = get_post_meta(get_the_ID(),'pooshe_page_column',true);
                    }
                }
                if(cb_is_index()){
                    $columns = pooshe_opt(ARCHIVE_OPTS,'columns','full');
                }

                if(!is_user_logged_in() and (is_page() and get_the_ID() == get_option('woocommerce_myaccount_page_id'))){
                    $columns = 'full';
                }
                if(function_exists('WC') and (is_cart() or is_checkout())){
                    $columns = 'full';
                }
                ?>
                <div class="<?php echo ($columns !== "full")? 'col-xxl-9 col-xl-8' : 'col-lg'; ?> <?php echo ($columns === "right")?' order-xl-2':''; ?>">
                    <?php
                    if ( is_singular() ) {
                        get_template_part('/views/layouts/layout','singular');
                    }else if(cb_is_index()){
                        get_template_part('/views/layouts/layout','archive');
                    }

                    ?>
                </div>
	            <?php if($columns !== "full"){?>
                    <div class="col-xxl-3 col-xl-4<?php echo ($columns === "right")?' order-xl-1':''; ?>">
			            <?php
			            if(is_front_page() or is_singular('post')){
				            get_sidebar('blog');
			            }else{
				            get_sidebar('page');
			            }
			            ?>
                    </div>
	            <?php } ?>
            </div>
        </div>
    </main>
<?php
get_footer();
