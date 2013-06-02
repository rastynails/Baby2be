<?php

class form_ForgotPassword extends SK_Form
{
	
	public function __construct()
	{
		parent::__construct('forgot_password');
	}
	
	
	public function setup()
	{
		$field = new fieldType_text('email');
		
		parent::registerField($field);
		
		parent::registerAction('form_ForgotPassword_Process');
	}
	
}

class form_ForgotPassword_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('send');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('email');
			
		parent::setup($form);
	}
	
	public function checkData( array $data, SK_FormResponse $response, SK_Form $form ) 
	{
		if (!preg_match(SK_ProfileFields::get('email')->regexp,$data['email'])) {
			$response->addError(SK_Language::text('%forms.forgot_password.messages.error.incorrect_email'));
			return ;
		}
	}
	
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		if ( app_Passwords::sendPassword( $post_data['email'] ) ){
			$response->addMessage(SK_Language::text('%forms.forgot_password.messages.success.sent'));
		}
		else{ 
			$response->addError(SK_Language::text('%forms.forgot_password.messages.error.incorrect_email'));
		}
		
		return true;	
	}
}
