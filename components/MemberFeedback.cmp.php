<?php

class component_MemberFeedback extends SK_Component
{

	public function __construct( array $params = null )
	{
		parent::__construct('member_feedback');
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form ) 
	{	
		$lang_labels = SK_Language::section('forms.member_feedback.labels');
		
		$feedback_to = array(
			$lang_labels->text('site_email_main') => 'site_email_main',
			$lang_labels->text('site_email_support') => 'site_email_support',
			$lang_labels->text('site_email_billing') => 'site_email_billing'
		);
		
		$form->getField('feedback_to')->setValues( $feedback_to );

	}

	
	public function render( SK_Layout $Layout ){
		
		if ( $_SESSION['feedback_to'] )
		{
			$section = new SK_Config_Section('site');
			$configs = $section->Section('official');
			$lang_msg = SK_Language::section('forms.member_feedback.messages.success');
			$lang_labels = SK_Language::section('forms.member_feedback.labels');
			
			$feedback['recipient'] = $lang_labels->text( $_SESSION['feedback_to'] );
			unset( $_SESSION['feedback_to'] );

			$Layout->assign( 'message', $lang_msg->text('success', $feedback) );
		}

		$Layout->assign( 'profile_id', SK_HttpUser::profile_id() );
		
		return parent::render($Layout);
	}
}
