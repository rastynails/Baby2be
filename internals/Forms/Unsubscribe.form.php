<?php

class form_Unsubscribe extends SK_Form
{
	public function __construct()
	{
		parent::__construct('unsubscribe');
	}

	public function setup()
	{
		$pass = new fieldType_password('pass');
		parent::registerField($pass);

		$unsubscriber = new fieldType_hidden('unsubscriber');
		parent::registerField($unsubscriber);

		parent::registerAction('form_Unsubscribe_Process');
	}
}

class form_Unsubscribe_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('process');
	}

	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$process_unsubscribe = false;
		$show_form = $post_data['unsubscriber'] != SK_HttpUser::profile_id();
		if ($show_form) {
			$profile_pass = app_Profile::getFieldValues($post_data['unsubscriber'], 'password');

			if ( $profile_pass != app_Passwords::hashPassword($post_data['pass']) ) {
				$response->addError(SK_Language::section('forms.unsubscribe.messages')->text('invalid_pass'));
			} else {
				$process_unsubscribe = true;
			}
		}
		if ($process_unsubscribe || !$show_form) {
			app_Unsubscribe::setUnsubscribe($post_data['unsubscriber']);
			$response->addMessage(SK_Language::section('forms.unsubscribe.messages')->text('success'));
		}
	}
}
