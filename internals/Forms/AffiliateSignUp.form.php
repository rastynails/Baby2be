<?php

class form_AffiliateSignUp extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('affiliate_sign_up');
	}

	public function setup()
	{		
		$full_name = new fieldType_text('full_name');
		parent::registerField($full_name);
		
		$email = new fieldType_text('email');
		$email->setRegExPatterns( SK_ProfileFields::get('email')->regexp );
		parent::registerField($email);
		
		$password = new fieldType_password('password');
		parent::registerField($password);

		$password_conf = new fieldType_password('password_conf');
		parent::registerField($password_conf);
		
		$captcha = new field_captcha('aff_captcha');
		parent::registerField($captcha);
				
		parent::registerAction('formAffiliateSignUp_SignUp');
	}
	
}

class formAffiliateSignUp_SignUp extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('signup');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('full_name', 'email', 'password', 'password_conf');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$email = trim($post_data['email']);
		$full_name = trim($post_data['full_name']);
		$pass = trim($post_data['password']);
		$pass_conf = trim($post_data['password_conf']);
		
		if ($pass != $pass_conf)
			$response->addError(SK_Language::section('forms.affiliate_sign_up.error_msg')->text('confirm_fail'));
			
		elseif ( !app_Affiliate::checkIsAffiliateEmailUnique($email) )
			$response->addError(SK_Language::section('forms.affiliate_sign_up.error_msg')->text('email_used'));
		else
		{
			$insert_result = app_Affiliate::insertNewAffiliate( array( 'full_name' => $full_name, 'password' => $pass, 'email' => $email ) );
			if (!$insert_result)
				$response->addError(SK_Language::section('forms.affiliate_sign_up.error_msg')->text('signup_failed'));
			else
			{
				app_Affiliate::addRequestEmailVerification($insert_result);
				$response->addMessage(SK_Language::section('forms.affiliate_sign_up.msg')->text('signup_success'));
		
				$response->exec("location.href='".SK_Navigation::href('affiliate_home')."'"); 
			}
		}
	}
}



