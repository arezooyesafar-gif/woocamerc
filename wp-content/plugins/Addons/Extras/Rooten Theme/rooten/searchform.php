<?php $rooten_unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>

<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" class="bdt-search bdt-search-default bdt-width-1-1">
    <span bdt-search-icon></span>
    <input id="<?php echo esc_attr($rooten_unique_id); ?>" name="s" placeholder="<?php esc_html_e('Search...', 'rooten'); ?>" type="search" class="bdt-search-input">
</form>