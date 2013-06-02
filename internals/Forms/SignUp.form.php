<?php

class form_SignUp extends form_FieldForm 
{
	public function __construct()
	{
		parent::__construct('sign_up');
	}
	
	
	public function stepPrepare(){
		$this->captcha_enabled = false;
		$this->step = 1;
	}
	
	public function fields()
	{
		$fieds = array('email','username','password','i_agree_with_tos');
		return $fieds;
	}
	
	public function addField($name)
	{
		if ($name=='username' || $name=="email") {
			$field = new fieldType_text($name);
			parent::registerField($field);
		}
		parent::addField($name);
	}
		
	public function actionsPrepare(){
		parent::addAction("sign_up","formAction_SignUp");
	}
}

class formAction_SignUp extends formAction_FieldForm 
{
	
	public function process_fields($uniqid) {
		return array('email','username','password','i_agree_with_tos');
	}
		
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
	{
		app_JoinProfile::clearSession();
		foreach ($post_data as $field => $value){
			app_JoinProfile::setValue($field,$value);
		}
		
		if (!$response->hasErrors()) {
			$location = SK_Navigation::href('join_profile');
			$response->redirect($location);
		}
	}
	
}

