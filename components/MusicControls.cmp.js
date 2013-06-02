function component_MusicControls(auto_id)
{
	this.DOMConstruct('MusicControls', auto_id);
	
	var handler = this;
}

component_MusicControls.prototype =
	new SK_ComponentHandler({
	
	construct: function(music_id) {
	
		this.music_id = music_id;
		var handler = this;
				
		var $delete_control = this.$("#delete_control");
		var $delete_title = this.$("#delete_title").children();
		var $delete_msg = this.$("#delete_msg").children();
		
		$delete_control.click(function(){		
			SK_confirm($delete_title, $delete_msg, function() {
				handler.ajaxCall("ajax_DeleteMusic", {music_id: handler.music_id});
			});
		});		
	},
	
	reloadList: function(){
		var handler = this;
		handler.parent.reload();
	}		
});

