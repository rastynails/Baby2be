<?php

class component_MailboxControls extends SK_Component
{
	
	private $profile_id;
	
	private $folder = 'inbox';
	
	private $conversation_id;
	
	private $controls = array();
	
	
	public function __construct( array $params = null )
	{
		$this->profile_id = ( isset($params['profile_id']) && ($profile_id = intval($params['profile_id'])) ) ? $profile_id	: 0;
		$this->folder = isset($params['folder']) ? $params['folder'] : 'inbox';	
		$this->conversation_id = ( isset($params['conversation_id']) && ($conversation_id = intval($params['conversation_id'])) ) ? $conversation_id : 0;
		
		switch ($this->folder)
		{
			case 'sentbox':
				$this->controls = array('delete');
				break;
			case 'inbox':
			default:
				$this->controls = array('reply','block','delete');
				break;	
		}

		parent::__construct('mailbox_controls');
	}
	
	
	private function addControls(SK_ComponentFrontendHandler $handler)
	{
		$text_ns = SK_Language::section('components.mailbox_controls.labels');
		
		foreach ($this->controls as $control) 
		{
			switch ($control) 
			{
				case 'reply':
					$handler->registerControl(
						array(
							'name' => 'reply',
							'label' => $text_ns->text('reply'),
							'backend_func' => 'ajax_Handler'
						)
					);					
					break;
				case 'block':
					$handler->registerControl(
						array(
							'name' => 'block',
							'label' => $text_ns->text('block'),
							'backend_func' => 'ajax_Handler'
						)
					);
					$handler->registerControl(
						array(
							'name' => 'unblock',
							'label' => $text_ns->text('unblock'),
							'backend_func' => 'ajax_Handler'
						)
					);
					break;
				case 'delete':
					$handler->registerControl(
						array(
							'name' => 'delete',
							'label' => $text_ns->text('delete'),
							'msg' => SK_Language::section('components.mailbox_controls.msg')->text('confirm_delete'),
							'backend_func' => 'ajax_Handler'
						)
					);
					break;
			}
		}
	}
	
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('MailboxControls');
		
		$handler->construct($this->profile_id, $this->conversation_id, $this->folder);

		$this->addControls($handler);
		
		if ( in_array('reply', $this->controls) )
			$handler->displayControl('reply');	
				
		if ( in_array('block', $this->controls) && !app_Bookmark::isProfileBlocked(SK_HttpUser::profile_id(), $this->profile_id) ) 
			$handler->displayControl('block');	
		else 
			$handler->displayControl('unblock');
		
		if ( in_array('delete', $this->controls ) ) 
			$handler->displayControl('delete');
				
		$this->frontend_handler = $handler;
				
		parent::prepare($Layout, $Frontend);
	}
	
	
	public static function ajax_Handler($params, SK_ComponentFrontendHandler $handler)
	{
		$profile_id = (int)$params->profile_id;
		$control = $params->control;
		$folder = $params->folder;
		$conversation = $params->conversation;
		
		$error_ns = SK_Language::section('components.mailbox_controls.error_msg');
		$message_ns = SK_Language::section('components.mailbox_controls.msg');
		
		switch ($control)
		{
			case 'block':
				$service = new SK_Service('block_members', $profile_id);
				if ( $service->checkPermissions() != SK_Service::SERVICE_FULL ) {
					$handler->error($service->permission_message['message']);
					break;
				}
				
				if ( !app_Bookmark::BlockProfile(SK_HttpUser::profile_id(), $profile_id) ) {
					$handler->error(SK_Language::section('components.profile_references.messages.error')->text('block'));
					break;
				}
				
				$handler->message(SK_Language::section('components.profile_references.messages.success')->text('block'));
				$handler->redirect();
				break;
				
			case 'unblock':
				if ( !app_Bookmark::UnblockProfile(SK_HttpUser::profile_id(), $profile_id) ) {
					$handler->error(SK_Language::section('components.profile_references.messages.error')->text('unblock'));
					break;
				}
				
				$handler->message(SK_Language::section('components.profile_references.messages.success')->text('unblock'));
				$handler->redirect();
				break;
				
			case 'reply':
				$handler->openConversation( SK_Navigation::href('mailbox_conversation', array('conv_id'=>$conversation)).'#reply');
				break;
				
			case 'delete':
				$deleted = app_MailBox::deleteConversations( array($conversation), SK_HttpUser::profile_id() );
				if ($deleted)
					$handler->message(SK_Language::text('%forms.manage_mailbox.msg.deleted'));
					
				$handler->redirect();
				break;
		}
		
	}
	
}
