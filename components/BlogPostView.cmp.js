function component_BlogPostView(auto_id)
{
	this.DOMConstruct('BlogPostView', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_BlogPostView.prototype =
	new SK_ComponentHandler({
		
		construct : function( data ) {
			
			this.approve_status = data.status;
			
			var handler = this;
			
			this.$('#delete')
				.bind( 'click',
					function(){
					handler.ajaxCall('ajax_deletePost', {id:data.id});
					}
				);
			
			this.$('#approve')
				.bind( 'click',
					function(){
						handler.ajaxCall('ajax_updatePostStatus', {id:data.id,status:handler.approve_status}, {success: 
							function(){
								handler.$('#approve').attr( 'value', handler.approve_status );
								
								if( handler.approve_status == 'active' )
								{
									handler.approve_status = 'none';
									handler.$('#approve').attr( 'value', 'Block' );
								}
								else
								{
									handler.approve_status = 'active';
									handler.$('#approve').attr( 'value', 'Approve' );
								}
							}}
						);
					}
				);
		},
	
		redirect: function(url){
			window.location = url;
		}
		
	});