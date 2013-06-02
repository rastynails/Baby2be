function component_CommentsOverview(auto_id)
{
	this.DOMConstruct('CommentsOverview', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_CommentsOverview.prototype =
	new SK_ComponentHandler({
		
		construct : function( commentDeleteLinks, mode ){
			
			var handler = this;
			
			$.each( commentDeleteLinks.items, function( index, data ){
				handler.$('#' + data.delete_link_id).bind( 'click', function(){
					handler.ajaxCall('ajax_deleteComment',{ id_to_delete:data.id, feature:data.feature, entity_id:data.entity_id });
                    
                    //window.location.reload();
				} );
			} );	
		},

        remove: function(feature, id)
        {
            $('#'+feature+'_'+id).remove();
        }
	});