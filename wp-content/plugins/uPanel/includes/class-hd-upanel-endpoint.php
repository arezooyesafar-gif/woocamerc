<?php
Class VPTutorial {

function __construct() {

    register_activation_hook( __FILE__, array( $this, 'activate' ) );

    add_action( 'init', array( $this, 'rewrite' ) );
    add_action( 'template_include', array( $this, 'change_template' ) );

}

function activate() {
    set_transient( 'vpt_flush', 1, 60 );
}

function rewrite() {
    add_rewrite_endpoint( 'dump', EP_PERMALINK );

    if(get_transient( 'vpt_flush' )) {
        delete_transient( 'vpt_flush' );
        flush_rewrite_rules();
    }
}

function change_template( $template ) {

    if( get_query_var( 'dump', false ) !== false ) {

        //Check theme directory first
        $newTemplate = locate_template( array( 'template-dump.php' ) );
        if( '' != $newTemplate )
            return $newTemplate;

        //Check plugin directory next
        $newTemplate = plugin_dir_path( __FILE__ ) . 'templates/template-dump.php';
        if( file_exists( $newTemplate ) )
            return $newTemplate;
    }

    //Fall back to original template
    return $template;

}

}

new VPTutorial;