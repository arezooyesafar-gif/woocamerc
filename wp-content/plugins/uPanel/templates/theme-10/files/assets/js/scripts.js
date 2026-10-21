// app.controller('dashboard', function ($scope, $rootScope) {
//   $scope.data = {
//     modal: 'list',
//   }
//   $scope.addservice = function () {
//     console.log('test')
//   }
//   $scope.get_price = function (html) {


//     let txt = document.createElement("textarea");
//   txt.innerHTML = html;
//       let parser = new DOMParser();
//   let doc = parser.parseFromString(txt.value, 'text/html');
//   let price = doc.getElementsByTagName("bdi")[0].textContent

//   let label = (price.includes('تومان')?'تومان':'ریال')
//   return [price.replace(/\D/g,'').replace("تومان", "").replace("ريال", ""),label];
  
  
  
//     }
// })

// app.controller('notifications', function ($scope, $rootScope) {




// })
// app.controller('tickets', function ($scope, $rootScope) {



//   $scope.data = {
//     modal: 'list',
//     ticket : {},
//     replay:{
//       ticket_id : 0,
//       title : '',
//       ticket_text:'',
//       ticket_priority : '0',
//     parent : 0,
//     department : '',
//     ticket_status : 0,
//     user_id : $rootScope.user['ID'],
//     ticket_type : 'user'
//     }
//   }
  
//   $scope.applyer = function () {
    
//     $scope.$apply(function () {
//       $scope.data = {
//         modal: 'new',
//         ticket : {},
//         replay:{
//           ticket_id : 0,
//           title : '',
//           ticket_text:'',
//           ticket_priority : '0',
//         parent : 0,
//         department : '',
//         ticket_status : 0,
//         user_id : $rootScope.user['ID'],
//         ticket_type : 'user'
//         }
//       }
//  });

// console.log($scope.data.modal)

//   }
//   $scope.reply = function () {
//     $scope.loading = true
//     $scope.data.replay.parent = $scope.data.ticket.id
//      $.ajax({
//         type: "POST",
//         url: $rootScope.admin_url,
//         data: {
//           action: "hd-upanel-save-ticket",
//           data: $scope.data.replay
//         },
//         success: function (results) {
//           $scope.loading = false
  
//           $scope.data.ticket.childs.push({
//             ticket_id : 0,
//             title : '',
//             ticket_text:$scope.data.replay.ticket_text,
//             ticket_priority : '0',
//           parent : 0,
//           department : '',
//           ticket_status : 0,
//           user_id : $rootScope.user['ID'],
//           ticket_type : 'user'
//           })
//           $scope.data.replay.ticket_text = ''
//           $scope.$apply();
  
//         },
//         error: function (results) {
//           $scope.data.loading = false
//           $scope.$apply();
//         }
//       });
//     }

// $scope.new_modal = false;
// $scope.show_modal = false;

// $scope.new_ticket_success_modal = false;
// $scope.new_ticket = {
//   ticket_id : Math.floor(Math.random() * 899 ) + 100,
//   title : '',
//   ticket_text:'',
//   ticket_priority : '0',
// parent : 0,
// department : '',
// ticket_status : 0,
// user_id : $rootScope.user['ID'],
// ticket_type : 'user'
// }

// $scope.loading = false


// $scope.ticket_number = function (id,ticket_id) {

// return parseInt(id)+100+ticket_id

// }



// $scope.save_ticket = function () {
//   $scope.loading = true
//    $.ajax({
//       type: "POST",
//       url: $rootScope.admin_url,
//       data: {
//         action: "hd-upanel-save-ticket",
//         data: $scope.new_ticket
//       },
//       success: function (results) {
//         $scope.loading = false
//         location.reload();
//         $scope.$apply();

//       },
//       error: function (results) {
//         $scope.data.loading = false
//         $scope.$apply();
//       }
//     });
//   }


// })
// app.controller('header', function ($scope, $rootScope) {
//   $scope.data = {
//     test: 'full',
//   }
// })

