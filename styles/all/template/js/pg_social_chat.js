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
						$("#pg_social_chat_people").addClass("opened");
						getchatPeople();
					} else if(arr[i] != 'undefined') {
						chat_new(arr[i]);
					}
				}
			}
		}
		setInterval(messageCheck, 900);
		if($("#pg_social_chat_people").hasClass("opened")) {
			getchatPeople();
		}	

		
	});	
	
	/* DEV SCROLL CHAT
	$('ul.pg_social_chat_messages').scroll(function() {
			console.log(e);
			console.log($(this).scrollTop());
			console.log("aa");
	});*/
	
	$(document).on('keyup', '#pg_social_chat_people_search', function(e) {
		getchatPeople($(this).val());
	});
	
	$(document).on('click', '#pg_social_chat #pg_social_chat_box #pg_social_chat_people_buttons #pg_social_chat_people_button_openclose', function() {
		if($("#pg_social_chat_people").hasClass("opened")) {
			$("#pg_social_chat_people").removeClass("opened");
			$("ul#pg_social_chat_people_online").html("");		
			$("#pg_social_chat_people_search").val("");	
			var newcokie = jQuery.cookie('pg_social_chat').replace("0,", "").replace(",0", "").replace("0", "");
			$.cookie('pg_social_chat', newcokie);
		} else {
			$("#pg_social_chat_people").addClass("opened");
			getchatPeople();
			if(jQuery.cookie('pg_social_chat') == undefined) newW = '0'; else newW = jQuery.cookie('pg_social_chat')+',0';
			$.cookie('pg_social_chat', newW);
		}
	});
	
	$(document).on('click', '#pg_social_chat #pg_social_chat_box #pg_social_chat_people ul#pg_social_chat_people_online li', function(e) {
		if($("#pg_social_chat #pg_social_chat_box_"+$(this).attr("data-people")).length){
			$("#pg_social_chat #pg_social_chat_box_"+$(this).attr("data-people")).remove();
			var newcokie = jQuery.cookie('pg_social_chat').replace(","+$(this).attr("data-people"), "");
			$.cookie('pg_social_chat', newcokie);		
		} else {
			$("#pg_social_chat_people_search").val("");
			getchatPeople();
			chat_new($(this).attr("data-people")); 
			$.cookie('pg_social_chat', jQuery.cookie('pg_social_chat')+","+$(this).attr("data-people"));
		}
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
		chat_messageSend($(this).find(".pg_social_chat_messsage_new").val(), $(this).parent().attr("data-people"));
	});
	
	function messageCheck() {		 
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
			data: "mode=messageCheck&exclude="+chat,
			cache: false, 
			async: true,
			success: function(data) {
				if(data) $("#pg_social_chat").prepend(data);
			}
		});
	}		
	
	function getchatPeople(person = null) {
		if(person) person = "&person="+person; else person = '';
		$.ajax({
			method: "POST",
			url: root, 
			data: "mode=getchatPeople"+person,
			cache: false,
			async: true, 
			success: function(data) {	
				$("#pg_social_chat_people").addClass("opened");
				if(data) {
					$("#pg_social_chat #pg_social_chat_box #pg_social_chat_people ul#pg_social_chat_people_online").html(data);
				} else {
					$("#pg_social_chat_people").removeClass("opened");
				}
			}
		});		
	}
	
	function chat_new(person) {
		$.ajax({
			method: "POST",
			url: root, 
			data: "mode=getchatPerson&person="+person,
			cache: false,
			async: true, 
			success: function(data) {	
				$("#pg_social_chat").prepend(data);
				$("#pg_social_chat #pg_social_chat_box_"+person+" form.pg_social_chat_form input[type='text'].pg_social_chat_messsage_new").focus();				
			}
		});
	}
	
	function chat_messageLoad(person, order) {
		if(order == 'seguel') var lastmessage = $("#pg_social_chat #pg_social_chat_box_"+person+" ul.pg_social_chat_messages li:first-child").attr("data-message");
		if(order == 'prequel') var lastmessage = $("#pg_social_chat #pg_social_chat_box_"+person+" ul.pg_social_chat_messages li:lastst-child").attr("data-message");
		
		if(lastmessage == undefined) lastmessage = 0; 
		//if($(this).attr("data-people")) chat_messageLoad(person, lastmessage);
		$.ajax({
			method: "POST",
			url: root,
			data: "mode=getchatMessage&person="+person+"&lastmessage="+lastmessage+"&order="+order,
			cache: false,
			async: true,
			success: function(data) {
				//console.log(order);
				if(order == 'prequel') {
					$("#pg_social_chat #pg_social_chat_box_"+person+" ul.pg_social_chat_messages").prepend(data);
				} else {
					$("#pg_social_chat #pg_social_chat_box_"+person+" ul.pg_social_chat_messages").prepend(data);
				}				
			}
		});	
	}
	
	function chat_messageSend(message, person) {
		if($.trim(message) != "") {
			$.ajax({
				method: "POST",
				url: root,
				data: "mode=messageSend&person="+person+"&message="+message,
				cache: false, 
				async: true,
				success: function(data) {
					$("#pg_social_chat #pg_social_chat_box_"+person+" form.pg_social_chat_form input[type='text'].pg_social_chat_messsage_new").val("");
				}
			});
		}
	}
})(jQuery);