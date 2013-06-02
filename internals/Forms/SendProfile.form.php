<?php

class form_SendProfile extends SK_Form
{
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct('send_profile');
	}
	
	public function setup()
	{		
		$this->registerField( new fieldType_text( 'email' ) );
		$this->registerField( new fieldType_hidden('profile_id') );
		$this->registerAction( new SendProfileFormAction() );
	}
	
}

class SendProfileFormAction extends SK_FormAction
{	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'send_profile' );
	}
	
	/**
	 * @see SK_FormAction::process()
	 *
	 * @param array $data
	 * @param SK_FormResponse $response
	 * @param SK_Form $form
	 */
	public function process( array $data, SK_FormResponse $response, SK_Form $form )
	{
		if( !SK_HttpUser::is_authenticated() )
			return;
		
		$profile_info = app_Profile::getFieldValues( (int)$data['profile_id'], array( 'profile_id', 'username' ) );
		$sender_info = app_Profile::getFieldValues( SK_HttpUser::profile_id(), array( 'username', 'profile_id' ) );
		
		$emails = explode( ',', $data['email'] );
		
		$message = 0;
		$error = 0;
		
		foreach ( $emails as $value )
		{
			if( preg_match( "/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_-]+?.[a-zA-Z0-9_]{2,}(\.\w{2})?$/i", trim( $value ) ) )
			{
                //if ( !app_Unsubscribe::isProfileUnsubscribed( $data['profile_id'] ) )
                //{
                    // send notify mail
                    $msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
                            ->setRecipientEmail($value)
                            ->setTpl('send_profile')
                            ->assignVar('profile_url', SK_Navigation::href( 'profile_view', array( 'profile_id' => $profile_info['profile_id'] )))
                            ->assignVar('username', $sender_info['username']);
                    app_Mail::send($msg);
               // }
                $message++;
			}
			else 
			{
				$error++;
			}
		}
		
		if( $message > 0 )
		{
			$response->addMessage( SK_Language::text( 'components.send_profile.msg_send_profile' ) );
			
			if( $error > 0 )
				$response->addMessage( SK_Language::text( 'components.send_profile.err_send_profile' ));
				
			return;
		}
			
		if( $error > 0 )
			$response->addError( SK_Language::text( 'components.send_profile.err_send_profile' ));
	}
	
	/**
	 * @see SK_FormAction::setup()
	 *
	 * @param SK_Form $form
	 */
	public function setup( SK_Form $form)
	{
		$this->required_fields = array( 'email' );
		parent::setup($form);
	}
}