function component_CommentList(auto_id)
{
	this.DOMConstruct('CommentList', auto_id);

	var handler = this;

	this.references = [];

	this.delegates = {

	};
}

component_CommentList.prototype =
	new SK_ComponentHandler({

		construct : function( pages, commentDeleteLinks, mode ){

			var handler = this;

			var feature = commentDeleteLinks.feature;

			var entity_id = commentDeleteLinks.entity_id;

            var entityType = commentDeleteLinks.entityType;

			var page = commentDeleteLinks.page;

			if( pages != null )
			{
				$.each( pages, function(index,data){
					if( data.active ) return;
					handler.$('#' + data.id).bind( 'click',
						function(){
							//handler.$('#comment_list_cont').css({backgroundColor:'red'});
							handler.reload({feature:feature, entity_id:entity_id, entityType: entityType, page:data.label});
						}
					);
				} );
			}

			$.each( commentDeleteLinks.items, function( index, data ){
				handler.$('#' + data.delete_link_id).bind( 'click', function(){
					//handler.$('#comment_list_cont').css({backgroundColor:'red'});
					handler.ajaxCall('ajax_deleteComment',{id_to_delete:data.id, feature:feature, entity_id:entity_id, entityType: entityType, page:page, mode:mode});
				} );
			} );
            
            setTimeout(function(){handler.reload({feature:feature, entity_id:entity_id, entityType: entityType, page:page}); }, 30000);
		}
	});