function component_HotRate(auto_id)
{
	this.DOMConstruct('HotRate',auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_HotRate.prototype =
	new SK_ComponentHandler({

	photo: null,
	
	sex: null,
		
	construct : function(){
		var handler = this;
		
		var change_photo = function() {
			handler.ajaxCall("ajax_changePhoto", {sex: handler.sex}, {success: function(data){
					if (data.result){
						handler.setPhoto(data);
						handler.$(".rate_container").show();
						handler.$(".profile_link").show();
					} else {
						handler.$(".photo_container").css("background", "");
						handler.$(".photo_container").html('<div class="no_content">' + data.msg + '</div>')
						handler.$(".rate_container").hide();
						handler.$(".profile_link").hide();
					}
			}});
		};
		
		this.$(".sex_switch select").unbind().change(function(){
			handler.sex = $(this).val();
			change_photo();
		});
		
		this.$(".rate_container .skip a").unbind().click(function(){
			change_photo();
		});
		
		
		
		this.rate_cmp_ready(function(cmp){
			cmp.rateSuccess = function() {
				change_photo();
			}
		});
	},
	
	setPhoto: function(photo){
		
		var $photo_cont = this.$(".photo_container");
		$photo_cont.empty();
		$photo_cont.css("opacity", "0.7");
		this.$(".profile_link a").attr("href", photo.profile_url);
		
		this.reloadRate(photo.photo_id, function(){
			$photo_cont.css("background", "url(" + photo.url + ") center no-repeat");
			$photo_cont.css("opacity", "1");
		});
	},
	
	
	rate_cmp_ready: function(callback_func) {
		for (var i = 0; i < this.children.length; i++) {
			if (this.children[i].cmp_class=="Rate") {
				callback_func(this.children[i]);
				return true;
			}
		}
		
		var handler = this;
		window.setTimeout(function(){
			handler.rate_cmp_ready(callback_func);
		}, 50);
	},
	
	reloadRate: function(item_id, callback_func) {
		var handler = this;
		this.rate_cmp_ready(function(cmp){
			cmp.reload({entity_id: item_id, feature: "photo", block: false}, {success: function(){
				if (callback_func != undefined) {
					callback_func();
				}
				handler.construct();
			}});
		});
	}
	
});

