<?php

class form_ManageConversation extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('manage_conversation');
	}

	public function setup()
	{
		$field = new fieldType_hidden('conversation_id');
		parent::registerField($field);
		
		$owner = new fieldType_hidden('mark_for');
		parent::registerField($owner);
		
		parent::registerAction('formManageConversation_MarkUnread');
		parent::registerAction('formManageConversation_Remove');
	}
	
	public function renderStart( array $params = null )
	{
		if ( !isset($params['conversation_id']) ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() missing param "conversation_id"', E_USER_WARNING);
		}
		if ( !isset($params['mark_for']) ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() missing param "mark_for"', E_USER_WARNING);
		}
	
		$this->getField('mark_for')->setValue($params['mark_for']);
		$this->getField('conversation_id')->setValue($params['conversation_id']);
		
		return parent::renderStart($params); 
	}
}


class formManageConversation_MarkUnread extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('mark_unread');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('conversation_id','mark_for');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
	{
		if (isset($post_data['conversation_id']) && isset($post_data['mark_for']))
		{
			$marked = app_MailBox::markConversationsUnread( array($post_data['conversation_id']), $post_data['mark_for'] );
			
			if ($marked)
			{
				$response->addMessage(SK_Language::text('%forms.manage_mailbox.msg.marked_unread'));
				$response->exec('window.location="'.SK_Navigation::href('mailbox').'"');
			}
		}
		else $response->addError(SK_Language::text('%forms.manage_mailbox.error_msg.not_marked')); 
	}
}


class formManageConversation_Remove extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('remove');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('conversation_id', 'mark_for');
		
		$this->setConfirmation('%forms.manage_conversation.actions.delete_confirm');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
	{
		$deleted = app_MailBox::deleteConversations( array($post_data['conversation_id']), $post_data['mark_for'] );
				
		if ($deleted)
		{
			$response->addMessage(SK_Language::text('%forms.manage_mailbox.msg.deleted'));
			$response->exec('window.location="'.SK_Navigation::href('mailbox').'"');	
		}
	}
}

