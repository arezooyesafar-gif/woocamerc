<?php

function version_badge()
{
    global $product;
    $new_version = get_post_meta($product->get_id(), 'pooshe_new_version', false);
    if (!empty($new_version) && $new_version[0] === 'on') { ?>
        <div class="new-version"><span>نسخه جدید!</span>
        </div>
        <?php
    }
}