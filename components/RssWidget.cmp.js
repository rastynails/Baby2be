function component_RssWidget(auto_id)
{
	this.DOMConstruct('RssWidget', auto_id);
}

component_RssWidget.prototype =
	new SK_ComponentHandler({

		construct : function(){
			var handler = this;
			
			this.$('#edit_cmp').bind('click',
				function(){
					handler.floatBox = new SK_FloatBox({
						$title		: handler.$( '#form_label' ),
						$contents	: handler.$( '#form' ),
						width		: 400
					});		
				}
			);
			
		},
		
		onSubmit: function(data){
			this.reload(data);
			this.floatBox.close();
		}
		
	});