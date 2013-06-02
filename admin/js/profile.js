
var adminProfile = 
{
	actions: [],
	
	profile_id: undefined,
	
	profile_notes: [],
	
	construct: function(profile_id) {
		
		this.profile_id = profile_id;
		
		for(var key in this.actions) {
			this.actions[key](this);
		}
	},
	
	ajaxCall: function(apply_func, params, callback) {
		var handler = this;
		$jq.ajax({
				url: URL_ADMIN_RSP + "profile.rsp.php?apply_func="+apply_func,
				type: "post",
				dataType: "json",
				data: params,
				success: function(result){
					if ( callback != undefined ) {
						callback(result.result, handler);	
					}
					new Function(result.script)();
				}
			});
	}
}


//Profile Status
adminProfile.actions.push(function(handler){
	var $node = $jq("#profile_status");
	var last_status = $node.val();
	$node.change(function(){
		handler.ajaxCall("changeProfileStatus",{profile_id: handler.profile_id, status: $node.val()}, function(result){
			if (result) {
				$node.attr("className", "profile_select_status_" + $node.val())
				
				$jq("#main_label_profile_status").attr("className","label_profile_status_" + $node.val()).text($node.val());
				
			} else {
				$node.find("option[value=" + last_status + "]").attr("selected", true);
			}
		});
	});
	
});

// Jump to member's page

adminProfile.actions.push(function(handler){
	var $node = $jq("#jump_to_members_page");

	$node.click(function(){
		handler.ajaxCall("authenticateAdminAsProfile",{profile_id: handler.profile_id}, function(result){
			if ( parseInt(result.profile_id) == handler.profile_id) {
                window.open(result.href, '_blank');
			} 
		});
	});

});


//Photos



//Profile Email Verified
adminProfile.actions.push(function(handler){
	var $node = $jq("#profile_email_verified");
	var last_status = $node.val();
	$node.change(function(){
		handler.ajaxCall("setEmailVerified",{profile_id: handler.profile_id, status: $node.val()}, function(result){
			if (result) {
				$node.attr("className", "profile_email_verified_" + $node.val())
			} else {
				$node.find("option[value=" + last_status + "]").attr("selected", true);
			}
		});
	});	
	
	
});

//Profile Reviewed Status
adminProfile.actions.push(function(handler){
	var $node = $jq("#profile_reviewed");
	var last_status = $node.val();
	$node.change(function(){
		handler.ajaxCall("setReviewedStatus",{profile_id: handler.profile_id, status: $node.val()}, function(result){
			if (result) {
				$node.attr("className", "profile_reviewed_" + $node.val())
				if ($node.val()=="y") {
					$jq("#cont_edit_set_reviewed").attr("className", "cont_edit_set_reviewed_hide");
				} else {
					$jq("#cont_edit_set_reviewed").attr("className", "cont_edit_set_reviewed_show");
				}
				
			} else {
				$node.find("option[value=" + last_status + "]").attr("selected", true);
			}
		});
	});	
	
	
});


//Profile Featured
adminProfile.actions.push(function(handler){
	var $node = $jq("#set_featured_profile");
	var last_value = $node.attr("checked");
	$node.click(function(){
		handler.ajaxCall("markAsFeatured",{profile_id: handler.profile_id, status: $node.attr("checked")}, function(result){
			if (!result) {
				$node.attr("checked", last_value);
			}
		});
	});	
	
	
});

//Profile SEt Site Moderator
adminProfile.actions.push(function(handler){
	var $node = $jq("#set_profile_site_moderator");
	var last_value = $node.attr("checked");
	$node.click(function(){
		handler.ajaxCall("setSiteModerator",{profile_id: handler.profile_id, flag: $node.attr("checked")}, function(result){
			if (!result) {
				$node.attr("checked", last_value);
			}
		});
	});	
	
	
});


