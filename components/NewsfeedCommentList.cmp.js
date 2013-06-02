function component_NewsfeedCommentList(auto_id)
{
	this.DOMConstruct('NewsfeedCommentList', auto_id);

	var handler = this;

	this.references = [];

	this.delegates = {

	};
}

component_NewsfeedCommentList.prototype =
	new SK_ComponentHandler({

		construct : function( pages, commentDeleteLinks, mode ){

			var handler = this;

			this.feature = commentDeleteLinks.feature;

            this.entityType = commentDeleteLinks.entityType;

			this.entity_id = commentDeleteLinks.entity_id;

			this.page = commentDeleteLinks.page;

			if( pages != null )
			{
				$.each( pages, function(index,data){
					if( data.active ) return;
					handler.$('#' + data.id).bind( 'click',
						function(){

							handler.reload({feature:handler.feature, entity_id:handler.entity_id, entityType: handler.entityType, page:data.label});
						}
					);
				} );
			}

			$.each( commentDeleteLinks.items, function( index, data ){
				handler.$('#' + data.delete_link_id).bind( 'click', function(){

					handler.ajaxCall('ajax_deleteComment',{id_to_delete:data.id, feature:handler.feature, entity_id:handler.entity_id, entityType:handler.entityType, page:handler.page, mode:mode});
                    SK_EventManager.trigger(handler.entityType+'_'+handler.entity_id+'_onNewsfeedCommentListCommentDelete');
				} );
			} );

            handler.$('.newsfeed_comments_view_all').click(function(){
                handler.$('.item').css('display', 'block');
                handler.$('.newsfeed_comments_view_all').hide();
            });
		}
	});