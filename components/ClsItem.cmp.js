
function component_ClsItem(auto_id)
{
	this.DOMConstruct('ClsItem', auto_id);
	
	var handler = this;
	
	this.delegates = {
			
	};
}

component_ClsItem.prototype =
	new SK_ComponentHandler({
		
	construct: function(fullsize, item_info) 
	{
		var handler = this;
		
		this.size = fullsize;
		
		this.item_info = item_info;
		
		this.$("img.item_files").bind("click", function() {
			handler.showPhotoThickbox(this);
		});
		
		this.$("#delete_item").bind("click", function() {
			handler.showConfirmBox(item_info.item_id);
		});
		
		this.$("#approve_item").bind("click", function() {
			handler.approveItem(item_info.item_id);
		});
	}, 
	
	approveItem: function(item_id)
	{
		var handler = this;		
		handler.ajaxCall( 'ajax_ApproveItem', {entity_id: handler.item_info.item_id} );
		this.$("#approve_item").remove();
	},
	
	showConfirmBox: function(item_id) 
	{
		var handler = this;
		
		handler.confirm_box = new SK_confirm( handler.$("#confirm_title").text(), handler.$("#confirm_content").show(), function(){
			handler.ajaxCall( 'ajax_DeleteItem', {entity_id: handler.item_info.item_id} );
		});
	},
	
	showPhotoThickbox: function(photo_node) 
	{
		var width = $(photo_node).width();
		var height = $(photo_node).height();
		
		var photo_dimension = this.getPhotoSize(width, height);	
				
		var photo_node = $(photo_node).clone().addClass("show_photo").removeClass("item_files");

		this.$(".item_file_content").empty();
		this.$(".item_file_content").html(photo_node);
		
		var $photo = this.$("img.show_photo");
		
		$photo.attr( {width: photo_dimension.width, height: photo_dimension.height} );
		$photo.height(photo_dimension.height);
		
		this.photo_box = new SK_FloatBox({
			$title		: this.$(".item_file_title").text(),
			$contents	: this.$(".item_file_content"),
			width		: photo_dimension.width + 8,
			height		: photo_dimension.height + 37
		});	
	},
	
	getPhotoSize: function(width, height)
	{
		var w1 = Math.floor(this.size/height*width);
		var h1 = Math.floor(this.size/width*height);
		
		if ( w1 <= this.size && w1 >= h1 || h1 >= this.size) {
			var photo_dimension = { width : w1, height: this.size };
		}
		else {
			var photo_dimension = { width : this.size, height: h1 };
		}
		
		return photo_dimension;
	},
	
	redirect: function(url) {
		if ( url == undefined ) 
			window.location.reload();
		else 
			window.location.href = url;
	}
});
