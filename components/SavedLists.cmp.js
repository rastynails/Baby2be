function component_SavedLists(auto_id)
{
	this.DOMConstruct('SavedLists', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_SavedLists.prototype =
	new SK_ComponentHandler({
		
		$prototype_node : {},
		
		$parent_node : {},
		
		lists : {},
		
		construct : function(){
			var handler = this;
			this.$prototype_node = this.$(".prototype_node");
			this.$parent_node = this.$(".prototype_node").parent();
			this.lists.length = 0;
		},
		
		
		display : function(name, href){
			var handler = this;
			var $node = this.$prototype_node.clone().removeClass('prototype_node').show();
			var $link = $node.find("a.list_link");
			var $delete_btn = $node.find(".delete_btn");
			var $edit_btn = $node.find(".edit_btn");
			
			$link.attr("href", href);
			$link.text(name.substring(0, 14));
			
			$delete_btn.click(function(){
				handler.ajaxCall("ajax_deleteList",{list_name: name});				
			});
			
			$edit_btn.click(function(){
				handler.ajaxCall("ajax_editList",{list_name: name});				
			});
			
			this.lists[name] = $node;
			this.lists.length = this.lists.length + 1;
			this.$block("#saved_lists").$body.find(".list_container").append($node);
		},
		
		remove : function(name)
		{
			var handler = this;
			if (this.lists[name]==undefined) {
				return;
			}
				
			handler.lists[name].animate( { opacity:"0" } , 200 ,function(){
				handler.lists[name].animate( { height:"0px" } , 200 ,function(){
					handler.lists[name].remove();
					handler.lists[name] = undefined;
					handler.lists.length = handler.lists.length - 1;
					if (handler.lists.length == 0) {
						$(handler.container_node).hide();
					}
				});
			});
							
		},
		
		redirect: function(url){
			window.location.href=url;
		}
		
	});