//Admin notes
adminProfile.actions.push(function(handler){
	//methods
	
	var obj = {
		
		showAddForm: function() {
			var self = this;
			self.$new_note_cont.find(".add_new_label").fadeOut(100, function(){
				
				var $form = self.$new_note_cont.find("form");
				var $input = $form.find("input[type=text]");
				
				$input.unbind().blur(function(){
					window.setTimeout(self.hideAddForm, 200);
				});
				
				self.$new_note_cont.find(".add_new_input").show();
				$input.focus();
			});
		},
		
		hideAddForm: function() {
			var self = this;
			if (!self.$new_note_cont) {
				return;
			}
		
			$input_node = self.$new_note_cont.find(".add_new_input");
			
			if (!$input_node.length) {
				return;
			}
			
			$input_node.fadeOut(100, function(){
				$jq(this).find("input[type=text]").unbind().val("");
				self.$new_note_cont.find(".add_new_label").show();
			});
		},
		
		
		addNote: function(note_id, note, date, animate) {
			var self = this;
			$note_node = self.$("tr.prototype_node").clone().removeClass("prototype_node");
			$note_node.mouseover(function(){
				$jq(this).attr("className","active");
			}).mouseout(function(){
				$jq(this).attr("className","");
			});
			
			$note_node.find(".delete_admin_note_button").click(function(){
				var $node = $jq(this).parents("tr:eq(0)");
				handler.ajaxCall("deleteAdminNote", {note_id: note_id}, function(result){
					if(result) {
						self.deleteNote($node);
					}
				});	
			});
					
			$note_node.find(".admin_note").text(note),
			$note_node.find(".admin_note_date").text(date);
			
			self.$("tr.prototype_node").after($note_node);
			if (animate) {
				self.$new_note_cont.children().hide();
				self.$new_note_cont.css("height", "1px");
				$note_node.show();
				self.$new_note_cont.animate({height:"24px"},200,function(){
					self.hideAddForm();
				});
			} else {
				$note_node.show();
			}
		},
		
		deleteNote: function($note_node) {
			$note_node.fadeOut(300, function(){
				$note_node.remove();
			});
		},
		
		$: function(x) {
			return $jq(x, "#admin_notes_container");
		},
		
		construct: function() {
			var self = this;
			this.profile_id = handler.profile_id;
			this.$new_note_cont = this.$(".add_new_note_cont");
			
			this.$new_note_cont.find("form").submit(function(){
				var $form = $jq(this);
				var $input = $form.find("input[type=text]");
				handler.ajaxCall("addAdminNote", {profile_id: handler.profile_id, note: $input.val()}, function(result){
					if(result) {
						self.addNote(result.note_id, result.note, result.date, true);
					}
				});
				return false;
			});
			
			this.$new_note_cont.click(function(){
				self.showAddForm();
			});
			
			$jq.each(handler.profile_notes, function(i, item) {
				self.addNote(item.note_id, item.note, item.date, false);
			});

		}
	};
	
	
	obj.construct();
	
	
});

/* Add to Hot list */
adminProfile.actions.push(function(handler){
	var $node = $jq("#add_to_hot_list");
	var last_value = $node.attr("checked");
	$node.click(function(){
		handler.ajaxCall("managerHotList",{profile_id: handler.profile_id, flag: $node.attr("checked")}, function(result){
			if (!result) {
				$node.attr("checked", last_value);
			}
		});
	});	
	
	
});

//---------------------------------------------------------------------------------------------

function showPhotoContainer( profile_id, status )
{
	$jq( '#profile_photo_container' ).fadeIn("normal");
	var rand = Math.random();
	var status_param = status ? '&status='+status : '';
	$jq( '#profile_photo_frame' ).attr("src", 'frame_profile_photo.php?profile_id='+profile_id+ status_param +'&' + rand);
}

function hidePhotoContainer( profile_id )
{
	$jq( '#profile_photo_container' ).fadeOut("normal");
}

function showMailboxContainer( profile_id )
{
	$jq( '#profile_mailbox_container' ).fadeIn("normal");
	$jq( '#profile_mailbox_frame' ).attr("src", 'frame_profile_mails.php?profile_id=' + profile_id);
}

function hideMailboxContainer( )
{
	$jq( '#profile_mailbox_container' ).fadeOut("normal");
}

function updatePhotosInfo(profile_id) {
	adminProfile.ajaxCall("getPhotoInfo",{profile_id:profile_id},function(result){
		if(!result) {
			return false;	
		}
		
		var count = result.count;
		var thumb_url = result.thumb_url;
		
		$jq("#profile_photo_link_total a .count").text(count.total);
		
		if (count.active!=undefined && count.active.count > 0) {
			$jq("#profile_photo_link_active").children().show();
			$jq("#profile_photo_link_active a .count").text(count.active.count);
		} else {
			$jq("#profile_photo_link_active").children().hide();
		}
		
		if (count.approval!=undefined && count.approval.count > 0) {
			$jq("#profile_photo_link_approval").children().show();
			$jq("#profile_photo_link_approval a .count").text(count.approval.count);
		} else {
			$jq("#profile_photo_link_approval").children().hide();
		}
		
		if (count.suspended!=undefined && count.suspended.count > 0) {
			$jq("#profile_photo_link_suspended").children().show();
			$jq("#profile_photo_link_suspended a .count").text(count.suspended.count);
		} else {
			$jq("#profile_photo_link_suspended").children().hide();
		}
		
		var $img = $jq(new Image());
		$img.attr("src", thumb_url);
		
		$jq("#profile_thumb_content img").replaceWith($img);
		
	});
}

