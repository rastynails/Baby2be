function component_PhotoTips(auto_id)
{
	this.DOMConstruct('PhotoTips', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_PhotoTips.prototype =
	new SK_ComponentHandler({
		
		construct : function() {
			var handler = this;
			this.$(".hide_btn, .show_btn").click(function(){
				var $btn = $(this);
				handler.ajaxCall("ajax_saveTipState", {}, {success: function(data){
					if (data) {
						if (data.show) {
							handler.$(".tip_container").show();
							handler.$(".show_btn").hide();
						} else {
							handler.$(".show_btn").show();
							handler.$(".tip_container").hide();
						}
						
					}
				}});
			});
		}
		
});