(function($) {
	$(document).ready(function() {
		if (where === undefined || where === '') {
			where = 'all';
		}
		if (window.location.hash) {
			var hash = window.location.hash.substring(1);
			$('ul#pg_social_menu li').removeClass();
			$("ul#pg_social_menu li[data-social_menu='"+hash+"']").addClass('active');
			$('#pg_social #pg_social_cont .pg_social_pagesub').hide();
			$("#pg_social #pg_social_cont #page_"+hash+"").show();
		} else {
			if (window.location.href.indexOf('gall') > -1)  {
				$('ul#pg_social_menu li').removeClass();
				$('#pg_social #pg_social_cont .pg_social_pagesub').hide();
				$("ul#pg_social_menu li[data-social_menu='gallery']").addClass('active');
				$('#pg_social #pg_social_cont #page_gallery').show();
			} else {
				$('ul#pg_social_menu li:first-child').addClass('active');
				$('#pg_social #pg_social_cont .pg_social_pagesub:first-child').show();
			}
		}

		$('ul#pg_social_menu li').on('click', function() {
			$('ul#pg_social_menu li').removeClass();
			$(this).addClass('active');
			$('#pg_social #pg_social_cont .pg_social_pagesub').hide();
			$("#pg_social #pg_social_cont #page_"+$(this).attr('data-social_menu')).show();
		});

		$("#wall_post").on('submit', function(e) {
			e.preventDefault();
			e.stopPropagation();
			if ($("#wall_post").hasClass("openform")) {
				if ($('#wall_post_img').length && $('#wall_post_img').val() !== "") {
					$("#wall_post").removeClass("openform");
					uploadPhoto($('#wall_post_text').val(), $('#wall_post_img')[0].files[0], "wall", where, $('#wall_post_privacy option:selected').val());
				} else {
					$("#wall_post").removeClass("openform");
					pgwall_add_status($('#wall_post_text').val(), $('#wall_post_privacy option:selected').val());
				}
			}
		});

		//ACTION FRIENDS
		phpbb.addAjaxCallback('request_friend', function(response) {
			location.reload(true);
		});

		//DELETE STATUS
		$(document).on('click', '#posts_status .post_status .post_status_head .post_status_remove', function() {
			if (confirm(useLang['ARE_YOU_SURE'])) {
				var post_status = $(this).parent().parent().parent().parent().parent().parent().attr('data-lastp');
				pgwall_removeStatus(post_status);
			}
		});

		//DELETE PHOTO
		$(document).on('click', '#pg_social_photo_img ul#pg_social_photo_img_footer li ul#pg_social_photo_img_options li#pg_social_photo_img_option_delete a', function() {
			if (confirm(useLang['ARE_YOU_SURE_PHOTO'])) pgwall_delete_photo($('#pg_social_photo_img').attr('data-photo'));
		});

		$(document).on('scroll', function() {
			if ($(document).scrollTop() >= ($('#pg_social_sidec > #posts_status').height() - ($('#pg_social_sidec > #posts_status').height() / 2.5))) {
				pgwall_get_status('prequel', where);
			}
		});

		$('form#wall_post #wall_post_text').on('keyup', function() {
			var content = $(this).val();
			if (content.match(/@(\w+)/g)) {
				tag_system_search(content.match(/@(\w+)/g));
			} else {
				$('ul#pg_social_wall_tag_system').html('');
			}
		});
		/*
		$(document).on('click', '#pg_social_wall_tag_systemClose', function() {
			$('#pg_social_wall_tag_system').html('');
		});
		*/
		$(document).on('click', '#pg_social form#wall_post ul#pg_social_wall_tag_system li.tag_system_search_people', function() {
			var username = $(this).attr('data-people_username');
			var user = $(this).attr('data-people');
			var content = $('#wall_post_text').val();

			content = content.replace(/@(\w+)/ig,'');
			$('#wall_post_text').val(content);
			$('#wall_post_text').append(" <span data-people='"+user+"' data-people_tagged='"+username+"' class='people_tagged' contenteditable='false'>"+username+"</span> ");
			$('#pg_social form#wall_post ul#pg_social_wall_tag_system').html('');
		});

		$('#wall_post_img').on('change', function(e) {
			if ($(this).val() !== '') {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#wall_post_thumb img#wall_post_thumb_img').attr('src', e.target.result);
					$('#wall_post_thumb').show();
				};
				reader.readAsDataURL($(this)[0].files[0]);
			}
		});

		//SHARE STATUS
		$(document).on('click', '#posts_status .post_status .post_status_footer .post_status_share a', function() {
			if (confirm(useLang['DO_YOU_WANT_SHARE'])) {
				pgwall_shareStatus($(this).parent().attr('data-parent'));
			}
		});

		$(document).on('click', '#pg_social #pg_social_cont ul#posts_status li.post_status .post_status_footer .post_status_comment a', function(e) {
			var post_status = $(this).parent().parent().parent().parent().attr('data-lastp');
			if ($('#post_status_'+post_status+' ul.post_status_comments').hasClass('active')) {
				$('#post_status_'+post_status+' .post_status_comment').removeClass('active');
				$('#post_status_'+post_status+' ul.post_status_comments').removeClass('active').html('');
			} else {
				$(this).parent().addClass('active');
				pgwall_get_comments(post_status, 'post');
			}
		});

		$(document).on('click', '.post_comment_action .post_comment_action_delete', function() {
			if (confirm(useLang['ARE_YOU_SURE'])) {
				pgwall_remove_comment($(this).parent().parent().parent().attr('data-comment'));
			}
		});

		//UPLOAD PROFILE
		$('#profile_upload input#profile_upload_submit').on('click', function(e) {
			var whatchange = $(this).attr('data-change');
			e.preventDefault();
			e.stopPropagation();
			if (whatchange === 'cover') {
				uploadPhoto('', $('#pg_social #pg_social_header #pg_social_actionprofile label#pg_social_edit_cover input')[0].files[0], $('#pg_social #pg_social_header #pg_social_actionprofile label#pg_social_edit_cover input').attr('data-type'), where, 2, $('img#coverdrag').position().top);
			} else if (whatchange === 'avatar') {
				uploadPhoto('', $('#pg_social #pg_social_header #pg_social_actionprofile label#pg_social_edit_avatar input')[0].files[0], $('#pg_social #pg_social_header #pg_social_actionprofile label#pg_social_edit_avatar input').attr('data-type'), where, 2, 0);
			}
		});

		//CHANGE COVER
		$('#pg_social #pg_social_header #pg_social_actionprofile label#pg_social_edit_cover input').on('change', function(e) {
			$('#profile_upload input#profile_upload_submit').attr('data-change', 'cover');
			$('#pg_social_header img#coverdrag').remove();
			$('#pg_social #pg_social_main .profile_avatar').css('z-index', '80');
			$('#profile_upload_submit, #profile_upload_canc, .darkenwrapper').show();
			$('#pg_social_header').addClass('canMove').prepend("<img id='coverdrag' src='"+URL.createObjectURL(e.target.files[0])+"' />");
			$('#pg_social_header img#coverdrag').css('cursor', 's-resize').draggable({
				scroll: false,
				axis: 'y',
				cursor: 's-resize',
				drag: function (event, ui) {
					y1 = $('#pg_social_header').height();
					y2 = $('#pg_social_header img#coverdrag').height();
					if (ui.position.top >= 0) {
						ui.position.top = 0;
					}
					else
					if (ui.position.top <= (y1-y2)) {
						ui.position.top = y1-y2;
					}
				},
				stop: function(event, ui) {
					$('#pg_social_header img#coverdrag').val(ui.position.top);
				}
			});
		});

		//CHANGE AVATAR
		$('#pg_social #pg_social_header #pg_social_actionprofile label#pg_social_edit_avatar input').on('change', function(e) {
			$('#profile_upload input#profile_upload_submit').attr('data-change', 'avatar');
			$('#pg_social_header img#coverdrag').remove();
			$('#pg_social .profile_avatar').css('z-index', '80');
			$('#profile_upload_submit, .darkenwrapper').show();
			$('#pg_social_header').addClass('canMove');
			$('#pg_social .profile_avatar img').css('background-image', 'url('+URL.createObjectURL(e.target.files[0])+')');
		});

		$('#profile_upload_canc').on('click', function() {
			closePopup(true);
		});

		$('#pg_social #pg_social_cont ul.colums li#pg_social_gallery_create').on('click', function() {
			$(this).find('.centeralign *').hide();
			$(this).find('.centeralign input').show();
		});

		//CREATE ALBUM
		$('#pg_social #pg_social_cont ul.colums li#pg_social_gallery_create input[type="submit"]').on('click', function() {
			var fdata = new FormData();
			fdata.append('mode', 'add_gallery');
			fdata.append('gallery_name', $("#pg_social #pg_social_cont ul.colums li#pg_social_gallery_create input[name='pgsocial_galleryNew_title']").val());
			$.ajax({
				method: 'POST',
				url: root,
				data: fdata,
				contentType: false,
				processData: false,
				success: function(data) {
					location.reload(true);
				}
			});
		});

		//UPLOAD PHOTO ON ALBUM
		$('input[type="file"]#pgsocial_gallery_newPhoto').on('change', function(e) {
			uploadPhoto('', $(this)[0].files[0], $(this).attr('data-gall'), where, $(this).parent().find('#pgsocial_gallery_newPhotoPrivacy').val(), '');
		});

		//POPUP PHOTO
		$(document).on('click', '#pg_social #pg_social_cont ul#pg_social_photos li.photo, .post_status_content img.photo_popup, #pg_social ul.template_photos li', function(e) {
			var photo = $(this).attr('data-photo');
			if (photo) popupPhoto($.trim(photo));
		});

		//PRE AND NEXT PHOTO POPUP
		$(document).on('click', '.phpbb_alert.pg_social_photo #pg_social_photo_img .pg_social_photo_side #pg_social_photo_sidePre, .phpbb_alert.pg_social_photo #pg_social_photo_img .pg_social_photo_side #pg_social_photo_sideNex', function() {
			var ord = '0';
			if ($(this).attr('id') === 'pg_social_photo_sideNex') {
				ord = '1';
			}
			var fdata = new FormData();
			fdata.append('mode', 'prenext_photo');
			fdata.append('photo', $(this).parent().parent().attr('data-photo'));
			fdata.append('ord', ord);
			fdata.append('where', where);
			$.ajax({
				method: 'POST',
				url: root,
				data: fdata,
				contentType: false,
				processData: false,
				success: function(data) {
					popupPhoto($.trim(data));
				}
			});
		});

		//CLOSE POPUP PHOTO
		$(document).on('click', '.phpbb_alert.pg_social_photo #pg_social_photo_img .pg_social_photo_side ul#pg_social_photo_img_footer li#pg_social_photo_img_close', function() {
			closePopup(true);
		});

		//LIKE POPUP PHOTO
		$(document).on('click', "#pg_social_photo_social .pg_social_photo_likshare .post_status_like a", function() {
			pgwall_like_action($(this).parent().parent().attr('data-post'));
		});

		$(document).on('click', 'li.colum .cards ul.bubbles .bubble', function() {
			if (!$(this).hasClass('active')) {
				var friend = $(this).parent().parent().parent().attr('data-friend');
				$('li.colum[data-friend="'+friend+'"] .cards .card').removeClass('active');
				$('li.colum[data-friend="'+friend+'"] .cards ul.bubbles li.bubble').removeClass('active');
				$('li.colum[data-friend="'+friend+'"] .cards ul.bubbles li[data-bubble='+$(this).attr('data-bubble')+']').addClass('active');
				$('li.colum[data-friend="'+friend+'"] .cards .card[data-card='+$(this).attr('data-bubble')+']').addClass('active');
			}
		});

		$('a#page_new_button').on('click', function() {
			closePopup(false);
			$('body').css('overflow', 'hidden');
			$('.darkenwrapper').show();
			$('#page_create').show();
		});

		$('a.page_list_buttonLike').on('click', function() {
			pgwall_pagelike_action($(this).attr('data-page'));
		});

		$(document).on('click', '#posts_status .post_status .post_status_footer .post_status_like a', function() {
			pgwall_like_action($(this).parent().parent().parent().attr('data-lastp'));
		});

		$(document).on('keydown', '.pg_social_photo_comment_text', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				pgwall_add_comment($(this).val(), $(this).parent().parent().find('.pg_social_photo_comments').attr('data-post'));
			}
		});

		$(document).on('keydown', '.wall_comment_text', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				pgwall_add_comment($(this).val(), $(this).parent().parent().attr('data-lastp'));
			}
		});

		$(document).on('click', function(e) {
			if (e.target.id === 'darken') {
				closePopup(true);
			}
		});

		$(document).on('click', '.gallery_covhovEl i', function() {
			if($(this).parent().hasClass('active')) {
				$(this).parent().removeClass('active');
			} else {
				$('.gallery_covhovEl').removeClass('active');
				$(this).parent().addClass('active');
			}
		});

		$(document).on('mouseleave', '.gallery_covhov, ul#posts_status li.post_status', function() {
			$('.gallery_covhovEl').removeClass('active');

		});

		$(document).on('click', 'a.gallery_name_renameLink', function() {
			$(this).parent().parent().removeClass('active');
			$(this).parent().parent().parent().parent().parent().find('.gallery_name').addClass('rename');
		});

		$(document).on('click', 'i.gallery_name_renameSub', function() {
			var newTitle = $(this).parent().find('input.gallery_name_renameTitle').val();
			var album = $(this).parent().parent().parent().attr('data-album');
			var fdata = new FormData();
			fdata.append('mode', 'album_action');
			fdata.append('element', album);
			fdata.append('action', 'rename');
			fdata.append('value', newTitle);
			$.ajax({
				method: 'POST',
				url: root,
				data: fdata,
				contentType: false,
				processData: false,
				success: function() {
					$('.gallery[data-album="'+album+'"] .gallery_name a h5').text(newTitle);
					$('.gallery_name').removeClass('rename');
				}
			});
		});

		$(document).on('click', '.gallery_name_delete', function() {
			if (confirm('sicuro?')) {
				var element = $(this).parent().parent().parent().parent().parent();
				var fdata = new FormData();
				fdata.append('mode', 'album_action');
				fdata.append('element', $(element).attr('data-album'));
				fdata.append('action', 'delete');
				fdata.append('value', '');
				$.ajax({
					method: 'POST',
					url: root,
					data: fdata,
					contentType: false,
					processData: false,
					success: function(data) {
						$(element).remove();
					}
				});
			}
		});

		$(document).on('change', 'select.post_status_privacy_set', function() {
		var mode = id = '';
			switch($(this).attr('data-type')) {
				case 'album':
					mode = 'album_action';
					id = $(this).parent().parent().parent().parent().parent().attr('data-album');
				break;
				case 'status':
					mode = 'status_action';
					id = $(this).parent().parent().parent().parent().parent().attr('data-lastp');
				break;
			}
			var fdata = new FormData();
			fdata.append('mode', mode);
			fdata.append('element', id);
			fdata.append('action', 'privacy');
			fdata.append('value', $(this).val());
			$.ajax({
				method: 'POST',
				url: root,
				data: fdata,
				contentType: false,
				processData: false,
			});
		});
	});

	/* POST ACTION */
	pgwall_get_status = function(order, post_where) {
		if (!post_where) post_where = 'all';
		if ($('#load_more').is(':visible')) {
			$('#load_more').hide();
			var lastp;
			if (order === 'seguel') {
				lastp = $('#posts_status .post_status:first-child[data-lastp]').attr('data-lastp');
			} else if (order === 'prequel') {
				lastp = $('#posts_status .post_status:last-child[data-lastp]').attr('data-lastp');
			} else {
				lastp = 0;
			}
			if(lastp) {
				var fdata = new FormData();
				fdata.append('mode', 'get_status');
				fdata.append('post_where', post_where);
				fdata.append('profile_id', profile_id);
				fdata.append('lastp', lastp);
				fdata.append('where', where);
				fdata.append('order', order);

				$.ajax({
					method: 'POST',
					url: root,
					data: fdata,
					contentType: false,
					processData: false,
					success: function(data) {
						if (data) {
							if (order === 'prequel') {
								$('#posts_status').append(data);
							} else {
								$('#posts_status #wall_post').after().prepend(data);
							}
							console.log(data);
						}
						$('#load_more').show();
					},
				});
			}
		}
	}

	pgwall_add_status = function(texta, privacy) {
		if (!privacy) privacy = 1;
		if ($.trim(texta) !== '') {
			var fdata = new FormData();
			fdata.append('mode', 'add_status');
			fdata.append('post_where', where);
			fdata.append('profile_id', profile_id);
			fdata.append('text', encodeURIComponent($.trim(texta)));
			fdata.append('privacy', privacy);
			$.ajax({
				method: 'POST',
				url: root,
				data: fdata,
				contentType: false,
				processData: false,
				success: function(data) {
					$('#wall_post_text').val('');
					$('#wall_post').addClass('openform');
				}
			});
		}
	}

	pgwall_shareStatus = function(statu) {
		var fdata = new FormData();
		fdata.append('mode', 'shareStatus');
		fdata.append('status', statu);
		$.ajax({
			method: 'POST',
			url: root,
			data: fdata,
			contentType: false,
			processData: false,
			success: function(data) {
			}
		});
	}

	pgwall_removeStatus = function(post_status) {
		$.ajax({
			method: 'POST',
			url: root,
			data: 'mode=delete_status&post_status='+post_status,
			success: function(data) {
				$('#post_status_'+post_status).remove();
				pgwall_get_status('prequel', where);
			}
		});
	}

	/* LIKE ACTION */
	pgwall_like_action = function(post_like) {
		$.ajax({
			method: 'POST',
			url: root,
			data: 'mode=like_action&post_like='+post_like,
			success: function(data) {
				$('#post_status_'+post_like+' .post_status_footer .post_status_like').replaceWith(data);
				$('.pg_social_photo #pg_social_photo_social .pg_social_photo_likshare[data-post="'+post_like+'"] .post_status_like').replaceWith(data);
			}
		});
	}

	pgwall_pagelike_action = function(page) {
		$.ajax({
			method: 'POST',
			url: root,
			data: 'mode=pagelike_action&page='+page,
			success: function(data) {
				$("a.page_list_buttonLike[data-page='"+page+"']").removeClass('likepage dislikepage').addClass(data);
			}
		});
	}

	/* COMMENT ACTION */
	pgwall_get_comments = function(post_status, type) {
		$.ajax({
			method: 'POST',
			url: root,
			data: 'mode=get_comments&post_status='+post_status+'&type='+type,
			success: function(data) {
				$('ul.post_status_comments').html('');
				$('#post_status_'+post_status+' ul.post_status_comments').addClass('active');
				$('#post_status_'+post_status+' ul.post_status_comments').prepend(data);
				$('#pg_social_photo_comments_'+post_status).html(data);
			}
		});
	}

	pgwall_add_comment = function(comment, post_status) {
		var fdata = new FormData();
		fdata.append('mode', 'add_comment');
		fdata.append('post_status', post_status);
		fdata.append('comment', encodeURIComponent($.trim(comment)));
		$.ajax({
			method: 'POST',
			url: root,
			data: fdata,
			contentType: false,
			processData: false,
			success: function(data) {
				$('.wall_comment_text').val('');
				pgwall_get_comments(post_status);
			}
		});
	}

	pgwall_remove_comment = function(comment) {
		var fdata = new FormData();
		fdata.append('mode', 'remove_comment');
		fdata.append('comment', comment);
		$.ajax({
			type: 'POST',
			url: root,
			data: fdata,
			contentType: false,
			processData: false,
			success: function(data) {
				$('#post_comment_'+comment).remove();
			}
		});
	}

	/* PHOTO UPLOAD */
	uploadPhoto = function(msg, photo, type, where, privacy, itop) {
		var fdata = new FormData();
		fdata.append('mode', 'addPhoto');
		if (msg) fdata.append('msg', encodeURIComponent($.trim(msg)));
		fdata.append('post_where', where);
		fdata.append('profile_id', profile_id);
		fdata.append('type', type);
		fdata.append('where', where);
		fdata.append('photo', photo);
		fdata.append('privacy', privacy);
		if (itop) fdata.append('top', itop);
		$.ajax({
			type: 'POST',
			url: root,
			data: fdata,
			contentType: false,
			processData: false,
			beforeSend: pgsocial_loadStart,
			complete: pgsocial_loadStop,
			success: function(data) {
				if (type === '1') {
					$('ul#pg_social_photos').prepend(data);
				} else if (type === 'cover' || type === 'avatar') {
					location.reload();
				} else if (type === 'wall') {
					$('#wall_post_text').val('');
					$('#wall_post_img, #pgsocial_gallery_newPhoto').val('');
					$('#wall_post_thumb').removeAttr('style');
					$('#wall_post_thumb img#wall_post_thumb_img').removeAttr('src');
				} else {
					$('#pg_social_photos').prepend(data);
				}
			},
		})
	}

	pgwall_delete_photo = function(photo) {
		$.ajax({
			type: 'POST',
			url: root,
			data: 'mode=delete_photo&photo='+photo,
			success: function(data) {
				if (data === 'deleted') {
					closePopup(true);
					$('#page_gallery ul#pg_social_photos #gallery_'+photo).remove();
					$("img.photo_popup[data-photo='"+photo+"']").parent().parent().parent().remove();
				}
			}
		});
	}

	popupPhoto = function(photo) {
		closePopup(false);
		$('body').css('overflow', 'hidden');
		$('.darkenwrapper').show();
		$('#page-footer').append('<div id="pg_social_photo_'+photo+'" data-photo="'+photo+'" class="phpbb_alert pg_social_photo"></div>');
		var fdata = new FormData();
		fdata.append('mode', 'get_photo');
		fdata.append('photo', $.trim(photo));
		$.ajax({
			type: 'POST',
			url: root,
			data: fdata,
			contentType: false,
			processData: false,
			success: function(data) {
				$('#pg_social_photo_'+photo).html(data).show();
				$.ajax({
				method: 'POST',
					url: root,
					data: "mode=get_comments&post_status="+$(".pg_social_photo_comments").attr('data-post')+"&type=photo",
					cache: false,
					async: true,
					success: function(data) {
						$('.pg_social_photo_comments').html(data);
					}
				});
			}
		});
	}

	closePopup = function(as) {
		$('.pg_social_photo').remove();
		$('#page_create').hide();
		if (as) $('#darken').parent().hide(); $('body').css('overflow', '');
		if (!$('#pg_social #pg_social_header.canMove').length) $('#darken').parent().hide();
		$('#pg_social_header').removeClass('canMove');
		$('#profile_upload_submit, #profile_upload_canc').hide();
		$('#pg_social_header img#coverdrag').remove();
	}

	/* TAG SYSTEM */
	tag_system_search = function(who) {
		var fdata = new FormData();
		fdata.append('mode', 'tag_system_search');
		fdata.append('who', who[0]);
		$.ajax({
			type: 'POST',
			url: root,
			data: fdata,
			contentType: false,
			processData: false,
			success: function(data) {
				$('ul#pg_social_wall_tag_system').html(data);
			}
		})
	}

	pgsocial_loadStart = function () {
		$('.darkenwrapper').show();
		$('#pg_social #pg_social_header.canMove').removeClass('canMove');
		$('#pg_social #pg_social_header #pg_social_actionprofile input[type="submit"]').removeAttr('style');
		$('.darkenwrapper #darken').append('<div id="pgsocial_loading"><i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i></div>');
	}

	pgsocial_loadStop = function() {
		$('.darkenwrapper').hide();
		$('#pgsocial_loading').remove();
	}
})(jQuery);