function sendMessageToProfile(profile_id, subject, message , ignore_unsubscribe)
{
	adminProfile.ajaxCall("sendMessage", {
		profile_id: profile_id,
		subject: subject,
		message: message,
		ignore_unsubscribe: ignore_unsubscribe 
	}, function(){
		
	});
}


function blockProfileIP(ip) {
	adminProfile.ajaxCall("blockIp",{ip:ip});
}

function setProfileUnsubscribed(profile_id, unsub) {
	adminProfile.ajaxCall("setProfileUnsubscribed",{profile_id: profile_id, unsubscribe:unsub});
}

function show_assign_vars( node_id )
{
	if($(node_id).style.opacity==1 || $(node_id).style.display=='block')
		hide_node( node_id );
	else
		show_node( node_id );
}

function showMusicContainer( profile_id, status )
{
	$jq( '#profile_music_frame' ).attr('src', 'frame_profile_music.php?profile_id='+profile_id+'&status='+status);
	//setTimeout( 'show_node( \'profile_media_container\' )', 800 );

	$jq( '#profile_music_container' ).show();
}

function hideMusicContainer( profile_id )
{
	$jq( '#profile_music_container' ).hide();
}

function showMediaContainer( profile_id, status )
{
	$jq( '#profile_media_frame' ).attr('src', 'frame_profile_media.php?profile_id='+profile_id+'&status='+status);
	//setTimeout( 'show_node( \'profile_media_container\' )', 800 );
	
	$jq( '#profile_media_container' ).show();
}

function hideMediaContainer( profile_id )
{
	$jq( '#profile_media_container' ).hide();
}

function play_media( object_id, path_to_file, object_height, object_width, media_mode, path_to_swf, id )
{
	if (media_mode == 'code')
	{
		adminProfile.ajaxCall("getEmbedCode",{video_id: id}, function(code){
			if (code) {
				$jq("#mediaplayer_container").empty();
				$jq(':not("#mediaplayer_container")' ,'#view_media_object').remove();
				$jq('#view_media_object').append(code);
			}
		});
	}
	else if (media_mode == 'flash_video')
	{
		$jq(':not("#mediaplayer_container")' ,'#view_media_object').remove();

		var s1 = new SWFObject( path_to_swf, "single", "400", "350", "7" );
			s1.addParam("allowfullscreen","true");
			s1.addVariable("autostart","true");
			s1.addVariable("showdownload","true");
			s1.addVariable("file", path_to_file );
			s1.addVariable("bufferlength","5");
			s1.write("mediaplayer_container");
	}
	else
	{
		$jq(':not("#mediaplayer_container")' ,'#view_media_object').remove();
		$jq('#view_media_object').append('<object id="mediaobject" name="mediaobject" width="'+object_width+'" height="'+object_height+'"	classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#version=5,1,52,701" standby="loading microsoft windows media player components..." type="application/x-oleobject"> <param name="filename" value="'+path_to_file+'"> <param name="animationatstart" value="false"> <param name="transparentatstart" value="false"> <param name="autostart" value="true"> <param name="showcontrols" value="true"> <param name="showtracker" value="false"> <param name="showaudiocontrols" value="true"> <param name="showstatusbar" value="true"> <embed name="mediaobject" id="mediaobject" type="application/x-mplayer2" width="'+object_width+'" height="'+object_height+'" src="'+path_to_file+'" autostart="1"  showstatusbar="1" showdisplay="0" showcontrols="1" controltype="1" showtracker="0" pluginspage="http://www.microsoft.com/windows/downloads/contents/products/mediaplayer/"></embed></object>');		 	
	}
}
function play_music( object_id, path_to_file, object_height, object_width, media_mode, path_to_swf, id )
{
        if (media_mode == 'code')
	{
		adminProfile.ajaxCall("getMusicEmbedCode",{music_id: id}, function(code){
			if (code) {
				$jq("#musplayer_container").empty();
				$jq(':not("#musplayer_container")' ,'#view_music_object').remove();
				$jq('#view_music_object').append(code);
			}
		});
	}
	else if (media_mode == 'flash_video')
	{
		$jq(':not("#musplayer_container")' ,'#view_music_object').remove();

		var s1 = new SWFObject( path_to_swf, "single", "100%", "20", "7" );
			s1.addParam("allowfullscreen","true");
			s1.addVariable("autostart","true");
			s1.addVariable("showdownload","true");
			s1.addVariable("file", path_to_file );
			s1.addVariable("bufferlength","5");
			s1.write("musplayer_container");
	}
	else
	{
		$jq(':not("#musplayer_container")' ,'#view_media_object').remove();
		$jq('#view_music_object').append('<object id="mediaobject" name="mediaobject" width="'+object_width+'" height="'+object_height+'"classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#version=5,1,52,701" standby="loading microsoft windows media player components..." type="application/x-oleobject"> <param name="filename" value="'+path_to_file+'"> <param name="animationatstart" value="false"> <param name="transparentatstart" value="false"> <param name="autostart" value="true"> <param name="showcontrols" value="true"> <param name="showtracker" value="false"> <param name="showaudiocontrols" value="true"> <param name="showstatusbar" value="true"> <embed name="mediaobject" id="mediaobject" type="application/x-mplayer2" width="'+object_width+'" height="'+object_height+'" src="'+path_to_file+'" autostart="1"  showstatusbar="1" showdisplay="0" showcontrols="1" controltype="1" showtracker="0" pluginspage="http://www.microsoft.com/windows/downloads/contents/products/mediaplayer/"></embed></object>');
	}
}

