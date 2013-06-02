function component_HotList(auto_id)
{
	this.DOMConstruct('HotList', auto_id);
}

component_HotList.prototype =
	new SK_ComponentHandler({
	
	construct: function(confirm_msg, auth_msg){
			
		var handler = this;
		
		var $join_link = this.$('#become_hot');
				
		$join_link.click(function() {
			SK_confirm($("<span>" + confirm_msg + "</span>"), function() {
				if ( auth_msg != '' )
				{
					SK_drawError(auth_msg);
				}
				else
				{
					handler.ajaxCall( 
						'ajax_AddToHotList', 
						{},
						{success: function(data){ if (data.result) window.location.reload(); } } 
					);
				}
			});
		});	
	}
});
