function component_SendMessage(auto_id)
{
	this.DOMConstruct('SendMessage', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_SendMessage.prototype =
	new SK_ComponentHandler({
	
	construct: function(error_msg) {
		var handler = this;
				
		var $send_link = this.$("#send_message_link");
		var $form_title = this.$("#send_message_link").text();
		var $form_content = this.$("#send_message_cont").children();
		
		$send_link.click(function(){
			if (error_msg == undefined) {		
				window.send_message_floatbox = new SK_FloatBox({
					$title: $form_title,
					$contents: $form_content,
					width: 500
				});
			}
			else {
				handler.error(error_msg);
			}
			
		});
	}		
});
