<div class="container-fluid ">
	<div ng-app="hd-upanel-login" data-ng-init="loaded()" class="row hd-upanel-login hd-upanel 
	<?php if (isset($_COOKIE['upanel-dark-mode']) && $_COOKIE['upanel-dark-mode'] == 'true') {
		echo 'dark';
	} else {
		echo 'light';
	} ?> login ">

		<div class="col-12 col-md-12 align-center">
			<div class="login-btns">
				<p class="login-label">حساب کاربری دارید؟</p>
				<button type="button" class="change-tab " id="login">
					ورود به حساب
				</button>
			</div>
			<div class="login-form" ng-controller="login">
				<?php if (isset($_COOKIE['upanel-dark-mode']) && $_COOKIE['upanel-dark-mode'] == 'true') { ?>
					<img class="logo" src="<?php if ($all_settings->main_logo) {
						echo $all_settings->main_logo;
					} else {
						echo plugin_dir_url(__FILE__) . 'upanel/assets/img/logo.svg';
					} ?>">
				<?php } else { ?>
					<img class="logo" src="<?php if ($all_settings->main_logo) {
						echo $all_settings->main_logo;
					} else {
						echo plugin_dir_url(__FILE__) . 'upanel/assets/img/logo.svg';
					} ?>">
				<?php } ?>

				<p ng-if="data.step == 1" class="title">ورود | ثبت نام</p>
				<p ng-if="data.step == 2" class="title">ورود | ثبت نام</p>

				<div class="row" style="width: 100%;">
					<form name="registerForm" style="padding:0 !important">
						<div ng-if="data.step == 1" class="col-12 col-md-12">

							<?php

							$arr = [];
							if (isset($all_settings->login_by_mobile) && $all_settings->login_by_mobile == 'on') {
								array_push($arr, 'شماره موبایل');
							}
							if (!isset($all_settings->login_by_mobile)) {
								array_push($arr, 'شماره موبایل');
							}
							if (isset($all_settings->login_by_email) && $all_settings->login_by_email == 'on') {
								array_push($arr, 'ایمیل');
							}
							if (!isset($all_settings->login_by_email)) {
								array_push($arr, 'ایمیل');
							}
							if (isset($all_settings->login_by_username) && $all_settings->login_by_username == 'on') {
								array_push($arr, 'نام کاربری');
							}
							if (!isset($all_settings->login_by_username)) {
								array_push($arr, 'نام کاربری');
							}

							function upanel_replace_string($search, $replace, $subject)
							{
								$pos = strrpos($subject, $search);
								if ($pos !== false) {
									$subject = substr_replace($subject, $replace, $pos, strlen($search));
								}
								return $subject;
							}
							?>

							<p class="hi">سلام!</p>
							<p class=" login-info">لطفا
								<?php echo upanel_replace_string('،', ' یا ', implode("، ", $arr)); ?> خود را وارد کنید
							</p>
							<div class="upanel-material-textfield icon ">
								<input ng-keyup="($event.keyCode === 13 && mobile_valid())?check_mobile():''"
									type="text" class="ltr" ng-model="data.mobile" placeholder=" " value="">
								<span class="valid"></span><span class="in-valid"></span><img
									src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
							</div>
							<p ng-if="data.check_mobile_error" class="error-input">اطلاعات وارد شده معتبر نیست</p>

							<button ng-disabled="!mobile_valid()" ng-click="check_mobile()" type="button"
								ripple="ripple" ripple-color="#FFF" class="default-btn">
								<img ng-if="data.check_mobile_loading"
									src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
								{{(data.check_mobile_loading)?'':'تایید'}}
							</button>
						</div>
						<div ng-if="data.step == 2 &&  !data.send_reset_password_loading" class="col-12 col-md-12">
							<p class="login-info">لطفا رمز عبور خود را وارد کنید</p>
							<div class="upanel-material-textfield icon mb-0">
								<input type="password" class="ltr" ng-model="data.password" placeholder=" " value="">
								<span class="valid"></span><span class="in-valid"></span><img
									src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
							</div>
							<div class="error-area">
								<p ng-if="data.login_password_error" class="sms-error">رمز عبور وارد شده اشتباه است</p>
								<p ng-if="data.send_sms_error" class="sms-error">خطا در ارسال پیامک</p>

							</div>

							<div class="row mx-0">

								<button ng-if="all_settings['login_sms'] == 'on'"
									ng-class="{loading: data.send_sms_login_loading}" ng-click="send_sms_login()"
									type="button" ripple="ripple" ripple-color="#FFF" class="default-btn send-sms-otp">
									<img ng-if="data.send_sms_login_loading"
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading">
									ورود با رمز یکبار مصرف
								</button>

								<button ng-disabled="data.password.length == 0" ng-click="login_by_password()"
									type="button" ripple="ripple" ripple-color="#FFF" class="default-btn mt-16">
									<img ng-if="data.login_password_loading"
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading ">
									{{(data.login_password_loading)?'':'تایید'}}
								</button>





							</div>

						</div>
						<img style="filter:invert(1);opacity:0.5;display:block;margin:0 auto"
							ng-if="data.step == 2 && data.send_reset_password_loading"
							ng-if="data.send_reset_password_loading"
							src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
							class="loading">
						<button ng-if="data.step == 2 && !data.send_reset_password_loading"
							ng-click="reset_password_handler()" type="button" ripple="ripple" ripple-color="#FFF"
							class="default-btn reset-password">

							{{(data.send_reset_password_loading)?'':'فراموشی رمز عبور'}}
						</button>

						<div ng-if="data.step == 7 " class="col-12 col-md-12">
							<p class="title"> فراموشی رمز عبور</p>

							<p class=" login-info"> لطفا برای دریافت کد اعتبار سنجی یک مورد را انتخاب کنید </p>
							<div class="upanel-material-textfield icon ">


								<button ng-disabled="data.reset_password_send_email_loading"
									ng-click="reset_password_send_code('email')" type="button" ripple="ripple"
									ripple-color="#FFF" class="default-btn ">
									<img ng-if="data.reset_password_send_email_loading"
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading">
									{{(data.reset_password_send_email_loading)?'':'ارسال کد به ایمیل'}}
								</button>

								<button ng-class="{loading: data.reset_password_send_sms_loading}"
									ng-disabled="data.reset_password_send_sms_loading"
									ng-click="send_sms_forgot_password()" type="button" ripple="ripple"
									ripple-color="#FFF" class="default-btn">
									<img ng-if="data.reset_password_send_sms_loading"
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading">
									{{(data.reset_password_send_sms_loading)?'':'ارسال کد به شماره همراه'}}
								</button>

								<p class="sms-error">

								</p>

							</div>

						</div>

						<div ng-if="data.step == 8 " class="col-12 col-md-12">
							<p class="title"> فراموشی رمز عبور</p>

							<p class=" login-info">لطفا جهت تکمیل فرآیند اطلاعات زیر را تکمیل نمایید</p>


							<div class="upanel-material-textfield icon ">
								<input type="tel" class="ltr" ng-model="data.reset_code" placeholder=" " value="">
								<label>کد ارسال شده</label></span><span class="valid"></span><span
									class="in-valid"></span>
								<img src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
							</div>

							<div class="upanel-material-textfield icon ">
								<input type="password" class="ltr" ng-model="data.password_reset" placeholder=" "
									value="">
								<label>رمز عبور جدید</label><span class="valid"></span><span class="in-valid"></span>
								<img src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
							</div>

							<div class="upanel-material-textfield icon ">
								<input type="password" class="ltr" ng-model="data.password_reset_repeat" placeholder=" "
									value="">
								<label>تکرار رمز عبور جدید</label><span class="valid"></span><span
									class="in-valid"></span>
								<img src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
							</div>



							<div class="row mx-0">

								<button ng-class="{loading: data.reset_password_loading}"
									ng-disabled="data.password_reset.length == 0 || data.password_reset != data.password_reset_repeat || data.reset_password_loading"
									ng-click="reset_password()" type="button" ripple="ripple" ripple-color="#FFF"
									class="default-btn">
									<img ng-if="data.reset_password_loading"
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading">
									{{(data.login_password_loading)?'':'تایید'}}
								</button>





							</div>

						</div>

						<div ng-if="data.step == 9 " class="col-12 col-md-12 pass-changed">
							<svg style="width: 100px; display: block;margin: 10px auto;"
								xmlns="http://www.w3.org/2000/svg" id="Layer_1" x="0" y="0" version="1.1"
								viewBox="0 0 100 100" xml:space="preserve">
								<circle cx="50" cy="50" r="48" fill="#2ec19c" />
								<path fill="#348969"
									d="M96.632 61.376L68.827 33.579l-.05-.05a24.886 24.886 0 0 0-18.78-8.53c-13.79 0-25 11.21-25 25 0 7.57 3.39 14.38 8.73 18.96l27.669 27.668C78.755 92.4 92.41 78.737 96.632 61.376z" />
								<path fill="#fff"
									d="M50 25c-13.79 0-25 11.21-25 25 0 13.78 11.21 25 25 25 13.78 0 25-11.22 25-25 0-13.79-11.22-25-25-25zm11.689 18.49L47.262 57.915a1 1 0 0 1-1.414 0l-7.539-7.54a1 1 0 1 1 1.414-1.414l6.832 6.833 13.72-13.72a1 1 0 1 1 1.414 1.414z" />
							</svg>
							<p>رمز حساب کاربری شما با موفقیت تغییر پیدا کرد</p>
							<div class="btns">
								<a ng-href="/{{all_settings['panel_url']}}">ورود به حساب</a>
								<a href="/">صفحه اصلی</a>
							</div>
							<div>

							</div>

						</div>

						<div ng-if="data.step == 3" class="col-12 col-md-12 registerForm">
							<p class="title">ثبت نام</p>

							<p class=" login-info">لطفا اطلاعات زیر را تکمیل نمایید</p>

							<?php if (isset($all_settings->register_firstname) && $all_settings->register_firstname == 'on') { ?>
								<div class="upanel-material-textfield icon"
									ng-class="{'in-valid no-icon': (register_errors.first_name != '')}"><input type="text"
										name="first_name"
										ng-change="(registerForm.first_name.$touched)?check_first_name():''"
										ng-model="data.first_name" value=""><label>نام</label><span
										class="icon first_name"></span><span class="valid"></span><span
										class="in-valid"></span><img
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading"></div>
								<p ng-if="register_errors.first_name != ''" class="error-input">
									{{register_errors.first_name}}</p>
							<?php } ?>

							<?php if (isset($all_settings->register_lastname) && $all_settings->register_lastname == 'on') { ?>
								<div class="upanel-material-textfield icon"
									ng-class="{'in-valid no-icon': (register_errors.last_name != '')}"><input type="text"
										name="last_name" ng-change="(registerForm.last_name.$touched)?check_last_name():''"
										ng-model="data.last_name" value=""><label>نام خانوادگی</label><span
										class="icon last_name"></span><span class="valid"></span><span
										class="in-valid"></span><img
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading"></div>
								<p ng-if="register_errors.last_name != ''" class="error-input">{{register_errors.last_name}}
								</p>
							<?php } ?>






							<?php if (isset($all_settings->register_username) && $all_settings->register_username == 'on') { ?>
								<div class="upanel-material-textfield icon"
									ng-class="{loading: check_username_loading ,valid: (!check_username_loading && register_errors.username == '' && data.username.length>0),'in-valid': (!check_username_loading && register_errors.username != '' && data.username.length>0)}">
									<input autocomplete="off" ng-disabled="data.input_type == 'username'"
										ng-change="check_username('username')" type="text" class="ltr"
										ng-model="data.username" value=""><label>نام کاربری</label><span
										class="icon username"></span><span class="valid"></span><span
										class="in-valid"></span><img
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading">
								</div>
								<p ng-if="!check_username_loading && register_errors.username != ''" class="error-input">
									{{register_errors.username}}</p>
							<?php } ?>

							<?php if (isset($all_settings->register_mobile) && $all_settings->register_mobile == 'on') { ?>
								<div class="upanel-material-textfield icon"
									ng-class="{loading: check_mobile_loading ,valid: (!check_mobile_loading && register_errors.mobile == '' && data.mobile.length>0),'in-valid': (!check_mobile_loading && register_errors.mobile != '' && data.mobile.length>0)}">
									<input autocomplete="off" ng-disabled="data.input_type == 'mobile'"
										ng-change="check_username('mobile')" type="text" class="ltr" ng-model="data.mobile"
										value=""><label> شماره موبایل</label><span class="icon mobile"></span><span
										class="valid"></span><span class="in-valid"></span><img
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading">
								</div>
								<p ng-if="!check_mobile_loading && register_errors.mobile != ''" class="error-input">
									{{register_errors.mobile}}</p>
							<?php } ?>

							<?php if (isset($all_settings->register_email) && $all_settings->register_email == 'on') { ?>
								<div class="upanel-material-textfield icon"
									ng-class="{loading: check_email_loading,valid: (!check_email_loading && register_errors.email == '' && data.email.length>0),'in-valid': (!check_email_loading && register_errors.email != '' && data.email.length>0)}">
									<input autocomplete="off" ng-disabled="data.input_type == 'email'"
										ng-change="check_username('email')" type="email" class="ltr" ng-model="data.email"
										value=""><label>ایمیل</label><span class="icon email"></span><span
										class="valid"></span><span class="in-valid"></span><img
										src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
										class="loading">
								</div>
								<p ng-if="!check_email_loading && register_errors.email != ''" class="error-input">
									{{register_errors.email}}</p>
							<?php } ?>


							<div class="upanel-material-textfield icon"
								ng-class="{'in-valid': (register_errors.password != '')}">
								<input autocomplete="off" type="password" name="password"
									ng-change="registerForm.password.$touched?check_password(1):''" class="ltr"
									ng-model="data.password" value=""><label>رمز عبور</label><span
									class="icon password"></span><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
							</div>
							<p ng-if="register_errors.password != ''" class="error-input">{{register_errors.password}}
							</p>

							<div class="upanel-material-textfield icon"
								ng-class="{'in-valid': (register_errors.repeat_password != '')}">
								<input autocomplete="off" type="password" name="repeat_password"
									ng-change="registerForm.repeat_password.$touched?check_password(2):''" class="ltr"
									ng-model="data.repeat_password" value=""><label>تکرار رمز عبور</label><span
									class="icon repeat_password"></span><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
							</div>
							<p ng-if="register_errors.repeat_password != ''" class="error-input">
								{{register_errors.repeat_password}}</p>

							<button ng-disabled="!register_valid()" ng-click="register()" type="button" ripple="ripple"
								ripple-color="#FFF" class="default-btn">
								<img ng-if="data.register_loading"
									src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
								{{(data.register_loading)?'':'ثبت نام'}}
							</button>
							<p ng-if="data.send_sms_error" class="sms-error">خطا در ارسال پیامک</p>
						</div>

						<div ng-if="data.step == 4" class="col-12 col-md-12">
							<p class="title">کد تایید را وارد کنید</p>
							<p class=" login-info">کد تایید برای شماره <span>{{data.mobile}}</span> پیامک شد</p>
							<div class="upanel-material-textfield icon "
								ng-class="{'in-valid no-icon': (register_errors.sms != '')}"><input type="text"
									class="ltr" name="sms" ng-change="(registerForm.sms.$touched)?check_sms():''"
									ng-model="data.sms" value=""><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p ng-if="data.sms_code_error" class="sms-error">کد تایید وارد شده اشتباه است</p>

							<span class="sms-titmer" ng-if="!data.can_resend_sms">{{get_clock(data.timer)}} مانده تا
								دریافت مجدد کد</span>
							<button ng-click="register()" ng-if="data.can_resend_sms" class="resend_sms"
								type="button">دریافت مجدد کد </button>
							<button ng-disabled="data.sms.length != 5" ng-click="check_sms()" type="button"
								ripple="ripple" ripple-color="#FFF" class="default-btn">
								<img ng-if="data.check_sms_loading"
									src="<?php echo plugin_dir_url(__FILE__) . 'upanel/assets/img/loading.svg'; ?>"
									class="loading">
								{{(data.check_sms_loading)?'':'تایید'}}
							</button>
						</div>
						<div ng-if="data.step == 5" class="col-12 col-md-12">

							<p class="text-center">کاربر گرامی شما با موفقیت وارد حساب کاربری شدید. </p>

						</div>
						<div ng-if="data.step == 6" class="col-12 col-md-12">

							<p class="text-center">کاربر گرامی ثبت نام شما با موفقیت انجام شد </p>

						</div>

					</form>
				</div>

			</div>




		</div>



		<div class="register-form" ng-controller="login">


			<?php if (isset($_COOKIE['upanel-dark-mode']) && $_COOKIE['upanel-dark-mode'] == 'true') { ?>
				<img class="logo" src="<?php
				if ($all_settings->main_logo) {
					echo $all_settings->main_logo;
				} else {
					echo plugin_dir_url(__FILE__) . 'upanel/assets/img/logo.svg';
				} ?>">
			<?php } else { ?>
				<img class="logo" src="<?php if ($all_settings->main_logo) {
					echo $all_settings->main_logo;
				} else {
					echo plugin_dir_url(__FILE__) . 'upanel/assets/img/logo.svg';
				} ?>">
			<?php } ?>
			<p class="title">ثبت نام</p>


			<div class="row">
				<div class="col-12 col-md-12">
					<div class="upanel-material-textfield">
						<input type="text" ng-model="data.edit.first_name" placeholder=" " value="">
						<label>نام</label>
					</div>
				</div>
			</div>

		</div>

	</div>
