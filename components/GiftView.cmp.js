function component_GiftView(auto_id)
{
	this.DOMConstruct('GiftView', auto_id);
	
	var handler = this;
}

component_GiftView.prototype =
	new SK_ComponentHandler({
	
	construct: function(params) {

		var handler = this;
		handler.params = params;
				
		var $del_btn = this.$("#gift_delete_btn");
		
		$del_btn.click(function(){
			SK_confirm($("<span>" + handler.params.confirm + "</span>"), function() {
				handler.ajaxCall( 
					'ajax_DeleteGift',
					{gift_id: handler.params.gift_id, confirm: handler.params.confirm}, 
					{success: function(data){
						if (data.result){
							window.location.href = handler.params.location;
						}
					}
				});
			});
		});
	}		
});

