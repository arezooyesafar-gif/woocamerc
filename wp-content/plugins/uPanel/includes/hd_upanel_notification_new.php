<div id="hd-upanel" class="container">
  <div class="row">
    <div class="col-12 col-md-12">


      <h4 style="margin-bottom:30px;margin-top:20px">اعلان جدید</h4>

      <?php
            add_action('admin_enqueue_scripts', 'my_admin_scripts');        
            wp_enqueue_media();
    global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'hd_upanel_notifications';

        $users = get_users( array( 'fields' => array( 'ID','user_login' ) ) );
 
foreach($users as $key => $user){
        $meta = get_user_meta ( $user->ID);

        $users[$key]->first_name = $meta['first_name'][0];
        $users[$key]->last_name = $meta['last_name'][0];
    }


             if(isset($_POST['submit'])){
        

              $selected_users = '';
              if($_POST['user_type'] == '1'){
                $selected_users = implode(",",$_POST['users']);
              }

            
           $wpdb->insert( $table_name, array(
                  'title' => $_POST['hd-upanel-notif-title'],
                  'image' => $_POST['calendar_img'],
                  
                  'description' => stripslashes($_POST['description']),
                  'user_type' => $_POST['user_type'],
                  'user' => $selected_users
              ));

 $record_id = $wpdb->insert_id;
 if($record_id != 0) {
    $_POST['status'] = 'success';
    ?>
            <div class="col-md-12">
            <div class="success-submit">اعلان با موفقیت ثبت شد</div>
        </div>
        <?php 

 }else{

    $_POST['status'] = 'error';
    ?>
      <div class="col-md-12">
           <div class="error-submit">خطا در ثبت اعلان</div>
         </div>

       <?php
}

             }


?>

    </div>
  </div>

  <form id="orders" method="post">

  <div class="col-12 col-md-12">
<p  class="hd-upanel-label"> عنوان </p>
 <input style="width:400px" id="title"  name="hd-upanel-notif-title" type="text" value="" class="hd-upanel-input">
</div>
<div class="col-12 col-md-12">
<p  class="hd-upanel-label"> ارسال به </p>
<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="all-users">
  <input type="radio" id="all-users" class="mdl-radio__button" name="user_type" value="0" checked>
  <span class="mdl-radio__label">همه کاربران</span>
</label>
<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="special-users">
  <input type="radio" id="special-users" class="mdl-radio__button" name="user_type" value="1">
  <span class="mdl-radio__label">کاربران خاص</span>
</label>

</div>

<div class="col-12 col-md-12 users"   style="margin-bottom:30px;display:none">
<p  class="hd-upanel-label"> کاربران </p>
<select style="width:50%" class="select2  select2-users"  name="users[]" multiple="multiple" dir="rtl">
  <?php 
  foreach($users as $key => $user){ ?>
<option  value="<?php echo $user->ID; ?>"><?php echo $user->user_login.'('.$user->first_name.' '.$user->last_name.')'; ?></option>

<?php } ?>

		</select>
</div>

  <div class="row">





    <div class="col-12 col-md-12" style="margin-bottom:40px">
    <input class="button upload_image_button" type="button" value=" تصویر اعلان" />
                                    <input style="display:none" id="calendar_img"  value="" type="text" name="calendar_img">
                          
                                    <img  style="display:none;float:right" id="uploaded-img" src="#">
      </div>




    <div class="col-8 col-md-8">
    <?php 
    $args = array(
      'tinymce'       => array(
          'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
          'toolbar2'      => '',
          'toolbar3'      => '',
      ),
  );
    wp_editor( '', 'description' ); ?>
    </div>
    <div class="col-12 col-md-12">
    <input style="margin: 20px 0;padding: 7px 17px;font-size: 16px;" type="submit" name="submit" id="submit" class="button button-primary" value="ثبت اعلان">
    </div>


  </div>
  </form>

</div>