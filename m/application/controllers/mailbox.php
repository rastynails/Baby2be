<?php defined('SYSPATH') OR die('No direct access allowed.');

class Mailbox_Controller extends Controller
{	
	private $folder;
	
	private $conv_list;
	
	private $content;

	function __construct()
	{
		parent::__construct();

		if ( !SKM_User::is_authenticated() ) {
			Session::instance()->set("requested_url", "/".url::current()); 
			url::redirect('sign_in');
		}
	}
	
	public function conversations( $folder = 'inbox', $page = 1 )
	{
		$valid_folders = array('inbox', 'sent');
		$this->folder = in_array($folder, $valid_folders) ? $folder : 'inbox';
		if ($this->folder == 'sent')
			$this->folder = 'sentbox';
		
		$profile_id = SKM_User::profile_id();
		
		$view = new View('default');
		$view->header = SKM_Template::header(); 
		
		// menu
		$m = new SKM_Menu('main', 'mailbox', SKM_Language::text('%nav_doc_item.headers.mailbox'));
		$view->menu = $m->get_view();
		
		$on_page = SK_Config::Section('site')->Section('additional')->Section('mailbox')->mails_per_page;
		
		$service = new SK_Service('read_message', $profile_id);
		$serv_perm = $service->checkPermissions(); 
		$show_rdbl = false;
		
		if ($serv_perm != SK_Service::SERVICE_FULL) {
			SKM_Template::addTplVar('perm_msg', $service->permission_message['message']);
			
			$ms = app_Membership::profileCurrentMembershipInfo($profile_id);			
			if ($ms['type'] == 'subscription' && $ms['limit'] == 'unlimited')
				$show_rdbl = true;
		}
		SKM_Template::addTplVar('check_rdbl', $show_rdbl);
		
		$this->conv_list = app_MailBox::getProfileConversations( $profile_id, $this->folder, $page);

		if ( $this->conv_list['total'] )
		{
			foreach ($this->conv_list['conversations'] as &$conv)
			{
				$conv['conv_href'] = url::base() . "mailbox/conv/".$conv['conversation_id'];
				$conv['text'] = preg_replace('/ <a href="(.*?)member\/gift.php(.*?)<\/a>/', '', $conv['text']);
			}
			
			$pagination = new Pagination(array(
		        'base_url'    => 'mailbox/' . $folder,
		        'total_items' => $this->conv_list['total'],
				'items_per_page' => $on_page,
		        'style' => 'ska'
		    ));
		    
		    SKM_Template::addTplVar('conv_arr', $this->conv_list['conversations']);
		    SKM_Template::addTplVar('paging', $pagination);
		    SKM_Template::addTplVar('folder', $this->folder);
		    SKM_Template::addTplVar('profile_id', $profile_id);
		    SKM_Template::addTplVar('conv_url', url::base(). 'mailbox/conv/');
		}
		
		$mailbox = new View('mailbox', SKM_Template::getTplVars());
		$view->content = $mailbox;
		$view->footer = SKM_Template::footer();
		
		$view->render(TRUE);
	}
	
