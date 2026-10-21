<?php

global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'hd_upanel_tickets';



if (isset($_POST['checked'])) {
    $id = $_POST["id"];



    if (
        $wpdb->update(
            $table_name,
            array(
                'checked' => 1,
            ),
            array(
                "id" => $id
            )
        ) === FALSE
    ) {



    } else {

    }

}




$per_page = 10;
$paged = 1;
if (isset($_GET['paged'])) {
    $paged = (int) $_GET['paged'];
}
$offset = ($paged - 1) * $per_page;

$search = '';
if (isset($_GET['s'])) {
    $search = $_GET['s'];

}
$variables = $wpdb->get_results("SELECT * FROM $table_name WHERE parent=0 AND id LIKE '%$search%' ORDER BY checked ASC, id DESC LIMIT $per_page OFFSET $offset");


$sql = "SELECT COUNT(*) as count FROM $table_name WHERE parent=0 ";


$ipquery = $wpdb->get_results($sql);

$count = $ipquery[0]->count;

$total_pages = round($count / $per_page) + 1;


?>


<div id="hd-upanel" class="wrap">




<div class="bootstrap-wrapper">
    <div class="container-fluid">

    

    <div class="row">
    <div class="col-md-12">
    <h1 class="wp-heading-inline">تیکت های پشتیبانی  </h1>
    <br><br>
    </div>
  

</div>

        <div class="row">
        
            <div class="col-md-10">

            <table class="wp-list-table widefat fixed striped ">
            <thead>
                <tr>
                    
            
                    <th scope="col"  class="manage-column ">ردیف</th>
                    <th scope="col"  class="manage-column "> شماره تیکت </th>
                    <th scope="col"  class="manage-column "> عنوان</th>
                    <th scope="col"  class="manage-column "> اولویت</th>
                    <th scope="col"  class="manage-column ">دپارتمان</th>
                    <th scope="col"  class="manage-column ">وضعیت</th>
                    <th scope="col"  class="manage-column ">تاریخ ثبت</th>
                    <th scope="col"  class="manage-column "> کاربر</th>
            <th> عملیات </th>
                   
                </tr>
            </thead>

            <tbody id="the-list">
<?php
foreach ($variables as $key => $value) { ?>
   
                                <tr>
                
                        <td class="slug">
                        <?php echo $value->id; ?>
                        </td>
                        <td class="slug">

                        <?php if ($value->checked == 0) { ?>
                                <span class="new">
                                    جدید
                                </span>
        <?php } else { ?>
  
        <?php } ?>

                        <?php echo ((int) $value->id + 100) . (strval($value->ticket_id)); ?>
                        </td>
                        <td class="slug">
                        <?php echo $value->title; ?>
                        </td>
                        <td class="slug">
                        <?php echo ($value->ticket_priority == 0 ? 'عادی' : '') . ($value->ticket_priority == 1 ? 'متوسط' : '') . ($value->ticket_priority == 2 ? 'فوری' : ''); ?>
                        </td>

                        <td class="slug">
                        <?php echo $value->department; ?>
                        </td>
                        <td class="slug">
                        <?php if ($value->ticket_status == 0) { ?> در  انتظار پاسخ <?php } ?>
                        <?php if ($value->ticket_status == 1) { ?> پاسخ داده شده <?php } ?>
                        <?php if ($value->ticket_status == 2) { ?> در حال بررسی <?php } ?>
                        <?php if ($value->ticket_status == 3) { ?> بسته شده <?php } ?>

      
                        </td>
                        <td class="slug">
                        <?php echo date_i18n('j F Y h:i', strtotime($value->created_at)); ?>
                        </td>
                    
                        <td class="value">
                        <?php

                        $current_user = wp_get_current_user($value->user_id);
                        $user_meta = get_user_meta($current_user->data->ID);
                        echo $user_meta['first_name'][0] . ' ';
                        echo $user_meta['last_name'][0];
                        echo '<br>'.$current_user->display_name;
                        // print_r($current_user);

                        ?>
                        </td>
                        <td>
    <form method="post">

    <input type="hidden" name="id" value="<?php echo $value->id; ?>">
    <?php if ($value->checked == 0) { ?>
            <!-- <input type="submit" name="checked" value="بررسی شد"> -->
        <?php } else { ?>
  
        <?php } ?>
    </form>

    <a class="edit"
                                    href="<?php echo get_admin_url(); ?>admin.php?page=hd-upanel-tickets-edit&id=<?php echo $value->id; ?>">ویرایش</a>
                        </td>
                   
                
                    </tr>

     <?php } ?>        
            
            </tbody>

        </table>
