function component_PhotoView(auto_id)
{
	this.DOMConstruct('PhotoView', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_PhotoView.prototype =
	new SK_ComponentHandler({
		
	position: undefined,	
		
	construct: function(photo_id, pos) {
		var handler = this;
		if (photo_id == undefined) {
			return ;
		}
		
		this.position = pos;
				
		this.$(".preview_cont .password form").submit(function(){
			handler.ajaxCall("ajax_unlock", {photo_id: photo_id, password: $(this.password).val()});
			return false;
		});
		
		$(window).load(function(){
			handler.pageReady();
		});
			
	},
	
	pageReady: function() {
		this.$(".carousel_preloader").hide();
		
		this.$("#carousel").show().jcarousel({
			start: this.position,
			scroll: 8
		});
	},
	
	refresh: function(){
		window.location.reload();
	}
});