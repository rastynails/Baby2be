function component_EventSpeedDatingEdit(auto_id)
{
	this.DOMConstruct('EventSpeedDatingEdit', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_EventSpeedDatingEdit.prototype =
	new SK_ComponentHandler({
		
		construct : function( bId ) {
			
			var handler = this;
			
			if( bId )
			{
				this.$('#img_file_cont a').bind( 'click', function(){
					handler.$('#img_file_cont').css({display:'none'});
					
					handler.ajaxCall('ajax_deleteImage', {id:bId}, {success: function(){
						handler.$('#input_file_cont').css({display:'block'});													
					}} );
				} );
			}
		}
		
	});