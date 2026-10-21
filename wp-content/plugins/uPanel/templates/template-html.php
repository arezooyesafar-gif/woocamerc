
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <base href="/" target="_blank" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <style>
html,body{
    padding:0;
    margin:0 !important;
}
*, ::after, ::before {
    box-sizing: border-box;
}
#wpadminbar{
    display:none !important
}
.loading {
    width: 100%;
    height: 100%;
    position: absolute;
    z-index: 1;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

   </style>
  <?php wp_head(); ?>
</head>
<body dir="rtl">

<?php

if ($_SERVER['HTTP_SEC_FETCH_DEST'] != 'iframe') { 
   get_header();
}
$slug = $_GET['q'];
echo do_shortcode(stripslashes(get_option($slug)));

if ($_SERVER['HTTP_SEC_FETCH_DEST'] != 'iframe') { 
    get_footer();
}
wp_footer();

?>

</body>
</html>

