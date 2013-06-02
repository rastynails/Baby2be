
function component_ClsIndexItems(auto_id)
{
	this.DOMConstruct('ClsIndexItems', auto_id);
	
	var handler = this;
	
	this.delegates = {
			
	};
}

component_ClsIndexItems.prototype =
	new SK_ComponentHandler({
	
	menu_items: [],
	
	construct: function($menu_items, $view_all_url) {
		var handler = this;
		this.menu_items = $menu_items;

		this.$(".item_list").css("display", "none");
		
		$.each($menu_items, function($index, $item) {
			if ($item.active) {
				handler.view_all = $item.type;
				handler.$("#"+$item.type).css("display", "block");
			}
		});
		
		$.each(handler.$(".menu-block a"), function(index){
			$(handler.$(".menu-block a").get(index)).bind("click", function(){
				handler.changeList(index);
			});
		});
		
		this.$("#view_more").bind("click", function(){
			handler.redirect( $view_all_url[handler.view_all] );
		});		
		
	},
	
	changeList: function($index) {	
		this.$(".menu-block a").removeClass("active");
		this.$(".menu-block li").removeClass("active");
		
		$(this.$(".menu-block a").get($index)).addClass("active");
		$(this.$(".menu-block li").get($index)).addClass("active");
		
		this.$(".item_list").css("display", "none");
		$list = this.menu_items[$index].type;
		this.$("#"+$list).css("display", "block");
		
		this.view_all = $list;
	},
	
	redirect: function(url) {
		if ( url == undefined ) 
			window.location.reload();
		else 
			window.location.href = url;
	}
});
