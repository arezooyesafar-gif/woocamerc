jQuery(function($) {
$(document).ready(function() {


	
	$('textarea[name=ticket_text]').on('keyup',function(e){
		if($(this).val().length > 0){
			$('button[name=massage]').prop('disabled', false);
		}else{
			$('button[name=massage]').prop('disabled', true);
		}
	
	   });

	$('body').on('click', '.ticket-departmets .plus' ,function(){

		
		var input = `
		<div>
		<button type="button" class="minus">-</button>
		<input class="mt-1" type="text"  name="departmets-[]" value="">
		
		<button type="button" class="plus">+</button>
		
		</div>
		`;
		$(input).appendTo(".ticket-departmets")
	  });

	  $('body').on('click', '.ticket-departmets .minus' ,function(){
$(this).parent().remove();
	  });




	$('input[name="user_type"]').on('change', function() {
		console.log(this.value)
if(this.value == 0){
	$('.users').hide()
}else{
	$('.users').show()
}


	})

	
	$('input[name="email_encode"]').on('change', function() {
		console.log(this.value)
if(this.value == 1){
	$('input[name="email_smtp"]').val('465')
}else if(this.value == 2){
	$('input[name="email_smtp"]').val('587')
}else if(this.value == 3){
	$('input[name="email_smtp"]').val('25')
}


	})



	var dialog = document.querySelector('dialog');
	$('.hd-upanel-notification-delete').click(function () {
		$('#delete_id').val($(this).attr('data-id'))
		dialog.showModal();
	})

	$('.select2-users').select2({
		placeholder: 'انتخاب کاربر',
	  });

	$('#register_by_mobile').on('change', function() {
		if($(this).prop('checked') == true){
			if($('#register_mobile').prop('checked') == false){
				$('#register_mobile').click();
			}
		}else{
			if($('#register_email').prop('checked') == false && $('#register_username').prop('checked') == false ){
				$('#register_username').click()
			}
			if($('#register_mobile').prop('checked') == true){
				$('#register_mobile').click();
			}
		}
	  });
	  $('#register_mobile').on('change', function() {
		if($(this).prop('checked') == true){
			if($('#register_by_mobile').prop('checked') == false){
				$('#register_by_mobile').click();
			}
		}else{
		
			if($('#register_email').prop('checked') == false && $('#register_username').prop('checked') == false ){
				$('#register_username').click()
			}
			if($('#register_by_mobile').prop('checked') == true){
				$('#register_by_mobile').click();
			}
		}
	  });
	  $('#register_by_email').on('change', function() {
		if($(this).prop('checked') == true){
			if($('#register_email').prop('checked') == false){
				$('#register_email').click();
			}
		}else{
			if($('#register_mobile').prop('checked') == false && $('#register_username').prop('checked') == false ){
				$('#register_username').click()
			}

			if($('#register_email').prop('checked') == true){
				$('#register_email').click();
			}
		}
	  });
	  $('#register_email').on('change', function() {
		if($(this).prop('checked') == true){
			if($('#register_by_email').prop('checked') == false){
				$('#register_by_email').click();
			}
		}else{
			if($('#register_mobile').prop('checked') == false && $('#register_username').prop('checked') == false ){
				$('#register_username').click()
			}
			if($('#register_by_email').prop('checked') == true){
				$('#register_by_email').click();
			}
		}
	  });
	  $("input[name=email_password]").bind("paste", function(e){

		var pastedData = e.originalEvent.clipboardData.getData('text').replace(/'/g, "").replace(/"/g, "").replace(/`/g, "");

		console.log(pastedData);
		setTimeout(() => {
			$("input[name=email_password]").val(pastedData)	
		}, 50);

	} );

	  $("input[name=email_password]").bind({
		keydown: function(e) {
			console.log(e.which);
			if (e.which==222) {
				return false;
			}
			if (e.which==192) {
				return false;
			}
			return true;
		}
	});


	$('.select-provider').on('change', function() {
		console.log(this.value)
		$('.providers-fields').hide()
		$('#provider-'+this.value).show()
	  });

	
	$('#hd-upanel .tabs li button').click(function () {
		$('#hd-upanel .tabs li button').removeClass('active')
		$(this).addClass('active')
		$('#settings-tab').hide()
		$('#mail-tab').hide()
		$('#sms-tab').hide()
		$('#login-tab').hide()
		$('#ticket-tab').hide()
		$('#'+$(this).attr('id')+'-tab').show()
		
	})
	$('#close').click(function () {
		$('#delete_id').val($(this).attr('data-id'))
		dialog.close();
	})


	var custom_uploader;
	$('.upload_image_button').on('click',function(e) {
		   textName= $(this).parent().prev().find('input');
		   e.preventDefault();
		   //If the uploader object has already been created, reopen the dialog
		   if (custom_uploader) {
			   custom_uploader.open();
			   return;
		   }
		   custom_uploader = wp.media.frames.file_frame = wp.media({
			   title: 'انتخاب عکس',
			   button: {
				   text: 'انتخاب عکس'
			   },
			   multiple: false
		   });
		   custom_uploader.on('select', function() {
			   var attachment = custom_uploader.state().get('selection').first().toJSON();
			   textName.val(attachment.url);
			   $("#calendar_img").val(attachment.url);
			   $("#icon_image").val(attachment.url);
			   $('#uploaded-img').attr('src', attachment.url);
			   $('#uploaded-img').show();
   
			   console.log(attachment)
		   });
   
		   //Open the uploader dialog
		   custom_uploader.open();
   
	   });


	   var input;
	   var global_uploader;
	   $('input.image').on('click',function(e) {
		// textName= $(this).parent().prev().find('input');
		e.preventDefault();
		 input = $(this)
		//If the uploader object has already been created, reopen the dialog
		if (global_uploader) {
			global_uploader.open();
			return;
		}
		global_uploader = wp.media.frames.file_frame = wp.media({
			title: 'انتخاب عکس',
			button: {text: 'انتخاب عکس'},
			multiple: false
		});
		global_uploader.on('select', function() {
			var attachment = global_uploader.state().get('selection').first().toJSON();
			// textName.val(attachment.url);
			input.next().val(attachment.url);
			// $("#icon_image").val(attachment.url);
			input.next().next().attr('src', attachment.url);
			input.next().next().show();

		});
		global_uploader.open();

	});

	   setTimeout(function(){
		var order = []
		$('ul#hd-upanel-sortable.sidemenu li').each(function (index,ele) {
			order.push($(ele).attr('item'))
		})
		$('input[name="items"]').val(JSON.stringify(order))
					 }, 1000);


	$('ul#hd-upanel-sortable.sidemenu li .dashicons-admin-generic').click(function () {
		var tab = $(this).closest('li').attr('item')
console.log(tab)
		$('#tickets-card,#dashboard-card,#orders-card,#addresses-card,#notifications-card,#downloads-card,#settings-card,#logout-card,#divider-card,#link_1-card,#link_2-card,#link_3-card,#link_4-card,#link_5-card,#html_1-card,#html_2-card,#html_3-card,#html_4-card,#html_5-card,#tickets-tab,#membership-card').hide()
		$('#'+tab+'-card').show()
	})

	  var helper = $( "#hd-upanel-sortable" ).sortable( {
		change: function( event, ui ) {
		
			setTimeout(function(){
var order = []
$('ul#hd-upanel-sortable.sidemenu li').each(function (index,ele) {
	order.push($(ele).attr('item'))
})
$('input[name="items"]').val(JSON.stringify(order))
			 }, 1000);

		}
	  });

})
});