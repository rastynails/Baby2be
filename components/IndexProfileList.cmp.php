<?php

class component_IndexProfileList extends SK_Component
{
	private $items_count = 6;
	
	private $active_list;
	
	private $lists = array();
	
	public function __construct( array $params = null )
	{
		if (isset($params["count"]) && intval($params["count"])) {
			$this->items_count = intval($params["count"]);
		}
		
		$this->active_list = isset($params["active"]) ? trim($params["active"]) : null;
		
		if (isset($params["lists"])) {
			$lists = explode("|", $params["lists"]);
			$this->lists = count($lists) ? $lists : array();
		}
		
		
		parent::__construct('index_profile_list');
	}
	
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		if (!count($this->lists) && !isset($this->active_list)) {
			return false;
		}
		
		if (!isset($this->active_list) && count($this->lists)) {
			$this->active_list = $this->lists[0];
		}

		if (!count($this->lists) && isset($this->active_list)) {
			$this->lists[] = $this->active_list;
		}	
		
		$handler = new SK_ComponentFrontendHandler('IndexProfileList');
		$handler->construct($this->items_count);
		
		$lang = SK_Language::section('components.index_profile_list.tabs');
		
		foreach ($this->lists as $list)
		{
			switch ($list) {
				case 'featured':
					$handler->addList('featured', $lang->text('featured'), SK_Navigation::href("featured_list"));			
					break;
					
				case 'online':
					$handler->addList('online', $lang->text('online'), SK_Navigation::href("online_list"));
					break;
					
				case 'new':
					$handler->addList('new', $lang->text('new'), SK_Navigation::href("profile_new_members_list"));
					break;
			}
			
			if ($this->active_list == $list) {
				$items = self::items($list, $this->items_count);
				$handler->fillList($list, $items);
				$handler->activateList($list);		
			}
			
		}
		
		$handler->complete();
		
		$this->frontend_handler = $handler;
		
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign('menu_items', array(array(
			'href'	=> 'javascript://',
			'label'	=> '',
			'active'=> false
		)));
		return parent::render($Layout);
	}
	
	public static function items($list_name, $count) {
		app_ProfileList::setLimit($count);
		
		$list["profiles"] = array();
		switch ($list_name) {
			case 'online':
				$list = app_ProfileList::OnlineList();
				break;
			
			case 'featured':
				$list = app_ProfileList::FeaturedList();
				break;
				
			case 'new':
				$list = app_ProfileList::NewMembersList();
				break;
				
			case 'most_viewed':
				
				break;
				
		}
		
		$_list = array();
	
		$items_list = ( isset($list["profiles"]) && is_array($list["profiles"]) ) ? $list["profiles"] : array();

        $dataArray = array();
        $idListToRequire = array();

        foreach ( $items_list as $item )
        {
            $idListToRequire[] = $item["profile_id"];
        }

        $dataArray['thumb'] = app_ProfilePhoto::getThumbUrlList($idListToRequire);
        $dataArray['username'] = app_Profile::getUsernamesForUsers($idListToRequire);

		foreach ($items_list as $item) {
			$profile_id = $item["profile_id"];
			
			$_list[] = array(
				'img_src'		=> $dataArray['thumb'][$profile_id],
				'img_url'		=> SK_Navigation::href("profile", array('profile_id'=>$profile_id)),
				'label'			=> $dataArray['username'][$profile_id],
			    'sex'           => $item['sex']
			);
		}
		
		app_ProfileList::unsetLimit();
		return $_list;
	}
	
	public static function ajax_LoadList($params, SK_ComponentFrontendHandler $handler) 
	{
		$list = self::items($params->list_name, $params->count);
		
		$handler->fillList($params->list_name, $list);
		
		$handler->activateList($params->list_name);
	}
	
}
