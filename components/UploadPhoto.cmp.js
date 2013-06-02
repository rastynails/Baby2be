function component_UploadPhoto(auto_id)
{
	this.DOMConstruct('UploadPhoto', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_UploadPhoto.prototype =
	new SK_ComponentHandler({
		
		$list_container : {},
		
		$details_container : {},	
		
		$slot_prototype : {},
		
		slots : [],
			
		current_slot_number : undefined,
		
		current_slot : undefined,
		
		optoins : {},
		
		upload_no_permited: false,
		
		no_permited_msg: "",
		
		
		construct : function(options){
			this.$details_container = this.$("#photo_details");
			this.$list_container = this.$("#photo_list");
			var $slot_prototype_node = this.$list_container.find(".prototype_node");
			this.$slot_prototype = $slot_prototype_node.clone().removeClass("prototype_node");
			$slot_prototype_node.remove();
			
			var handler = this;
			
			
			if (options == undefined) {
				options = {};
			}
			
			this.options = options;
			
			var $large_img = this.$details_container.find(".preview");
			var $imageInProgress = this.$('.image_in_progress');
			
			if (this.options.image_height != undefined) {
				$large_img.css("height", this.options.image_height);
				$imageInProgress.css("height", this.options.image_height);
			}
			else {
				$large_img.css("height", "250px");
				$imageInProgress.css("height", "250px");
			}
			
			if (this.options.image_width != undefined) {
				$large_img.css("width", this.options.image_width);
				$imageInProgress.css("width", this.options.image_width);
			}
			else {
				$large_img.css("width", "250px");
				$imageInProgress.css("width", "250px");
			}
			
			
			var $imageControls = $large_img.find('.image_controls');
			
			$imageControls.find('.rotate_cw').click(function(){
				handler.rotateImage(90);
			});
			
			$imageControls.find('.rotate_ccw').click(function(){
				handler.rotateImage(270);
			});
		},
		
		
		rotateImage: function(angle){
			var handler = this;
			this.convertionStart();
			var pid = this.current_slot.image.id;
			var $large_img = this.$details_container.find(".preview");
			this.ajaxCall('ajax_Rotate', {photo_id: pid, angle: angle}, {success: function(r){
				if (r) {
					handler.current_slot.image.src = r;
					var $newThumb = $('<img src="'+r+'" />');
					handler.current_slot.image.$node.replaceWith($newThumb);
					handler.current_slot.image.$node = $newThumb; 
					$large_img.css("background", "url("+r+") center no-repeat");
				}
				handler.convertionEnd();
			}});
		},
		
		convertionStart: function() {
			this.$details_container.find(".preview").hide();
			this.$('.image_in_progress').show();
			this.$('.list_preloader').show();
		},
		
		convertionEnd: function() {
			this.$details_container.find(".preview").show();
			this.$('.image_in_progress').hide();
			this.$('.list_preloader').hide();
		},
		
		refreshCurrentSlot: function() {
			this.setCurrentSlot(this.current_slot_number);
		},
		
		noPermited: function(msg) {
			this.no_permited_msg = msg;
			this.upload_no_permited = true;
		},
		
		setCurrentSlot : function(number){
			if (this.current_slot != undefined){
				this.deactivateSlot();
			}
			
			this.current_slot_number = number;
			this.current_slot = this.slots[number];
			this.slots[number].active = true;
			this.activateSlot();
			
			this.$details_container.find(".upload_form input[name=slot_number]").val(this.current_slot_number);
			
			if (this.current_slot.image != undefined) {	
				this.showDetails(this.current_slot_number);	
			}else {
				this.showForm();
			}
		},
		
		/**
		* Sets image to slot
		*/
		setSlotImage : function(slot_number, image ){
			var slot = this.slots[slot_number];
			
			if ($.trim(image.src) != "") {
				
				slot.image = image;
				
				slot.$node.hide();
				
				var $image = $("<img>");
				
				$image.load(function(){
					slot.image.$node = $(this);
					slot.$node.removeClass("empty");
					slot.$node.removeClass("empty_active");
					if (slot.active) {
						slot.$node.addClass("active");
					}
					slot.$node.show();
					slot.empty = false;
				});
				
				$image.appendTo(slot.$node).attr("src", image.src);
			}
		},
		
		
		activateSlot : function(){
			var $slot = this.current_slot.$node;
			
			if (this.current_slot.image != undefined) {
				$slot.addClass("active");
			}
			else {
				$slot.removeClass("empty");
				$slot.addClass("empty_active");
				
				//this.$(".list_preloader").css("height", this.$list_container.parent().height());
			}
			
		},
		
		deactivateSlot : function()	{
			var $slot = this.current_slot.$node;
			$slot.removeClass("active");
			$slot.removeClass("empty_active");
			if (this.current_slot.image == undefined) {	
				$slot.addClass("empty");
			}
			
			
		},
		
		addSlotRange : function(count){
			for(var i = 1 ; i <= count; i++){
				this.addSlot(i);
			}
		},
			
		
		addSlot : function(number){
			
			var handler = this;
			var $slot = this.$slot_prototype.clone();
			
			$slot.click(function(){
				handler.setCurrentSlot(number);
			});
			
			$slot.appendTo(this.$list_container);				
			
			$slot.addClass("empty");
			
			$slot.show();

	/*-----< Slot Object >------*/	
	
			this.slots[number] ={
				$node 	: $slot,
				number 	: number,
				image	: undefined,
				empty	: true,
				active	: false,
				
				setImage: function(src){
					handler.setSlotImage(this, src);
				}
			}
					
	/*-----< / Slot Object >------*/	
			
		},
		
		
	/*-------------------< Upload >------------------------*/		
		
		showDetails : function(slot_number){
			var $det_cont = this.$details_container;
			var $large_img = $det_cont.find(".preview");
			var slot = this.slots[slot_number];
			
			$large_img.css("background", "url("+slot.image.src+") center no-repeat")
			
			$det_cont.find(".upload_form").hide();
			$det_cont.find(".preview_empty").hide();
			$det_cont.find(".photo_details").show().find(".preview").show();
			
			this.showToolbar(slot_number);
			this.showInfo(slot_number);
			this.showStatus(slot_number);
			this.showAdmonStatus(slot_number);
			this.showMoveToAlbum(slot_number);
		},
		
		showMoveToAlbum: function(slot_number) {
			var handler = this;
			var $det_cont = this.$details_container;
			var $cont = $det_cont.find('.album_select_cont').show();
			if (!$cont.length) {
				return false;
			}
			var img_id = this.slots[slot_number].image.id
			
			$cont.find('input.move').unbind().click(function(){
				var album_id = $cont.find('select').val();
				handler.ajaxCall('ajax_moveTo', {photo_id: img_id, album_id: album_id});
			});
		},
		
		hideMoveToAlbum: function() {
			var $det_cont = this.$details_container;
			$det_cont.find('.album_select_cont').hide();
		},
		
		showForm : function(){
			
			this.hideToolbar();
			this.hideInfo();
			this.hideStatus();
			this.hideMoveToAlbum();
						
			var $det_cont = this.$details_container;
			$det_cont.find(".photo_details").hide().find(".preview").hide();
			$det_cont.find(".preview_empty").hide();
			
			if (this.upload_no_permited) {
				$det_cont.find(".upload_form .form").hide();
				$det_cont.find(".upload_form .permission_msg .message").html(this.no_permited_msg);
				$det_cont.find(".upload_form .permission_msg").show();
			} else {
				$det_cont.find(".upload_form .form").show();
				$det_cont.find(".upload_form .permission_msg").hide();
			}
			
			$det_cont.find(".upload_form").show();
		},
		
		showToolbar : function(slot_number)
		{
			var handler = this;
			var $det_cont = this.$details_container;
			var $toolbar = $det_cont.find('.photo_toolbar');
			
			$toolbar.find('.delete').unbind().click(function(){
				
				SK_confirm(handler.$("#confirm_title").show(), handler.$("#confirm_content").show(), function(){
					handler.ajaxCall('ajax_DeletePhoto', {photo_id: handler.slots[slot_number].image.id});
				});
			});
			
			$toolbar.find('.make_thumb').unbind().click(function(){
				handler.makeThumbnail(handler.slots[slot_number].image.id);
			});
			
			$toolbar.find("a.permalink").attr("href", this.slots[slot_number].image.fullsize_url);
			
			$toolbar.show();			
		},
		
		makeThumbnail: function(photo_id) {
			this.showConsoleThumbPreloader();
			this.ajaxCall('ajax_CreateThumb', {photo_id: photo_id});
		},
		
		hideToolbar : function() {
			var $det_cont = this.$details_container;
			var $toolbar = $det_cont.find('.photo_toolbar');
			$toolbar.find("a").unbind();
			$toolbar.find("a.permalink").attr("href", "");
			$toolbar.hide();
		},
		
		hideInfo : function() {
			$desc_cont = this.$details_container.find(".photo_info").hide();
			$desc_cont.find(".info_cont").hide();
			$desc_cont.find(".info_form").hide();
			$desc_cont.find("a.edit").unbind().hide();
			$desc_cont.find("a.add").unbind().hide();
			$desc_cont.find(".info_form form").unbind().find("textarea[name=description]").val("");
			$desc_cont.find(".info_form form").unbind().find("input[name=title]").val("");
		},
		
		//showDescription : 
		
		showInfo: function(slot_number) {
			var $desc_cont = this.$details_container.find(".photo_info");
			var slot = this.slots[slot_number];
			if (slot == undefined) {
				slot = this.current_slot;
			}
			
			var image = slot.image;
			var handler = this;
			
			this.hideInfo();
				
			$desc_cont.find(".info_form form input[name=cancel]").unbind().click(function(){
				handler.showInfo();
			});
			
			$desc_cont.find(".info_form form").unbind().submit(function(){
				var description = $(this).find("textarea[name=description]").val();
				var title = $(this).find("input[name=title]").val();
				
				image.txt_description = description;
				
				handler.ajaxCall('ajax_SaveInfo',{photo_id: image.id, description: description, title: title}, {success: function(result){
					image.txt_description = result.txt_description;
					image.html_description = result.html_description;
					image.title = result.title;
					handler.showInfo();
				}});
				
				return false;
			});
			
			if (image.txt_description || image.title) {
				$desc_cont.find("a.edit").unbind().click(function(){
					$(this).hide();
					$desc_cont.find(".info_cont").hide();
					$desc_cont.find(".info_form").show().find("input[name=title]").val(image.title);
					$desc_cont.find(".info_form").show().find("textarea[name=description]").val(image.txt_description);
				}).show();
				
				$desc_cont.find(".info_cont").show().find(".title").text(image.title);
				$desc_cont.find(".info_cont").show().find(".description").html(image.html_description);
			}
			else {
				$desc_cont.find("a.add").unbind().click(function(){
					$(this).hide();
					$desc_cont.find(".info_form").show().find("textarea[name=description]").val("");
					$desc_cont.find(".info_form").show().find("input[name=title]").val("");
				}).show();
			}
			
			$desc_cont.show();
		},
		
		showAdmonStatus: function(slot_number) {
			var $info_cont = this.$details_container.find(".photo_info");
			var $status_cont = $info_cont.find(".status_cont").show();
			$status_cont.find("span").hide();
			var image = this.slots[slot_number].image;

			if (image.authed) {
				$info_cont.find('.authed').show()
				$info_cont.find('.unauthed').hide()
			} else {
				$info_cont.find('.unauthed').show();
				$info_cont.find('.authed').hide()
			}
				
			$status_cont.find("span."+image.status).show();
			
		},
		
		
		showStatus: function(slot_number) {
			var $state_cont = this.$details_container.find(".status_cont").hide();
			var slot = this.slots[slot_number];
			var handler = this;
			if (slot == undefined) {
				slot = this.current_slot;
			}
			var $password_cont = $state_cont.find(".password").hide();
			
			var image = slot.image;
			
			var showPassword = function() {
				handler.ajaxCall('ajax_GetPassword',{photo_id: image.id}, {success: function(result){
					showPasswordForm(result);
				}});
			}
			
			var showPasswordForm = function(old_password) {
				$state_cont.find(".password").show();
				
				if (old_password != undefined && old_password) {
					$state_cont.find(".password input[name=password]").val(old_password);
				}else {
					$state_cont.find(".password input[name=password]").val("");
				}
								
				$state_cont.find(".password form").unbind().submit(function(){
					var password = $(this).find("input[name=password]").val();
					if(password) {
						handler.ajaxCall('ajax_ChangeStatus',{photo_id: image.id, status: 'password_protected' , password: password}, {success: function(){
							image.publishing_status = 'password_protected';
						}});
					}
					return false;
				});
			}

			$state_cont.find("option[value="+image.publishing_status+"]").attr("selected",true);
			if ($state_cont.find("select[name=status]").val()=="password_protected") {
				showPassword();
			}
						
			$state_cont.find("select[name=status]").unbind().change(function(){
				$status_select = $(this);
				
				if ($status_select.val()=="password_protected") {
					showPassword();
				}
				else {
					$state_cont.find(".password").hide();
					handler.ajaxCall('ajax_ChangeStatus',{photo_id: image.id, status: $status_select.val() , password: ""}, {success: function(){
						image.publishing_status = $status_select.val();
					}});
				}
			
			});
			
			$state_cont.show();
			
		},
		
		hideStatus: function(){
			var $state_cont = this.$details_container.find(".status_cont").hide();
			$state_cont.find(".password").hide().find("input[name=password]").val("");
			$state_cont.find("a.change_password").hide();
		},
				
		emptySlot : function(slot_number) {
			var slot = this.slots[slot_number];
			if (slot == undefined) {
				slot = this.current_slot;
			}
			
			if (slot.active && !slot.empty) {
				this.hideToolbar();
				slot.image.$node.remove();
				slot.image = undefined;
				this.setCurrentSlot(slot.number);
			}
		},
		
		
		changeConsoleThumb: function(src){
			if(window.sk_component_member_console != undefined) {
				window.sk_component_member_console.changeThumb(src);
			}
		},
		
		showConsoleThumbPreloader: function(){
			if(window.sk_component_member_console != undefined) {
				window.sk_component_member_console.showPreloader();
			}
		},
		
	/*-------------------< Upload >------------------------*/
	
		reloadPage: function() {
			window.location.reload(true);
		},
		
		debug : function(x) {
			console.debug(x);
		}
		
	});
	
	
String.prototype.nl2br = function() {
    breakTag = '<br />';
    return (this + '').replace(/([^>]?)\n/g, '$1'+ breakTag +'\n');
}

	
	
	
	