<?php

class form_SendMessage extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('send_message');
	}

	public function setup()
	{
		$subject = new fieldType_text('subject');
		parent::registerField($subject);
		
		$text = new fieldType_textarea('text');
		$text->maxlength = 2000;
		parent::registerField($text);
		
		$conversation_id = new fieldType_hidden('conversation_id');
		parent::registerField($conversation_id);
		
		$sender_id = new fieldType_hidden('sender_id');
		parent::registerField($sender_id);
		
		$recipient_id = new fieldType_hidden('recipient_id');
		parent::registerField($recipient_id);
		
		$type = new fieldType_hidden('type');
		parent::registerField($type);
		
		parent::registerAction('formSendMessage_Send');
		parent::registerAction('formSendMessage_Reply');
	}
}

class formSendMessage_Reply extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('reply');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('conversation_id', 'sender_id', 'recipient_id', 'text', 'type');
		$this->process_fields = array('conversation_id', 'sender_id', 'recipient_id', 'text', 'type');
				
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$sender_id = $post_data['sender_id'];
		$recipient_id = $post_data['recipient_id'];
		$text = trim($post_data['text']);
		$subject = trim($post_data['subject']);
		$conv_id = $post_data['conversation_id'];
		
		// check if profile-sender has not status 'suspended'
		$status = app_Profile::getFieldValues($sender_id, 'status');

		if ( $status == 'suspended' ) {
			$response->addError(SK_Language::section('forms.send_message.error_msg')->text('sender_status_suspended'));
			return true;
		}

		// check if profile was blocked by the recipient
		if ( app_Bookmark::isProfileBlocked( $recipient_id, $sender_id ) ) {
			$response->addError(SK_Language::section('forms.send_message.error_msg')->text('profile_blocked'));
			return true;
		}

		$is_readable = app_MailBox::conversationIsReadable($conv_id);
		
		// check service permission
		$send_msg_service = new SK_Service('send_message', $sender_id);
		$send_msg_permission = $send_msg_service->checkPermissions();
		
		$send_rdbl_msg_service = new SK_Service('send_readable_message', $sender_id);
		$send_rdbl_msg_permission = $send_rdbl_msg_service->checkPermissions();
		
		$reply_rdbl_msg_service = new SK_Service('reply_readable_message', $sender_id);
		$reply_rdbl_msg_permission = $reply_rdbl_msg_service->checkPermissions();
		$membership = app_Membership::profileCurrentMembershipInfo($sender_id);
		$free_member_cond = $is_readable && $membership['type'] == 'subscription' && $membership['limit'] == 'unlimited' &&  $reply_rdbl_msg_permission == SK_Service::SERVICE_FULL;
		
		// check if the Mailbox feature is available
		if ( $send_msg_permission != SK_Service::SERVICE_NO || $free_member_cond)
		{
			if ( $send_rdbl_msg_permission == SK_Service::SERVICE_FULL )
			{
				$msg_sent = app_MailBox::sendReplyMessage( $conv_id, $sender_id, $recipient_id, $text, true );

				switch ( $msg_sent )
				{
					case -1:
						$response->addError( SK_Language::section('forms.send_message.error_msg')->text('send_nobody') );
						break;
					case -2:
						$response->addError( SK_Language::section('forms.send_message.error_msg')->text('send_yourself') );
						break;
					default:
						if ($msg_sent == 1)
						{
							app_MailBox::sendEmailNotification( $recipient_id, $sender_id );
						}
						$send_rdbl_msg_service->trackServiceUse();					
						$response->addMessage( SK_Language::section('forms.send_message.msg')->text('readable_msg_sent')); 
						$response->exec('window.location.reload();');
						break;
				}
			}
			elseif ( $send_msg_permission == SK_Service::SERVICE_FULL || $free_member_cond)
			{
				$msg_sent = app_MailBox::sendReplyMessage( $conv_id, $sender_id, $recipient_id, $text, false ); 
				switch ( $msg_sent )
				{
					case -1:
						$response->addError( SK_Language::section('forms.send_message.error_msg')->text('send_nobody') );
						break;
					case -2:
						$response->addError( SK_Language::section('forms.send_message.error_msg')->text('send_yourself') );
						break;
					default:
						if ($msg_sent == 1)
							app_MailBox::sendEmailNotification( $recipient_id, $sender_id );

						$send_msg_service->trackServiceUse();					
						$response->addMessage( SK_Language::section('forms.send_message.msg')->text('msg_sent')); 
						
						$response->exec('window.location.reload();');
						break;
				}
			}
			else {
				$response->addError($send_msg_service->permission_message['message']);				
			}
		}
		else 
			$response->addError($send_msg_service->permission_message['message']);
	}
}


