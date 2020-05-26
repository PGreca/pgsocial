(function($) {
	$(document).ready(function() {
		var pgsocial_chat_sound = $('#pgsocial_chat_sound')[0];
		pgsocial_chat();
		if(!Cookies.get('pgsocial_chat')) {
			Cookies.set('pgsocial_chat', '');
		} else {
			var arr = Cookies.get('pgsocial_chat').split(',');
			for(var i = 0; i < arr.length; i++) {
				if (arr[i] !== '') {
					if (arr[i] === '0') {
						$('#pgsocial_chat #pgsocial_chatRoot').addClass('opened');
					} else {
						chat_new(arr[i], 'read', false);
					}
				}
			}
		}
		setInterval(function() {
			pgsocial_chat();
		}, 5000);

		$(document).on('click', '#pgsocial_chatButton_ext i.fa-th-large', function() {
			if($('#pgsocial_chat #pgsocial_chatRoot').hasClass('opened')) {
				$('#pgsocial_chat #pgsocial_chatRoot, #pgsocial_chat #pgsocial_chatRoot #pgsocial_chat_settings').removeClass('opened');
				var newcokie = Cookies.get('pgsocial_chat').replace(',0', '');
				Cookies.set('pgsocial_chat', newcokie);
				$('#pgsocial_chat #pgsocial_chatRoot #pg_social_chat_people_search').val('');
			} else {
				$('#pgsocial_chat #pgsocial_chatRoot').addClass('opened');
				Cookies.set('pgsocial_chat', Cookies.get('pgsocial_chat')+',0');
			}
			chat_position();
		});

		$(document).on('keyup', '#pgsocial_chat #pgsocial_chatRoot #pg_social_chat_people_search', function(e) {
			pgsocial_chat_check($(this).val());
		});

		$(document).on('click', '#pgsocial_chat #pgsocial_chatRoot i.fa-sliders', function() {
			$('#pgsocial_chat #pgsocial_chatRoot #pgsocial_chat_settings').toggleClass('opened');
		});

		$(document).on('change', '#pgsocial_chat #pgsocial_chatRoot #pgsocial_chatButton_ext #pgsocial_chat_settings input', function() {
			var setting = $(this).attr('name');
			var fdata = new FormData();
			fdata.append('mode', 'pgsocial_chat_setting');
			fdata.append('setting', setting);
			fdata.append('value', $(this).val());
			$.ajax({
				method: 'POST',
				url: root,
				data: fdata,
				contentType: false,
				processData: false,
				success: function(data) {
					if (setting === 'pgsocial_setting_hide') {
						if ($('#pgsocial_chat #pgsocial_chatRoot ul#pgsocial_chatPeople').hasClass('canchat')) {
							$('#pgsocial_chat #pgsocial_chatRoot ul#pgsocial_chatPeople').removeClass('canchat');
							closeChat();
						} else {
							$('#pgsocial_chat #pgsocial_chatRoot ul#pgsocial_chatPeople').addClass('canchat');
						}
					}
				}
			});
		});

		$(document).on('click', function(e) {
			if($(e.target).closest('#pgsocial_chat #pgsocial_chatRoot').length === 0 && $('#pgsocial_chat #pgsocial_chatRoot').hasClass('opened')) {
				$('#pgsocial_chat #pgsocial_chatRoot, #pgsocial_chat #pgsocial_chatRoot #pgsocial_chat_settings').removeClass('opened');
				chat_position();
				var newcokie = Cookies.get('pgsocial_chat').replace(',0', '');
				Cookies.set('pgsocial_chat', newcokie);
				$('#pgsocial_chat #pgsocial_chatRoot #pg_social_chat_people_search').val('');
			}
		});

		$(document).on('submit', '.pg_social_chat_form', function(e) {
			e.preventDefault();
			e.stopPropagation();
			chat_message_send($(this).find('.pg_social_chat_messsage_new').val(), $(this).parent().attr('data-people'));
		});
	});

	$(document).on('click', '#pgsocial_chat #pgsocial_chatRoot ul.canchat li.pg_social_chat_people_online_single.tooltiped', function(e) {
		if($('#pgsocial_chat #pg_social_chat_box_'+$(this).attr('data-people')).length){
			closeChat($(this).attr('data-people'));
		} else {
			$('#pgsocial_chat #pgsocial_chatRoot #pg_social_chat_people_search').val('');
			chat_new($(this).attr('data-people'), 'read', true);
		}
	});

	$(document).on('click', '#pgsocial_chat .pgsocial_chat .pgsocial_chat_head .chat_close i', function() {
		closeChat($(this).parent().parent().parent().attr('data-people'));
	});

	$(document).on('keypress', '#pgsocial_chat .pgsocial_chat form.pg_social_chat_form textarea.pg_social_chat_messsage_new', function(e) {
        if (e.which === 13) {
			e.preventDefault();
			e.stopPropagation();
			chat_message_send($(this).val(), $(this).parent().parent().attr('data-people'));
        }
    });

	function pgsocial_chat() {
		if(!$('#pgsocial_chat #pgsocial_chatRoot ul#pgsocial_chatPeople').hasClass('canchat')) {
			closeChat();
		}
		$('#pgsocial_chat .pgsocial_chat[data-people]').each(function() {
			if($(this).find('ul.pg_social_chat_messages').scrollTop() <= ($(this).find('ul.pg_social_chat_messages').height() / 4)) {
				chat_messageLoad($(this).attr('data-people'), 'prequel');
			}
			chat_messageLoad($(this).attr('data-people'), 'seguel');
		});
		var people = null;
		if($('#pg_social_chat_people_search').val()) {
			people = $('#pg_social_chat_people_search').val();
		}
		pgsocial_chat_check(people);
		if($('#pgsocial_chat #pgsocial_chatRoot ul#pgsocial_chatPeople').hasClass('canchat')) {
			$.ajax({
				method: "POST",
				url: root,
				data: "mode=pgsocial_chat_check",
				cache: false,
				async: true,
				success: function(data) {
					if (data === 'sound') {
						pgsocial_chat_sound.play();
					}
				}
			});
		}
	}

	function pgsocial_chat_check(people = null) {
		if(people == null) people = '';
		$.ajax({
			method: "POST",
			url: root,
			data: "mode=getchat_people&people="+people,
			cache: false,
			async: true,
			success: function(data) {
				$("#pgsocial_chat #pgsocial_chatRoot ul#pgsocial_chatPeople").html(data);
			}
		});
	}

	function chat_new(person, read, cookie) {
		if($('ul#pgsocial_chatPeople').hasClass('canchat')) {
			$.ajax({
				method: "POST",
				url: root,
				data: "mode=getchat_person&person="+person+"&read="+read,
				cache: false,
				async: true,
				success: function(data) {
					var maxWidth = $(window).width();
					if(maxWidth < 750) {
						if(maxWidth < (($('#pgsocial_chat #pgsocial_chatRoot').width() + 10) + ((((275 + 10) + 10) * ($("#pgsocial_chat .pgsocial_chat.chat_person").length + 1))))) {
							$('#pgsocial_chat #pgsocial_chatRoot, #pgsocial_chat #pgsocial_chatRoot #pgsocial_chat_settings').removeClass('opened');
							closeChat();
						}
					}
					$('#pgsocial_chat').prepend(data);
					if(maxWidth < 750) $('#pgsocial_chat #pg_social_chat_box_'+person).css('width', (maxWidth - ($('#pgsocial_chat #pgsocial_chatRoot').width() + (10 * 2))));
					chat_position();
					if(cookie) Cookies.set('pgsocial_chat', Cookies.get('pgsocial_chat')+","+person);
				},
			});
		}
	}

	function chat_messageLoad(person, order) {
		if($('#pgsocial_chat #pg_social_chat_box_'+person+' .load_more_chat').is(':visible')) {
			$('#pgsocial_chat #pg_social_chat_box_'+person+' .load_more_chat').hide();
			let lastmessage;
			if (order === 'seguel') {
				lastmessage = $('#pgsocial_chat #pg_social_chat_box_'+person+' ul.pg_social_chat_messages li[data-message]').first().attr('data-message');
			} else if (order === 'prequel') {
				lastmessage = $('#pgsocial_chat #pg_social_chat_box_'+person+' ul.pg_social_chat_messages li[data-message]').last().attr('data-message');
			} else {
				lastmessage = 0
			}
			$.ajax({
				method: 'POST',
				url: root,
				data: 'mode=getchat_message&person='+person+'&lastmessage='+lastmessage+'&order='+order,
				cache: false,
				async: true,
				success: function(data) {
					if (order === 'prequel') {
						$('#pgsocial_chat #pg_social_chat_box_'+person+' ul.pg_social_chat_messages').append(data);
					} else {
						$('#pgsocial_chat #pg_social_chat_box_'+person+' ul.pg_social_chat_messages').prepend(data);
					}
					$('#pgsocial_chat #pg_social_chat_box_'+person+' .load_more_chat').show();
				}
			});
		}
	}

	function chat_message_send(message, person) {
		if ($.trim(message) !== '') {
			$('#pgsocial_chat #pg_social_chat_box_'+person+' form.pg_social_chat_form textarea.pg_social_chat_messsage_new').val('');
			var fdata = new FormData();
			fdata.append('mode', 'message_send');
			fdata.append('person', person);
			fdata.append('message', $.trim(message));
			$.ajax({
				method: 'POST',
				url: root,
				data: fdata,
				contentType: false,
				processData: false,
				success: function(data) {
					chat_messageLoad(person, 'seguel');
					pgsocial_chat_check();
				}
			});
		}
	}

	function chat_message_read(person) {
		var fdata = new FormData();
		fdata.append('mode', 'message_read');
		fdata.append('person', person);
		$.ajax({
			method: 'POST',
			url: root,
			data: fdata,
			contentType: false,
			processData: false,
		});
	}

	function closeChat(chat = null) {
		if(!chat) {
			$('#pgsocial_chat .chat_person').remove();
			Cookies.get('pgsocial_chat', '');
		} else {
			$('#pgsocial_chat #pg_social_chat_box_'+chat).remove();
			chat_position();
			var newcokie = Cookies.get('pgsocial_chat').replace(','+chat, '');
			Cookies.set('pgsocial_chat', newcokie);
		}
	}

	function chat_position() {
		$($('#pgsocial_chat .pgsocial_chat.chat_person').get().reverse()).each(function(index, element) {
			if (index === 0) {
				$(this).css('right', ($('#pgsocial_chat #pgsocial_chatRoot').width() + 10)+'px');
			} else {
				$(this).css('right', (($('#pgsocial_chat #pgsocial_chatRoot').width() + 10) + (((275 + 10) * (index))))+'px');
			}
		});
	}
})(jQuery);
