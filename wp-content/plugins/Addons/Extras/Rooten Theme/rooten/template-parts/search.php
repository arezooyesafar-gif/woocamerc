<?php

$rooten_style             = get_theme_mod( 'rooten_search_style', 'default');
$rooten_search            = [];
$rooten_toggle            = ['class' => 'bdt-search-icon bdt-padding-remove-horizontal'];
$rooten_layout_c          = get_theme_mod('rooten_header_layout', 'horizontal-left');
$rooten_layout_m          = get_post_meta( get_the_ID(), 'rooten_header_layout', true );
$rooten_layout            = (!empty($rooten_layout_m) and $rooten_layout_m != 'default') ? $rooten_layout_m : $rooten_layout_c;
$rooten_position          = get_theme_mod( 'rooten_search_position', 'header');
$rooten_id                = esc_attr( uniqid( 'search-form-' ) );
$rooten_attrs['class']    = array_merge(['bdt-search'], isset($rooten_attrs['class']) ? (array) $rooten_attrs['class'] : []);
$rooten_search            = [];
$rooten_search['class']   = [];
$rooten_search['class'][] = 'bdt-search-input';

if (($rooten_layout == 'side-left' or $rooten_layout == 'side-right') and $rooten_position == 'menu') {
    $rooten_style = 'default';
}
// TODO
$rooten_navbar = [
    'dropdown_align'    => get_theme_mod( 'rooten_dropdown_align', 'left' ),
    'dropdown_click'    => get_theme_mod( 'rooten_dropdown_click' ),
    'dropdown_boundary' => get_theme_mod( 'rooten_dropdown_boundary' ),
    'dropbar'           => get_theme_mod( 'rooten_dropbar' ),
];

if ($rooten_style) {
    $rooten_search['autofocus'] = true;
}

if ($rooten_style == 'modal') {
    $rooten_search['class'][] = 'bdt-text-center';
    $rooten_attrs['class'][] = 'bdt-search-large';
} else {
    $rooten_attrs['class'][] = 'bdt-search-default';
}

if (in_array($rooten_style, ['dropdown', 'justify'])) {
    $rooten_attrs['class'][] = 'bdt-search-navbar';
    $rooten_attrs['class'][] = 'bdt-width-1-1';
}

?>

<?php if ($rooten_style == 'default') : // TODO renders the default style only ?>

    <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" <?php rooten_helper::attrs($rooten_attrs) ?>>
        <span bdt-search-icon></span>
        <input id="<?php echo esc_attr($rooten_id); ?>" name="s" placeholder="<?php esc_html_e('Search...', 'rooten'); ?>" type="search" class="bdt-search-input">
    </form>

<?php elseif ($rooten_style == 'drop') : ?>

    <a<?php rooten_helper::attrs($rooten_toggle) ?> href="#" bdt-search-icon></a>
    <div bdt-drop="mode: click; pos: left-center; offset: 0">
        <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" <?php rooten_helper::attrs($rooten_attrs) ?>>
            <span bdt-search-icon></span>
            <input id="<?php echo esc_attr($rooten_id); ?>" name="s" placeholder="<?php esc_html_e('Search...', 'rooten'); ?>" type="search" class="bdt-search-input">
        </form>
    </div>

<?php elseif (in_array($rooten_style, ['dropdown', 'justify'])) :

    $rooten_drop = [
        'mode'           => 'click',
        'cls-drop'       => 'bdt-navbar-dropdown',
        'boundary'       => $rooten_navbar['dropdown_align'] ? '!nav' : false,
        'boundary-align' => $rooten_navbar['dropdown_boundary'],
        'pos'            => $rooten_style == 'justify' ? 'bottom-justify' : 'bottom-right',
        'flip'           => 'x',
        'offset'         => !$rooten_navbar['dropbar'] ? 28 : 0
    ];

    ?>

    <a<?php rooten_helper::attrs($rooten_toggle) ?> href="#" bdt-search-icon></a>
    <div class="bdt-navbar-dropdown bdt-width-medium" <?php rooten_helper::attrs(['bdt-drop' => json_encode(array_filter($rooten_drop))]) ?>>
        <div class="bdt-grid bdt-grid-small bdt-flex-middle">
            <div class="bdt-width-expand">
               <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" <?php rooten_helper::attrs($rooten_attrs) ?>>
                   <span bdt-search-icon></span>
                   <input id="<?php echo esc_attr($rooten_id); ?>" name="s" placeholder="<?php esc_html_e('Search...', 'rooten'); ?>" type="search" class="bdt-search-input">
               </form>
            </div>
            <div class="bdt-width-auto">
                <a class="bdt-navbar-dropdown-close" href="#" bdt-close></a>
            </div>
        </div>

    </div>

<?php elseif ($rooten_style == 'modal') : ?>

    <a<?php rooten_helper::attrs($rooten_toggle) ?> href="#<?php echo esc_attr($rooten_id).'-modal' ?>" bdt-search-icon bdt-toggle></a>

    <div id="<?php echo esc_attr($rooten_id).'-modal' ?>" class="bdt-modal-full" bdt-modal>
        <div class="bdt-modal-dialog bdt-modal-body bdt-flex bdt-flex-center bdt-flex-middle" bdt-height-viewport>
            <button class="bdt-modal-close-full" type="button" bdt-close></button>
            <div class="bdt-search bdt-search-large">
               <form id="search-230" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" <?php rooten_helper::attrs($rooten_attrs) ?>>
                    <input id="<?php echo esc_attr($rooten_id); ?>" name="s" placeholder="<?php esc_html_e('Type Word and Hit Enter', 'rooten'); ?>" type="search" class="bdt-search-input bdt-text-center" autofocus="">
               </form>
            </div>
        </div>
    </div>

<?php endif ?>
