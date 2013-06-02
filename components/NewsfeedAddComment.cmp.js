function component_NewsfeedAddComment(auto_id)
{
	this.DOMConstruct('NewsfeedAddComment', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_NewsfeedAddComment.prototype =
	new SK_ComponentHandler({
		
		construct : function(){

            var handler = this;
            
            //var commentDeleteLinks = this.ownerComponent.children;
/*            var children = this.ownerComponent.children;

			for (var i = 0; i < children.length; i++) {
				var child = children[i];

				if (child instanceof component_CommentList && child.feature == data.feature && child.entity_id == data.entity_id) {
					child.reload({entity_id:data.entity_id, feature:data.feature, page:1, mode:data.mode});
				}
			}

			$.each( commentDeleteLinks.items, function( index, data ){
				handler.$('#' + data.delete_link_id).bind( 'click', function(){
                    
                    handler.ownerComponent.parent.comments--;
					
				} );
			} );
*/
		},
		
		commentListRepaint: function( html ){
			this.$('#comment_list_cont').empty().append(html);
		}
		
	});