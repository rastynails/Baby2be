function component_BlogPostImageList(auto_id)
{
	this.DOMConstruct('BlogPostImageList', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_BlogPostImageList.prototype =
	new SK_ComponentHandler({

		construct : function( images, post_idP ){
			
			var handler = this;
			
			if( images != null)
			{
				$.each( images, function(index,data){
					handler.$('#del_image_' + data.id).bind('click', 
						function(){
							handler.ajaxCall('ajax_deleteImage', {id:data.id}, {success:
								function(){
									handler.reload({post_id:post_idP});	
								}}); 
						}
					);
					
					handler.$('#image_link_' + data.id).bind( 'click',
						function(){
							handler.parent.onSelect(data.url, data.label);
						}
					);
				} );
			}
		}
	});