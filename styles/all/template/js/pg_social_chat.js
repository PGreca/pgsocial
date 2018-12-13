(function($) {
	$(document).ready(function() {
		var pg_social_chat_sound = $('#pg_social_chat_sound')[0];  
		if(!jQuery.cookie('pg_social_chat')) {
			
		} else {
			var pg_social_chat_cookie = jQuery.cookie('pg_social_chat');
			var arr = pg_social_chat_cookie.split(',');
			for(var i=0; i< arr.length; i++) {
				if(arr[i] != "") {
					if(arr[i] == "0") {
						$("#pg_social_chat_people").parent().addClass("opened");
						getchat_people();
					} else if(arr[i] != 'undefined') {
						chat_new(arr[i]);
					}
				}
			}
		}
		setInterval(message_check, 900);
		if($("#pg_social_chat_people").parent().hasClass("opened")) {
			getchat_people();
		}	
	});	
	
	$(document).on('scroll', 'ul.pg_social_chat_messages', function(e) {
		if($("ul.pg_social_chat_messages").scrollTop() <= ($("ul.pg_social_chat_messages").height() - ($("ul.pg_social_chat_messages").height() / 1.9))) {
			chat_messageLoad(49, 'prequel');
		}
		console.log($(e.target));
	});
	
	
	$(document).on('keyup', '#pg_social_chat_people_search', function(e) {
		getchat_people($(this).val());
	});
	
	$(document).on('click', '#pg_social_chat #pg_social_chat_box #pg_social_chat_people_buttons #pg_social_chat_people_button_openclose', function() {
		if($("#pg_social_chat_people").parent().hasClass("opened")) {
			$("#pg_social_chat_people").parent().removeClass("opened");
			$("ul#pg_social_chat_people_online").html("");		
			$("#pg_social_chat_people_search").val("");	
			var newcokie = jQuery.cookie('pg_social_chat').replace("0,", "").replace(",0", "").replace("0", "");
			$.cookie('pg_social_chat', newcokie);
		} else {
			$("#pg_social_chat_people").parent().addClass("opened");
			getchat_people();
			if(jQuery.cookie('pg_social_chat') == undefined) newW = '0'; else newW = jQuery.cookie('pg_social_chat')+',0';
			$.cookie('pg_social_chat', newW);
		}
	});
	
	$(document).on('click', '#pg_social_chat #pg_social_chat_box #pg_social_chat_people ul#pg_social_chat_people_online li', function(e) {
		if($("#pg_social_chat #pg_social_chat_box_"+$(this).attr("data-people")).length){
			closeChat($(this).attr("data-people"));	
		} else {
			$("#pg_social_chat_people_search").val("");
			getchat_people();
			chat_new($(this).attr("data-people")); 
			$.cookie('pg_social_chat', jQuery.cookie('pg_social_chat')+","+$(this).attr("data-people"));
		}
	});	
	
	$(document).on('click', '#pg_social_chat .pg_social_chat .pg_social_chat_head .chat_close', function() {
		closeChat($(this).parent().parent().attr("data-people"));	
	});
	
	$(document).on('click', '.pg_social_chat_head_minimize i.icon.chat_down', function() {
		var chat = $(this).parent().parent().parent().attr("data-people");
		$("#pg_social_chat #pg_social_chat_box_"+chat).removeClass("opened");
		
	});
	
	$(document).on('click', '.pg_social_chat_head_minimize i.icon.chat_up', function() {
		var chat = $(this).parent().parent().parent().attr("data-people");
		$("#pg_social_chat #pg_social_chat_box_"+chat).addClass("opened");
		$("#pg_social_chat #pg_social_chat_box_"+chat+" form.pg_social_chat_form input[type='text'].pg_social_chat_messsage_new").focus();
	});
	
	$(document).keyup(function(e) {
		if(e.keyCode == 27) {
			var chat = $("#pg_social_chat .pg_social_chat form.pg_social_chat_form input.pg_social_chat_messsage_new:focus").parent().parent().attr("data-people");
			$("#pg_social_chat #pg_social_chat_box_"+chat).remove();
			var newcokie = jQuery.cookie('pg_social_chat').replace(","+chat, "");
			$.cookie('pg_social_chat', newcokie);		
		}
	});
	
	$(document).on('change', '.pg_social_chat_messsage_new', function() {
	});
	
	$(document).on('submit', '.pg_social_chat_form', function(e) {
		e.preventDefault();
		e.stopPropagation();
		chat_message_send($(this).find(".pg_social_chat_messsage_new").val(), $(this).parent().attr("data-people"));
	});
	
	function message_check() {		 
		var chats = $(".pg_social_chat[data-people]");
		var chat = "";
		chats.each(function(index) {
			chat_messageLoad($(this).attr("data-people"), 'seguel');
			if(chat != "") chat += ", ";
			chat += $(this).attr("data-people");
		});
		if(!chat) chat = 0;
		$.ajax({
			method: "POST",
			url: root,
			data: "mode=message_check&exclude="+chat,
			cache: false, 
			async: true,
			success: function(data) {
				if(data) $("#pg_social_chat").prepend(data);
			}
		});
	}		
	
	function getchat_people(person = null) {
		if(person) person = "&person="+person; else person = '';
		$.ajax({
			method: "POST",
			url: root, 
			data: "mode=getchat_people"+person,
			cache: false,
			async: true, 
			success: function(data) {	
				$("#pg_social_chat_people").parent().addClass("opened");
				if(data) {
					$("#pg_social_chat #pg_social_chat_box #pg_social_chat_people ul#pg_social_chat_people_online").html(data);
				} else {
					$("#pg_social_chat_people").parent().removeClass("opened");
				}
			}
		});		
	}
	
	function chat_new(person) {
		$.ajax({
			method: "POST",
			url: root, 
			data: "mode=getchat_person&person="+person,
			cache: false,
			async: true, 
			success: function(data) {	
				$("#pg_social_chat").prepend(data);
				$("#pg_social_chat #pg_social_chat_box_"+person+" form.pg_social_chat_form input[type='text'].pg_social_chat_messsage_new").focus();				
			}
		});
	}
	
	function chat_messageLoad(person, order) {
		if($("#pg_social_chat #pg_social_chat_box_"+person+" .load_more_chat").is(":visible")) {
			$("#pg_social_chat #pg_social_chat_box_"+person+" .load_more_chat").hide();
			if(order == 'seguel') var lastmessage = $("#pg_social_chat #pg_social_chat_box_"+person+" ul.pg_social_chat_messages li:first-child[data-message]").attr("data-message");
			if(order == 'prequel') var lastmessage = $("#pg_social_chat #pg_social_chat_box_"+person+" ul.pg_social_chat_messages li:last-child[data-message]").attr("data-message");
		
			if(lastmessage == undefined) lastmessage = 0; 
			//if($(this).attr("data-people")) chat_messageLoad(person, lastmessage);
			$.ajax({
				method: "POST",
				url: root,
				data: "mode=getchat_message&person="+person+"&lastmessage="+lastmessage+"&order="+order,
				cache: false,
				async: true,
				success: function(data) {
					if(order == 'prequel') {
						$("#pg_social_chat #pg_social_chat_box_"+person+" ul.pg_social_chat_messages").append(data);
					} else {
						$("#pg_social_chat #pg_social_chat_box_"+person+" ul.pg_social_chat_messages").prepend(data);
					}	
					$("#pg_social_chat #pg_social_chat_box_"+person+" .load_more_chat").show();
				}
			});	
		}
	}
	
	function chat_message_send(message, person) {
		if($.trim(message) != "") {
			var fdata = new FormData()
			fdata.append("mode", "message_send");
			fdata.append("person", person);
			fdata.append("message",  encodeURIComponent($.trim(message)));
			$.ajax({
				method: "POST",
				url: root,
				data: fdata,
				contentType: false,
				processData: false, 
				success: function(data) {
					$("#pg_social_chat #pg_social_chat_box_"+person+" form.pg_social_chat_form input[type='text'].pg_social_chat_messsage_new").val("");
				}
			});
		}
	}
	
	function closeChat(chat) {
		$("#pg_social_chat #pg_social_chat_box_"+chat).remove();
		var newcokie = jQuery.cookie('pg_social_chat').replace(","+chat, "");
		$.cookie('pg_social_chat', newcokie);	
		
	}
})(jQuery);