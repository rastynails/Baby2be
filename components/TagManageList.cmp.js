function component_TagManageList(auto_id)
{
	this.DOMConstruct('TagManageList', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_TagManageList.prototype =
	new SK_ComponentHandler({
		
		construct : function( tag_links ){
			
			var handler = this;
			
			var feature = tag_links.feature;
			
			var entity_id = tag_links.entity_id;
			
			$.each( tag_links.items, function( index, data ){
				handler.$('#' + data.link_id).bind( 'click', function(){ 
					handler.ajaxCall('ajax_deleteTag',{tag_id:data.id, feature:feature, entity_id:entity_id});													
				} );
			} );
		},
		
		updateTagList: function( html ){
			this.$('#tag_list').replaceWith( html );
		}
	});