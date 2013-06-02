function component_SendVirtualGift(auto_id)
{
	this.DOMConstruct('SendVirtualGift', auto_id);
	
	var handler = this;
}

component_SendVirtualGift.prototype =
	new SK_ComponentHandler({
	
	construct: function(sender_id, recipient_id) {
		this.sender_id = sender_id;
		
		var handler = this;
				
		var $send_btn = this.$("#send_gift_btn");
		var $list_title = this.$("#send_gift_title").text();
		var $list_content = this.$("#gifts_list").children();
		
		$send_btn.click(function(){
			handler.forms[0].$form.find("input[type=submit]").disable();
			
			if (handler.forms[0].$form.find("input[name=tpl_id]").val())
				handler.forms[0].$form.find("input[type=submit]").enable();
			
			window.send_gift_floatbox = new SK_FloatBox({
				$title: $list_title,
				$contents: $list_content,
				width: 610
			});
		});
		
		var $tpl_list = this.$("#gifts_list .tpl_box");
		
		$tpl_list
			.click(function() {
								
				$tpl_list.each(function(){
					$(this).removeClass("selected");
				});
				
				$(this).addClass("selected");
				handler.forms[0].$form.find("input[name=tpl_id]").val($(this).find(".tpl_id").text());
				
				handler.forms[0].$form.find("input[type=submit]").enable();
			});
	}		
});

