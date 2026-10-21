
<?php


global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$id = $_GET['id'];
$table_tickets = $wpdb->prefix . 'hd_upanel_tickets';

$wpdb->update( 
    $table_tickets, 
    array( 
        'checked'     => 1,
    ),
    array(
        "id" => $id
    )
    );

// $tickets = $wpdb->get_results("SELECT * FROM $table_tickets WHERE id=$id  ORDER BY id DESC" );
/* foreach ($tickets as $key => $value) {

    $tickets[$key]->created_at = date_i18n('j F Y',$tickets[$key]->created_at);
    $parent = $value->id;
    $tickets[$key]->childs  = $wpdb->get_results("SELECT * FROM $table_tickets WHERE parent=$parent ORDER BY id ASC" );
    foreach ($tickets[$key]->childs as $key01 => $value01) {

        $tickets[$key]->childs[$key01]->created_at = date_i18n('j F Y',$tickets[$key]->childs[$key01]->created_at);
    }
}
*/
if(isset($_POST['massage'])){
$wpdb->insert( 
    $table_tickets, 
    array( 
        'title'     => '',
        'parent'     => $id,
        'ticket_text'     => $_POST['ticket_text'],
        'ticket_type'     => 'admin',
    )
);
$record_id = $wpdb->insert_id;
if($record_id != 0) {
 
    $wpdb->update( 
        $table_tickets, 
        array( 
            'ticket_status'     => 1,
        ),
        array(
            "id" => $id
        )
        );

    $data['status'] = 'success';
    ?>
    <div class="col-md-12">
         <div class="submited">تیکت با موفقیت ثبت شد</div>       
   </div>
    <?php
   } else {
    $data['status'] = 'error';
   ?>
    <div class="col-md-12">
         <div class="error-submit">خطا در ثبت تیکت</div>       
   </div>
   <?php
   }
}
if(isset($_POST['save'])){
    
    $table_tickets = $wpdb->prefix . 'hd_upanel_tickets';
    if($wpdb->update( 
        $table_tickets, 
        array( 
            'title'     => $_POST['title'],
            'ticket_priority'     => $_POST['ticket_priority'],
            'ticket_status'     => $_POST['ticket_status'],
        ),
        array(
            "id" => $id
        )
    )=== FALSE) {
        
        $data['status'] = 'error';
        ?>
                        <div class="col-md-12">
                            <div class="error-submit">خطا در ویرایش تیکت</div>
                        </div>
                        <?php
    } else {
    
        $data['status'] = 'success';
        ?>
                        <div class="col-md-12">
                            <div class="submited">تیکت با موفقیت ویرایش شد</div>
                        </div>
                        <?php
    
    };
 

                 } 



                 $tickets = $wpdb->get_results("SELECT * FROM $table_tickets WHERE id=$id  ORDER BY id DESC" );
foreach ($tickets as $key => $value) {

    $tickets[$key]->created_at = date_i18n('j F Y h:i',strtotime($tickets[$key]->created_at));
    $parent = $value->id;
    $tickets[$key]->childs  = $wpdb->get_results("SELECT * FROM $table_tickets WHERE parent=$parent ORDER BY id ASC" );
    foreach ($tickets[$key]->childs as $key01 => $value01) {

        $tickets[$key]->childs[$key01]->created_at = date_i18n('j F Y h:i',strtotime($tickets[$key]->childs[$key01]->created_at));
    }
}
?>


<div id="hd-upanel" class="container">
  <div class="row">
  <h4 style="margin-bottom:30px;margin-top:20px">ویرایش تیکت </h4>
  <div class="col-12 col-md-3">
    <div class="ticket-data">
    <p>
    <span class="title" > <strong>کاربر:</strong>   </span>
    <span class="value"><?php 

     $current_user = wp_get_current_user($tickets[0]->user_id);
     $user_meta = get_user_meta($tickets[0]->user_id);
     echo  $user_meta['first_name'][0].' ';
     echo $user_meta['last_name'][0];
     echo ' ('.get_user_by( 'id', $tickets[0]->user_id )->display_name.')';
    ?></span>
</p>
    <p>
    <span class="title" > <strong>عنوان:</strong>   </span>
    <span class="value"><?php echo $tickets[0]->title; ?></span>
</p>

