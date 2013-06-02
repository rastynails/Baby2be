
function component_VideoUpload(auto_id)
{
	this.DOMConstruct('VideoUpload', auto_id);
	
	var handler = this;
	
}

component_VideoUpload.prototype =
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

		$form_1.find("select[name=privacy_status]").change(function(){
			if ( $(this).val() == 'password_protected' )
			{
				$form_1.find("tr.password_tr").css("display", "table-row");
			}
			else
			{
				$form_1.find("tr.password_tr").css("display", "none");
			}
		});
		
		$form_2.find("select[name=privacy_status]").change(function(){
			if ( $(this).val() == 'password_protected' )
			{
				$form_2.find("tr.password_tr").css("display", "table-row");
			}
			else
			{
				$form_2.find("tr.password_tr").css("display", "none");
			}
		});
		
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
