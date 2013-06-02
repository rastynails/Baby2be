<?php

class form_ManageMailbox extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('manage_mailbox');
	}

	public function setup()
	{
		$field = new fieldType_custom_set('conversations');
		parent::registerField($field);
		
		$owner = new fieldType_hidden('mark_for');
		parent::registerField($owner);
		
		parent::registerAction('formManageMailbox_MarkUnread');
		parent::registerAction('formManageMailbox_MarkRead');
		parent::registerAction('formManageMailbox_Remove');
	}
	
	public function renderStart( array $params = null )
	{
		if ( !isset($params['mark_for']) ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() missing param "mark_for"', E_USER_WARNING);
		}
	
		$this->getField('mark_for')->setValue($params['mark_for']);
		
		return parent::renderStart($params); 
	}
}


class formManageMailbox_MarkUnread extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('mark_unread');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('mark_for');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
	{
		if (!isset($post_data['conversations']))
			$response->addError(SK_Language::text('%forms.manage_mailbox.error_msg.not_selected'));	
		elseif (isset($post_data['conversations']) && isset($post_data['mark_for']))
		{
			$marked = app_MailBox::markConversationsUnread( $post_data['conversations'], $post_data['mark_for'] );
			
			if ($marked)
			{
				$response->addMessage(SK_Language::text('%forms.manage_mailbox.msg.marked_unread'));
				$response->exec('window.location.reload();');
			}
		}
		else $response->addError(SK_Language::text('%forms.manage_mailbox.error_msg.not_marked'));
	}
}


class formManageMailbox_MarkRead extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('mark_read');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('mark_for');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
	{
		if (!isset($post_data['conversations']))
			$response->addError(SK_Language::text('%forms.manage_mailbox.error_msg.not_selected'));	
		elseif (isset($post_data['conversations']) && isset($post_data['mark_for']))
		{
			$marked = app_MailBox::markConversationsRead( $post_data['conversations'], $post_data['mark_for'], false );
			
			if ($marked)
			{
				$response->addMessage(SK_Language::text('%forms.manage_mailbox.msg.marked_read'));
				$response->exec('window.location.reload();');
			}
		}
		else $response->addError(SK_Language::text('%forms.manage_mailbox.error_msg.not_marked'));
	}
}

class formManageMailbox_Remove extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('remove');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('mark_for');
		
		$this->setConfirmation('%forms.manage_mailbox.actions.delete_confirm');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
	{
		if (!isset($post_data['conversations']))
			$response->addError(SK_Language::text('%forms.manage_mailbox.error_msg.not_selected'));
		elseif (isset($post_data['conversations']) && isset($post_data['mark_for']))
		{	
			$deleted = app_MailBox::deleteConversations( $post_data['conversations'], $post_data['mark_for'] );
			
			if ($deleted)
			{
				$response->addMessage(SK_Language::text('%forms.manage_mailbox.msg.deleted'));
				$response->exec('window.location.reload();');
			}
		}
	}
}

