function component_ProfileReferences(auto_id)
{
	this.DOMConstruct('ProfileReferences', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ProfileReferences.prototype =
	new SK_ComponentHandler({
	
	prototype_node : undefined,
	
	mode : "",
	
	construct : function(profile_id, mode){
		this.profile_id = profile_id;
		this.mode = mode;		
		var handler = component_ProfileReferences.prototype;
		
		if (handler.prototype_node == undefined) {
			var prototype_node = this.$(".prototype_node");
			handler.prototype_node = prototype_node.clone();
			prototype_node.remove();
		}
			
		handler.prototype_node.removeClass("prototype_node");
	},
	
	registerReference : function(ref_data){
		
		var handler = this;
		
		var reference = this.prototype_node.clone();
		reference.addClass(ref_data.name);
		
		var $link_node = reference.find("a");
		
		if( !$link_node.is("a")){
			$link_node= reference_cont;
		}
		
		$link_node.text(ref_data.label);
        $link_node.attr("title", ref_data.label);
		
		delete ref_data.label;
		
		var send_data = {};
		
		$.each(ref_data, function(prop, item){
			send_data[prop] = item;
		});
			
		send_data.profile_id = this.profile_id;
		send_data.reference = ref_data.name;
		delete send_data.name;
		
		$link_node.click(function() {
			if ( ref_data.click_func != undefined )
			{
				$.globalEval(ref_data.click_func + '(' + send_data.profile_id + ')');
			}
			else
			{
				$link_node.disable();
				handler.ajaxCall(ref_data.backend_func
					, send_data
					, {
						complete: function() {
							$link_node.enable();
						}
					});
			}
		});
		
		handler.references[ref_data.name] = reference;
	},
	
	displayReference: function(name){
		if (this.references[name]==undefined){
			return;
		}
			
		var reference_pos = document.createElement('div');
		
		var $reference_pos = $(reference_pos).appendTo(this.container_node);
		$reference_pos.append(this.references[name]);
		
		this.references[name].ref_container = $reference_pos;
		
		$(this.container_node).prepend(this.references[name].ref_container);
	},
	
	changeReference: function(name, to_name) {
		if (this.references[name]==undefined || this.references[name].ref_container==undefined){
			return;
		}
		
		this.references[name].hide();
		this.references[name].ref_container.append(this.references[to_name]);
		this.references[to_name].show();
		this.references[to_name].ref_container = this.references[name].ref_container;
	},
	
	hide : function(name){
		if (this.references[name].ref_container!=undefined){
			this.references[name].ref_container.hide();
		}
	},
	
	show : function(name){
		if (this.references[name].ref_container!=undefined){
			this.references[name].ref_container.show();
		}
	},
	
	hideOther: function(name){
		for(key in this.references){
			if (this.references[name].ref_container != this.references[key].ref_container){
				this.hide(key);
			}
		}
	},
	
	showOther: function(name){
		for(key in this.references){
			if (this.references[name].ref_container != this.references[key].ref_container){
				this.show(key);
			}
		}
	},
	
	redirect : function(url){
		if (url==undefined){
			window.location.reload();
		}
		else{
			window.location.href = url;
		}
	},
	
	alert : function(msg)
	{
		alert(msg);
	}
	
	
});