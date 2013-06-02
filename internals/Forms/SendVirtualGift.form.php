<?php

class form_SendVirtualGift extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('send_virtual_gift');
	}

	public function setup()
	{		
		$sender_id = new fieldType_hidden('sender_id');
		parent::registerField($sender_id);
			
		$recipient_id = new fieldType_hidden('recipient_id');
		parent::registerField($recipient_id);
		
		$tpl_id = new fieldType_hidden('tpl_id');
		parent::registerField($tpl_id);
        
		$sign = new fieldType_textarea('sign');
        parent::registerField($sign);
        
        $is_private = new fieldType_checkbox('is_private');
        parent::registerField($is_private);
		
		parent::registerAction('formSendVirtualGift_Send');
	}
}

class formSendVirtualGift_Send extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('send');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('sender_id', 'recipient_id', 'tpl_id');
				
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$sender_id = $post_data['sender_id'];
		$recipient_id = $post_data['recipient_id'];
		$tpl_id = $post_data['tpl_id'];
		$sign = trim($post_data['sign']);
		
		// check if profile-sender has not status 'suspended'
		$status = app_Profile::getFieldValues($sender_id, 'status');

		if ( $status == 'suspended' ) {
			$response->addError(SK_Language::section('forms.send_message.error_msg')->text('sender_status_suspended'));
			return;
		}

		// check if profile was blocked by the recipient
		if ( app_Bookmark::isProfileBlocked( $recipient_id, $sender_id ) ) {
			$response->addError(SK_Language::section('forms.send_message.error_msg')->text('profile_blocked'));
			return;
		}
		
		$tpl = app_VirtualGift::getGiftTemplate($tpl_id);
        $servCost = $tpl['credits'];
        
		$give_gift_service = new SK_Service('give_gifts', $sender_id);
		$give_gift_service->setCreditsCost($servCost);
        
        if ( $give_gift_service->checkPermissions() == SK_Service::SERVICE_FULL )
        {
            if ( app_VirtualGift::sendVirtualGift($sender_id, $recipient_id, $tpl_id, $sign, $post_data['is_private'] ) )
            {
                $give_gift_service->trackServiceUse();
                                 
                $response->addMessage(SK_Language::text('%components.send_virtual_gift.gift_sent'));
            }
            else
            {
                $response->addMessage(SK_Language::text('%components.send_virtual_gift.gift_not_sent'));
            } 
        }
        else 
        {
            $response->addError($give_gift_service->permission_message['message']);
        }
	}
}
