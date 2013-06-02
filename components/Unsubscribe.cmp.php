<?php

class component_Unsubscribe extends SK_Component
{
	private $unsubscriber;

	/**
	 * Component Unsubscribe constructor.
	 *
	 * @return component_Unsubscribe
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('unsubscribe');

		$this->unsubscriber = app_Unsubscribe::getProfileIdByCode(SK_HttpRequest::$GET['unsubscribe']);
                
		if( !intval($this->unsubscriber) )
			SK_HttpRequest::redirect(SK_Navigation::href('index'));
	}

	public function render( SK_Layout $Layout )
	{
		$profile_fields = app_Profile::getFieldValues( $this->unsubscriber, array('email', 'username') );
        $message = "";
		if ( app_Unsubscribe::isProfileUnsubscribed($this->unsubscriber) ) {

			$message =  SK_Language::section('components.unsubscribe')->text('already_unsubscribed', array(
				'profile_username' => $profile_fields['username'],
				'profile_email' => $profile_fields['email']
			));
			$show_form = false;

		} else {

			/*$message =  SK_Language::section('components.unsubscribe')->text('unsubscribe_text', array(
				'profile_username' => $profile_fields['username'],
				'profile_email' => $profile_fields['email']
			));*/
			$show_form = true;
		}

		$Layout->assign('message', $message);
		$Layout->assign('show_form', $show_form);

		$pwd_field = $this->unsubscriber != SK_HttpUser::profile_id();
		$Layout->assign('pwd_field', $pwd_field);

		return parent::render($Layout);
	}

	public function handleForm( SK_Form $form )
	{
		$form->getField('unsubscriber')->setValue($this->unsubscriber);

		$form->frontend_handler->bind('success', 'function(){
			window.location.href="'.SK_Navigation::href('index').'";
		}');
	}

}