// app.controller('html', ['$scope','$routeParams', function($scope, $routeParams) {

//   $scope.data = {
//     test: 'full',
//   }
// }])



// app.controller('settings', function ($scope, $rootScope) {

//   $scope.data = {
//     addpic: '',
//     addpic_url: '',
//     picture: '',
//     tab: 'user',
//     old_password: '',
//     password: '',
//     repeat_password: '',
//     first_name: $rootScope.user.first_name,
//     last_name: $rootScope.user.last_name,
//     nickname: $rootScope.user.nickname,
//     email: $rootScope.user.user_email,
//     loading: false,
//     success_massage: false,
//     error_massage: false
//   }



//   $scope.open_image_selector = function () {
//     $('#file-upload').click();
//   }
//   $(document).on('change', '#file-upload', function (event) {
//     var input = $(this)
//     if (event.target.files && event.target.files[0]) {
//       let urls = [];
//       urls = event.target.files;

//       if (!event.target.files[0]['type'].includes('image')) {
//         // error = 'فرمت فایل شما صحیح نمی باشد';
//       } else {

//         for (let i = 0; i < urls.length; i++) {
//           $scope.addpic_url = urls[0]
//           $scope.$apply();
//           const reader = new FileReader();
//           reader.readAsDataURL(urls[0]);
//           reader.onload = (event01) => {
//             input.next().attr('src', event01.target.result)
//             $scope.data.addpic = event01.target.result
//             // .replace("data:image/jpeg;base64,", "")
//             $scope.$apply();
//           };
//           // this.api.upload_profilepic(urls[i],name[i],'users') 
//         }

//       }
//     }
//   });



//   $rootScope.edit_user = function () {
//     $scope.data.loading = true
//     var user_data
//     if ($scope.data.addpic.length > 0) {
//       user_data = {
//         first_name: $scope.data.first_name,
//         last_name: $scope.data.last_name,
//         email: $scope.data.email,
//         nickname: $scope.data.nickname,
//         image: $scope.data.addpic.replace("data:image/jpeg;base64,", "").replace("data:image/png;base64,", "")
//       }
//     } else {
//       user_data = {
//         first_name: $scope.data.first_name,
//         last_name: $scope.data.last_name,

//         email: $scope.data.email,
//         nickname: $scope.data.nickname
//       }
//     }

//     $.ajax({
//       type: "POST",
//       url: $rootScope.admin_url,
//       data: {
//         action: "register_update_user_info",
//         data: user_data,
//         user_id: $rootScope.user['ID'],
//       },
//       success: function (results) {
//         $rootScope.user['first_name'] = user_data.first_name
//         $rootScope.user['last_name'] = user_data.last_name

//         $scope.data.success_massage = true
//         $scope.data.loading = false
//         setTimeout(function () {
//           $scope.data.success_massage = false
//         }, 3000);
//         $scope.$apply();
//         console.log(results);
//       },
//       error: function (results) {
//         $scope.data.loading = false
//       }
//     });
//   }
//   $rootScope.edit_password = function () {
//     $scope.data.loading = true
//     $.ajax({
//       type: "POST",
//       url: $rootScope.admin_url,
//       data: {
//         action: "register_update_user_password",
//         password: $scope.data.old_password,
//         user_id: $rootScope.user['ID'],
//         new_password: $scope.data.password,
//       },
//       success: function (results) {
//         results = JSON.parse(results.substring(0, results.length - 1))
//         if (results.code == '200') {
//           $scope.data.success_massage = true
//           setTimeout(function () {
//             $scope.data.success_massage = false;
//             $scope.$apply();
//           }, 3000);
//         } else {
//           $scope.data.error_massage = true
//           setTimeout(function () {
//             $scope.data.success_massage = false;
//             $scope.$apply();
//           }, 3000);
//         }
//         $scope.data.loading = false
//         console.log(results);
//         $scope.$apply();

