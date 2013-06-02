function component_MPlayer(auto_id)
{
	this.DOMConstruct('MPlayer', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_MPlayer.prototype =
	new SK_ComponentHandler({

		construct : function(param){
			
			if(param != 1) return;
			
			var handler = this;
			
			this.$('#edit_cmp').bind('click',
				function(){
					handler.floatBox = new SK_FloatBox({
						$title		: handler.$( '#edit_form_label' ),
						$contents	: handler.$( '#edit_form' ),
						width		: 500
						
						
					});		
				}
			);
			
		},
		
		submitMPlayerCode: function(data){
			this.reload(data);
			this.floatBox.close();
			window.location.reload();
		}
		
	});