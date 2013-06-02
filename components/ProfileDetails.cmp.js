function component_ProfileDetails(auto_id)
{
	this.DOMConstruct('ProfileDetails', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ProfileDetails.prototype =
	new SK_ComponentHandler({
		
		$page_cont: undefined,
		
		$menu_cont: undefined,
		
		$menu_node: undefined,
		
		$menu_prototype: undefined,
		
		active_page: undefined,
		
		pages: {},
		
		construct : function() {
						
			this.$page_cont = this.$(".page_cont");
			this.$menu_cont = this.$(".menu").removeClass("prototype_node");
			this.$menu_node = this.$menu_cont.find("ul");
			this.$menu_prototype = this.$menu_cont.find(".tab").remove();
		},
		
		addPage: function(page_id, label){
			
			var $page_node = this.$page_cont.find(".page_" + page_id);
			
			if (!$page_node.length) {
				return false;
			}
			
			var handler = this;
			
			var $menu_node = this.$menu_prototype.clone();
			$menu_node.find("span").text(label);
						
			$menu_node.appendTo(this.$menu_node);
			
			$menu_node.find("a").click(function(){
				handler.activatePage(page_id);
			}).addClass('step_' + page_id);
			
			this.pages[page_id] = {
				page_id: page_id,
				$menu_node: $menu_node,
				$page_node: $page_node
			}
		},
		
		activatePage: function(page_id) {

			
			if (this.pages[page_id] == undefined) {
				return false;
			}
			
			if (this.active_page != undefined) {
				this.active_page.$page_node.hide();
				this.active_page.$menu_node.removeClass("active");
                                this.active_page.$menu_node.find('a').removeClass('active');
			}
			
			this.active_page = this.pages[page_id];
			this.active_page.$page_node.show();
			this.active_page.$menu_node.addClass("active");
                        this.active_page.$menu_node.find('a').addClass('active');
			
		},
		
		complete: function() {
			this.$menu_node.find('.tab').removeClass('first').removeClass('last');
			this.$menu_node.find('.tab:first').addClass('first');
			this.$menu_node.find('.tab:last').addClass('last');
		}
			
});