//       },
//       error: function (results) {

//         $scope.data.error_massage = true
//         setTimeout(function () {
//           $scope.data.success_massage = false;
//           $scope.$apply();
//         }, 3000);
//         $scope.data.loading = false
//       }
//     });
//   }

// })

// app.controller('downloads', function ($scope, $rootScope) {
//   $scope.data = {
//     tab: 'user'
//   }
// })

// app.controller('orders', function ($scope, $rootScope) {
//   $scope.data = {
//     test: 'full',
//     filter: ['completed', 'pending', 'processing', 'on-hold', 'cancelled', 'refunded', 'failed'],
//     single_order: ''
//   }

//   $scope.get_price = function (html) {


//     let txt = document.createElement("textarea");
//   txt.innerHTML = html;
//       let parser = new DOMParser();
//   let doc = parser.parseFromString(txt.value, 'text/html');
//   let price = doc.getElementsByTagName("bdi")[0].textContent
  
//   let label = (price.includes('تومان')?'تومان':'ریال')
//   return [price.replace(/\D/g,'').replace("تومان", "").replace("ريال", ""),label];
  
  
  
//     }
    
// })


// app.controller('addresses', function ($scope, $rootScope) {
//   $scope.data = {
//     test: 'full',
//     edit_type: '',
//     edit: '',
//     edit_title: '',
//     loading: false,
//     success_massage: false
//   }
//   $scope.edit_billing = function () {
//     $scope.data.loading = true
//     var edit_data
//     if ($scope.data.edit_type == 'billing') {
//       edit_data = {
//         'billing_first_name': $scope.data.edit.first_name,
//         'billing_last_name': $scope.data.edit.last_name,
//         'billing_city': $scope.data.edit.city,
//         'billing_postcode': $scope.data.edit.postcode,
//         'billing_phone': $scope.data.edit.phone,
//         'billing_company': $scope.data.edit.company,
//         'billing_address_1': $scope.data.edit.address_1,
//         'billing_address_2': $scope.data.edit.address_2,
//         'billing_country': $scope.data.edit.country,
//         'billing_state': $scope.data.edit.state,
//       }
//     } else {
//       edit_data = {
//         'shipping_first_name': $scope.data.edit.first_name,
//         'shipping_last_name': $scope.data.edit.last_name,
//         'shipping_city': $scope.data.edit.city,
//         'shipping_postcode': $scope.data.edit.postcode,
//         'shipping_phone': $scope.data.edit.phone,
//         'shipping_company': $scope.data.edit.company,
//         'shipping_address_1': $scope.data.edit.address_1,
//         'shipping_address_2': $scope.data.edit.address_2,
//         'shipping_country': $scope.data.edit.country,
//         'shipping_state': $scope.data.edit.state,
//       }
//     }

//     $.ajax({
//       type: "POST",
//       url: $rootScope.admin_url,
//       data: {
//         action: "update_billing_address",
//         data: edit_data,
//         user_id: $rootScope.user['ID']
//       },
//       success: function (results) {
//         $scope.data.loading = false
//         $scope.data.success_massage = true
//         setTimeout(function () {
//           $scope.data.success_massage = false;
//           $scope.$apply();
//         }, 3000);
//         console.log(results);
//         $scope.$apply();

//       },
//       error: function (results) {
//         $scope.data.loading = false
//       }
//     });
//   }

// })


// app.controller('sidemenu', function ($scope, $rootScope) {

//   $scope.menu_collapse = function () {

//     if ($rootScope.menu == 'full') {
//       $rootScope.menu = 'mini'
//     } else {
//       $rootScope.menu = 'full'
//     }

//     document.cookie = "upanel-menu=" + $rootScope.menu;
//   }


// })

