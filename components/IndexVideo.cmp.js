
function component_IndexVideo(auto_id)
{
	this.DOMConstruct('IndexVideo', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_IndexVideo.prototype =
	new SK_ComponentHandler({
	
	construct: function()
	{
		var handler = this;
		
		var $tabs = this.$('ul.menu-ajax-block').find('a');
		
		$tabs.click(function(){
			handler.showVideoList($(this).attr('rel'));
			
			$tabs.removeClass('active');
			$tabs.parent().removeClass('active');
			$(this).addClass('active');
			$(this).parent().addClass('active');
		});
	},
	
	showVideoList: function(list_name){
	
		var $list_containers = this.$('#video_cont .video_list');
		var handler = this;		
		$list_containers.each(function(){
			if ($(this).attr("id") != handler.auto_id + '-video_'+list_name)
			{
				$(this).fadeOut('fast', function(){
					handler.$('#video_' + list_name).fadeIn('fast');	
				});
			}
		});
	}
			
});