	public function list_messages( $conv_id )
	{
		if ( isset($_POST['conv_id']) 
				&& isset($_POST['sender_id']) 
				&& isset($_POST['recipient_id'])
				&& isset($_POST['message']) )
		{
			$con_id = (int)$_POST['conv_id'];
			$sender_id = (int)$_POST['sender_id'];
			$recipient_id = (int)$_POST['recipient_id'];
			$message = trim($_POST['message']);
			
			//Kohana::debug($sender_id);
			$send_res = $this->replyMessage($sender_id, $recipient_id, $con_id, $message);
			switch ($send_res['code'])
			{
				case 1:
					SKM_Template::addMessage(SKM_Language::text('%forms.send_message.msg.msg_sent'));
					break;
				case -1:
					SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.sender_status_suspended'), 'notice');
					break;
				case -2:
					SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.profile_blocked'), 'notice');
					break;
				case -3:
					SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.send_nobody'), 'notice');
					break;	
				case -4:
					SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.send_yourself'), 'notice'); 
					break;
				case -5:
					SKM_Template::addMessage($send_res['msg'], 'notice');
					break;
			}
			url::redirect('mailbox/conv/' . $con_id . '#reply');
		}
		
		$profile_id = SKM_User::profile_id();
		
		$view = new View('default');
		$view->header = SKM_Template::header(); 
		
		// menu
		$m = new SKM_Menu('main', 'mailbox', SKM_Language::text('%nav_doc_item.headers.mailbox'));
		$view->menu = $m->get_view();
		
		$on_page = SK_Config::Section('site')->Section('additional')->Section('mailbox')->mails_per_page;
		
		$service = new SK_Service('read_message', $profile_id);
		$serv_perm = $service->checkPermissions(); 
		$show_rdbl = false;
		
		if ($serv_perm != SK_Service::SERVICE_FULL) {
			SKM_Template::addTplVar('perm_msg', $service->permission_message['message']);
			
			$ms = app_Membership::profileCurrentMembershipInfo($profile_id);		
			if ($ms['type'] == 'subscription' && $ms['limit'] == 'unlimited')
				$show_rdbl = true;
		}
		SKM_Template::addTplVar('check_rdbl', $show_rdbl);
		
		app_MailBox::markConversationsRead(array($conv_id), $profile_id, true);
		$msg_count = app_MailBox::newMessages($profile_id);
		$msg_count = $msg_count ? " (".$msg_count.")": '';

		$messages = app_MailBox::getConversationMessages( $conv_id, $profile_id );
		
		if ( !is_array($messages) )
		{
			/*if ($messages == -1)
				SK_HttpRequest::showFalsePage();
			else if ($messages == -2)
				SK_HttpRequest::showFalsePage();*/
		} else {
		    
		    foreach ( $messages['mails'] as &$msg )
		    {
		      $msg['text'] = preg_replace('/ <a href="(.*?)member\/gift.php(.*?)<\/a>/', '', $msg['text']);
		    }
			SKM_Template::addTplVar('messages', $messages['mails']);
	    	SKM_Template::addTplVar('conv', $messages['info']);
	    	SKM_Template::addTplVar('msg_count', $messages['total']);
		}

	    SKM_Template::addTplVar('profile_id', $profile_id);
	    SKM_Template::addTplVar('unread_url', url::base() . 'mailbox/conv/' . $conv_id . '/unread' );
	    SKM_Template::addTplVar('delete_url', url::base() . 'mailbox/conv/' . $conv_id . '/delete' );
	    
		$mailbox = new View('mailbox_conversation', SKM_Template::getTplVars());
				
		$view->content = $mailbox;
		$view->footer = SKM_Template::footer();
		$view->render(TRUE);
	}
	
