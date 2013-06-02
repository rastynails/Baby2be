function component_PhotoGallery(auto_id)
{
	this.DOMConstruct('PhotoGallery', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_PhotoGallery.prototype =
	new SK_ComponentHandler({
		
		$carousel: undefined,
		
		carousel_item_size : 50,
		
		$preview: undefined,
		
		$toolbar: undefined,
			
		photos: [],
		
		active_item: undefined,
		
		construct : function(){
			var handler = this;
			this.$carousel = this.$("#carousel");
			
			this.$carousel_item_prototype = this.$carousel.find(".carousel_item").clone();
			this.$carousel_item_prototype.remove();
			
			this.$preview = this.$(".preview");
					
			this.$toolbar = this.$(".toolbar");
		},
		
		registerPhoto: function(photo) {
			var handler = this;
			var $image_prototype = this.$carousel_item_prototype.find("img");
			var $item_node = $( document.createElement("li") );
			var size = this.carousel_item_size;	
			
			
									
			if ($image_prototype.attr("height")) {
				size = $image_prototype.attr("height");
			}
				
			var $item_image = $(new Image());
			
			var photo_item = {
					'thumb_src'			: photo.thumb_src,
					'preview_src'		: photo.preview_src,
					'$thumb_node'		: $item_node,
					'fullsize_url'		: photo.fullsize_url,
					'description'		: photo.description,
					'title'				: photo.title,
					'publishing_status'	: photo.publishing_status,
					'id'				: photo.id,
					'unlocked'			: photo.unlocked,
					'authed'			: photo.authed,
					'loaded'			: false,
					'view_tracked'		: false
			};
						
			$item_node.click(function(){
				handler.activatePhoto(photo_item);				
			});
			
			this.photos.unshift(photo_item);
			
			this.$carousel.append($item_node);
			
			$item_image.load(function(){
				
				$item_image.attr("width", size);
				$item_image.attr("height", size);
				$item_node.append($item_image);
				$item_node.show();
				photo_item.loaded = true;
			});	
			
			$item_image.attr("src", photo.thumb_src);	
		},
		
		activatePhoto: function(photo_item, refresh) {
			var handler = this;
			
			if ( (this.active_item == photo_item) && !refresh) {
				return ;
			}
			
			this.hideAuthMark();
			
			var on_track_func = function()
			{
				var $image = handler.$preview.find(".image");
			
				if (photo_item.publishing_status != 'password_protected' || photo_item.unlocked) {
					handler.$preview.find(".password_cont").hide();
				}
				
				if (this.active_item || typeof handler.active_item != "undefined") {
					$image.css("opacity", 0);
					handler.active_item.$thumb_node.removeClass("active");
				}
				handler.active_item = photo_item;
				photo_item.$thumb_node.addClass("active");
				
				handler.$toolbar.hide();
				if (photo_item.description || photo_item.title) {
					$image.unbind().tooltip(handler.$("#tooltip"));
				}else {
					$image.unbind();
				}
				
				handler.$preview.find(".image").click(function(){
					window.location.href = photo_item.fullsize_url;
				});
				
				handler.$(".photo_info .description").empty().html(photo_item.description);
				handler.$(".photo_info .title").empty().text(photo_item.title);
	
				if (photo_item.fullsize_url) {
						handler.$toolbar.show().find(".fullsize_btn")
							.attr("href", photo_item.fullsize_url);
				}
					
				handler.reloadRate(photo_item);
				
				if (!photo_item.unlocked) {
					handler.$preview.find(".rate_container").hide();
					if (photo_item.publishing_status == 'password_protected') {
						handler.showPassworForm(photo_item);
					}
				} else {
					handler.$preview.find(".rate_container").show();
				}
				
				if( photo_item.authed ) {
					handler.showAuthMark();
				}
				
				$image.css("background", "url("+photo_item.preview_src+") center no-repeat")
				.animate({opacity:1}, "slow", function(){
									
				});
			}
			
			
			if (photo_item.unlocked) {
				this.trackView(photo_item, function(result){
					if (result.result) {
						on_track_func();
					} else {
						handler.disableComponent(result.message);
					}
				});
			} else {
				on_track_func();
			}
			
		},
		
		disableComponent: function(msg) {
			this.$preview.find(".image").css("background", "");
			this.$preview.find(".image").html(msg);
			
		},
		
		
		trackView: function(item, callback_func) {
			if (!item.view_tracked) 
			{
				this.ajaxCall("ajax_trackView", {photo_id: item.id}, {success:function(result){
					if (result.result) {
						item.view_tracked = true;
					}
					
					if (callback_func !=undefined) {
							callback_func(result);
					}
					
				}});
				
			} else {
				if (callback_func !=undefined) {
						callback_func({result: true});
				}
			}
						
			item.first = false;
		},
		
		showPassworForm: function(item) {
			var $pass_cont = this.$preview.find(".password_cont").fadeIn("fast").mouseover(function(){
				return false;
			}).click(function(e){
				var $target = $(e.target);
				if ($target.is("input[type=submit]")) {
					$target.parents("form").submit();
				}
				return false;
			});
			
			var handler = this;
			$pass_cont.find("form").unbind().submit(function(){
				var password = $(this).find("input[name=password]").val();
				handler.ajaxCall("ajax_unlock", {photo_id: item.id, password: password}, {success: function(result){
					if (result) {
						item.preview_src = result.preview_src;
						item.thumb_src = result.thumb_src;
						item.description = result.description;
						item.unlocked = result.unlocked;
						handler.refreshThumb(item);
						handler.activatePhoto(item, true);
					}
					$pass_cont.find("form input[name=password]").val("");
				}});
								
				return false;
			});
		},
		
		refreshThumb: function(item) {
			var $img = item.$thumb_node;
			var handler = this;
			$new_img = $(new Image());
			$new_img.attr("src", item.thumb_src);
			$new_img.load(function(){
				$new_img.attr("width", handler.carousel_item_size);
				$new_img.attr("height", handler.carousel_item_size);
				item.$thumb_node.empty().append($new_img);
			});
			
		},
			
		
		first: true,
		reloadRate: function(item, fnc_callback) {
			if (!item.unlocked || this.first) {
				this.first = false;
				return false;
			}
			
			var rate_cmp;
			for (var i = 0; i < this.children.length; i++) {
				if (this.children[i].cmp_class=="Rate") {
					rate_cmp = this.children[i];
					break;
				}
			}
			if (rate_cmp != undefined) {
				rate_cmp.reload({entity_id: item.id, feature: "photo", block: false}, {success: function(result){
					if (fnc_callback!=undefined) {
						fnc_callback(result);
					}
				}});
			}
			
			this.$preview.find(".rate_container").show();
			rate_cmp = null;
		},
		
		display: function() {
			var handler = this;
			this.runCarousel(function(){
				var first_photo = handler.photos[handler.photos.length-1];
				first_photo.first = true;
				handler.activatePhoto(first_photo);
			});
			
		},
		
		showAuthMark: function(){
			this.$('#auth_mark').show();
		},
		
		hideAuthMark: function(){
			this.$('#auth_mark').hide();
		},
		
		runCarousel: function( callback ) {
			
			if ( this.photos.length == 0) {
				this.$(".carousel_cont").hide();
				var $image = this.$preview.find(".image");
				$image.attr('style', "").addClass("empty").fadeIn("normal");
				return ;
			} else if (this.photos.length == 1) {
				this.$(".carousel_cont").hide();
				this.activatePhoto(this.photos[0]);
				return ;
			}
			
			var handler = this;
			
			var pingPhotoLoaded = function() {
				
				for(var key in handler.photos) {
					
					if (!handler.photos[key].loaded) {
						return false;
					}
				}
				return true;
			}
			var interval;
			
			var ping_count = 0;
			
			var tryToRunCarousel = function() {
				ping_count++;
				if (pingPhotoLoaded() || ping_count > 50) {
					window.clearInterval(interval);
					handler.$(".carousel_cont").removeClass("preloader");
					handler.$carousel.show().jcarousel({size: handler.photos.length});
					callback();
				}
			}
			
			interval = window.setInterval(tryToRunCarousel, 50);
		},
		
		debug: function(x) {
			console.debug(x);
		}
		
		
	});