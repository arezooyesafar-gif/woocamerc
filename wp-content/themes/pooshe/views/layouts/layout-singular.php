<?php
if ( have_posts() ) {

    while ( have_posts() ) {
        the_post();
        if(is_page()){
            get_template_part('/views/layouts/layout','page');
        }else{
            get_template_part('/views/layouts/layout','single');
        }
    }
}
?>