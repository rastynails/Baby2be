<?php

class form_SendMessagePage extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('send_message_page');
	}

	public function setup()
	{
		$subject = new fieldType_text('subject');
		parent::registerField($subject);
		
		$text = new fieldType_textarea('text');
		$text->maxlength = 2000;
		parent::registerField($text);
		
		$sender_id = new fieldType_hidden('sender_id');
		parent::registerField($sender_id);
        
		$recipient = new field_username_suggest('recipient');
		$recipient->setResponderAction('suggest_mailbox_recipients');
		parent::registerField($recipient);
		
		parent::registerAction('formSendMessage_Send');
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
		$this->required_fields = array('sender_id', 'recipient', 'subject', 'text');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$sender_id = $post_data['sender_id'];
		$recipient = $post_data['recipient']['user_name'];
		$text = $post_data['text'];
		$subject = $post_data['subject'];
		
		$recipient_id = app_Profile::getProfileIdByUsername(trim($recipient));
		
		if ( !strlen($recipient) )
		{
		    $response->addError(SK_Language::text('%forms.send_message.error_msg.recipient_not_found'), 'recipient');
            return true;
		}
		
		if ( !$recipient_id )
		{
            $response->addError(SK_Language::text('%forms.send_message.error_msg.recipient_not_found'), 'recipient');
            return true;
		}
		
		// check if profile-sender has not status 'suspended'
		$status = app_Profile::getFieldValues($sender_id, 'status');

		if ( $status == 'suspended' ) 
		{
			$response->addError(SK_Language::text('%forms.send_message.error_msg.sender_status_suspended'));
			return true;
		}

		// check if profile was blocked by the recipient
		if ( app_Bookmark::isProfileBlocked( $recipient_id, $sender_id ) ) 
		{
			$response->addError(SK_Language::text('%forms.send_message.error_msg.profile_blocked'));
			return true;
		}
			
		// check service permission
		$send_msg_service = new SK_Service('send_message', $sender_id);
		$send_msg_permission = $send_msg_service->checkPermissions();
		
		$send_rdbl_msg_service = new SK_Service('send_readable_message', $sender_id);
		$send_rdbl_msg_permission = $send_rdbl_msg_service->checkPermissions();
		
		$redirect_url = json_encode(SK_Navigation::href('mailbox', array('folder' => 'sentbox')));

		// check if the Mailbox feature is available
		if ( $send_msg_permission != SK_Service::SERVICE_NO )
		{			
			if ( $send_rdbl_msg_permission == SK_Service::SERVICE_FULL )
			{
				$msg_sent = app_MailBox::sendMessage($sender_id, $recipient_id, $text, $subject, true);
				switch ( $msg_sent )
				{
					case -1:
						$response->addError(SK_Language::text('%forms.send_message.error_msg.send_nobody'));
						break;
					case -2:
						$response->addError(SK_Language::text('%forms.send_message.error_msg.send_yourself'));
						break;
					default:
						if ( $msg_sent == 1 )
						{
							app_MailBox::sendEmailNotification($recipient_id, $sender_id);
						}
						
						$send_rdbl_msg_service->trackServiceUse();			
						$response->addMessage(SK_Language::text('%forms.send_message.msg.readable_msg_sent')); 
						$response->exec('window.location.href='.$redirect_url.';');
						break;
				}
			}
			else if ( $send_msg_permission == SK_Service::SERVICE_FULL )
			{
				$msg_sent = app_MailBox::sendMessage($sender_id, $recipient_id, $text, $subject, false);
				switch ( $msg_sent )
				{
					case -1:
						$response->addError(SK_Language::text('%forms.send_message.error_msg.send_nobody'));
						break;
					case -2:
						$response->addError(SK_Language::text('%forms.send_message.error_msg.send_yourself'));
						break;

					default:
						if ( $msg_sent == 1 )
							app_MailBox::sendEmailNotification($recipient_id, $sender_id);
						
						$send_msg_service->trackServiceUse();
						$response->addMessage(SK_Language::text('%forms.send_message.msg.readable_msg_sent')); 
						$response->exec('window.location.href='.$redirect_url.';');
						break;
				}
			}
			else {
				$response->addError($send_msg_service->permission_message['message']);
			}
		}
	}
}
