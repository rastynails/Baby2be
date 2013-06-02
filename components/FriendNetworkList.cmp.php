<?php

class component_FriendNetworkList extends component_ProfileList 
{
	
	private $mode;
	
	private $profile_id;
	
	public function __construct($profile_id = null)
	{
		$this->profile_id = ($profile_id = intval($profile_id)) ? $profile_id : SK_HttpUser::profile_id();
		
		$this->mode = isset(SK_HttpRequest::$GET['tab']) ? SK_HttpRequest::$GET['tab'] : 'friends'; 
		 		
		switch ($this->mode){
			case 'sent_requests':
				$name = 'sent_requests';
				break;
			case 'got_requests':
				$name = 'got_requests';	
				break;
			default:
				$name = 'friends';
		}

		if ($this->profile_id != SK_HttpUser::profile_id()) {
			SK_Navigation::removeBreadCrumbItem();
			SK_Navigation::removeBreadCrumbItem();

			$username = app_Profile::username($this->profile_id);
	
			SK_Navigation::addBreadCrumbItem(
				$username,	SK_Navigation::href('profile', array('profile_id'=>$this->profile_id))
			);

			SK_Navigation::addBreadCrumbItem(SK_Language::text('navigation.breadcrumb.profile_friend_list'));

			$meta = new SK_HttpDocumentMeta();
			$meta->content_header = SK_Language::text('components.friend_list.list_title', array('username'=> $username));
			$meta->title = SK_Language::text('components.friend_list.list_title', array('username'=> $username));
			$this->document_meta = $meta;
		}

		parent::__construct($name);
	}
	
	protected function profiles()
	{
		if ($this->mode == 'friends') {
			$items = app_FriendNetwork::FriendNetworkList($this->profile_id, $this->mode);
		} else {
			$items = app_FriendNetwork::FriendNetworkList($this->profile_id, $this->mode, false);
		}
		return $items;
				
	}
	
	public function setup()
	{
		switch ($this->mode)
		{
			case 'sent_requests':
			case 'got_requests':
				$this->view_mode = 'details';
				$this->change_view_mode = false;
				$this->enable_pagging = false;
				break;
		}
	}
	
	protected function tabs()
	{
		if ($this->profile_id != SK_HttpUser::profile_id()) {
			return array();
		}
		
		$modes = array('friends'=>'friends_tab_label', 'sent_requests'=>'sent_requests_list_tab_label', 'got_requests'=>'got_requests_list_tab_label');
		
		$tabs = array();
		
		foreach ($modes as $mode => $label_key )
		{
			$label = '';
			switch ($mode)
			{
				case 'friends':
					$label = SK_Language::text('%profile.list.friend_list.friends_tab_label');
					break;
					
				case 'sent_request':
					$label = SK_Language::text('%profile.list.friend_list.sent_requests_list_tab_label');
					break;
			}
			
			$tabs[] = array(
							'href'	=> SK_Navigation::href('profile_friend_list',array('tab'=>$mode)),
							'label'	=> SK_Language::section('profile.list.friend_list')->text($label_key)
										.' ('.app_FriendNetwork::countFriendNetworkList(SK_HttpUser::profile_id(), $mode).')',
							'active'=> $this->mode == $mode
							);
		}
		return $tabs;
	}
	
	
	protected function is_permitted()
	{
		if (!app_Features::isAvailable(14)) {
			$this->no_permission_msg = SK_Language::section('membership')->text("feature_inactive");
			return false;	
		}
		return true;
	}
	
}