	public function compose( $recipient = '' )
	{		
		// form handler
		if ( isset($_POST['profile']) && isset($_POST['subject']) && isset($_POST['message']) )
		{
			$profile = trim($_POST['profile']);
			$recipient_id = app_Profile::getProfileIdByUsername($profile);
			
			$sender_id = SKM_User::profile_id();
			
			$subject = trim($_POST['subject']);
			$message = trim($_POST['message']);
			
			if ( $recipient_id && $sender_id )
			{
				$mb = new Mailbox_Model($sender_id, $recipient_id);
				
				// check if profile-sender has not status 'suspended'
				$status = app_Profile::getFieldValues($sender_id, 'status');
				if ( $status == 'suspended' ) {
					SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.sender_status_suspended'), 'error');
					return;
				}
		
				// check if profile was blocked by the recipient
				if ( app_Bookmark::isProfileBlocked( $recipient_id, $sender_id ) ) {
					SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.profile_blocked'), 'error');
					return;
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
						$msg_sent = $mb->sendMessage( $message, $subject, true );
						switch ( $msg_sent )
						{
							case -1:
								SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.send_nobody'), 'error');
								break;
							case -2:
								SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.send_yourself'), 'notice');
								break;
							default:
								if ( $msg_sent == 1 )
								{
									app_MailBox::sendEmailNotification( $recipient_id, $sender_id, 'message' );
								}
								$send_rdbl_msg_service->trackServiceUse();
								SKM_Template::addMessage(SKM_Language::text('%forms.send_message.msg.readable_msg_sent'));
								break;
						}
					}
					else if ( $send_msg_permission == SK_Service::SERVICE_FULL )
					{
						$msg_sent = $mb->sendMessage( $message, $subject, false );
						switch ( $msg_sent )
						{
							case -1:
								SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.send_nobody'), 'error');
								break;
							case -2:
								SKM_Template::addMessage(SKM_Language::text('%forms.send_message.error_msg.send_yourself'), 'notice');
								break;
		
							default:
								if ( $msg_sent == 1 )
									app_MailBox::sendEmailNotification( $recipient_id, $sender_id, 'message' );
								
								$send_msg_service->trackServiceUse();
								SKM_Template::addMessage(SKM_Language::text('%forms.send_message.msg.msg_sent')); 
								break;
						}
					}
					else {
						SKM_Template::addMessage($send_msg_service->permission_message['message'], 'notice');
					}
				}
			}
			else 
				SKM_Template::addMessage('No profile found', 'notice');
				
			url::redirect('mailbox/sent');
		}
		
		$profile_id = SKM_User::profile_id();
		
		if (strlen($recipient))
		{
			$r_id = app_Profile::getProfileIdByUsername($recipient);
			if ($r_id)
				SKM_Template::addTplVar('send_to', $recipient);		
		}
		
		// check service permission
        $send_msg_service = new SK_Service('send_message', $profile_id);
        $send_msg_permission = $send_msg_service->checkPermissions();
        
        $send_rdbl_msg_service = new SK_Service('send_readable_message', $profile_id);
        $send_rdbl_msg_permission = $send_rdbl_msg_service->checkPermissions();
        
        if ( $send_rdbl_msg_permission != SK_Service::SERVICE_FULL && $send_msg_permission != SK_Service::SERVICE_FULL )
        {
            SKM_Template::addTplVar('service_msg', $send_msg_service->permission_message['alert']);  
        }
		
		$view = new View('default');
		$view->header = SKM_Template::header(); 
		
		// menu
		$m = new SKM_Menu('main', 'mailbox', SKM_Language::text('%nav_doc_item.headers.mailbox'));
		$view->menu = $m->get_view();
			    
	    SKM_Template::addTplVar('profile_id', $profile_id);
	    
		$mailbox = new View('sendmsg', SKM_Template::getTplVars());
				
		$view->content = $mailbox;

		$view->footer = SKM_Template::footer();
		
