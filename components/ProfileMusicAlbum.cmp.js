
function component_ProfileMusicAlbum(auto_id)
{
	this.DOMConstruct('ProfileMusicAlbum', auto_id);
	
	var handler = this;
	
}

component_ProfileMusicAlbum.prototype =
	new SK_ComponentHandler({
	
	construct: function()
	{
		var handler = this;
		
		var $btn1 = this.$('ul.menu-ajax-block li a:eq(0)');
		var $btn2 = this.$('ul.menu-ajax-block li a:eq(1)');
		
		var $menu_item1 = this.$('ul.menu-ajax-block li:eq(0)');
		var $menu_item2 = this.$('ul.menu-ajax-block li:eq(1)');
		
		var $form_2 = this.$("#music_latest");
		var $form_1 = this.$("#music_toprated");

		$btn1.click(function(){
			$btn1.addClass('active');
			$menu_item1.addClass('active');
			$btn2.removeClass('active');
			$menu_item2.removeClass('active');
			
			$form_1.fadeOut('fast', function(){
				$form_2.fadeIn('fast');
			});
		});
		
		$btn2.click(function(){
			$btn2.addClass('active');
			$menu_item2.addClass('active');
			$btn1.removeClass('active');
			$menu_item1.removeClass('active');
		
			$form_2.fadeOut('fast', function(){
				$btn2.addClass('active');
				$form_1.fadeIn('fast');
			});
		});	
	}
	
});
