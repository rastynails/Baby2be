function component_GroupModerators(auto_id)
{
	this.DOMConstruct('GroupModerators', auto_id);
}

component_GroupModerators.prototype =
	new SK_ComponentHandler({
	
	construct: function(group_id, confirm_msg){
			
		var handler = this;
		handler.group_id = group_id;
		
		var $remove_links = this.$('ul.moderators span').children();
		
		$.each( $remove_links, 
			function( index, data ){		
				handler.rel = $(this).attr("rel");
				
				$(this).bind('click', function(){
					SK_confirm($("<span>" + confirm_msg + "</span>"), function() {
					handler.ajaxCall( 
						'ajax_RemoveModerator', 
						{group_id: handler.group_id, mod_id: handler.rel }, 
						{success: function(data){
								if (data.result){
									window.location.reload();
								}
							}
						});
					});
				});				
			}
		);						
	}
});
