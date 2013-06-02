function component_Attachment(auto_id)
{
	this.DOMConstruct('Attachment', auto_id);
}

component_Attachment.prototype =
	new SK_ComponentHandler({
		
		construct : function(){
			var self = this;
			this.$('.delete_attachment').click(function(){
				self.ajaxCall('ajax_Delete', {id: $(this).attr('sk-attachment-id')});
				$(this).parents('.attachment:eq(0)').remove();
				if (!self.$('.attachment').length) {
					$(self.container_node).remove();
				}
				
				return false;
			});
		}
});