class formSendMessage_Send extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('send');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('sender_id', 'recipient_id', 'subject', 'text', 'type');
		$this->process_fields = array('sender_id', 'recipient_id', 'subject', 'text', 'type');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$sender_id = $post_data['sender_id'];
		$recipient_id = $post_data['recipient_id'];
		$text = $post_data['text'];
		$subject = $post_data['subject'];
				
		// check if profile-sender has not status 'suspended'
		$status = app_Profile::getFieldValues($sender_id,'status');

		if ( $status == 'suspended' ) {
			$response->addError(SK_Language::section('forms.send_message.error_msg')->text('sender_status_suspended'));
			return true;
		}

		// check if profile was blocked by the recipient
		if ( app_Bookmark::isProfileBlocked( $recipient_id, $sender_id ) ) {
			$response->addError(SK_Language::section('forms.send_message.error_msg')->text('profile_blocked'));
			return true;
		}
			
		// check service permission
		$send_msg_service = new SK_Service('send_message', $sender_id);
		$send_msg_permission = $send_msg_service->checkPermissions();
		
		$send_rdbl_msg_service = new SK_Service('send_readable_message', $sender_id);
		$send_rdbl_msg_permission = $send_rdbl_msg_service->checkPermissions();

		// check if the Mailbox feature is available
		if ( $send_msg_permission != SK_Service::SERVICE_NO )
		{
			
			if ( $send_rdbl_msg_permission == SK_Service::SERVICE_FULL )
			{
				$msg_sent = app_MailBox::sendMessage( $sender_id, $recipient_id, $text, $subject, true );
				switch ( $msg_sent )
				{
					case -1:
						$response->addError( SK_Language::section('forms.send_message.error_msg')->text('send_nobody') );
						break;
					case -2:
						$response->addError( SK_Language::section('forms.send_message.error_msg')->text('send_yourself') );
						break;
					default:
						if ($msg_sent == 1)
						{
							app_MailBox::sendEmailNotification( $recipient_id, $sender_id );
						}
						$send_rdbl_msg_service->trackServiceUse();					
						$response->addMessage( SK_Language::section('forms.send_message.msg')->text('readable_msg_sent')); 
						break;
						//$response->exec('window.location.reload();');
				}
			}
			else if ( $send_msg_permission == SK_Service::SERVICE_FULL )
			{
				$msg_sent = app_MailBox::sendMessage( $sender_id, $recipient_id, $text, $subject, false );
				switch ( $msg_sent )
				{
					case -1:
						$response->addError( SK_Language::section('forms.send_message.error_msg')->text('send_nobody') );
						break;
					case -2:
						$response->addError( SK_Language::section('forms.send_message.error_msg')->text('send_yourself') );
						break;

					default:
						if ($msg_sent == 1)
							app_MailBox::sendEmailNotification( $recipient_id, $sender_id );
						
						$send_msg_service->trackServiceUse();					
						$response->addMessage( SK_Language::section('forms.send_message.msg')->text('msg_sent')); 
						break;
						//$response->exec('window.location.reload();');
				}
			}
			else {
				$response->addError($send_msg_service->permission_message['message']);				
			}
		}
	}
}

