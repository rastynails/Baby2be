
function component_ChuppoIm(auto_id)
{
	this.DOMConstruct('ChuppoIm', auto_id);
	
	var handler = this;
	
	this.delegates = {
			
	};
}

component_ChuppoIm.prototype =
	new SK_ComponentHandler({
	
	construct: function( activityInterval, chuppoImVars ) {
		var handler = this;		
		loadIm( 'chuppo_im_cont', chuppoImVars );
		window.setInterval( function(){
			handler.updateProfileActivity( handler );
		}, activityInterval );
	},
	
	updateProfileActivity: function( handler ) {
		handler.ajaxCall( 'ajax_updateProfileActivity', {} );
	}	
	
});