		$view->render(TRUE);
	}
	
	public function unread( $conv_id )
	{		
		if ( (int)$conv_id )
		{
			$mark_for = SKM_User::profile_id(); 
			$marked = app_MailBox::markConversationsUnread( array($conv_id), $mark_for );
			
			if ($marked)
			{
				SKM_Template::addMessage(SKM_Language::text('%forms.manage_mailbox.msg.marked_unread'));
			}
			else 
				SKM_Template::addMessage(SKM_Language::text('%forms.manage_mailbox.error_msg.not_marked'), 'notice');
		}
		else 
			SKM_Template::addMessage(SKM_Language::text('%forms.manage_mailbox.error_msg.not_marked'), 'notice');
		
		url::redirect('mailbox/inbox');
	} 
	
	public function delete_confirm( $conv_id )
	{
		$view = new View('default');
		$view->header = SKM_Template::header(); 
		
		// menu
		$m = new SKM_Menu('main', 'mailbox', SKM_Language::text('%nav_doc_item.headers.mailbox'));
		$view->menu = $m->get_view();
		
		SKM_Template::addTplVar('confirm_msg', SKM_Language::text('%forms.manage_conversation.actions.delete_confirm'));
		SKM_Template::addTplVar('cancel_url', url::base() . 'mailbox/conv/' . $conv_id );
		SKM_Template::addTplVar('confirm_url', url::base() . 'mailbox/conv/' . $conv_id . '/delconfirmed' );
		
		$mailbox = new View('confirm', SKM_Template::getTplVars());
				
		$view->content = $mailbox;
		$view->footer = SKM_Template::footer();
		$view->render(TRUE);
	}
	
	public function delete( $conv_id )
	{		
		if ( (int)$conv_id )
		{
			$delete_for = SKM_User::profile_id();
			$deleted = app_MailBox::deleteConversations( array($conv_id), $delete_for );
			 		
			if ($deleted)
			{
				SKM_Template::addMessage(SKM_Language::text('%forms.manage_mailbox.msg.deleted'));
			}	
		}
				
		url::redirect('mailbox/inbox');
	} 
	
	private static function replyMessage( $sender_id, $recipient_id, $conv_id, $text )
	{
		// check if profile-sender has not status 'suspended'
		$status = app_Profile::getFieldValues($sender_id, 'status');

		if ( $status == 'suspended' ) {
			return array('code' => -1);
		}

		// check if profile was blocked by the recipient
		if ( app_Bookmark::isProfileBlocked( $recipient_id, $sender_id ) ) {
			return array('code' => -2);
		}

		// check service permission
		$send_msg_service = new SK_Service('send_message', $sender_id);
		$send_msg_permission = $send_msg_service->checkPermissions();
		
		$send_rdbl_msg_service = new SK_Service('send_readable_message', $sender_id);
		$send_rdbl_msg_permission = $send_rdbl_msg_service->checkPermissions();
		
		$reply_rdbl_msg_service = new SK_Service('reply_readable_message', $sender_id);
		$reply_rdbl_msg_permission = $reply_rdbl_msg_service->checkPermissions();
		$membership = app_Membership::profileCurrentMembershipInfo($sender_id);
		Kohana::debug($membership);
		$free_member_cond = $membership['type'] == 'subscription' && $membership['limit'] == 'unlimited' &&  $reply_rdbl_msg_permission == SK_Service::SERVICE_FULL;
		
		$mb = new Mailbox_Model($sender_id, $recipient_id);
		
		// check if the Mailbox feature is available
		if ( $send_msg_permission != SK_Service::SERVICE_NO || $free_member_cond)
		{
			if ( $send_rdbl_msg_permission == SK_Service::SERVICE_FULL )
			{
				$msg_sent = $mb->sendReplyMessage($conv_id, $text, true ); 

				switch ( $msg_sent )
				{
					case -1:
						return array('code' => -3);
						break;
						
					case -2:
						return array('code' => -4);
						break;
						
					default:
						if ($msg_sent == 1)
							app_MailBox::sendEmailNotification( $recipient_id, $sender_id, 'message' );
						
						$send_rdbl_msg_service->trackServiceUse();
						return array('code' => 1); 
				}
			}
			elseif ( $send_msg_permission == SK_Service::SERVICE_FULL || $free_member_cond)
			{
				$msg_sent = $mb->sendReplyMessage($conv_id, $text, false );
				 
				switch ( $msg_sent )
				{
					case -1:
						return array('code' => -3);
						break;
						
					case -2:
						return array('code' => -4);
						break;
						
					default:
						if ($msg_sent == 1)
							app_MailBox::sendEmailNotification( $recipient_id, $sender_id, 'message' );

						$send_msg_service->trackServiceUse();
						return array('code' => 1);
				}
			}
			else {
				return array('code' => -5, 'msg' => $send_msg_service->permission_message['message']);				
			}
		}		
	}
	
}
