<?php

class component_ForgotPassword extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('forgot_password');
	}
	
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$Frontend->onload_js(
			'$("#'.$this->getTagAutoId('btn').'").click(
				function() {
					var $title = $("#'.$this->getTagAutoId('thick_title').'").children();
					var $contents = $("#'.$this->getTagAutoId('thick_content').'").children();
					
					window.sk_component_forgot_password_floatbox = new SK_FloatBox({
						$title: $title,
						$contents: $contents,
						width: 320
					});
				}
		)');
		
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		return parent::render($Layout);
	}
	
	public function handleForm(SK_Form $Form){
		$Form->frontend_handler->bind("success",'function(data){
			this.$form.get(0).reset();
			window.sk_component_forgot_password_floatbox.close();
		}');
		parent::handleForm($Form);
	}
}
