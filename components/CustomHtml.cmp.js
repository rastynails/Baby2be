function component_CustomHtml(auto_id)
{
	this.DOMConstruct('CustomHtml', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_CustomHtml.prototype =
	new SK_ComponentHandler({

		construct : function(param){
			
			if(param != 1) return;
			
			var handler = this;
			
			this.$('#edit_cmp').bind('click',
				function(){
					handler.floatBox = new SK_FloatBox({
						$title		: handler.$( '#edit_form_label' ),
						$contents	: handler.$( '#edit_form' ),
						width		: 622
						
						
					});		
				}
			);
			
		},
		
		submitCustomHtml: function(data){
			this.reload(data);
			this.floatBox.close();
			window.location.reload();
		}
		
	});