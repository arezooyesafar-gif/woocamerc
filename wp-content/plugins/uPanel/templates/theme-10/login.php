<div class="container-fluid ">


	<div id="cr-dashboard-login"
		class="row cr-dashboard-login  <?php if(isset($_COOKIE['cr-dashboard-dark-mode']) && $_COOKIE['cr-dashboard-dark-mode'] == 'true') {  echo 'dark'; }else { echo 'light'; } ?> login ">
		<div class="col-12 col-md-6 align-center">
			<div class="login-form" ng-controller="login">

				<?php if(isset($_COOKIE['cr-dashboard-dark-mode']) && $_COOKIE['cr-dashboard-dark-mode'] == 'true') {  ?>
				<img class="logo" src="<?php if($all_settings->main_logo){ echo $all_settings->main_logo; }
else{ echo plugin_dir_url( __FILE__ ).'files/assets/img/logo.svg'; }  ?>">
				<?php	}else { ?>
				<img class="logo" src="<?php if($all_settings->main_logo){ echo $all_settings->main_logo; }
else{ echo plugin_dir_url( __FILE__ ).'files/assets/img/logo.svg'; }  ?>">
				<?php	} ?>

				<p v-if="data.step == 1" class="title">ورود / ثبت نام</p>
				<p v-if="data.step == 2" class="title">ورود / ثبت نام</p>

				<div class="row" style="width: 100%;">
					<form  v-on:submit.prevent="onSubmit" name="registerForm">
						<div v-if="data.step == 1" class="col-12 col-md-12">

							<?php 

	$arr = [];
	if(isset($all_settings->login_by_mobile) && $all_settings->login_by_mobile == 'on'){ array_push($arr,'شماره موبایل'); } if(!isset($all_settings->login_by_mobile)){ array_push($arr,'شماره موبایل'); }
	if(isset($all_settings->login_by_email) && $all_settings->login_by_email == 'on'){ array_push($arr,'ایمیل'); } if(!isset($all_settings->login_by_email)){ array_push($arr,'ایمیل'); }
	if(isset($all_settings->login_by_username) && $all_settings->login_by_username == 'on'){ array_push($arr,'نام کاربری'); } if(!isset($all_settings->login_by_username)){ array_push($arr,'نام کاربری'); }
	
	function upanel_replace_string($search, $replace, $subject){
    $pos = strrpos($subject, $search);
    if($pos !== false){
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;}
	?>


							<p class="text-center">برای ورود یا ثبت نام
								<?php echo upanel_replace_string('،', ' یا ', implode("، ",$arr)); ?> خود را وارد کنید
							</p>
							<div class="cr-dashboard-text icon ">
								<input v-on:keyup="($event.keyCode === 13 && mobile_valid())?check_mobile():''"
									type="text" class="ltr" v-model="data.mobile" placeholder=" " value="">
								<label><?php echo upanel_replace_string('،', ' یا ', implode("، ",$arr)); ?></label><span
									class="icon user"></span><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="data.check_mobile_error" class="error-input">اطلاعات وارد شده معتبر نیست</p>

							<button :disabled="!mobile_valid()" @click="check_mobile()" type="button"
								ripple="ripple" ripple-color="#FFF" class="default-btn">
								<img v-if="data.check_mobile_loading"
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading">
								{{(data.check_mobile_loading)?'':'تایید'}}
							</button>
						</div>
						<div v-if="data.step == 2" class="col-12 col-md-12">
							<p class="text-center">لطفا رمز عبور خود را وارد کنید</p>
							<div class="cr-dashboard-text icon ">
								<input type="password" class="ltr" v-model="data.password" placeholder=" " value="">
								<label>رمز عبور</label><span class="icon password"></span><span
									class="valid"></span><span class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="data.login_password_error" class="sms-error">رمز عبور وارد شده اشتباه است</p>
							<p v-if="data.send_sms_error" class="sms-error">خطا در ارسال پیامک</p>

							<div class="row mx-0">

								<button :disabled="data.password.length == 0" @click="login_by_password()"
									type="button" ripple="ripple" ripple-color="#FFF" class="default-btn">
									<img v-if="data.login_password_loading"
										src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
										class="loading">
									{{(data.login_password_loading)?'':'تایید'}}
								</button>

								<button v-if="all_settings['login_sms'] == 'on'" @click="send_sms_login()"
									type="button" ripple="ripple" ripple-color="#FFF" class="default-btn">
									<img v-if="data.send_sms_login_loading"
										src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
										class="loading">
									{{(data.send_sms_login_loading)?'':'ورود با رمز یکبار مصرف'}}
								</button>

							</div>

						</div>



						<div v-if="data.step == 3" class="col-12 col-md-12">
							<p class="text-center">لطفا اطلاعات زیر را تکمیل نمایید</p>

							<?php if(isset($all_settings->register_firstname) && $all_settings->register_firstname == 'on'){ ?>
							<div class="cr-dashboard-text icon"
								:class="{'in-valid no-icon': (register_errors.first_name != '')}"><input type="text"
									name="first_name"
									@change="(data.first_name.length > 0)?check_first_name():''"
									v-model="data.first_name" value=""><label>نام</label><span
									class="icon first_name"></span><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="register_errors.first_name != ''" class="error-input">
								{{register_errors.first_name}}</p>
							<?php } ?>

							<?php if(isset($all_settings->register_lastname) && $all_settings->register_lastname == 'on'){ ?>
							<div class="cr-dashboard-text icon"
								:class="{'in-valid no-icon': (register_errors.last_name != '')}"><input type="text"
									name="last_name" @change="(data.last_name.length > 0)?check_last_name():''"
									v-model="data.last_name" value=""><label>نام خانوادگی</label><span
									class="icon last_name"></span><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="register_errors.last_name != ''" class="error-input">{{register_errors.last_name}}
							</p>
							<?php } ?>

							<?php if(isset($all_settings->register_username) && $all_settings->register_username == 'on'){ ?>
							<div class="cr-dashboard-text icon"
								:class="{loading: data.check_username_loading ,valid: (!data.check_username_loading && register_errors.username == '' && data.username.length>0),'in-valid': (!data.check_username_loading && register_errors.username != '' && data.username.length>0)}">
								<input autocomplete="off" :disabled="data.input_type == 'username'"
									@change="check_username('username')" type="text" class="ltr"
									v-model="data.username" value=""><label>نام کاربری</label><span
									class="icon username"></span><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="!data.check_username_loading && register_errors.username != ''" class="error-input">
								{{register_errors.username}}</p>
							<?php } ?>

							<?php if(isset($all_settings->register_mobile) && $all_settings->register_mobile == 'on'){ ?>
							<div class="cr-dashboard-text icon"
								:class="{loading: data.check_mobile_loading ,valid: (!data.check_mobile_loading && register_errors.mobile == '' && data.mobile.length>0),'in-valid': (!data.check_mobile_loading && register_errors.mobile != '' && data.mobile.length>0)}">
								<input autocomplete="off" :disabled="data.input_type == 'mobile'"
									@change="check_username('mobile')" type="text" class="ltr" v-model="data.mobile"
									value=""><label> شماره موبایل</label><span class="icon mobile"></span><span
									class="valid"></span><span class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="!data.check_mobile_loading && register_errors.mobile != ''" class="error-input">
								{{register_errors.mobile}}</p>
							<?php } ?>

							<?php if(isset($all_settings->register_email) && $all_settings->register_email == 'on'){ ?>
							<div class="cr-dashboard-text icon"
								:class="{loading: data.check_email_loading,valid: (!data.check_email_loading && register_errors.email == '' && data.email.length>0),'in-valid': (!data.check_email_loading && register_errors.email != '' && data.email.length>0)}">
								<input autocomplete="off" :disabled="data.input_type == 'email'"
									@change="check_username('email')" type="email" class="ltr" v-model="data.email"
									value=""><label>ایمیل</label><span class="icon email"></span><span
									class="valid"></span><span class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="!data.check_email_loading && register_errors.email != ''" class="error-input">
								{{register_errors.email}}</p>
							<?php } ?>


							<div class="cr-dashboard-text icon"
								:class="{'in-valid': (register_errors.password != '')}">
								<input autocomplete="off" type="password" name="password"
									@change="data.password.length > 0?check_password(1):''" class="ltr"
									v-model="data.password" value=""><label>رمز عبور</label><span
									class="icon password"></span><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="register_errors.password != ''" class="error-input">{{register_errors.password}}
							</p>

							<div class="cr-dashboard-text icon"
								:class="{'in-valid': (register_errors.repeat_password != '')}">
								<input autocomplete="off" type="password" name="repeat_password"
									@change="data.repeat_password.length > 0?check_password(2):''" class="ltr"
									v-model="data.repeat_password" value=""><label>تکرار رمز عبور</label><span
									class="icon repeat_password"></span><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="register_errors.repeat_password != ''" class="error-input">
								{{register_errors.repeat_password}}</p>

							<button :disabled="!register_valid()" @click="register()" type="button" ripple="ripple"
								ripple-color="#FFF" class="default-btn">
								<img v-if="data.register_loading"
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading">
								{{(data.register_loading)?'':'ثبت نام'}}
							</button>
							<p v-if="data.send_sms_error" class="sms-error">خطا در ارسال پیامک</p>
						</div>

						<div v-if="data.step == 4" class="col-12 col-md-12">
							<p class="text-center">لطفا کد ارسال شده به تلفن همراه خود را وارد کنید</p>
							<div class="cr-dashboard-text icon "
								:class="{'in-valid no-icon': (register_errors.sms != '')}"><input type="text"
									class="ltr" name="sms" @change="(data.sms.length > 0)?check_sms():''"
									v-model="data.sms" value=""><label>کد تایید</label><span
									class="icon sms"></span><span class="valid"></span><span
									class="in-valid"></span><img
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading"></div>
							<p v-if="data.sms_code_error" class="sms-error">کد تایید وارد شده اشتباه است</p>

							<span class="sms-titmer" v-if="!data.can_resend_sms">{{get_clock(data.timer)}}</span>
							<button @click="register()" :disabled="!data.can_resend_sms" class="resend_sms"
								type="button">ارسال مجدد پیامک </button>
							<button :disabled="data.sms.length != 5" @click="check_sms()" type="button"
								ripple="ripple" ripple-color="#FFF" class="default-btn">
								<img v-if="data.check_sms_loading"
									src="<?php echo plugin_dir_url( __FILE__ ).'files/assets/img/loading.svg'; ?>"
									class="loading">
								{{(data.check_sms_loading)?'':'تایید'}}
							</button>
						</div>
						<div v-if="data.step == 5" class="col-12 col-md-12">

							<p class="text-center">کاربر گرامی شما با موفقیت وارد حساب کاربری شدید. </p>

						</div>
						<div v-if="data.step == 6" class="col-12 col-md-12">

							<p class="text-center">کاربر گرامی ثبت نام شما با موفقیت انجام شد </p>

						</div>

					</form>
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
$result = $wpdb->get_results ( "SELECT * FROM $table_name LIMIT 1" )[0];
$items = json_decode( stripslashes($result->items));
$settings = json_decode($result->settings);
$all_settings = json_decode($result->all_settings);

if(!isset($all_settings->login_by_mobile)){
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


	 $plugin_url =plugin_dir_url( __FILE__ );
	 $admin_url = admin_url( 'admin-ajax.php' );
	 ?>
<!-- <script src="<?php echo plugin_dir_url( __FILE__ ).'/files/assets/components/login.vue.js'; ?>"></script> -->
<script>
// Vue.component("Login", Login);


const routes = [{
	//   path: "/",
	//   component: Login,
}];

// const router = new VueRouter({
// 	routes: routes,
// 	mode: "history",
// 	base: "/",
// });

var app = new Vue({
	el: "#cr-dashboard-login",
	// router: router,
	data() {
		return {
			admin_url : '<?php echo $admin_url; ?>',
    notifications_readed : [],
	dashboard_settings : JSON.parse('<?php echo json_encode($settings); ?>'),
      all_settings : JSON.parse('<?php echo json_encode($all_settings); ?>'),
  plugin_path : '<?php echo plugin_dir_url( __DIR__ ); ?>',
			typingTimer : '',
			doneTypingInterval : 1000,
			sms_timer : '',
			data : {
    mobile: '',
    sms: '',
    first_name: '',
    last_name: '',
    username: '',
    email: '',
    input_type: '',
    timer: 120,
    password: '',
    repeat_password: '',
    step: 1,
    is_login: false,
    check_mobile_loading: false,
    check_mobile_error: false,
    check_email_loading: false,
    check_username_loading: false,
    check_sms_loading: false,
    send_sms_loading: false,
    register_loading: false,
    send_sms_error: false,
    can_resend_sms: false,
    sms_code_error: false,
    login_password_loading: false,
    send_sms_login_loading: false,
    register_validate_username_loading: [],
  },
  
  register_errors : {
    first_name: '',
    last_name: '',
    mobile: '',
    username: '',
    email: '',
    password: '',
    repeat_password: '',
    sms: ''
  }



		};
	},
	methods: {

		ss_timer() {
			this.data.can_resend_sms = false
			this.data.timer--;
			// $scope.$apply();
			if (this.data.timer == 0)
				reset_sms_timer()
		},
		reset_sms_timer() {
			clearInterval(this.sms_timer);
			this.data.timer = 120
			this.data.can_resend_sms = true
			// $scope.$apply();
		},
		get_clock(timer) {
			return ((parseInt(timer / 60) > 10 ? parseInt(timer / 60) : '0' + parseInt(timer / 60)) + ':' + (parseInt(timer % 60) > 10 ? parseInt(timer % 60) : '0' + parseInt(timer % 60)))

		},
		register_valid() {
			if (
				((this.register_errors.first_name.length == 0 && this.all_settings.register_firstname == 'on' && this.data.first_name.length > 0) || this.all_settings.register_firstname == 'off') &&
				((this.register_errors.last_name.length == 0 && this.all_settings.register_lastname == 'on' && this.data.last_name.length > 0) || this.all_settings.register_lastname == 'off') &&
				((this.register_errors.email.length == 0 && this.all_settings.register_email == 'on' && this.data.email.length > 0) || this.all_settings.register_email == 'off') &&
				((this.register_errors.username.length == 0 && this.all_settings.register_username == 'on' && this.data.username.length > 0) || this.all_settings.register_username == 'off') &&
				this.register_errors.password.length == 0 &&
				this.register_errors.repeat_password.length == 0 &&
				this.data.password.length > 0 &&
				this.data.repeat_password.length > 0 &&
				this.data.password == this.data.repeat_password
			) {
				return true
			} else {
				return false
			}

		},
		onSubmit () {

    },
		mobile_valid() {
			var result = false
			if (this.all_settings.login_by_email == 'on' && this.all_settings.login_by_mobile == 'on' && this.all_settings.login_by_username == 'on') {
				if (this.data.mobile.length > 0)
					result = true
			} else if (this.all_settings.login_by_email == 'on' && this.all_settings.login_by_mobile == 'on' && this.all_settings.login_by_username != 'on') {
				if (new RegExp('^09\\d{9}$').test(this.data.mobile) || /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(this.data.mobile))
					result = true
				else
					result = false
			} else if (this.all_settings.login_by_email == 'on' && this.all_settings.login_by_mobile != 'on' && this.all_settings.login_by_username == 'on') {
				if (this.data.mobile.length > 0)
					result = true
			} else if (this.all_settings.login_by_email != 'on' && this.all_settings.login_by_mobile == 'on' && this.all_settings.login_by_username == 'on') {
				if (this.data.mobile.length > 0)
					result = true
			} else if (this.all_settings.login_by_email == 'on' && this.all_settings.login_by_mobile != 'on' && this.all_settings.login_by_username != 'on') {
				if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(this.data.mobile))
					result = true
				else
					result = false
			} else if (this.all_settings.login_by_email != 'on' && this.all_settings.login_by_mobile == 'on' && this.all_settings.login_by_username != 'on') {
				if (new RegExp('^09\\d{9}$').test(this.data.mobile))
					result = true
				else
					result = false
			} else if (this.all_settings.login_by_email != 'on' && this.all_settings.login_by_mobile != 'on' && this.all_settings.login_by_username == 'on') {
				if (this.data.mobile.length > 0)
					result = true
			}
			return result
		},
		remove_loading(type) {
			if (type == 'username') {
				this.data.check_username_loading = false
			} else if (type == 'email') {
				this.data.check_email_loading = false
			} else if (type == 'mobile') {
				this.data.check_mobile_loading = false
			}
			// $scope.$apply();
		},
		check_username(type) {
			const _this = this
			clearTimeout(this.typingTimer);
			if (type == 'username') {
				this.data.check_username_loading = true
			} else if (type == 'email') {
				this.data.check_email_loading = true
			} else if (type == 'mobile' && new RegExp('^09\\d{9}$').test(this.data.mobile)) {
				this.data.check_mobile_loading = true
			}

			this.typingTimer = setTimeout(function () {

				if (type == 'username') {
					_this.data.check_username_loading = true
				} else if (type == 'email') {
					_this.data.check_email_loading = true
				} else if (type == 'mobile' && new RegExp('^09\\d{9}$').test(this.data.mobile)) {
					_this.data.check_mobile_loading = true
				}
				// $scope.$apply();

				if (type != 'mobile' || (type == 'mobile' && new RegExp('^09\\d{9}$').test(this.data.mobile))) {
					$.ajax({
						type: "POST",
						url: _this.admin_url,
						data: {
							action: "hd_upanel_validate_username",
							email: _this.data.email,
							username: _this.data.username,
							mobile: _this.data.mobile,
						},
						success: function (results) {
							results = JSON.parse(results)
							_this.register_errors.email = results.email
							_this.register_errors.username = results.username
							_this.register_errors.mobile = results.mobile
							remove_loading(type)
						},
						error: function (results) {

							remove_loading(type)

						}
					});
				}

			}, this.doneTypingInterval);
		},
		login_by_password() {
			const _this = this
			this.data.login_password_loading = true
			$.ajax({
				type: "POST",
				url: _this.admin_url,
				data: {
					action: "hd_upanel_login_password",
					mobile: _this.data.mobile,
					'type': _this.data.input_type,
					password: _this.data.password
				},
				success: function (results) {
					_this.data.login_password_loading = false
					if (results == 'true') {

						_this.data.step = 5
						setTimeout(function () {
							location.reload();
						}, 2000);
					} else {
						_this.data.login_password_error = true
						setTimeout(function () {
							_this.data.login_password_error = false;
							// $scope.$apply();
						}, 2000);
					}
					// $scope.$apply();
				},
				error: function (results) {
					_this.data.login_password_loading = false
					// $scope.$apply();
				}
			});


		},
		send_sms_login() {
			const _this = this
			this.data.send_sms_login_loading = true

			$.ajax({
				type: "POST",
				url: _this.admin_url,
				data: {
					action: "hd_upanel_send_sms",
					mobile: _this.data.mobile,
					is_login: _this.data.is_login,
					'type': _this.data.input_type
				},
				success: function (results) {
					_this.data.send_sms_login_loading = false
					results = JSON.parse(results)
					if (results.status == 'success') {
						_this.sms_timer = setInterval(this.ss_timer, 1000);

						_this.data.step = 4
					} else if (results.status == 'notfound') {
						_this.data.send_sms_error = true
						setTimeout(function () {
							_this.data.send_sms_error = false;
							// $scope.$apply();
						}, 2000);

					} else {
						_this.data.send_sms_error = true
						setTimeout(function () {
							_this.data.send_sms_error = false;
							// $scope.$apply();
						}, 2000);
					}
					// $scope.$apply();
				},
				error: function (results) {
					_this.data.send_sms_login_loading = false
					// $scope.$apply();
				}
			});
		},
		register() {
			const _this = this
			this.data.can_resend_sms = false
			this.data.register_loading = true
			this.data.send_sms_loading = true
			console.log(this.all_settings)
			if (this.all_settings['register_sms'] == 'on') {

				$.ajax({
					type: "POST",
					url: _this.admin_url,
					data: {
						action: "hd_upanel_send_sms",
						mobile: _this.data.mobile,
						is_login: _this.data.is_login,
						'type': _this.data.input_type
					},
					success: function (results) {
						_this.data.send_sms_loading = false
						_this.data.register_loading = false
						results = JSON.parse(results)

						if (results.status == 'success') {
							_this.sms_timer = setInterval(_this.ss_timer, 1000);
							_this.data.step = 4
						} else if (results.status == 'notfound') {
							_this.data.send_sms_error = true
							setTimeout(function () {
								_this.data.send_sms_error = false;
								// $scope.$apply();
							}, 2000);
						} else {
							_this.data.send_sms_error = true
							setTimeout(function () {
								_this.data.send_sms_error = false;
								// $scope.$apply();
							}, 2000);
						}


						// $scope.$apply();
					},
					error: function (results) {
						_this.data.send_sms_loading = false
						_this.data.register_loading = false
						// $scope.$apply();
					}
				});

			} else {

				$.ajax({
					type: "POST",
					url: _this.admin_url,
					data: {
						action: "hd_upanel_register_without_sms",
						mobile: _this.data.mobile,
						data: _this.data,
						is_login: _this.data.is_login,
						'type': _this.data.input_type
					},
					success: function (results) {
						_this.data.send_sms_loading = false
						if (results == 'true') {
							if (_this.data.is_login) {
								_this.data.step = 5
								setTimeout(function () {
									location.reload();
								}, 2000);
							} else {
								_this.data.step = 6
								setTimeout(function () {
									location.reload();
								}, 2000);
							}
						} else {
							_this.data.sms_code_error = true
							_this.data.sms = ''
							setTimeout(function () {
								_this.data.sms_code_error = false;
								// $scope.$apply();
							}, 2000);
						}


						// $scope.$apply();
					},
					error: function (results) {
						_this.data.send_sms_loading = false
						// $scope.$apply();
					}
				});

			}

		},
		check_sms() {
			const _this = this
			if (this.data.sms.length == 5) {
				this.data.check_sms_loading = true
				$.ajax({
					type: "POST",
					url: _this.admin_url,
					data: {
						action: "hd_upanel_verify_sms",
						mobile: _this.data.mobile,
						sms: _this.data.sms,
						data: _this.data,
						is_login: _this.data.is_login,
						'type': _this.data.input_type
					},
					success: function (results) {
						_this.data.check_sms_loading = false
						if (results == 'true') {
							if (_this.data.is_login) {
								_this.data.step = 5
								setTimeout(function () {
									location.reload();
								}, 2000);
							} else {
								_this.data.step = 6
								setTimeout(function () {
									location.reload();
								}, 2000);
							}
						} else {
							_this.data.sms_code_error = true
							_this.data.sms = ''
							setTimeout(function () {
								_this.data.sms_code_error = false;
								// $scope.$apply();
							}, 2000);
						}


						// $scope.$apply();
					},
					error: function (results) {
						_this.data.check_sms_loading = false
						// $scope.$apply();
					}
				});
			}
		},
		check_first_name() {
			if (this.data.first_name.length == 0) {
				this.register_errors.first_name = 'وارد کردن  نام اجباری است'
			} else if (this.data.first_name.length != 0) {
				this.register_errors.first_name = ''
			}
		},
		check_last_name() {
			if (this.data.last_name.length == 0) {
				this.register_errors.last_name = 'وارد کردن  نام اجباری است'
			} else if (this.data.last_name.length != 0) {
				this.register_errors.last_name = ''
			}
		},
		check_password(type) {
			console.log(type)
			if (type == 1) {
				if (this.data.password.length == 0) {
					this.register_errors.password = 'وارد کردن رمز عبور اجباری است'
				} else if (this.data.password.length < 7) {
					this.register_errors.password = 'رمز عبور باید حداقل 8 کاراکتر باشد'
				} else if (this.data.password.length >= 7) {
					this.register_errors.password = ''
				}
			} else if (type == 2) {

				if (this.data.repeat_password != this.data.password) {
					this.register_errors.repeat_password = 'رمز عبور و تکرار رمز عبور مطابقت ندارد'
				} else if (this.data.repeat_password.length == 0) {
					this.register_errors.repeat_password = 'وارد کردن تکرار رمز عبور اجباری است'
				} else {
					this.register_errors.repeat_password = ''
				}
			}
		},
		check_mobile() {
			const _this = this
			var type = ''
			this.data.check_mobile_loading = true
			if (new RegExp('^09\\d{9}$').test(this.data.mobile)) {
				type = 'mobile'
			} else if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(this.data.mobile)) {
				type = 'email'
			} else {
				type = 'username'
			}
			this.data.input_type = type
			$.ajax({
				type: "POST",
				url: _this.admin_url,
				data: {
					action: "hd_upanel_check_mobile",
					mobile: _this.data.mobile,
					'type': type
				},
				success: function (results) {
					if (results == 'true') {
					
						_this.data.step = 2
						_this.data.is_login = true
					} else if (results == 'error') {
						_this.data.check_mobile_error = true
						setTimeout(function () {
							_this.data.check_mobile_error = false;
							// $scope.$apply();
						}, 2000);
					} else {

						if (type == 'username') {

							_this.data.username = _this.data.mobile
							_this.data.mobile = ''
						} else if (type == 'email') {

							_this.data.email = _this.data.mobile
							_this.data.mobile = ''
						} else if (type == 'mobile') {}

						_this.data.step = 3
						_this.data.is_login = false
					}
					_this.data.check_mobile_loading = false

					// $scope.$apply();

				},
				error: function (results) {
					_this.data.check_mobile_loading = false
				}
			});
		}
	},
});








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


//     var app = angular.module('cr-dashboard-login', [ 'ngRoute', 'ngSanitize']);


//     app.run(function ($rootScope, $location) {
//      $rootScope.admin_url = '<?php echo $admin_url; ?>'
//     $rootScope.notifications_readed = [];
// 	$rootScope.dashboard_settings = JSON.parse('<?php echo json_encode($settings); ?>');
//       this.all_settings = JSON.parse('<?php echo json_encode($all_settings); ?>');

//     $rootScope.number_fa = function (number) {
//         let string = number.toLocaleString('fa-IR');
//         string = string.replace(/\٬/g, ",‬").replaceAll(",", "");
//         return string;
//       }

//   $rootScope.plugin_path = '<?php echo plugin_dir_url( __DIR__ ); ?>'

// 	})
</script>