<p>
    <span class="title"><strong>شماره تیکت: </strong>   </span>
          
    <span class="value"> <?php echo ((int)$tickets[0]->id+100).(strval($tickets[0]->ticket_id)); ?></span>
</p>
<p>
    <span class="title"> <strong>تاریخ ارسال:</strong> </span>
    <span class="value"><?php echo $tickets[0]->created_at; ?></span>
</p>
<p>
    <span class="title" > <strong>آخرین بروزرسانی:</strong> </span>
    <span class="value"><?php echo $tickets[0]->created_at; ?></span>
</p>
<p>
    <span class="title"> <strong> وضعیت:</strong></span>
    <span class="value">
            <?php if($tickets[0]->ticket_status == 0){ ?> در  انتظار پاسخ <?php } ?>
                    <?php if($tickets[0]->ticket_status == 1){ ?> پاسخ داده شده <?php } ?>
                    <?php if($tickets[0]->ticket_status == 2){ ?> در حال بررسی <?php } ?>
                    <?php if($tickets[0]->ticket_status == 3){ ?> بسته شده <?php } ?>
    
    </span>
</p>
<p>
    <span class="title"> <strong>اولویت:</strong> </span>
    <span class="value">
            <?php if($tickets[0]->ticket_priority == 0){ ?>   عادی  <?php } ?>
                    <?php if($tickets[0]->ticket_priority == 1){ ?>   متوسط <?php } ?>
                    <?php if($tickets[0]->ticket_priority == 2){ ?>   فوری <?php } ?>
                
    
    </span>
</p>
    </div>


<form class="edit-ticket" method="post">
    <h4>ویرایش اطلاعات تیکت</h4>
    <p>عنوان</p>
<input type="text" name="title" value="<?php echo $tickets[0]->title ?>">
<p>اولویت</p>
<select name="ticket_priority" >
  <option <?php if($tickets[0]->ticket_priority == 0){ echo 'selected';} ?> value="0">معمولی</option>
  <option <?php if($tickets[0]->ticket_priority == 1){ echo 'selected';} ?> value="1">متوسط</option>
  <option <?php if($tickets[0]->ticket_priority == 2){ echo 'selected';} ?> value="2">فوری</option>
</select>
<p>وضعیت</p>
<select name="ticket_status" id="cars">
  <option <?php if($tickets[0]->ticket_status == 0){ echo 'selected';} ?>  value="0"> در  انتظار پاسخ </option>
  <option <?php if($tickets[0]->ticket_status == 1){ echo 'selected';} ?>  value="1"> پاسخ داده شده </option>
  <option <?php if($tickets[0]->ticket_status == 2){ echo 'selected';} ?>  value="2">در حال بررسی </option>
  <option <?php if($tickets[0]->ticket_status == 3){ echo 'selected';} ?>  value="3">بسته شده </option>
</select>
<button name="save" type="submit">ذخیره
</div>
    <div class="col-12 col-md-9">


<div class="reply-ticket">
<div class="ticket-parent <?php echo $tickets[0]->ticket_type; ?>">
<div class="ticket   <?php echo $tickets[0]->ticket_type; ?>">
          <p class="title"> <?php echo $tickets[0]->ticket_type=='user'?'کاربر':'ادمین'; ?>
              <span class="date"> <?php echo $tickets[0]->created_at; ?></span>
          </p>
          <p class="ricket-text">
              <?php echo $tickets[0]->ticket_text; ?>
          </p>
      </div>
</div>


      <?php 
                    foreach ($tickets[0]->childs as $key => $value) { ?>
                    <div class="ticket-parent <?php echo $value->ticket_type; ?>">
      <div class="ticket  <?php echo $value->ticket_type; ?>">
          <p class="title">
              <?php echo $value->ticket_type=='user'?'کاربر':'ادمین'; ?>


              <span class="date"><?php echo $value->created_at; ?></span>
          </p>
          <p class="ricket-text">
              <?php echo $value->ticket_text; ?>
          </p>
      </div>
      </div>
      <?php } ?>
<div>
<form method="post">
          <label> متن پاسخ</label>
          <textarea style="height: 200px;" name="ticket_text" id="" cols="30" rows="5"></textarea>
          <button disabled="true" name="massage" type="submit">ارسال</button>
      </form>
</div>
    
</div>




  



</div>
</div>
</div>
