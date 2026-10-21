<div class="mdl-card mdl-shadow--2dp tab" id="mail-tab" style="width: 100%;display:none">
    <div class="mdl-card__supporting-text">
        <div class="row " style="margin-top:30px">
            <div class="col-12 col-md-12">
                <label class="pure-material-textfield-outlined">
                    <input type="text" name="email_host" value="<?php if (isset($settings->email_host)) { echo $settings->email_host; } else { echo ''; } ?>" placeholder=" ">
                    <span>میزبان (host)  </span>
                </label>
                </div>
                <div class="col-12 col-md-12" style="margin-bottom:20px">
                <p style="margin:20px 0 10px 0;">رمز نگاری</p>
                    <label style="margin-left:20px" class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="email_encode_1">
                        <input type="radio" id="email_encode_1" class="mdl-radio__button" name="email_encode" value="1" <?php if (isset($settings->email_encode) && $settings->email_encode == '1') { echo 'checked';}  ?>>
                        <span class="mdl-radio__label">ssl</span>
                    </label>
                    <label style="margin-left:20px" class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="email_encode_2">
                        <input type="radio" id="email_encode_2" class="mdl-radio__button" name="email_encode" value="2" <?php if (isset($settings->email_encode) && $settings->email_encode == '2') { echo 'checked';} ?>>
                        <span class="mdl-radio__label">tls</span>
                    </label>
                    <label style="margin-left:20px" class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="email_encode_3">
                        <input type="radio" id="email_encode_3" class="mdl-radio__button" name="email_encode" value="3" <?php if (isset($settings->email_encode) && $settings->email_encode == '3') { echo 'checked';} if(!isset($settings->email_encode)) { echo 'checked'; } ?>>
                        <span class="mdl-radio__label">هیچکدام</span>
                    </label>
            </div>
            <div class="col-12 col-md-3">
                <label class="pure-material-textfield-outlined" style="margin-bottom:20px">
                    <input type="text" name="email_smtp" value="<?php if (isset($settings->email_smtp)) { echo $settings->email_smtp; } else { echo '25'; } ?>" placeholder=" ">
                    <span>پورت SMTP</span>
                </label>
            </div>
            <div class="col-12 col-md-3">
                <label class="pure-material-textfield-outlined" style="margin-bottom:20px">
                    <input type="text" name="email_username" value="<?php if (isset($settings->email_username)) { echo $settings->email_username; } else { echo ''; } ?>" placeholder=" ">
                    <span>نام کاربری</span>
                </label>
            </div>
            <div class="col-12 col-md-3">
                <label class="pure-material-textfield-outlined" style="margin-bottom:20px">
                    <input type="password" name="email_password" value="<?php if (isset($settings->email_password)) { echo $settings->email_password; } ?>" placeholder=" ">
                    <span>رمز عبور</span>
                </label>
            </div>
            <div class="col-12 col-md-12">
            </div>
            <div class="col-12 col-md-3">
                <label class="pure-material-textfield-outlined" style="margin-bottom:20px">
                    <input type="text" name="email_email" value="<?php if (isset($settings->email_email)) { echo $settings->email_email; } ?>" placeholder=" ">
                    <span> ایمیل ارسال کننده</span>
                </label>
            </div>
            <div class="col-12 col-md-3">
                <label class="pure-material-textfield-outlined" style="margin-bottom:20px">
                    <input type="text" name="email_name" value="<?php if (isset($settings->email_name)) { echo $settings->email_name; }else{ echo get_bloginfo( 'name' );} ?>" placeholder=" ">
                    <span> نام ارسال کننده</span>
                </label>
            </div>
        </div>
    </div>
</div>