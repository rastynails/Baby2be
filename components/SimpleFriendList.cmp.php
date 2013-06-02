<?php

class component_SimpleFriendList extends SK_Component
{
	private $profile_id;
	
	private $items_count = 15;
	
	public function __construct( array $params = null )
	{
		if (isset($params["count"]) && intval($params["count"])) 
		{
			$this->items_count = intval($params["count"]);
		}
		else
		{
		    $this->items_count = SK_Config::section('site.additional.profile_list')->simple_friend_list_count;
		}
		
		$this->items_count = $this->items_count 
			? $this->items_count 
			: SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
		
		$this->profile_id = $params["profile_id"];
		parent::__construct('simple_friend_list');
	}
	
	public function render( SK_Layout $Layout )
	{
		if (!$this->profile_id) {
			return false;
		}
		
		$list = app_FriendNetwork::FriendList($this->profile_id, false);
        $profileIdList = array();
        
        foreach ( $list['profiles'] as $item )
        {
            $profileIdList[] = $item['profile_id'];
        }

        app_Profile::getUsernamesForUsers($profileIdList);
        app_ProfilePhoto::getThumbUrlList($profileIdList);
        app_Profile::getOnlineStatusForUsers($profileIdList);

		$out = array();
		$inc = 0;
		foreach ($list["profiles"] as $item) {
			$out[] = $item["profile_id"];
			if (++$inc >= $this->items_count) {
				break;
			}
		}
		
		$Layout->assign('profile_id', $this->profile_id);
		
		$Layout->assign('view_all', app_FriendNetwork::countFriends($this->profile_id) > $this->items_count);
		
		$Layout->assign('list', $out);

		$Layout->assign("username", app_Profile::username($this->profile_id));
			
		return parent::render($Layout);
	}
	
}
