<?php

class form_GroupSendMessage extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('group_send_message');
	}
	
	public function setup()
	{
		$group_id = new fieldType_hidden('group_id');
		parent::registerField($group_id);
		
		$subject = new fieldType_text('subject');
		parent::registerField($subject);
		
		$mess = new fieldType_textarea('message');
		parent::registerField($mess);
				
		parent::registerAction('form_GroupSendMessage_Send');
	}
}

class form_GroupSendMessage_Send extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('send');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('group_id', 'subject', 'message');
		
		parent::setup($form);
	}

	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$group_id = (int)trim($post_data['group_id']);
		$subject = trim($post_data['subject']);
		$message = trim($post_data['message']);
		
		if ( $group_id && strlen($subject) && strlen($message) )
		{			
			$sent = app_Groups::sendGroupMailing( SK_HttpUser::profile_id(), $group_id, $subject, $message );
			
			if ($sent)
			{
				$response->addMessage(SK_Language::text('forms.group_send_message.msg_sent', array('number' => $sent)));
				$response->redirect(SK_Navigation::href('group', array('group_id' => $group_id)));
			}
			else 
				$response->addError(SK_Language::text('forms.group_send_message.not_sent'));
		}
	}
}
