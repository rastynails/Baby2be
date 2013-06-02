function component_ForumBannedProfileList(auto_id)
{
	this.DOMConstruct('ForumBannedProfileList',auto_id);
	
	var handler = this;
	
	this.references = [];
		
	this.delegates = {
		
	};
}

component_ForumBannedProfileList.prototype =
	new SK_ComponentHandler({
	
	mode : "",
	
	items : [],
	
	construct : function(profiles){
		var handler = this;		
		$.each( profiles, function(index, profile_id){
			var $item_node = handler.$("#profile_" + profile_id);
			handler.items[profile_id] = $item_node;
			handler.$( "a", $item_node ).bind( 'click', function(){
				handler.ajaxCall( 'ajax_RemoveBan', {profile_id: profile_id} );
			} );
		} );
	},
	
	removeBlock : function(profile_id){
		var handler = this;		
		var $node = this.items[profile_id];
		
		$($node).remove();			
	}
		
});