// app.controller('login', function ($scope, $rootScope) {
//   $scope.data = {
//     mobile: '',
//     sms: '',
//     first_name: '',
//     last_name: '',
//     username: '',
//     email: '',
//     input_type: '',
//     timer: 120,
//     password: '',
//     repeat_password: '',
//     step: 1,
//     is_login: false,
//     check_mobile_loading: false,
//     check_mobile_error: false,
//     check_email_loading: false,
//     check_username_loading: false,
//     check_sms_loading: false,
//     send_sms_loading: false,
//     register_loading: false,
//     send_sms_error: false,
//     can_resend_sms: false,
//     sms_code_error: false,
//     login_password_loading: false,
//     send_sms_login_loading: false,
//     register_validate_username_loading: [],
//   }
  
//   $scope.register_errors = {
//     first_name: '',
//     last_name: '',
//     mobile: '',
//     username: '',
//     email: '',
//     password: '',
//     repeat_password: '',
//     sms: ''
//   }


//   let sms_timer

//   function ss_timer() {
//     $scope.data.can_resend_sms = false
//     $scope.data.timer--;
//     $scope.$apply();
//     if ($scope.data.timer == 0)
//       reset_sms_timer()
//   }

//   function reset_sms_timer() {
//     clearInterval(sms_timer);
//     $scope.data.timer = 120
//     $scope.data.can_resend_sms = true
//     $scope.$apply();
//   }



//   $scope.get_clock = function (timer) {
//     return ((parseInt(timer / 60) > 10 ? parseInt(timer / 60) : '0' + parseInt(timer / 60)) + ':' + (parseInt(timer % 60) > 10 ? parseInt(timer % 60) : '0' + parseInt(timer % 60)))

//   }

//   $scope.register_valid = function () {
    


//     if (
//       (($scope.register_errors.first_name.length == 0 && $rootScope.all_settings.register_firstname == 'on' && $scope.data.first_name.length > 0) || $rootScope.all_settings.register_firstname == 'off') &&
//       (($scope.register_errors.last_name.length == 0 && $rootScope.all_settings.register_lastname == 'on' && $scope.data.last_name.length > 0) || $rootScope.all_settings.register_lastname == 'off') &&
//       (($scope.register_errors.email.length == 0 && $rootScope.all_settings.register_email == 'on' && $scope.data.email.length > 0) || $rootScope.all_settings.register_email == 'off') &&
//       (($scope.register_errors.username.length == 0 && $rootScope.all_settings.register_username == 'on' && $scope.data.username.length > 0) || $rootScope.all_settings.register_username == 'off') &&


//       $scope.register_errors.password.length == 0 &&
//       $scope.register_errors.repeat_password.length == 0 &&

//       $scope.data.password.length > 0 &&
//       $scope.data.repeat_password.length > 0 && 
//       $scope.data.password == $scope.data.repeat_password 
//     ) {
//       return true
//     } else {
//       return false
//     }

//   }



//   $scope.mobile_valid = function () {
//     var result = false
//     if ($rootScope.all_settings.login_by_email == 'on' && $rootScope.all_settings.login_by_mobile == 'on' && $rootScope.all_settings.login_by_username == 'on') {
//       if ($scope.data.mobile.length > 0)
//         result = true
//     } else if ($rootScope.all_settings.login_by_email == 'on' && $rootScope.all_settings.login_by_mobile == 'on' && $rootScope.all_settings.login_by_username != 'on') {
//       if (new RegExp('^09\\d{9}$').test($scope.data.mobile) || /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test($scope.data.mobile))
//         result = true
//       else
//         result = false
//     } else if ($rootScope.all_settings.login_by_email == 'on' && $rootScope.all_settings.login_by_mobile != 'on' && $rootScope.all_settings.login_by_username == 'on') {
//       if ($scope.data.mobile.length > 0)
//         result = true
//     } else if ($rootScope.all_settings.login_by_email != 'on' && $rootScope.all_settings.login_by_mobile == 'on' && $rootScope.all_settings.login_by_username == 'on') {
//       if ($scope.data.mobile.length > 0)
//         result = true
//     } else if ($rootScope.all_settings.login_by_email == 'on' && $rootScope.all_settings.login_by_mobile != 'on' && $rootScope.all_settings.login_by_username != 'on') {
//       if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test($scope.data.mobile))
//         result = true
//       else
//         result = false
//     } else if ($rootScope.all_settings.login_by_email != 'on' && $rootScope.all_settings.login_by_mobile == 'on' && $rootScope.all_settings.login_by_username != 'on') {
//       if (new RegExp('^09\\d{9}$').test($scope.data.mobile))
//         result = true
//       else
//         result = false
//     } else if ($rootScope.all_settings.login_by_email != 'on' && $rootScope.all_settings.login_by_mobile != 'on' && $rootScope.all_settings.login_by_username == 'on') {
//       if ($scope.data.mobile.length > 0)
//         result = true
//     }


