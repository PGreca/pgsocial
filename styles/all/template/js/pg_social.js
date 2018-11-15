(function($) {
	$(document).ready(function() {		
		if(where == undefined || where == '') where = "all";
				
		if(window.location.hash) {
			var hash = window.location.hash.substring(1);
			$("ul#pg_social_menu li").removeClass();
			$("ul#pg_social_menu li[data-social_menu='"+hash+"']").addClass("active");
			$("#pg_social #pg_social_cont .pg_social_pagesub").hide();
			$("#pg_social #pg_social_cont #page_"+hash+"").show();
		} else {
			if(window.location.href.indexOf("gall") > -1)  {		
				$("ul#pg_social_menu li").removeClass();
				$("#pg_social #pg_social_cont .pg_social_pagesub").hide();
				$("ul#pg_social_menu li[data-social_menu='gallery']").addClass("active");
				$("#pg_social #pg_social_cont #page_gallery").show();
			} else {
				$("ul#pg_social_menu li:first-child").addClass("active");
				$("#pg_social #pg_social_cont .pg_social_pagesub:first-child").show();	
			}
		}
			
		//ACTION FRIENDS
		phpbb.addAjaxCallback('requestFriend', function(response) {
			location.reload(true);
		});
		
		//SHARE STATUS 
		$(document).on('click', '#posts_status .post_status .post_status_footer .post_status_share a', function() {
			if(confirm(useLang['DO_YOU_WANT_SHARE'])) {
				pgwall_shareStatus($(this).parent().attr('data-parent'));
			}
		});
		
		//DELETE STATUS
		$(document).on('click', "#posts_status .post_status .post_status_head .post_status_remove", function() {
			if(confirm(useLang['ARE_YOU_SURE'])) {
				var post_status = $(this).parent().parent().parent().attr('data-lastp');
				pgwall_removeStatus(post_status);
			}
		});
		
		//DELETE PHOTO
		$(document).on('click', "#pg_social_photo_img ul#pg_social_photo_img_footer li ul#pg_social_photo_img_options li#pg_social_photo_img_option_delete a", function() {
			if(confirm(useLang['ARE_YOU_SURE_PHOTO'])) pgwall_deletePhoto($("#pg_social_photo_img").attr("data-photo"));
		});
	});
	
	$(document).on('scroll', function() { 
		if($(document).scrollTop() >= ($("#posts_status").height() - ($("#posts_status").height() / 1.9))) {
			pgwall_getStatus('prequel', where);
		}
	});
	
	$(document).on('keyup', "#wall_post_text", function() {
		var content = $(this).html();
		if(content.match(/@(\w+)/g)) {
			tag_system_search(content.match(/@(\w+)/g));
		} else {
			$("ul#pg_social_wall_tag_system").html("");
		}
	});
	/*
	$(document).on('click', '#pg_social_wall_tag_systemClose', function() {
		$('#pg_social_wall_tag_system').html("");
	});
	*/
	$(document).on('click', "#pg_social form#wall_post ul#pg_social_wall_tag_system li.tag_system_search_people", function() {
		var username = $(this).attr('data-people_username');
		var user = $(this).attr('data-people');
		var content = $('#wall_post_text').val();
		
		content = content.replace(/@(\w+)/ig,"");
		$('#wall_post_text').val(content);
		$("#wall_post_text").append(" <span data-people='"+user+"' data-people_tagged='"+username+"' class='people_tagged' contenteditable='false'>"+username+"</span> ");
		$("#pg_social form#wall_post ul#pg_social_wall_tag_system").html("");
	});
	
	$(document).on('change', "#wall_post_img", function(e) {
		if($(this).val() != "") {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#wall_post_thumb img#wall_post_thumb_img').attr('src', e.target.result);
				$('#wall_post_thumb').show();
			}
			reader.readAsDataURL($(this)[0].files[0]);
			$("#wall_post_privacy").hide();
		} else {
			$("#wall_post_privacy").show();
		}
	});
	
	$(document).on('click', "ul#pg_social_menu li", function() {
		$("ul#pg_social_menu li").removeClass();
		$(this).addClass("active");
		$("#pg_social #pg_social_cont .pg_social_pagesub").hide();
		$("#pg_social #pg_social_cont #page_"+$(this).attr('data-social_menu')).show();
	});
		
	$(document).on('click', '#pg_social #pg_social_cont ul#posts_status li.post_status .post_status_footer .post_status_comment a', function(e) {
		var post_status = $(this).parent().parent().parent().attr('data-lastp');
		if($('#post_status_'+post_status+' ul.post_status_comments').hasClass('active')) {
			$('#post_status_'+post_status+' ul.post_status_comments').removeClass('active').html("");
		} else {
			pgwall_getComments(post_status, 'post');	
		}
	});
	
	$(document).on('click', '.post_comment_action .post_comment_action_delete', function() {
		if(confirm(useLang['ARE_YOU_SURE'])) {
			pgwall_removeComment($(this).parent().parent().parent().attr('data-comment'));
		}
	});
		 
	//UPLOAD PROFILE
	$(document).on('click', "#profile_upload input#profile_upload_submit", function(e) {
		e.preventDefault();
		e.stopPropagation();
		var upload_type = $("#pg_social #pg_social_header #pg_social_actionprofile label#pg_social_edit_cover input").attr("data-type");
		uploadPhoto("", $("#pg_social #pg_social_header #pg_social_actionprofile label#pg_social_edit_cover input")[0].files[0], upload_type, where, $("img#coverdrag").position().top);
	});
	
	//CHANGE COVER
	$(document).on('change', "#pg_social #pg_social_header #pg_social_actionprofile label#pg_social_edit_cover input", function(e) {
		$("#pg_social_header img#coverdrag").remove();
		$("#pg_social #pg_social_main .profile_avatar").css("z-index", "80");
		$("#profile_upload_submit, .darkenwrapper").show();
		$("#pg_social_header").addClass("canMove").prepend("<img id='coverdrag' src='"+URL.createObjectURL(e.target.files[0])+"' />");
		$('#pg_social_header img#coverdrag').css('cursor', 's-resize').draggable({
			scroll: false,
			axis: "y",
			cursor: "s-resize",
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
	
	//POPUP PHOTO
	$(document).on('click', '#pg_social #pg_social_cont ul#pg_social_photos li.photo, .post_status_content img.photo_popup, #pg_social ul.template_photos li', function(e) {
		var photo = $(this).attr("data-photo");
		if(photo) popupPhoto($.trim(photo));
	});
	
	$(document).on('click', '.phpbb_alert.pg_social_photo #pg_social_photo_img .pg_social_photo_side #pg_social_photo_sidePre, .phpbb_alert.pg_social_photo #pg_social_photo_img .pg_social_photo_side #pg_social_photo_sideNex', function() {
		var ord = 0;
		if($(this).attr("id") == "pg_social_photo_sideNex") ord = 1;
		var fdata = new FormData()
		fdata.append("mode", "prenextPhoto");
		fdata.append("photo", $(this).parent().parent().attr('data-photo'));
		fdata.append("ord", ord);
		fdata.append("where", where);
		$.ajax({
			method: "POST", 
			url: root,
			data: fdata,
			contentType: false,
			processData: false,
			success: function(data) {
				popupPhoto($.trim(data));
			}
		});
	});
	
	$(document).on('click', '.phpbb_alert.pg_social_photo #pg_social_photo_img .pg_social_photo_side ul#pg_social_photo_img_footer li#pg_social_photo_img_close', function() {
		closePopup(true);		
	});
	
	$(document).on('click', "#pg_social_photo_social .pg_social_photo_likshare .post_status_like a", function() {
		pgwall_likeAction($(this).parent().parent().attr('data-post'));
	});
	
	$(document).on('click', "a.page_list_buttonLike", function() {
		pgwall_pagelikeAction($(this).attr('data-page'));

	});
	
	/*
	$(document).on('click', 'a#page_new_form_open', function(data) {
		$("#page_new_form").show();
	});
	*/
	
	$(document).on("click", function(e) {
		if(e.target.id == "darken") {
			closePopup(true);
		}
	});
	
})(jQuery);

/* POST ACTION */
function pgwall_getStatus(order, post_where) {
	if(!post_where) post_where = 'all';
	if($("#load_more").is(":visible")) {
		$("#load_more").hide();
		if(order == 'seguel') var lastp = $("#posts_status .post_status:first-child[data-lastp]").attr("data-lastp");
		if(order == 'prequel') var lastp = $("#posts_status .post_status:last-child[data-lastp]").attr("data-lastp");
		if(lastp == undefined) lastp = 0;
		
		var fdata = new FormData()
		fdata.append("mode", "getStatus");
		fdata.append("post_where", post_where);
		fdata.append("profile_id", profile_id);
		fdata.append("lastp", lastp);
		fdata.append("where", where);
		fdata.append("order", order);
		
		$.ajax({
			method: "POST",
			url: root, 
			data: fdata,
			contentType: false,
			processData: false,
			success: function(data) {
				if(data) {
					if(order == 'prequel') {
						$("#posts_status").append(data);
					} else {
						$("#posts_status").prepend(data);
					}
				}
				$("#load_more").show();
			}, 
		});
	}
}

function pgwall_addStatus(texta, privacy) {
	if(!privacy) privacy = 1;
	if($.trim(texta) != "") {
		var fdata = new FormData()
		fdata.append("mode", "addStatus");
		fdata.append("post_where", where);
		fdata.append("profile_id", profile_id);
		fdata.append("text", encodeURIComponent($.trim(texta)));
		fdata.append("privacy", privacy);
		$.ajax({
			method: "POST",
			url: root,
			data: fdata,
			contentType: false,
			processData: false, 
			success: function(data) {
				$('#wall_post_text').html("");
			}
		});
	}
}

function pgwall_shareStatus(statu) {
	var fdata = new FormData()
	fdata.append("mode", "shareStatus");
	fdata.append("status", statu);
	$.ajax({
		method: "POST",
		url: root,
		data: fdata,
		contentType: false, 
		processData: false,
		success: function(data) {
		}		
	});
}

function pgwall_removeStatus(post_status) {
	$.ajax({
		method: "POST", 
		url: root,
		data: "mode=deleteStatus&post_status="+post_status,
		success: function(data) {
			$("#post_status_"+post_status).remove();
			pgwall_getStatus('prequel', where);
		}				
	});
}

/* LIKE ACTION */
function pgwall_likeAction(post_like) {
	$.ajax({
		method: "POST",
		url: root,
		data: "mode=likeAction&post_like="+post_like,
		success: function(data) {
			$('#post_status_'+post_like+' .post_status_footer .post_status_like').replaceWith(data);
			$('.pg_social_photo #pg_social_photo_social .pg_social_photo_likshare[data-post="'+post_like+'"] .post_status_like').replaceWith(data);
		}
	});	
}

function pgwall_pagelikeAction(page) {
	$.ajax({
		method: "POST",
		url: root,
		data: "mode=pagelikeAction&page="+page,
		success: function(data) {
			$("a.page_list_buttonLike[data-page='"+page+"'] span").attr('class', '').addClass(data);
		}		
	});
}

/* COMMENT ACTION */
function pgwall_getComments(post_status, type) {
	$.ajax({
		method: "POST",
		url: root,
		data: "mode=getComments&post_status="+post_status+"&type="+type,
		success: function(data) {
			$('ul.post_status_comments').html("");
			$('#post_status_'+post_status+' ul.post_status_comments').addClass('active');
			$('#post_status_'+post_status+' ul.post_status_comments').prepend(data);
			$('#pg_social_photo_comments_'+post_status).html(data);
		}		
	});
}

function pgwall_addComment(comment, post_status) {	
	var fdata = new FormData()
	fdata.append("mode", "addComment");
	fdata.append("post_status", post_status);
	fdata.append("comment", encodeURIComponent($.trim(comment)));		
	$.ajax({
		method: "POST",
		url: root,
		data: fdata,
		contentType: false,
		processData: false, 
		success: function(data) {
			$(".wall_comment_text").val("");
			pgwall_getComments(post_status);
		}
	});
}

function pgwall_removeComment(comment) {
	var fdata = new FormData()
	fdata.append("mode", "removeComment");
	fdata.append("comment", comment);
	$.ajax({
		type: 'POST',
		url: root,
		data: fdata,
		contentType: false,
		processData: false,
		success: function(data) {
			$("#post_comment_"+comment).remove();
		}
	});	
}

/* PHOTO UPLOAD */
function uploadPhoto(msg, photo, type, where, itop) {
	var fdata = new FormData()
	fdata.append("mode", "addPhoto");
	if(msg) fdata.append("msg", encodeURIComponent($.trim(msg)));
	fdata.append("post_where", where);
	fdata.append("profile_id", profile_id);
	fdata.append("type", type);
	fdata.append("where", where);
	fdata.append("photo", photo);
	if(itop) fdata.append("top", itop);
	$.ajax({
		type: 'POST',
		url: root,
		data: fdata,
		contentType: false,
		processData: false, 
		success: function(data) {	
			if(type == 'cover') location.reload();
			$('#wall_post_text').html("");
			$("#wall_post_img").val("");
			$("#wall_post_thumb").removeAttr("style");
			$("#wall_post_thumb img#wall_post_thumb_img").removeAttr('src');
		}
	})
}	

function pgwall_deletePhoto(photo) {
	$.ajax({
		type: 'POST',
		url: root, 
		data: 'mode=deletePhoto&photo='+photo,
		success: function(data) {
			console.log(data);
			if(data == 'deleted') {
				closePopup(true);
				$('#page_gallery ul#pg_social_photos #gallery_'+photo).remove();
				$("img.photo_popup[data-photo='"+photo+"']").parent().parent().parent().remove();
			}
		}
	});	
}

function popupPhoto(photo) {
	closePopup(false);
	$("body").css("overflow", "hidden");
	$(".darkenwrapper").show();
	$("#page-footer").append('<div id="pg_social_photo_'+photo+'" data-photo="'+photo+'" class="phpbb_alert pg_social_photo"></div>');
	var fdata = new FormData()
	fdata.append("mode", "getPhoto");
	fdata.append("photo", $.trim(photo));
	$.ajax({
		type: 'POST',
		url: root,
		data: fdata,
		contentType: false,
		processData: false,
		success: function(data) {
			$('#pg_social_photo_'+photo).html(data).show();
			$.ajax({
			method: "POST", 
				url: root,
				data: "mode=getComments&post_status="+$(".pg_social_photo_comments").attr('data-post')+"&type=photo",
				cache: false,
				async: true, 
				success: function(data) {
					$(".pg_social_photo_comments").html(data);	
				}
			});
		}
	});
}

function closePopup(as) {
	history.pushState(site, site.Title, site.Url);
	$(".pg_social_photo").remove();
	if(as) $("#darken").parent().hide(); $("body").css("overflow", "");
	if(!$('#pg_social #pg_social_header.canMove').length) $("#darken").parent().hide();
}

/* TAG SYSTEM */
function tag_system_search(who) {
	var fdata = new FormData()
	fdata.append("mode", "tag_system_search");
	fdata.append("who", who[0]);
	$.ajax({
		type: 'POST',
		url: root,
		data: fdata,
		contentType: false,
		processData: false,
		success: function(data) {
			$("ul#pg_social_wall_tag_system").html(data);
		}
	})
}