function component_Event(auto_id)
{
	this.DOMConstruct('Event', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_Event.prototype =
	new SK_ComponentHandler({
		
		construct : function( data ) {
			
			this.approve_status = data.status;
			
			var handler = this;
			
			this.$('#delete')
				.bind( 'click',
					  function(){
						  SK_confirm( $('<div>'+data.confirm_cap+'</div>'), 
										$('<div>'+data.confirm_msg+'</div>'), 
											function(){handler.ajaxCall('ajax_deleteEvent', {id:data.id});} );
				 
					  }
				);
			
			this.$('#approve')
				.bind( 'click',
					function(){
						handler.ajaxCall('ajax_updateEventStatus', {id:data.id,status:handler.approve_status}, {success: 
							function(){
								handler.$('#approve').attr( 'value', handler.approve_status );
								
								if( handler.approve_status == 'active' )
								{
									handler.approve_status = 'none';
									handler.$('#approve').attr( 'value', data.block_label );
								}
								else
								{
									handler.approve_status = 'active';
									handler.$('#approve').attr( 'value', data.approve_label );
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