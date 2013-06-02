function component_ClsCommentList(auto_id)
{
	this.DOMConstruct('ClsCommentList', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ClsCommentList.prototype =
	new SK_ComponentHandler({
		
		construct : function( commentDeleteLinks ){
			var handler = this;
			
			var feature = commentDeleteLinks.feature;
			
			this.entity_id = commentDeleteLinks.entity_id;
			
			this.currency = commentDeleteLinks.currency;
			
			$.each( commentDeleteLinks.items, function( index, data ){
				handler.$('#' + data.delete_link_id).bind( 'click', function(){ 
					handler.ajaxCall('ajax_deleteComment',{id_to_delete:data.id, feature:feature, entity_id:handler.entity_id, currency: handler.currency});												
				} );
			} );	
		},
		
		updateBidInfo: function(){
			var handler = this;
			var entity = this.parent.entity;
			var currency = this.parent.currency;
			var parent_sibling = this.parent.parent.children;
						
			for (var i = 0; i < parent_sibling.length; i++) {
				var parent_sib = parent_sibling[i];
				
				if (parent_sib instanceof component_ClsItemBid) {
					parent_sib.reload({entity_id:this.entity_id, entity: entity, currency: currency});	
				}
			}
		}
		
	});