//     return result


//   }




//   function remove_loading(type) {

//     if (type == 'username') {
//       $scope.check_username_loading = false
//     } else if (type == 'email') {
//       $scope.check_email_loading = false
//     }else if (type == 'mobile') {
//       $scope.check_mobile_loading = false
//     }

 

//     $scope.$apply();
//   }



//     var typingTimer;  
// var doneTypingInterval = 1000; 



//   $scope.check_username = function (type) {
//     clearTimeout(typingTimer);
//     if (type == 'username') {
//       $scope.check_username_loading = true
//     } else if (type == 'email') {
//       $scope.check_email_loading = true
//     }else if (type == 'mobile'  &&  new RegExp('^09\\d{9}$').test($scope.data.mobile)) {
//       $scope.check_mobile_loading = true
//     }

//     typingTimer = setTimeout(function () {
      
//       if (type == 'username') {
//         $scope.check_username_loading = true
//       } else if (type == 'email') {
//         $scope.check_email_loading = true
//       }else if (type == 'mobile'  &&  new RegExp('^09\\d{9}$').test($scope.data.mobile)) {
//         $scope.check_mobile_loading = true
//       }
//       $scope.$apply();

// if(type != 'mobile' || (type == 'mobile' &&  new RegExp('^09\\d{9}$').test($scope.data.mobile))){
//   $.ajax({
//     type: "POST",
//     url: $rootScope.admin_url,
//     data: {
//       action: "hd_upanel_validate_username",
//       email: $scope.data.email,
//       username: $scope.data.username,
//       mobile: $scope.data.mobile,
//     },
//     success: function (results) {
//       results = JSON.parse(results)

    


//       $scope.register_errors.email = results.email
//       $scope.register_errors.username = results.username
//       $scope.register_errors.mobile = results.mobile
//       remove_loading(type)
//     },
//     error: function (results) {
 
//         remove_loading(type)
      
//     }
//   });
// }

//     }, doneTypingInterval);





//   }





//   $scope.login_by_password = function () {
//     $scope.data.login_password_loading = true
//     $.ajax({
//       type: "POST",
//       url: $rootScope.admin_url,
//       data: {
//         action: "hd_upanel_login_password",
//         mobile: $scope.data.mobile,
//         'type': $scope.data.input_type,
//         password: $scope.data.password
//       },
//       success: function (results) {
//         $scope.data.login_password_loading = false
//         if (results == 'true') {

//           $scope.data.step = 5
//           setTimeout(function () {
//             location.reload();
//           }, 2000);
//         } else {
//           $scope.data.login_password_error = true
//           setTimeout(function () {
//             $scope.data.login_password_error = false;
//             $scope.$apply();
//           }, 2000);
//         }
//         $scope.$apply();
//       },
//       error: function (results) {
//         $scope.data.login_password_loading = false
//         $scope.$apply();
//       }
//     });


//   }
//   $scope.send_sms_login = function () {
//     $scope.data.send_sms_login_loading = true

