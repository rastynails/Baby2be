<?php

class component_IndexVideo extends SK_Component
{
	private $lists;
	
	private $active_list; 
	
	public function __construct( array $params = null )
	{
		parent::__construct('index_video');
		
		if (! app_Features::isAvailable(4))
			$this->annul();
		else {	
			$valid_lists = array('latest', 'toprated');
			
			$param_lists = explode('|', $params['lists']);
			
			$this->active_list = in_array($params['active'], $valid_lists) ? $params['active'] : 'toprated';
			
			foreach ($param_lists as $list_name)
			{
				if (in_array($list_name, $valid_lists))
					$this->lists[] = $list_name;
			}
			
			$this->profile_id = isset($params['profile_id']) ? $params['profile_id'] : SK_HttpUser::profile_id();
		}
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('IndexVideo');
		
		$this->frontend_handler->construct();
		
		parent::prepare($Layout, $Frontend);
	}

	
	public function render( SK_Layout $Layout)
	{	
		$viewer = SK_HttpUser::profile_id();
		$service = new SK_Service('view_video', $viewer);
		
		if ($service->checkPermissions() == SK_Service::SERVICE_FULL) {
			
			if (isset($this->lists))
			{
				$menu_items = array();
				
				$video = array();
				$video_list = array();
				
				foreach ($this->lists as $list)
				{
					$menu_items[] = array(
						'href'   => $list,
						'label'  => SK_Language::section('components.index_video')->text('submenu_item_'.$list),
						'active' => ($this->active_list == $list) ? true : false,
					    'class' => $list
					);
					
					$video = null;
					$video = app_VideoList::getIndexVideo($list);
					if (isset($video['for_player']['video_id'])) {
						
						$video_list[$list]['player'] = $video['for_player'];
						$video_list[$list]['thumbs'] = $video['for_thumbs'];
						$video_list[$list]['view_more'] = SK_Navigation::href($list.'_video');
					}
					else 
						$video_list[$list]['no_video'] = SK_Language::section('components.index_video')->text('no_video');

					$video_list[$list]['is_active'] = ($this->active_list == $list) ? true : false;
				}

				$Layout->assign('video_menu_items', $menu_items);
				$Layout->assign('player_width', SK_Config::section('video')->get('small_video_width'));
				$Layout->assign('player_height', SK_Config::section('video')->get('small_video_height'));
				$Layout->assign('thumb_width', SK_Config::section('video')->get('video_thumb_width'));

				$Layout->assign('video', $video_list);
			}
		}
		else {
			$Layout->assign('service_msg', $service->permission_message['message']);					
		}
		
		return parent::render($Layout);	
	}
}
