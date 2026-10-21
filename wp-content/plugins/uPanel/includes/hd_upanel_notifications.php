<div id="hd-upanel" class="container">
  <div class="row">
    <div class="col-12 col-md-12">


      <h4 style="margin-bottom:30px;margin-top:20px">  اعلان ها</h4>

      <?php
            add_action('admin_enqueue_scripts', 'my_admin_scripts');        
            wp_enqueue_media();
    global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'hd_upanel_notifications';


 if(isset($_POST['submit'])){
    
            $wpdb->delete( $table_name, array( 'id' => $_POST['delete_id'] ) );
            $record_id = $wpdb->rows_affected;
            
               if($record_id != 0) {
                    $_POST['status'] = 'success';
                    ?>
                                <div class="col-md-12">
                                    <div class="submited">اعلان با موفقیت حذف شد</div>
                                </div>
                                <?php
                   } else {
                    $_POST['status'] = 'error';
                   ?>
                                <div class="col-md-12">
                                    <div class="error-submit">خطا در حذف اعلان</div>
                                </div>
                                <?php
                   }
            
            }





$result = $wpdb->get_results ( "SELECT * FROM $table_name" );
foreach ($result as $key => $value) {
  $result[$key]->created_at = date_i18n('j F Y' , strtotime($result[$key]->created_at));
}
 
function get_users_name($data){
return 'کاربران خاص';
}

?>

    </div>
  </div>

  <form id="orders" method="post">
  <div class="row">

  <dialog id="dialog" class="mdl-dialog">
    <p style="font-size:16px" class="mdl-dialog__title">اعلان حذف شود؟</p>
    <div class="mdl-dialog__content">

    </div>
    <div class="mdl-dialog__actions">
        <form method="post">
<input id="delete_id" name="delete_id" type="hidden">

        <button type="submit" name="submit" class="mdl-button">بله</button>
      <button type="button" id="close"  class="mdl-button close">خیر</button>

        </form>

    </div>
  </dialog>

    <div class="col-12 col-md-12">

    <a class="new" href="<?php echo get_admin_url(); ?>admin.php?page=hd-upanel-new-notification">جدید</a>




    <table class="mdl-data-table mdl-js-data-table  mdl-shadow--2dp">
  <thead>
    <tr>
    <th>ردیف</th>
                    <th> عنوان</th>
                    <th>کاربر</th>
                    <th>تاریخ ثبت</th>
                    <th>عملیات</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($result as $key => $value) { ?>
                    <tr>
                    <td><?php echo $key+1; ?></td>
                    <td><?php echo $value->title; ?></td>
                    <td><?php echo $value->user==''?'همه کاربران':get_users_name($value->user); ?></td>
                    <td><?php echo date_i18n('d F Y', strtotime($value->created_at)) ; ?></td>
                    <td>
                    <a class="new" href="<?php echo get_admin_url(); ?>admin.php?page=hd-upanel-edit-notification&id=<?php echo $value->id; ?>">ویرایش</a>
                    <button data-id="<?php echo $value->id; ?>" class="hd-upanel-notification-delete" type="button">حذف</button>
                    </td>
                </tr>
                    <?php } ?>
  </tbody>
</table>






    </div>


  </div>
  </form>

</div>