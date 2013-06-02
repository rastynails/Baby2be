function component_ForgotPassword(auto_id)
{
	this.DOMConstruct('ForgotPassword', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ForgotPassword.prototype =
	new SK_ComponentHandler({
		
		construct : function(input_id, send_btn_id, cancel_id){
			var $input = $("#"+input_id);
			$("#".send_btn_id).click()
			this.$status_node = $("#"+status_node);
		},
		
		display : function(btn_label, status_label, status){
			var handler = this;
			
			this.$change_btn_node.empty();
			this.$status_node.empty();
			
			this.$status_node.text(status_label);
						
			var $btn_node = $('<a href="javascript://">').appendTo(this.$change_btn_node);
			$btn_node.text(btn_label);
			$btn_node.click(function(){
				handler.ajaxCall('ajax_ChangeStatus',{last_status: status})
			});
		},
	});