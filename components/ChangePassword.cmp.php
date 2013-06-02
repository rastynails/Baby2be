<?php

class component_ChangePassword extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('change_password');
	}
	
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$Frontend->onload_js(
			'$("#'.$this->getTagAutoId('btn').'").click(
				function() {
					var $title = $("#'.$this->getTagAutoId('thick_title').'").children();
					var $contents = $("#'.$this->getTagAutoId('thick_content').'").children();
					
					window.sk_component_change_password_floatbox = new SK_FloatBox({
						$title: $title,
						$contents: $contents,
						width: 320
					});
					
					$("#'.$this->getTagAutoId('cancel').'").click(function(){
						window.sk_component_change_password_floatbox.close();
					});
				}
		)');
		
		parent::prepare($Layout, $Frontend);
	}
	
	
	public function handleForm( SK_Form $form )
	{
		$form->frontend_handler->bind('success', 'function() {
				try {
					this.$form.get(0).reset();
					window.sk_component_change_password_floatbox.close();
				} catch (e) {}
			}');
	}
	
}
