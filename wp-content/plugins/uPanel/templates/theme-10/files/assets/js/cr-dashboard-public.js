var $ = jQuery;
$(document).ready(function() {

if($('.hd-upanel-login').length){
	setTimeout(function(){ $('#hd-upanel-loading').fadeOut(); }, 1000);
}
   
$('[ripple]').on('click', function(e) {

    var rippleDiv = $('<div class="ripple" />'),
      rippleOffset = $(this).offset(),
      rippleY = e.pageY - rippleOffset.top,
      rippleX = e.pageX - rippleOffset.left,
      ripple = $('.ripple');

    rippleDiv.css({
      top: rippleY - (ripple.height() / 2),
      left: rippleX - (ripple.width() / 2),
      background: $(this).attr("ripple-color")
    }).appendTo($(this));

    window.setTimeout(function() {
      rippleDiv.remove();
    }, 1500);
  });
	
	$('button.register , .register-btns #register').click(function () {
		// document.cookie = "tab=register";
		// $('.hd-upanel-login').removeClass('login')
		// $('.hd-upanel-login').addClass('register')
	})

	$('button.login , .login-btns #login').click(function () {
		document.cookie = "tab=login";
		$('.hd-upanel-login').removeClass('register')
		$('.hd-upanel-login').addClass('login')
	})

	$('#register-button').on('click',function(e){
		console.log('test')
        e.preventDefault();
        var newUserName = $('#new-username').val();
        var newUserEmail = $('#new-useremail').val();
        var newUserPassword = $('#new-userpassword').val();
        $.ajax({
          type:"POST",
          url:$('#admin-ajax').val(),
          data: {
            action: "register_user_front_end",
            new_user_name : newUserName,
            new_user_email : newUserEmail,
            new_user_password : newUserPassword
          },
          success: function(results){
            console.log(results);
            $('.register-message').text(results).show();
          },
          error: function(results) {

          }
        });
      });

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write $ code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})
