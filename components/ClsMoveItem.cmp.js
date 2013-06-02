
function component_ClsMoveItem(auto_id)
{
	this.DOMConstruct('ClsMoveItem', auto_id);
	
	var handler = this;
	
	this.delegates = {
			
	};
}

component_ClsMoveItem.prototype =
	new SK_ComponentHandler({
	
	construct: function(item_type) 
	{
		var handler = this;
		
		this.hideItemType(item_type);
		
		this.$("#move_item").bind("click", function() {
			handler.showMovebox();
		});
	},
	
	showMovebox: function() {
		var handler = this;
		this.movebox = new SK_FloatBox({
			$title		: this.$(".move_item_title").text(),
			$contents	: this.$(".move_item_content")
		});
	},
	
	hideMovebox: function() {
		this.movebox.close();
	},
	
	hideItemType: function(item_type) {
		var $wanted_cat = $(this.forms[0].$form[0].category).children(".wanted");
		var $offer_cat = $(this.forms[0].$form[0].category).children(".offer");
		
		if (item_type == 'wanted') {
			$offer_cat.css("display", "none");			
		}
		else {
			$wanted_cat.css("display", "none");			
		}
	}	
});
