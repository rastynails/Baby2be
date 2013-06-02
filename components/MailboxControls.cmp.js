function component_MailboxControls(auto_id)
{
	this.DOMConstruct('MailboxControls', auto_id);
	
	var handler = this;
	
	this.controls = [];
	
	this.delegates = {
		
	};
}

component_MailboxControls.prototype =
	new SK_ComponentHandler({
	
	prot: undefined,
	
	folder: "",
	
	construct : function(profile_id, conversation, folder) {
		this.profile_id = profile_id;
		this.conversation = conversation;
		this.folder = folder;
		var handler = component_MailboxControls.prototype;
		
		if (handler.prot == undefined) {
			var prot = this.$(".control_node");
			handler.prot = prot.clone();
			prot.remove();
		}
			
		handler.prot.removeClass("control_node");
	},
	
	registerControl : function(params){
		
		var handler = this;
		var control = this.prot.clone();
		var $control_link = control.find("a");
		
		$control_link.text(params.label);
		
		$control_link.click(function() {

			if (params.name == 'delete') {
			
				var msg =  params.msg;
				SK_confirm($("<span>" + msg + "</span>"), function() {
					handler.ajaxCall(
						params.backend_func,
						{	
							profile_id: handler.profile_id,
							control: params.name,
							conversation: handler.conversation,
							folder: handler.folder 
						}
					);
				});
			}
			else {
				handler.ajaxCall(
					params.backend_func,
					{
						profile_id: handler.profile_id,
						control: params.name,
						conversation: handler.conversation,
						folder: handler.folder 
					}
				);
			}
		});
		
		handler.controls[params.name] = control;
	},
	
	displayControl: function(name) {
		if (this.controls[name]==undefined){
			return;
		}
			
		var reference_pos = document.createElement('div');
		
		var $reference_pos = $(reference_pos).appendTo(this.container_node);
		$reference_pos.append(this.controls[name]);
		
		this.controls[name].ref_container = $reference_pos;
		
		$(this.container_node).prepend(this.controls[name].ref_container);
	},
	
	changeControl: function(name, to_name) {
		if (this.controls[name].ref_container==undefined){
			return;
		}
		
		this.controls[name].hide();
		this.controls[name].ref_container.append(this.controls[to_name]);
		this.controls[to_name].show();
		this.controls[to_name].ref_container = this.controls[name].ref_container;
	},
	
	openConversation: function(href) {
		this.redirect(href);
	},
	
	redirect: function(url) {
		if ( url == undefined ) 
			window.location.reload();
		else 
			window.location.href = url;
	}
});

