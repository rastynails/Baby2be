function component_VideoView(auto_id)
{
	this.DOMConstruct('VideoView', auto_id);
	
	var handler = this;
}

component_VideoView.prototype =
	new SK_ComponentHandler({
		
	construct: function(hash, profile_id) {
		var handler = this;
						
		this.$("#password_unlock form").submit(function(){
			handler.ajaxCall("ajaxUnlock", {hash: hash, profile_id: profile_id, password: $(this.password).val()});
			return false;
		});
	},
		
	refresh: function(){
		window.location.reload();
	}
});