//     $.ajax({
//       type: "POST",
//       url: $rootScope.admin_url,
//       data: {
//         action: "hd_upanel_send_sms",
//         mobile: $scope.data.mobile,
//         is_login: $scope.data.is_login,
//         'type': $scope.data.input_type
//       },
//       success: function (results) {
//         $scope.data.send_sms_login_loading = false
//         results = JSON.parse(results)
//         if (results.status == 'success') {
//           sms_timer = setInterval(ss_timer, 1000);

//           $scope.data.step = 4
//         } else if (results.status == 'notfound') {
//           $scope.data.send_sms_error = true
//           setTimeout(function () {
//             $scope.data.send_sms_error = false;
//             $scope.$apply();
//           }, 2000);

//         } else {
//           $scope.data.send_sms_error = true
//           setTimeout(function () {
//             $scope.data.send_sms_error = false;
//             $scope.$apply();
//           }, 2000);
//         }
//         $scope.$apply();
//       },
//       error: function (results) {
//         $scope.data.send_sms_login_loading = false
//         $scope.$apply();
//       }
//     });




//   }



//   $scope.register = function () {
//     $scope.data.can_resend_sms = false
//     $scope.data.register_loading = true
//     $scope.data.send_sms_loading = true
//     console.log($rootScope.all_settings)
//     if ($rootScope.all_settings['register_sms'] == 'on') {

//       $.ajax({
//         type: "POST",
//         url: $rootScope.admin_url,
//         data: {
//           action: "hd_upanel_send_sms",
//           mobile: $scope.data.mobile,
//           is_login: $scope.data.is_login,
//           'type': $scope.data.input_type
//         },
//         success: function (results) {
//           $scope.data.send_sms_loading = false
//           $scope.data.register_loading = false
//           results = JSON.parse(results)

//           if (results.status == 'success') {
//             sms_timer = setInterval(ss_timer, 1000);
//             $scope.data.step = 4
//           } else if (results.status == 'notfound') {
//             $scope.data.send_sms_error = true
//             setTimeout(function () {
//               $scope.data.send_sms_error = false;
//               $scope.$apply();
//             }, 2000);
//           } else {
//             $scope.data.send_sms_error = true
//             setTimeout(function () {
//               $scope.data.send_sms_error = false;
//               $scope.$apply();
//             }, 2000);
//           }


//           $scope.$apply();
//         },
//         error: function (results) {
//           $scope.data.send_sms_loading = false
//           $scope.data.register_loading = false
//           $scope.$apply();
//         }
//       });

//     } else {

//       $.ajax({
//         type: "POST",
//         url: $rootScope.admin_url,
//         data: {
//           action: "hd_upanel_register_without_sms",
//           mobile: $scope.data.mobile,
//           data: $scope.data,
//           is_login: $scope.data.is_login,
//           'type': $scope.data.input_type
//         },
//         success: function (results) {
//           $scope.data.send_sms_loading = false
//           if (results == 'true') {
//             if ($scope.data.is_login) {
//               $scope.data.step = 5
//               setTimeout(function () {
//                 location.reload();
//               }, 2000);
//             } else {
//               $scope.data.step = 6
//               setTimeout(function () {
//                 location.reload();
//               }, 2000);
//             }
//           } else {
//             $scope.data.sms_code_error = true
//             $scope.data.sms = ''
//             setTimeout(function () {
//               $scope.data.sms_code_error = false;
//               $scope.$apply();
//             }, 2000);
//           }


//           $scope.$apply();
//         },
//         error: function (results) {
//           $scope.data.send_sms_loading = false
//           $scope.$apply();
//         }
//       });

//     }

//   }



