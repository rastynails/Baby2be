<?php

class component_MailboxConversationsList extends SK_Component
{
	/**
	 * Mailbox folder
	 * 
	 * possible values:
	 * <ul>
	 * <li>inbox</li>
	 * <li>sentbox</li>
	 * </ul>
	 *
	 * @var string
	 */
	private $folder;
	
	private $conv_list;
	
		
	/**
	 * Component MailboxConversationsList constructor.
	 *
	 * @return component_MailboxConversationsList
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('mailbox_conversations_list');
		
		$valid_folders = array('inbox', 'sentbox', 'write');
		if ( strlen($params['folder']))
			$param_folder = $params['folder'];
		else
			$param_folder = isset(SK_HttpRequest::$GET['folder']) ? SK_HttpRequest::$GET['folder'] : 'inbox';
		
		$this->folder = in_array($param_folder, $valid_folders) ? $param_folder : 'inbox';
		$page = ( isset( SK_HttpRequest::$GET['page'] ) && intval( SK_HttpRequest::$GET['page'] ) ) ? SK_HttpRequest::$GET['page'] : 1;
		
		$this->conv_list = app_MailBox::getProfileConversations( SK_HttpUser::profile_id(), $this->folder, $page );
				
		if (count($this->conv_list['conversations']) == 0 && intval(SK_HttpRequest::$GET['page']) > 1)
			SK_HttpRequest::redirect(SK_Navigation::href('mailbox', array('folder' => $this->folder, 'page' => intval(SK_HttpRequest::$GET['page']) - 1)));
		
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('MailboxConversationsList');
		
		$handler->construct();
				
		$this->frontend_handler = $handler;
		
        if ( $this->folder == 'write' )
        {
            $this->tpl_file = 'write.tpl';
        }
				
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$profile_id = SK_HttpUser::profile_id();
		
		$msg_count = app_MailBox::newMessages($profile_id);
		$msg_count = $msg_count ? " (".$msg_count.")": '';
		
		$Layout->assign('mailbox_menu_items', array (
			array (
				'href'	 =>	SK_Navigation::href('mailbox', array('folder'=>'inbox')),
				'label'	 =>	SK_Language::text('%components.mailbox_conversations_list.submenu_item_inbox').$msg_count,
				'active' => $this->folder == 'inbox',
				'class' => 'inbox'
			),
			array (
				'href'	 =>	SK_Navigation::href('mailbox', array('folder'=>'sentbox')),
				'label'	 =>	SK_Language::text('%components.mailbox_conversations_list.submenu_item_sentbox'),
				'active' => $this->folder == 'sentbox',
			    'class' => 'sentbox'
			),
			array(
                'href'   => SK_Navigation::href('mailbox', array('folder'=>'write')),
                'label'  => SK_Language::text('%components.mailbox_conversations_list.submenu_item_write'),
                'active' => $this->folder == 'write',
                'class' => 'write'
			)
		));
		
		if ( $this->folder != 'write' )
		{
            $service = new SK_Service('read_message');
            $serv_perm = $service->checkPermissions(); 
            $show_rdbl = false;
            
            if ($serv_perm != SK_Service::SERVICE_FULL) {
                $Layout->assign('perm_msg', $service->permission_message['message']);
                
                $ms = app_Membership::profileCurrentMembershipInfo($profile_id);            
                if ($ms['type'] == 'subscription' && $ms['limit'] == 'unlimited')
                    $show_rdbl = true;
            } 
            
            $Layout->assign('check_rdbl', $show_rdbl);
                    
            $Layout->assign('paging', array
            (
                'total'     => $this->conv_list['total'],
                'on_page'   => SK_Config::Section('site')->Section('additional')->Section('mailbox')->mails_per_page,
                'pages'     => SK_Config::Section('site')->Section('additional')->Section('profile_list')->nav_per_page
            ));
            
            $Layout->assign('conv_arr', $this->conv_list['conversations']);
		}
				
		$Layout->assign('folder', $this->folder);
		$Layout->assign('profile_id', $profile_id);
		
		return parent::render($Layout);
	}
	
	
	public function handleForm( SK_Form $form )
	{
	    switch ( $form->getName() )
	    {
	        case 'manage_mailbox':
	            /*$form->frontend_handler->bind('success', "function(data) {
                    this.ownerComponent.reload({folder: '".SK_HttpRequest::$GET['folder']."'});
                }");*/
	            break;
	            
	        case 'send_message_page':
	            if ( isset(SK_HttpRequest::$GET['username']) )
	            {
                    $form->getField('recipient')->setValue(htmlspecialchars(SK_HttpRequest::$GET['username']));
	            }
	            $form->getField('sender_id')->setValue(SK_HttpUser::profile_id());
	            break;
	    }
	}
}