</div>
</div>
</div>


<?php


global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'hd_upanel_sidemenu';
$result = $wpdb->get_results("SELECT * FROM $table_name LIMIT 1")[0];
$items = json_decode(stripslashes($result->items));
$settings = json_decode($result->settings);
$all_settings = json_decode($result->all_settings);

if (!isset($all_settings->login_by_mobile)) {
	$all_settings->login_by_mobile = 'on';
	$all_settings->login_by_email = 'on';
	$all_settings->login_by_username = 'on';
	$all_settings->register_by_mobile = 'on';
	$all_settings->register_by_email = 'on';
	$all_settings->register_by_username = 'on';
	$all_settings->login_sms = 'on';
	$all_settings->register_sms = 'on';
	$all_settings->register_firstname = 'on';
	$all_settings->register_lastname = 'on';
	$all_settings->register_username = 'on';
	$all_settings->register_email = 'on';
	$all_settings->register_mobile = 'on';
}


$plugin_url = plugin_dir_url(__FILE__);
$admin_url = admin_url('admin-ajax.php');
?>
<script>

	function getCookie(cname) {
		let name = cname + "=";
		let decodedCookie = decodeURIComponent(document.cookie);
		let ca = decodedCookie.split(';');
		for (let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}


	var app = angular.module('hd-upanel-login', ['ngRoute', 'ngSanitize']);


	app.run(function ($rootScope, $location) {
		$rootScope.admin_url = '<?php echo $admin_url; ?>'
		$rootScope.notifications_readed = [];

		<?php
		echo 'var settings = ' . json_encode($settings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
		?>
		<?php
		echo 'var all_settings = ' . json_encode($all_settings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
		?>
		$rootScope.dashboard_settings = settings;
		$rootScope.all_settings = all_settings;

		$rootScope.number_fa = function (number) {
			let string = number.toLocaleString('fa-IR');
			string = string.replace(/\٬/g, ",‬").replaceAll(",", "");
			return string;
		}
		//   $.ajax({
		//           type: "POST",
		//           url: $rootScope.admin_url,
		//           data: {
		//             action: "get_notification_content",
		//             notification_id: id
		//           },
		//           success: function (results) {
		//             $rootScope.notification_content = results.substring(0, results.length - 1);
		//             $rootScope.$apply();
		//           },
		//           error: function (results) {}
		//         });
		$rootScope.plugin_path = '<?php echo plugin_dir_url(__DIR__); ?>'

	})
</script>