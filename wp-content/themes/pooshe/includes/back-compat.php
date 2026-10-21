<?php
function pooshe_php_error() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>قالب نیازمند php نسخه 7.4 می باشد.</p>
    </div>
    <?php
}
function pooshe_switch_theme() {
    switch_theme( WP_DEFAULT_THEME );
    unset( $_GET['activated'] );
    add_action( 'admin_notices', 'pooshe_php_error' );
}
add_action( 'after_switch_theme', 'pooshe_switch_theme' );
