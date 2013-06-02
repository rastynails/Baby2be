function component_MailboxConversationsList(auto_id)
{
	this.DOMConstruct('MailboxConversationsList', auto_id);
	
	var handler = this;
}

component_MailboxConversationsList.prototype =
	new SK_ComponentHandler({
	
	construct : function() {
		
		var $chb = $("#select_all");
		
		var $inputs = $(".mailbox_threads input[type=checkbox]");

		$chb.click(function(){
			var toggle = $(this);

			$inputs.each(function(){
				$(this).attr('checked', toggle.attr('checked')); 
			});
		});
	}
});

