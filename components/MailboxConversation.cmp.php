<?php

class component_MailboxConversation extends SK_Component
{
	/**
	 * id of the conversation
	 *
	 * @var int
	 */
	private $conv_id;
	
		
	/**
	 * Component MailboxConversation constructor.
	 *
	 * @return component_MailboxConversation
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('mailbox_conversation');
		
		$this->conv_id = SK_HttpRequest::$GET['conv_id'];
	}
	
		
	public function render( SK_Layout $Layout )
	{
		$profile_id = SK_HttpUser::profile_id();
		
		$messages = app_MailBox::getConversationMessages($this->conv_id, $profile_id);

		if ( $messages['info']['is_system'] == 'yes' )
		{
		    $header = SK_Language::text('%components.mailbox_conversations_list.system_msg');
		}
		else
		{
            $header = isset($messages['info']['opponent']) ? SK_Language::text('%components.mailbox_conversation.header', array('opponent' => $messages['info']['opponent'])) : SK_Language::text('%label.deleted_member');
		}
		
		SK_Language::defineGlobal('header', $header);
			
		$service = new SK_Service('read_message');
		$serv_perm = $service->checkPermissions(); 
		$show_rdbl = false;
		
		if ($serv_perm != SK_Service::SERVICE_FULL) {
			$Layout->assign('perm_msg', $service->permission_message['message']);
			
			$ms = app_Membership::profileCurrentMembershipInfo($profile_id);			
			if ($ms['type'] == 'subscription' && $ms['limit'] == 'unlimited')
				$show_rdbl = true;
		}
	    else {
            if ( !app_MailBox::conversationIsRead($this->conv_id, $profile_id) )
            {
                $service->trackServiceUse();
            }
        } 
		
		$Layout->assign('check_rdbl', $show_rdbl);

		app_MailBox::markConversationsRead(array($this->conv_id), $profile_id, true);
		
		$msg_count = app_MailBox::newMessages($profile_id);
		
		$msg_count = $msg_count ? " (".$msg_count.")": '';
				
		$Layout->assign('mailbox_menu_items', array (
			array (
				'href'	 =>	SK_Navigation::href('mailbox', array('folder'=>'inbox')),
				'label'	 =>	SK_Language::section('components.mailbox_conversations_list')->text('submenu_item_inbox').$msg_count,
				'active' => false,
			),
			array (
				'href'	=>	SK_Navigation::href('mailbox', array('folder'=>'sentbox')),
				'label'	=>	SK_Language::section('components.mailbox_conversations_list')->text('submenu_item_sentbox'),
				'active'=>	false,
			),
		));
		
		if ( !is_array($messages) )
		{
			if ($messages == -1)
				SK_HttpRequest::showFalsePage();
			else if ($messages == -2)
				SK_HttpRequest::showFalsePage();
		}
		else
		{
			$Layout->assign('messages', $messages['mails']);
			$Layout->assign('conv', $messages['info']);
			$Layout->assign('msg_count', $messages['total']);
			/*$Layout->assign('paging',array(
				'total'=>$messages['total'],
				'on_page'=> SK_Config::Section('site')->Section('additional')->Section('mailbox')->mails_per_page,
				'pages'=> SK_Config::Section('site')->Section('additional')->Section('profile_list')->nav_per_page,
			));*/
			
			$Layout->assign('profile_id', $profile_id);
		}

				
		return parent::render($Layout);
	}
	
}

