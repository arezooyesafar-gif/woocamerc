<?php 
if (!function_exists('add_action'))
{
    echo "an error occured! You may not be able to access this plugin via direct URL...";
    exit();
}
else if (!defined('ABSPATH'))
{
    echo "an error occured! You may not be able to access this plugin via direct URL...";
    exit();
}

$admin_color_themes = array
(

    'modern' => '3858e9',
    'fresh' => '2271b1',
    'light' => '04a4cc',
    'blue' => 'e1a948',
    'coffee' => 'c7a589',
    'ectoplasm' => 'a3b745',
    'midnight' => 'e14d43',
    'ocean' => '9ebaa0',
    'sunrise' => 'dd823b',

    //admin color schemes themes
    'modern-evergreen' => '0F4232',
    'lawn' => 'A7B145',
    'seashore' => '456A7F',
    'primary' => 'D8B230',
    'vineyard' => 'CC9E14',
    'kirk' => 'BD3854',
    'flat' => '1ABC9C',
    'cruise' => '79B591',
    'contrast-blue' => '264D79',
    'aubergine' => 'C99416',
    'adderley' => '1730E5',
    '80s-kid' => 'D13674',

);

/**/
?>
<style>
body #wpcontent {
    background-color: #DFDBE5;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cg fill-rule='evenodd'%3E%3Cg id='hexagons' fill='%23<?php echo $admin_color_themes[$user_color];  ?>' fill-opacity='0.25' fill-rule='nonzero'%3E%3Cpath d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
body #wpcontent.nightmodeon {
    background-color: black;
}
.woosbymdz-con {
    background-color: #ffffffab!important;
}
.woosbymdz-con fieldset:not([closed= "yes"]) {
    background: #7953ab0d !important;
    backdrop-filter: saturate(180%) blur(5px);
    -webkit-backdrop-filter: saturate(180%) blur(5px);
    border: none !important;
}
.woosbymdz-con.nightmodeon {
    background-color: #070316ba!important;
}
.nightmodeon fieldset:not([closed= "yes"]) {
    background: #0e0624bf  !important;
}
.woosbymdz-con fieldset[closed="yes"] {
    border-style: solid !important;
    border-width: 2px !important;
}
.nightmodeon legend, .nightmodeon fieldset[closed="yes"] .acordion-btn {
    filter: contrast(0.7);
}
.nightmodeon p.helptip {
    color: #8c9effb5 !important;
}
#settings-mother #settings-side .settings-side-inner .block-setting-connector:hover {
    transition: .2s ease-out;
}
#settings-mother #settings-side .settings-side-inner .block-setting-connector:hover {
    transform: translateX(4px);
}
.woosbymdz-con[is-rtl="1"] #settings-mother #settings-side .settings-side-inner .block-setting-connector:hover {
    transform: translateX(-4px);
}
#settings-mother #settings-side .settings-side-inner .block-setting-connector:not(.saver-side-btn) {
    border-radius: 999px;
    background-color: #f5f5f529;
    backdrop-filter: saturate(180%) blur(1.5px);
    -webkit-backdrop-filter: saturate(180%) blur(1.5px);
}
.block-setting-connector.btn-sec-active {
    color: black !important;
    box-shadow: 0px 10px 2px -4px #10182c26;
}
.nightmodeon #settings-mother #settings-side .settings-side-inner .block-setting-connector:not(.saver-side-btn){
    color: #e3e6ffe6 !important;
    border-color: #f5f5f5bf;
    background-color: #f5f5f529 !important;
}
.nightmodeon #settings-mother #settings-side .settings-side-inner .block-setting-connector.btn-sec-active:not(.saver-side-btn) {
    border-color: black !important;
    box-shadow: 0px 10px 2px -4px #ffffff17;
    background-color: #1314167d !important;
}
</style>