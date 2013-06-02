<?php

class form_ChangePassword extends SK_Form
{
	
	public function __construct()
	{
		parent::__construct('change_password');
	}
	
	
	public function setup()
	{
		$old_pass = new fieldType_password('old_password');
		$new_password = new fieldType_password('new_password');
		$new_password_confirm = new fieldType_password('new_password_confirm');
		
		parent::registerField($old_pass);
		parent::registerField($new_password);
		parent::registerField($new_password_confirm);
		
		$new_password->setRegExPatterns(SK_ProfileFields::get('password')->regexp);
		$new_password->maxlength = SK_MySQL::describe(TBL_PROFILE,'password')->size();
		
		parent::registerAction('form_ChangePassword_Process');
	}
	
}

class form_ChangePassword_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('change');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('old_password', 'new_password', 'new_password_confirm');
			
		parent::setup($form);
	}
	
	public function checkData( array $data, SK_FormResponse $response, SK_Form $form ) 
	{
		$lang_errors = SK_Language::section('forms.change_password.messages.error');
		
		$query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `".TBL_PROFILE."` WHERE `profile_id`=? AND `password`='?'"
					, SK_HttpUser::profile_id(), app_Passwords::hashPassword($data['old_password']));
			
		if (!(bool)SK_MySQL::query($query)->fetch_cell()) {
			$response->addError($lang_errors->text('incorrect_old_pass'), 'old_password');
		}
		else 
		{
			if ( $data['new_password']!=$data['new_password_confirm'] ) {
				$response->addError($lang_errors->text('passwords_not_match'), 'new_password_confirm');
			}
		}
	}

	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$lang_msg = SK_Language::section('forms.change_password.messages.success');
		
		if (app_Profile::changePassword(SK_HttpUser::profile_id(), $post_data['new_password'])){
			$response->addMessage($lang_msg->text('success'));
		}
	}
}
