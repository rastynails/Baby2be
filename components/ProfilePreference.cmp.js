function component_ProfilePreference(auto_id)
{
	this.DOMConstruct('ProfilePreference', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ProfilePreference.prototype =
	new SK_ComponentHandler({
		
		showRestoreBtn : function() {
			var handler = this;
			this.$("#restore_defaults_btn").unbind().click(function(){
				var $btn = $(this);
				SK_confirm(handler.$("#restore_confirm_content"), function(){
						handler.ajaxCall("ajax_restoreDefaults",{},{success: function(result){
						if (result) {
							$btn.parent().hide();
						}
					}});	
				});
				
			}).parent().show();
		},
		
		setValue: function(config_name, value) {
			var $input = $(this.forms[0].$form.get(0)[config_name]);
			
			
			if ($input.length != 0) {
				if ($input.is("input[type=checkbox]")) {
					if (value == undefined || !value) {
						value = false;
					} else {
						value = true;
					}
					
					$input.attr("checked", value);
				} else {
					$input.val(value);
				}
			}
			
		},
		
		showSignIn: function(){
			SK_SignIn();
		}
	});