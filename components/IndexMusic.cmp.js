
function component_IndexMusic(auto_id)
{
	this.DOMConstruct('IndexMusic', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_IndexMusic.prototype =
	new SK_ComponentHandler({
	
	construct: function()
	{
		var handler = this;
		
		var $tabs = this.$('ul.menu-ajax-block').find('a');
		
		$tabs.click(function(){
			handler.showMusicList($(this).attr('rel'));
			
			$tabs.removeClass('active');
			$tabs.parent().removeClass('active');
			$(this).addClass('active');
			$(this).parent().addClass('active');
		});
	},
	
	showMusicList: function(list_name){
	
		var $list_containers = this.$('#music_cont .music_list');
		var handler = this;		
		$list_containers.each(function(){
			if ($(this).attr("id") != handler.auto_id + '-music_'+list_name)
			{
				$(this).fadeOut('fast', function(){
					handler.$('#music_' + list_name).fadeIn('fast');
				});
			}
		});
	}
			
});
