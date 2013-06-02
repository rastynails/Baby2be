function component_Group(auto_id)
{
	this.DOMConstruct('Group', auto_id);
}

component_Group.prototype =
	new SK_ComponentHandler({
	
	construct: function(params){
			
		var handler = this;
		handler.group_id = params.group_id;
		
		var $edit_btn = this.$('#edit_btn');
		var $invite_btn = this.$('#invite_btn');
		var $massmailing_btn = this.$('#massmailing_btn');
		var $claims_btn = this.$('#claims_btn');
		
		var $leave_btn = this.$('#leave_group');
		
		$edit_btn.click(function() {
			window.location = params.url_edit;
		});
		
		$invite_btn.click(function() {
			window.location = params.url_invite;
		});
		
		$claims_btn.click(function() {
			window.location = params.url_claims;
		});
		
		$massmailing_btn.click(function() {
			window.location = params.url_mails;
		});
		
		this.$('#btn_decline')
			.bind( 'click',
				function(){
					SK_confirm(
						$('<div>' + params.confirm_msg + '</div>'), 
						function(){ handler.ajaxCall(
							'ajax_declineInvitation', 
							{group_id: params.group_id, profile_id: params.profile_id},
							{success: function(){window.location.reload();}}
						);} 
					);
				}
			);
			
		this.$('#btn_accept')
			.bind( 'click',
				function(){ handler.ajaxCall(
					'ajax_acceptInvitation', 
					{group_id: params.group_id, profile_id: params.profile_id},
					{success: function(){window.location.reload();}}
				);} 
			);
			
		$leave_btn.click(function() {
			SK_confirm($("<span>" + params.confirm_leave + "</span>"), function() {
					handler.ajaxCall(
						 'ajax_LeaveGroup', 
						 {group_id: params.group_id},
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
