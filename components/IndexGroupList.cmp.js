
function component_IndexGroupList(auto_id)
{
	this.DOMConstruct('IndexGroupList', auto_id);
}

component_IndexGroupList.prototype =
	new SK_ComponentHandler({
	
	construct: function()
	{
		var handler = this;
		
		var $tabs = this.$('ul.menu-ajax-block').find('a');
		
		$tabs.click(function(){
			handler.showGroupList($(this).attr('rel'));
			
			$tabs.removeClass('active');
			$tabs.parent().removeClass('active');
			$(this).addClass('active');
			$(this).parent().addClass('active');
		});
	},
	
	showGroupList: function(list_name){
	
		var $list_containers = this.$('#groups_cont .group_list');
		var handler = this;
		$list_containers.each(function(){
			if ($(this).attr("id") != handler.auto_id + '-group_'+list_name)
			{
				$(this).fadeOut('fast', function(){
					handler.$('#group_' + list_name).fadeIn('fast');	
				});
			}
		});
	}
			
});
