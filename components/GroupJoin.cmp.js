function component_GroupJoin(auto_id)
{
	this.DOMConstruct('GroupJoin', auto_id);
}

component_GroupJoin.prototype =
	new SK_ComponentHandler({
	
	construct: function(group_id, confirm_msg, claim_msg){
			
		var handler = this;
		handler.group_id = group_id;
		
		var $join_link = this.$('#join_link');
		var $join_btn = this.$('#join_btn');
		var $claim_link = this.$('#claim_link');
				
		$join_link.click(function() {
			SK_confirm($("<span>" + confirm_msg + "</span>"), function() {
					handler.ajaxCall( 'ajax_JoinGroup', {group_id: handler.group_id} );
				});
			});
		
		$join_btn.click(function() {
			SK_confirm($("<span>" + confirm_msg + "</span>"), function() {
					handler.ajaxCall(
						 'ajax_JoinGroup', 
						 {group_id: handler.group_id},
						 {success: function(data){
								if (data.result){
									window.location.reload();
								}
							}
						} 
					);
				});
			});
				
		$claim_link.click(function() {
			SK_confirm($("<span>" + claim_msg + "</span>"), function() {
					handler.ajaxCall( 
						'ajax_ClaimGroupAccess', 
						{group_id: handler.group_id}, 
						{success: function(data){
								if (data.result){
									window.location.reload();
								}
							}
						}
					);
				});
			});	
		}
});
