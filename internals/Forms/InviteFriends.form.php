<?php

class form_InviteFriends extends SK_Form
{
	
	public function __construct()
	{
		parent::__construct('invite_friends');
	}
	
	
	public function setup()
	{
		$login = new field_email_list('email_addr');
		parent::registerField($login);
		
		$login = new fieldType_textarea('message');
		parent::registerField($login);
		
		
		parent::registerAction('form_InviteFriends_Process');
	}
	
}

class form_InviteFriends_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('process');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('email_addr');
		
		parent::setup($form);
	}
	
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$addr_list = preg_split('/\s+/', $post_data['email_addr']);
		
		try {
			$response->exec('
				this.$form.find("[name=email_addr]").val("");
			');
			$all_send = true;
			foreach ($addr_list as $address) {
				if ( strlen($address) ) {
					if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
						$response->exec('
								var value = this.$form.find("[name=email_addr]").val();
								this.$form.find("[name=email_addr]").val(value + "' . $address . '" + "\n" );
							');
						$all_send = false;
						continue;
					}
					
					try {
						app_Invitation::addRequestRegister($address, $post_data['message']);
					} catch (SK_EmailException $e) {
						$response->exec('
								var value = this.$form.find("[name=email_addr]").val();
								this.$form.find("[name=email_addr]").val(value + "' . $address . '" + "\n" );
							');
						$all_send = false;
					}
				}
			}
			if ($all_send) {
				$response->addMessage(SK_Language::section('forms.invite_friends.messages')->text('success'));
				$response->exec('
					this.$form.find("[name=message]").val("");
				');
			}
			else {
				$response->addMessage(SK_Language::section('forms.invite_friends.messages')->text('some_not_send'));	
			}
			
			
		} catch (Exception $e) {
			$error_message = SK_Language::section('forms.invite_friends.messages')->text($e->getMessage());
			$response->addError($error_message);
		}
	}
}
