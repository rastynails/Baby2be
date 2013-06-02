<?php

class component_ProfileNotices extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('profile_notices');
	}
	
	
	public function render( SK_Layout $Layout )
	{
		$profile_id = SK_HttpUser::profile_id();
		
		$friend_requests = app_FriendNetwork::countGotRequests($profile_id);
		$new_messages = app_MailBox::newMessages($profile_id);
		
		if (app_Features::isAvailable(38))
		{
			$group_invitations = app_Groups::getInvitations($profile_id);
			if ($group_invitations['count'] > 0)
			{
				$Layout->assign('invites_count', $group_invitations['count']);
				$Layout->assign('invites', $group_invitations['list']);
			}
		}
		
		if ( !$friend_requests && !$new_messages && !$group_invitations['count'] ) {
			return false;
		}
		
		$Layout->assign('friend_requests', $friend_requests);
			
		$Layout->assign('new_messages', $new_messages);
		
		return parent::render($Layout);
	}
	
}
