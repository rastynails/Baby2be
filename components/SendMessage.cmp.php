<?php

class component_SendMessage extends SK_Component
{
	private $conversation_id;
	
	private $sender_id;
	
	private $recipient_id;
	
	private $type;
	
	/**
	 * Component sendMessage constructor.
	 *
	 * @return component_SendMessage
	 */
	
	public function __construct( array $params = null )
	{
		parent::__construct('send_message');
		if (!app_Features::isAvailable(7))
			$this->annul();
		else {	
			$this->conversation_id = isset($params['conversation_id']) ? $params['conversation_id'] : 0;
			$this->sender_id = $params['sender_id'];
			$this->recipient_id = $params['recipient_id'];
			$this->type = isset($params['type']) ? $params['type'] : 'new';
		}
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		// check if profile-sender has not status 'suspended'
		$status = app_Profile::getFieldValues($this->sender_id,'status');

		$error_msg = null;
		
		if ( $status == 'suspended' )
			$error_msg = SK_Language::section('forms.send_message.error_msg')->text('sender_status_suspended');

		// check if profile was blocked by the recipient
		if ( app_Bookmark::isProfileBlocked( $this->recipient_id, $this->sender_id ) ) 
			$error_msg = SK_Language::section('forms.send_message.error_msg')->text('profile_blocked');
		
		// check service permission
		$send_msg_service = new SK_Service('send_message', $this->sender_id);
		$send_msg_permission = $send_msg_service->checkPermissions();
		
		$send_rdbl_msg_service = new SK_Service('send_readable_message', $this->sender_id);
		$send_rdbl_msg_permission = $send_rdbl_msg_service->checkPermissions();
		
		if ( $send_rdbl_msg_permission != SK_Service::SERVICE_FULL && $send_msg_permission != SK_Service::SERVICE_FULL )
			$error_msg = $send_msg_service->permission_message['message'];	
		
		$handler = new SK_ComponentFrontendHandler('SendMessage');
		$handler->construct($error_msg);
		$this->frontend_handler = $handler;
				
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign(array(
			'conversation_id'	=>	$this->conversation_id,
			'sender_id'			=>	$this->sender_id,
			'recipient_id'		=>	$this->recipient_id,
			'type'				=>  $this->type
		));
		return parent::render($Layout);
	}
	
	public function handleForm( SK_Form $form) 
	{
		$form->getField( 'recipient_id' )->setValue( $this->recipient_id );
		$form->getField( 'sender_id' )->setValue( $this->sender_id );
		$form->getField( 'conversation_id' )->setValue( $this->conversation_id );
		$form->getField( 'type' )->setValue( $this->type );
		
		$form->frontend_handler->bind('success', 'function(){
			if (window.send_message_floatbox) { 
				window.send_message_floatbox.close();
				this.$form.resetForm();
			}
		}');
	}
}
