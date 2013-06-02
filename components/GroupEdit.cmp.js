function component_GroupEdit(auto_id)
{
	this.DOMConstruct('GroupEdit', auto_id);
}

component_GroupEdit.prototype =
	new SK_ComponentHandler({
	
	construct: function(group_id, confirm_msg, url){
			
		var handler = this;
		handler.group_id = group_id;
		
		var $delete_btn = this.$('#delete_btn');
		
		this.$('#img_file_cont a').bind( 'click', function(){
			handler.$('#img_file_cont').css({display:'none'});
			
			handler.ajaxCall('ajax_deleteImage', {id:handler.group_id}, {success: function(){
				handler.$('#input_file_cont').css({display:'block'});													
			}} );
		} );
		
		$delete_btn.click(function() {
			SK_confirm($("<span>" + confirm_msg + "</span>"), function() {
					handler.ajaxCall(
						 'ajax_DeleteGroup', 
						 {group_id: handler.group_id},
						 {success: function(data){
								if (data.result){
									window.location = url;
								}
							}
						} 
					);
				});
			});	
		}
});
