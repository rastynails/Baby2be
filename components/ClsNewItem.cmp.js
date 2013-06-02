
function component_ClsNewItem(auto_id)
{
	this.DOMConstruct('ClsNewItem', auto_id);
	
	var handler = this;
	
	this.delegates = {
			
	};
}

component_ClsNewItem.prototype =
	new SK_ComponentHandler({

	construct: function( configs ) {
		var handler = this;
		this.configs = configs;
				
		for (var i = 0; i < this.forms.length; i++) {
			var form = this.forms[i];
			if (form.name=='cls_new_item') {
				this.form = form.$form[0];
			}
		}	
		
		if ( this.form == undefined )
		{
			return;
		}
			
		this.$wanted_cat = $(this.form.category).children(".wanted");
		this.$offer_cat = $(this.form.category).children(".offer");
		
		var $item_type = $(this.form.item_type);
		
		$item_type.bind("click", function() {
			handler.hideItemType($(this).val(), true);
		});
		
		var $allow_bids = $(this.form.allow_bids);
		$allow_bids.attr("disabled", true);
		
		var $allow_comments = $(this.form.allow_comments);
		$allow_comments.bind("click", function() {
			var checked = $allow_comments.attr("checked");
			$allow_bids.attr("disabled", !checked);
		});
		
		var $budget_to = $(this.form.budget_to);
		var bindBudgetTo = function() {
			if ( $budget_to.val() && $allow_bids.attr("checked") ) {
				handler.hideLimitedBids("offer");
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
				handler.hideLimitedBids("wanted");
				handler.showLimitedBids("offer");
			} else {
				handler.hideLimitedBids("offer");
			}
		};
		$price.bind("change", bindPrice);
		$allow_bids.bind("change", bindPrice);
		
		var $category = $(this.form.category);
		$category.bind("change", function() {
			var option = this.selectedIndex;
			handler.hideItemType( $(this.options[option]).attr("class") );
		});
	},
	
	hideItemType: function(item_type, onload) {
		
		if ( this.form == undefined )
		{
			return;
		}
		
		var $price = $(this.form.price).parents("tr:first");
		var $payment_dtls = $(this.form.payment_dtls).parents("tr:first");
		var $budget = $(this.form.budget_from).parents("tr:first");

		this.hideNotAllowedFields(item_type);
		this.displayLimitedBids(item_type);
		
		this.$("input:radio[value='"+item_type+"']").attr("checked", "true");
		
		if (item_type == 'wanted') {
			if ( onload ) {
				$(this.form.category).children().remove();
				$(this.form.category).append( this.$wanted_cat );
				$(this.form.category).val( this.$wanted_cat.get(1).value );
			}	
			$price.fadeOut("slow");
			$payment_dtls.fadeOut("slow");
			$budget.fadeIn("slow");			
		}
		else {
			if ( onload ) {
				$(this.form.category).children().remove();
				$(this.form.category).append( this.$offer_cat );				
				$(this.form.category).val( this.$offer_cat.get(1).value );
			}			
			$budget.fadeOut("slow");		
			$price.fadeIn("slow");
			$payment_dtls.fadeIn("slow");
		}
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
	},
	
	hideNotAllowedFields: function(type) {
		
		var $allow_comments = $(this.form.allow_comments).parents("tr:first");
		var $allow_bids = $(this.form.allow_bids).parents("tr:first");		
		
		if (type == 'wanted') {
			var $wanted_limited_bids = $(this.form.wanted_limited_bids).parents("tr:first");
			if ( !this.configs.wanted.allow_comments ) {
				$allow_comments.hide();
				$allow_bids.hide();
				$wanted_limited_bids.hide();
			}
			else if ( !this.configs.wanted.allow_bids ) {
				$allow_comments.show();
				$allow_bids.hide();
				$wanted_limited_bids.hide();			
			}
			else {
				$allow_comments.show();
				$allow_bids.show();		
			}
		}
		else 
		{
			var $offer_limited_bids = $(this.form.offer_limited_bids).parents("tr:first");
			if ( !this.configs.offer.allow_comments ) {
				$allow_comments.hide();
				$allow_bids.hide();
				$offer_limited_bids.hide();
			}
			else if ( !this.configs.offer.allow_bids ) {
				$allow_comments.show();
				$allow_bids.hide();
				$offer_limited_bids.hide();			
			}
			else {
				$allow_comments.show();
				$allow_bids.show();
			}
		}
	},
	
	displayLimitedBids: function(type) {
		var $price = $(this.form.price);
		var $budget_to = $(this.form.budget_to);
		var $allow_bids = $(this.form.allow_bids);
		
		if (type=='wanted') {
			this.hideLimitedBids("offer");
			
			if ( $budget_to.val() && $allow_bids.attr("checked") ) {
				this.showLimitedBids("wanted");
			} else {
				this.hideLimitedBids("wanted");
			}			
		}
		else {
			this.hideLimitedBids("wanted");

			if ( $price.val() && $allow_bids.attr("checked") ) {
				this.showLimitedBids("offer");
			} else {
				this.hideLimitedBids("offer");
			}		
		}	
	}
});
