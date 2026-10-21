<div class="mdl-card mdl-shadow--2dp tab" id="sms-tab" style="width: 100%;display:none">

    <div class="mdl-card__supporting-text">
        <p class="hd-upanel-label"> سرویس پیامک فعال</p>
        <select class="select-provider" name="sms">
            <?php foreach ($sms as $key => $value) { ?>
            <option value="<?php echo $value->id; ?>" <?php if ($value->is_default == 1) { echo 'selected'; } ?>>
                <?php echo $value->title; ?></option>
            <?php } ?>
        </select>
        <?php foreach ($sms as $key => $value) { ?>
        <div class="row providers-fields" <?php echo 'id="provider-' . $value->id . '"'; ?>
            style="margin-top:20px;display:<?php echo (($value->is_default == 1) ? 'flex' : 'none'); ?>">
            <?php if ($value->id == 8) {  echo '<p>برای تعریف متن اعتبار سنجی از عبارت %code% به عنوان متغیر استفاده کنید.</p>'; } ?>
            <?php if ($value->id == 9) {  echo '<p>برای تعریف متن اعتبار سنجی از عبارت #CODE# به عنوان متغیر استفاده کنید.</p>'; } ?>
            <div class="col-12 col-md-3"
                <?php if ($value->type == 1 && $value->id != 8) {  echo 'style="display:none"'; } ?>>
                <input type="hidden" name="sms-id[]" value="<?php echo $value->id; ?>">
                <label class="pure-material-textfield-outlined">
                    <input type="text" name="username[]" value="<?php echo $value->username; ?>" placeholder=" ">
                    <span>
                        <?php if ($value->id == 9) { echo 'کد پترن '; } else { echo "نام کاربری"; } ?>
                    </span>
                </label>
            </div>
            <div class="col-12 col-md-3"
                <?php if ($value->type == 1 && $value->id != 8) { echo 'style="display:none"';  } ?>>
                <label class="pure-material-textfield-outlined">
                    <input type="text" name="password[]" value="<?php echo $value->password; ?>" placeholder=" ">
                    <span> رمز عبور</span>
                </label>
            </div>
            <div class="col-12 col-md-3"
                <?php if ($value->type == 0 && $value->id != 8) { echo 'style="display:none"'; } ?>>
                <label class="pure-material-textfield-outlined">
                    <input type="text" name="token[]" value="<?php echo $value->token; ?>" placeholder=" ">
                    <span><?php if ($value->id == 8) { echo 'کد پترن  '; } else { echo "توکن"; } ?> </span>
                </label>
            </div>
            <div class="col-12 col-md-3">
                <label class="pure-material-textfield-outlined">
                    <input type="text" name="number[]" value="<?php echo $value->number; ?>" placeholder=" ">
                    <span><?php 
                        if ($value->id == 2 || $value->id == 9) { echo 'الگو  ';
                        } else if ($value->id == 7) { echo 'کد متن  ';
                        } else if ($value->id == 8) { echo ' شماره خط';
                        } else { echo "شماره خط";
                        } ?> </span>
                </label>
            </div>
            <div class="col-12 col-md-3" <?php if ($value->id == 1 || $value->id == 3 || $value->id == 4 || $value->id == 5 || $value->id == 6) {
                                                echo 'style="display:none"';
                                            } ?>>
                <label class="pure-material-textfield-outlined">
                    <input type="text" name="reset_password_pattern[]"
                        value="<?php echo $value->reset_password_pattern; ?>" placeholder=" ">
                    <span><?php if ($value->id == 8) { echo 'کد پترن فراموشی رمز  ';
                                } else {
                                 echo "الگوی فراموشی رمز عبور";
                                 } ?>
                    </span>
                </label>
            </div>
           



        </div>

        <?php } ?>

        <div class="col-12 col-md-12 sms-text" <?php if ($value->id == 2 || $value->id == 7 || $value->id == 8 || $value->id == 9) {
                                                echo 'style="display:none"';
                                            } ?>>
                <p class="dividerd"></p>
                <p class="hd-upanel-label">متن پیامک برای ارسال کد تایید ورود و ثبت‌نام</p> X="کد تایید"
                <br>
             
                <label for="sms_content">

                    <textarea name="sms_content" id="sms_content" cols="30" rows="3"><?php if (isset($settings->sms_content)) {  echo $settings->sms_content; } else { echo ''; } ?></textarea>

                </label>
                <p class="dividerd"></p>
                <p class="hd-upanel-label">متن پیامک برای ارسال کد فراموشی رمز عبور</p>
                X="کد تایید"
                <br>
                <label for="sms_content_reset_password">
                    <textarea name="sms_content_reset_password" id="sms_content_reset_password" cols="30" rows="3"><?php if (isset($settings->sms_content_reset_password)) {
                                                echo $settings->sms_content_reset_password;
                                            } else {
                                                echo '';
                                            } ?></textarea>

                </label>
            </div>

    </div>
</div>