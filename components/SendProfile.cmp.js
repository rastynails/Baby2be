function component_SendProfile(auto_id)
{
	this.DOMConstruct('SendProfile', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_SendProfile.prototype =
	new SK_ComponentHandler({

		construct : function(){
			var handler = this;
			
			this.$('#send_profile').bind( 'click', 
				function(){
					handler.floatBox = new SK_FloatBox({
						$title		: handler.$( '#send_profile_title' ),
						$contents	: handler.$( '#send_profile_cont' ),
						width		: 400						
					});		
				} 
			);
		}
	});