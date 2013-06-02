function component_PaymentSelection(auto_id)
{
	this.DOMConstruct('PaymentSelection', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_PaymentSelection.prototype =
	new SK_ComponentHandler({
		
		construct : function(trial_memb)
		{
			var handler = this;
			
			var $plans = this.$('tr .plans input');
			
			var $claim_label = this.$('#claim_btn_label');
			var $checkout_btn = this.$('.providers input[type=submit]');
			var $type_input = this.$('input[type=hidden][name=type_id]');
			var $provider_select = this.$('#provider_select');
			
			var restore_checkout = $checkout_btn.val();
			var restore_claim = $claim_label.text();
			
			var $def_type = this.$('input[type=radio][checked]');
			
			if ($def_type.val() == undefined)
				this.$('.providers').css("display","none");
			else {
				var def_type_id = $def_type.val().split("_")[0];
					
				$plans.each(function() {
					
					$.each(trial_memb, function(i, memb) {
						if(memb.type_id == def_type_id) {
						
							if(memb.is_free)
								$provider_select.css("display","none");
								
							$checkout_btn.attr("value",$claim_label.text());
						}
					});
				
					$(this).click(function() {
						var plan_struct = new Array(); 
						
						plan_struct = this.value.split("_");
						
						var type_id = plan_struct[0];
						var plan_id = plan_struct[1];
						$type_input.attr("value",type_id)
						var f = false;
						
						$.each(trial_memb, function(i, memb){
							 
							if(type_id == memb.type_id && !f) {
							
								$checkout_btn.attr("value",restore_claim);
								
								if(memb.is_free)
									$provider_select.css("display","none");
								
								else 
									$provider_select.css("display","");
								f = true;
							}
							else 
							{
								if (memb.is_free && !f) {
									$provider_select.css("display","");
									$f = true;
									$checkout_btn.attr("value",restore_checkout);
								}	
								else 
									if (!memb.is_free ) 
										$checkout_btn.attr("value",restore_checkout);
							}
						
						});
					});
				});
			}
		}  
});

