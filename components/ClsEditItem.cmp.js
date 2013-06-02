
function component_ClsEditItem(auto_id)
{
	this.DOMConstruct('ClsEditItem', auto_id);
	
	var handler = this;
	
	this.delegates = {
			
	};
}

component_ClsEditItem.prototype =
	new SK_ComponentHandler({
	
	construct: function(files) 
	{
		var handler = this;
		
		for (var i = 0; i < this.forms.length; i++) {
			var form = this.forms[i];
			if (form.name=='cls_edit_item') {
				this.form = form.$form[0];
			}
		}
		
		var $allow_comments = $(this.form.allow_comments);
		var $allow_bids = $(this.form.allow_bids);
		
		$allow_comments.bind("click", function() {
			$allow_bids.attr("disabled", !$allow_comments.attr("checked"));
		});
		
		$allow_bids.attr("disabled", !$allow_comments.attr("checked"));		
		
		$.each( files, function(index, file){
			var $item_node = handler.$("#file_" + file.file_id);
			handler.$(".delete_item_file", $item_node).bind("click", function(){
				handler.ajaxCall( 'ajax_DeleteItemFile', {file_id: file.file_id} );
				$item_node.remove();
			});
		} );
		
		var $budget_to = $(this.form.budget_to);
		var bindBudgetTo = function() {
			if ( $budget_to.val() && $allow_bids.attr("checked") ) {
				handler.showLimitedBids("wanted");
			} else {
				handler.hideLimitedBids("wanted");
			}
		};
		$budget_to.bind("change", bindBudgetTo);
		$allow_bids.bind("change", bindBudgetTo);			
		
		var $price = $(this.form.price);
		var bindPrice = function() {
			if ( $price.val() && $allow_bids.attr("checked") ) {
				handler.showLimitedBids("offer");
			} else {
				handler.hideLimitedBids("offer");
			}
		};
		$price.bind("change", bindPrice);
		$allow_bids.bind("change", bindPrice);
	},
	
	showLimitedBids: function(type) {
		if (type == 'wanted') {
			var $wanted_limited_bids = $(this.form.wanted_limited_bids).parents("tr:first");
			$wanted_limited_bids.show();			
		} else {
			var $offer_limited_bids = $(this.form.offer_limited_bids).parents("tr:first");
			$offer_limited_bids.show();	
		}
	},
	
	hideLimitedBids: function(type) {
		if (type == 'wanted') {
			var $wanted_limited_bids = $(this.form.wanted_limited_bids).parents("tr:first");
			$wanted_limited_bids.hide();			
		} else {
			var $offer_limited_bids = $(this.form.offer_limited_bids).parents("tr:first");
			$offer_limited_bids.hide();	
		}
	}

});
