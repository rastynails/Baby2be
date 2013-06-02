function component_SignIn(auto_id)
{
	this.DOMConstruct('SignIn', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_SignIn.prototype =
	new SK_ComponentHandler({
		
		saveLink : function() {
			window.sk_component_sign_in = this;
		},
		
		
		shown: false,
		showBox: function() {
			if (this.shown) {
				return false;
			}
			
			var box = new SK_FloatBox({
				$title:  this.$(".fb_title"),
				$contents: this.$(".fb_content"),
				width: "300px"
			});
			this.shown = true;
			
			var handler = this;
			
			box.bind("close", function(){
				handler.shown = false;
			});
			
			return box;
		}
});