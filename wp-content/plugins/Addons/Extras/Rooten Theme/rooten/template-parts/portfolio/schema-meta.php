<?php
$rooten_categories = get_the_category();
$rooten_category   = '';

if (!empty($rooten_categories)) {
    $rooten_category = $rooten_categories[0]->name;
} ?>

<meta property="name" content="<?php echo esc_html(get_the_title()) ?>">
<meta property="author" typeof="Person" content="<?php echo esc_html(get_the_author()) ?>">
<meta property="dateModified" content="<?php echo get_the_modified_date('c') ?>">
<meta property="datePublished" content="<?php echo get_the_date('c') ?>">
<meta property="articleSection" content="<?php echo esc_html($rooten_category) ?>">