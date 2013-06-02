function component_VideoControls(auto_id)
{
	this.DOMConstruct('VideoControls', auto_id);
	
	var handler = this;
}

component_VideoControls.prototype =
	new SK_ComponentHandler({
	
	construct: function(video_id) {
	
		this.video_id = video_id;
		var handler = this;
				
		var $delete_control = this.$("#delete_control");
		var $delete_title = this.$("#delete_title").children();
		var $delete_msg = this.$("#delete_msg").children();
		
		$delete_control.click(function(){		
			SK_confirm($delete_title, $delete_msg, function() {
				handler.ajaxCall("ajax_DeleteVideo", {video_id: handler.video_id});
			});
		});		
	},
	
	reloadList: function(){
		var handler = this;
		handler.parent.reload();
	}		
});

