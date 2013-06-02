
function component_MusicUpload(auto_id)
{
	this.DOMConstruct('MusicUpload', auto_id);
	
	var handler = this;
	
}

component_MusicUpload.prototype =
	new SK_ComponentHandler({
	
	construct: function()
	{
		var handler = this;
		
		var $btn1 = this.$('ul.menu-ajax-block li a[rel=embed_code]');
		var $btn2 = this.$('ul.menu-ajax-block li a[rel=file]');
		
		var $menu_item1 = this.$('ul.menu-ajax-block a[rel=embed_code]').parent();
		var $menu_item2 = this.$('ul.menu-ajax-block a[rel=file]').parent();
		
		var $form_2 = this.$("#embed_code_form");
		var $form_1 = this.$("#upload_file_form");

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
