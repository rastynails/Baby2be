function component_ProfileComponentSelect(auto_id)
{
	this.DOMConstruct('ProfileComponentSelect', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.cmps = {};
	
	this.delegates = {
	
	};
}

component_ProfileComponentSelect.prototype =
	new SK_ComponentHandler({
		construct : function( param ){
			
			var handler = this;
			//console.debug(param);
			
			//TEMP
			var floatBox;
			
			if ( param.is_available ) {
				this.$('#input').bind( 'click', 
						function(){
							$data = handler.$( '#profile_cmp_select' );
							floatBox = new SK_FloatBox({
								$title		: $( '.title', $data ),
								$contents	: $( '.content', $data ),
								width		: 690
								
								
							});		
						} 
					);				
			}
			else {
				this.$('#input').attr('disabled', 'disabled');
			}
			
			$.each( param.cmps, 
				function( index, data ){
					handler.cmps[data.id] = {id:data.id, status:false};
					handler.$( '#' + data.id ).bind( 'click', function(){
						if( handler.cmps[data.id].status == true ){
							$( this ).removeClass('selected');
							handler.cmps[data.id].status = false;
						}
						else{
							$( this ).addClass('selected');
							handler.cmps[data.id].status = true;
						}
						
					} );
				}
			);
			//console.debug( this.cmps );
			handler.$('#cmps_submit').bind( 'click', function(){
				
				var cmpArray = [];
				
				$.each( handler.cmps, 
					function( index, data ){
						if( data.status == true ){
							cmpArray[parseInt(data.id)] = data.id;
						}
					}
				);
				
				if( cmpArray.length == 0 ){
					return;
				}
				
				handler.ajaxCall('ajax_addCmp', 
					{cmps:cmpArray}, 
					{success: function(){window.location.reload();}}
				);
	
				$(this).unbind('click');
			} );
		}
		
	});