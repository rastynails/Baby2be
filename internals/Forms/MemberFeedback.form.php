<?php

class form_MemberFeedback extends SK_Form
{	
	public function __construct()
	{
		parent::__construct('member_feedback');
	}
	
	public function setup()
	{
		$feedback_to = new field_forums_select('feedback_to');
		$member_email = new field_email('member_email');
		$subject = new fieldType_text('subject');
		$message = new fieldType_textarea('message');
		$captcha = new field_captcha('captcha');
		
		parent::registerField($feedback_to);
		parent::registerField($member_email);
		parent::registerField($subject);		
		parent::registerField($message);
		parent::registerField($captcha);
		
		
		$subject->maxlength = 256;
		$message->maxlength = 65535;		
		
		parent::registerAction('form_MemberFeedback_Process');
	}
	
}

class form_MemberFeedback_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('send');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('feedback_to', 'subject', 'message');
			
		parent::setup($form);
	}
	
	/**
	 * @see SK_FormAction::checkData()
	 *
	 * @param array $data
	 * @param SK_FormResponse $response
	 * @param SK_Form $form
	 */
	public function checkData($data, $response, $form) 
	{
		$lang_errors = SK_Language::section('forms.member_feedback.messages.error');
		
		if ( trim($post_data['feedback_to']) ) {
			$response->addError($lang_errors->text('false_department'));
		}
		if ( !SK_HttpUser::profile_id() && trim($post_data['member_email']) ) {
			$response->addError($lang_errors->text('false_email'));
		}
		if ( trim($post_data['subject']) ) {
			$response->addError($lang_errors->text('no_subject'));
		}

		
	}

	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{		
		$section = new SK_Config_Section('site');
		$configs = $section->Section('official');
		
		$lang_errors = SK_Language::section('forms.member_feedback.messages.error');
		
		$email_arr = array(
			'site_email_main' => $configs->site_email_main,
			'site_email_support' => $configs->site_email_support,
			'site_email_billing' => $configs->site_email_billing
		);
				
		$post_data['feedback_to'] = trim($post_data['feedback_to']);
		
		if ( !$configs->$post_data['feedback_to'] ) {
			$response->addError($lang_errors->text('false_department'));
			return ;		
		}
		
		if ( isset($post_data['member_email']) && in_array(trim($post_data['member_email']), array_values($email_arr)) ) {
			$response->addError($lang_errors->text('false_email'), "member_email");
			return ;
		}
		
		
		if ( $profile_id = SK_HttpUser::profile_id() ) {
			$profile_fields = app_Profile::getFieldValues( $profile_id, array('username', 'email') );
		}
		
		$toEmail = $email_arr[$post_data['feedback_to']];
		
		try {
			$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
					->setRecipientEmail($toEmail)
					->setSenderEmail((@$profile_fields['email']) ? $profile_fields['email'] : trim( $post_data['member_email'] ))
					->setSenderName((@$profile_fields['username']) ? $profile_fields['username'] : trim( $post_data['member_email'] ))
					->setSubject($post_data['subject'])
					->setContent($post_data['message']);
			app_Mail::send($msg);
		} catch (SK_EmailException $e) {
			if ($e->getCode() == SK_EmailException::WRONG_SENDER_EMAIL_ADDR)
			{
				$response->addError($lang_errors->text('false_email'), "member_email");
				return;
			}
		}
				
		$_SESSION['feedback_to'] = $post_data['feedback_to'];
		$response->redirect( SK_Navigation::href('contact') );
	}
}