//   $scope.check_sms = function () {
//     if ($scope.data.sms.length == 5) {
//       this.data.check_sms_loading = true
//       $.ajax({
//         type: "POST",
//         url: $rootScope.admin_url,
//         data: {
//           action: "hd_upanel_verify_sms",
//           mobile: $scope.data.mobile,
//           sms: $scope.data.sms,
//           data: $scope.data,
//           is_login: $scope.data.is_login,
//           'type': $scope.data.input_type
//         },
//         success: function (results) {
//           $scope.data.check_sms_loading = false
//           if (results == 'true') {
//             if ($scope.data.is_login) {
//               $scope.data.step = 5
//               setTimeout(function () {
//                 location.reload();
//               }, 2000);
//             } else {
//               $scope.data.step = 6
//               setTimeout(function () {
//                 location.reload();
//               }, 2000);
//             }
//           } else {
//             $scope.data.sms_code_error = true
//             $scope.data.sms = ''
//             setTimeout(function () {
//               $scope.data.sms_code_error = false;
//               $scope.$apply();
//             }, 2000);
//           }


//           $scope.$apply();
//         },
//         error: function (results) {
//           $scope.data.check_sms_loading = false
//           $scope.$apply();
//         }
//       });
//     }
//   }


//   $scope.check_first_name = function () {
//     if ($scope.data.first_name.length == 0) {
//       $scope.register_errors.first_name = 'وارد کردن  نام اجباری است'
//     } else if ($scope.data.first_name.length != 0) {
//       $scope.register_errors.first_name = ''
//     }
//   }
//   $scope.check_last_name = function () {
//     if ($scope.data.last_name.length == 0) {
//       $scope.register_errors.last_name = 'وارد کردن  نام اجباری است'
//     } else if ($scope.data.last_name.length != 0) {
//       $scope.register_errors.last_name = ''
//     }
//   }




//   $scope.check_password = function (type) {
//     console.log(type)
//     if (type == 1) {
//       if ($scope.data.password.length == 0) {
//         $scope.register_errors.password = 'وارد کردن رمز عبور اجباری است'
//       } else if ($scope.data.password.length < 7) {
//         $scope.register_errors.password = 'رمز عبور باید حداقل 8 کاراکتر باشد'
//       } else if ($scope.data.password.length >= 7) {
//         $scope.register_errors.password = ''
//       }
//     } else if (type == 2) {

//       if ($scope.data.repeat_password != $scope.data.password) {
//         $scope.register_errors.repeat_password = 'رمز عبور و تکرار رمز عبور مطابقت ندارد'
//       } else if ($scope.data.repeat_password.length == 0) {
//         $scope.register_errors.repeat_password = 'وارد کردن تکرار رمز عبور اجباری است'
//       } else {
//         $scope.register_errors.repeat_password = ''
//       }

//     }

//   }

//   $scope.check_mobile = function () {
//     var type = ''
//     $scope.data.check_mobile_loading = true
//     if (new RegExp('^09\\d{9}$').test($scope.data.mobile)) {
//       type = 'mobile'
//     } else if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test($scope.data.mobile)) {
//       type = 'email'
//     } else {
//       type = 'username'
//     }
//     $scope.data.input_type = type

//     $.ajax({
//       type: "POST",
//       url: $rootScope.admin_url,
//       data: {
//         action: "hd_upanel_check_mobile",
//         mobile: $scope.data.mobile,
//         'type': type
//       },
//       success: function (results) {
//         if (results == 'true') {
//           $scope.data.step = 2
//           $scope.data.is_login = true
//         } else if (results == 'error') {
//           $scope.data.check_mobile_error = true
//           setTimeout(function () {
//             $scope.data.check_mobile_error = false;
//             $scope.$apply();
//           }, 2000);
//         } else {
          
//           if (type == 'username') {
         
//             $scope.data.username = $scope.data.mobile
//             $scope.data.mobile = ''
//           } else if (type == 'email') {
      
//             $scope.data.email = $scope.data.mobile
//             $scope.data.mobile = ''
//           }else if (type == 'mobile') {
//           }

//           $scope.data.step = 3
//           $scope.data.is_login = false
//         }
//         $scope.data.check_mobile_loading = false

//         $scope.$apply();

//       },
//       error: function (results) {
//         $scope.data.check_mobile_loading = false
//       }
//     });
//   }



// })

// app.controller('register', function ($scope, $rootScope) {




// })