function updateVideoCounters( profile )
{
	adminProfile.ajaxCall("updateVideoInfo", {profile_id: profile}, function(info) {
		if (info) {

			$jq("#profile_media_link_total").empty();
			$jq("#profile_media_link_active").empty();
			$jq("#profile_media_link_approval").empty();
			$jq("#profile_media_link_suspended").empty();
			
			$jq("#profile_media_link_total")
				.html('<a id="link_total" href="javascript://" class="label_profile_total_photo">View multimedia (<b>' + info.total + '</b>)</a>')
				.find("#link_total").click(function(){
					showMediaContainer( profile );
				});
		
			if (info.active != undefined) {
			
				$jq("#profile_media_link_active")
					.html('<a id="link_active" href="javascript://" class="label_profile_active_photo">active (<b>' + info.active.count + '</b>)</a>')
					.find("#link_active").click(function(){
						showMediaContainer( profile, 'active' );
					});
			}
			
			if (info.approval != undefined) {
			
				$jq("#profile_media_link_approval")
					.html('<a id="link_approval" href="javascript://" class="label_profile_approval_photo">approval (<b>' + info.approval.count + '</b>)</a>')
					.find("#link_approval").click(function(){
						showMediaContainer( profile, 'approval' );
					});
			}

			if (info.suspended != undefined) {
			
				$jq("#profile_media_link_suspended")
					.html('<a id="link_suspended" href="javascript://" class="label_profile_suspended_photo">suspended (<b>' + info.suspended.count + '</b>)</a>')
					.find("#link_suspended").click(function(){
						showMediaContainer( profile, 'suspended' );
					});
			}
		}
	});

       
}

 function updateMusicCounters( profile )
{
	adminProfile.ajaxCall("updateMusicInfo", {profile_id: profile}, function(info) {
		if (info) {

			$jq("#profile_music_link_total").empty();
			$jq("#profile_music_link_active").empty();
			$jq("#profile_music_link_approval").empty();
			$jq("#profile_music_link_suspended").empty();

			$jq("#profile_music_link_total")
				.html('<a id="link_total" href="javascript://" class="label_profile_total_photo">View music (<b>' + info.total + '</b>)</a>')
				.find("#link_total").click(function(){
					showMusicContainer( profile );
				});

			if (info.active != undefined) {

				$jq("#profile_music_link_active")
					.html('<a id="link_active" href="javascript://" class="label_profile_active_photo">active (<b>' + info.active.count + '</b>)</a>')
					.find("#link_active").click(function(){
						showMusicContainer( profile, 'active' );
					});
			}

			if (info.approval != undefined) {

				$jq("#profile_music_link_approval")
					.html('<a id="link_approval" href="javascript://" class="label_profile_approval_photo">approval (<b>' + info.approval.count + '</b>)</a>')
					.find("#link_approval").click(function(){
						showMusicContainer( profile, 'approval' );
					});
			}

			if (info.suspended != undefined) {

				$jq("#profile_music_link_suspended")
					.html('<a id="link_suspended" href="javascript://" class="label_profile_suspended_photo">suspended (<b>' + info.suspended.count + '</b>)</a>')
					.find("#link_suspended").click(function(){
						showMusicContainer( profile, 'suspended' );
					});
			}
		}
    });
}