<br>
        <div class="tablenav-pages"><span class="displaying-num"><?php echo count($variables); ?> از <?php echo $count; ?> </span>

        <?php if (($paged) != 1) { ?>
    <a class="last-page button" href="<?php echo get_admin_url(); ?>admin.php?page=hd-upanel-tickets&paged=1<?php echo ((isset($_GET['s'])) ? '&s=' . $_GET['s'] : ""); ?>"><span class="screen-reader-text">برگهٔ آخر</span><span aria-hidden="true">«</span></a></span>
<?php } else { ?>
        <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
<?php } ?>



<?php if (($paged) != 1) { ?>
    <a class="next-page button" href="<?php echo get_admin_url(); ?>admin.php?page=hd-upanel-tickets&paged=<?php echo ($paged - 1) . ((isset($_GET['s'])) ? '&s=' . $_GET['s'] : ""); ?>"><span class="screen-reader-text">برگهٔ قبلی</span><span aria-hidden="true">‹</span></a>
<?php } else { ?>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
<?php } ?>





<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">برگهٔ فعلی</label>
<form action="<?php echo get_admin_url(); ?>admin.php?page=hd-upanel-tickets" class="d-inline-block">
<input type="hidden" name="page" value="hd-upanel-tickets">
<input type="hidden" name="s" value="<?php echo ((isset($_GET['s'])) ? $_GET['s'] : ""); ?>">
<input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $paged; ?>" size="1" aria-describedby="table-paging">
</form>
<span class="tablenav-paging-text"> از <span class="total-pages"><?php echo $total_pages; ?></span></span></span>


<?php if (($paged) != $total_pages) { ?>
    <a class="next-page button" href="<?php echo get_admin_url(); ?>admin.php?page=hd-upanel-tickets&paged=<?php echo ($paged + 1) . ((isset($_GET['s'])) ? '&s=' . $_GET['s'] : ""); ?>"><span class="screen-reader-text">برگهٔ بعدی</span><span aria-hidden="true">›</span></a>
<?php } else { ?>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
<?php } ?>

<?php if (($paged) != $total_pages) { ?>
    <a class="last-page button" href="<?php echo get_admin_url(); ?>admin.php?page=hd-upanel-tickets&paged=<?php echo round($count / $per_page) . ((isset($_GET['s'])) ? '&s=' . $_GET['s'] : ""); ?>"><span class="screen-reader-text">برگهٔ آخر</span><span aria-hidden="true">»</span></a></span>
<?php } else { ?>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
<?php } ?>
</div>


<form action="<?php echo get_admin_url(); ?>admin.php?page=hd-upanel-tickets" >
<p class="search-box">
    <label class="screen-reader-text" for="post-search-input">جست&zwnj;وجوی نوشته&zwnj;ها:</label>
    <input type="hidden" name="page" value="hd-upanel-tickets">

    <input type="search" id="post-search-input" name="s" value="<?php if (isset($_GET['s'])) {
        echo $_GET['s'];
    } ?>">
        <input type="submit" id="search-submit" class="button" value="جستجو   "></p>
</form>



            </div>
        
        
      




        </div>
    </div>
</div>





</div>