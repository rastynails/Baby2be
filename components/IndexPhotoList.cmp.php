<?php

class component_IndexPhotoList extends SK_Component
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
		
		parent::__construct('index_photo_list');
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
		
		$handler = new SK_ComponentFrontendHandler('IndexPhotoList');
		$handler->construct($this->items_count);
		
		$lang = SK_Language::section('components.index_photo_list.tabs');
		
		foreach ($this->lists as $list)
		{
			switch ($list) {
				case 'latest':
					$handler->addList('latest', $lang->text('latest'), SK_Navigation::href("latest_photo"));			
					break;
					
				case 'most_viewed':
					$handler->addList('most_viewed', $lang->text('most_viewed'), SK_Navigation::href("most_viewed_photo"));
					break;
					
				case 'top_rated':
					$handler->addList('top_rated', $lang->text('top_rated'), SK_Navigation::href("top_rated_photo"));
					break;
					
				case 'most_commented':
					$handler->addList('most_commented', $lang->text('most_commented'), SK_Navigation::href("most_commented_photo"));
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
		$list = array();
		$pp_and_fo = SK_Config::section('photo.general')->display_pp_and_fo_on_index;
		
		switch ($list_name) {
			case 'latest':
				$list = app_PhotoList::LatestPhotos($pp_and_fo);
				break;
			
			case 'top_rated':
				$list = app_PhotoList::TopRated($pp_and_fo);
				break;
				
			case 'most_commented':
				$list = app_PhotoList::MostCommented($pp_and_fo);
				break;
				
			case 'most_viewed':
				$list = app_PhotoList::MostViewed($pp_and_fo);
				break;
				
		}
		
		$_list = array();
		$inc = 0;

        $idList = array();
        foreach ($list["items"] as $item)
        {
            $idList[] = $item["photo_id"];
        }
        
        app_ProfilePhoto::getUrlList($idList, app_ProfilePhoto::PHOTOTYPE_THUMB);

		foreach ($list["items"] as $item) {
			switch ($item['publishing_status'])
			{
				case 'password_protected':
					$src = app_ProfilePhoto::password_protected_url();
					break;
				case 'friends_only':
					$src = app_ProfilePhoto::friend_only_url();
					break;
				default:
					$src = app_ProfilePhoto::getUrl($item["photo_id"], app_ProfilePhoto::PHOTOTYPE_THUMB );
					break;
			}
						
			$_list[] = array(
				'img_src'		=> $src,
				'img_url'		=> app_ProfilePhoto::getPermalink($item['photo_id']),
			);
			if ($inc++ >= $count-1) {
				break;
			}
		}
		
		return $_list;
	}
	
	public static function ajax_LoadList($params, SK_ComponentFrontendHandler $handler) 
	{
		$list = self::items($params->list_name, $params->count);
		
		$handler->fillList($params->list_name, $list);
		
		$handler->activateList($params->list_name);
	}
	
}
