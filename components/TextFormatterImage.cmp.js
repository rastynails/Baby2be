function component_TextFormatterImage(auto_id)
{
	this.DOMConstruct('TextFormatterImage', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_TextFormatterImage.prototype =
	new SK_ComponentHandler({

		construct : function(){
		
			var handler = this;
			
			window.SK_EventManager.bind('tf.suggestImage', function(e) {
				var box = new SK_FloatBox({
					$title		: handler.$( '.title' ),
					$contents	: handler.$( '.content' ),
					width		: 450
				});		
				
				handler.forms[0].fields.file.construct($(handler.forms[0].$form[0].file), handler.forms[0]);

				handler.onSelect = function(src, label){
					e.callback(src, label);
					box.close();
				};
				
				return false;
			});
		}
	});