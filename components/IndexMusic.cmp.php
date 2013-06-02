<?php

class component_IndexMusic extends SK_Component
{
	private $lists;
	
	private $active_list; 
	
	public function __construct( array $params = null )
	{
		parent::__construct('index_music');
		
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
		$this->frontend_handler = new SK_ComponentFrontendHandler('IndexMusic');
		
		$this->frontend_handler->construct();
		
		parent::prepare($Layout, $Frontend);
	}

	
	public function render( SK_Layout $Layout)
	{	
		$viewer = SK_HttpUser::profile_id();
		$service = new SK_Service('view_music', $viewer);

		if ($service->checkPermissions() == SK_Service::SERVICE_FULL) {
			
			if (isset($this->lists))
			{
				$menu_items = array();
				
				$music = array();
				$music_list = array();
				
				foreach ($this->lists as $list)
				{
					$menu_items[] = array(
						'href'   => $list,
						'label'  => SK_Language::text('components.index_music.submenu_item_'.$list),
						'active' => ($this->active_list == $list) ? true : false,
					    'class' => $list
					);
					$music = null;
					$music = app_MusicList::getIndexMusic($list);
                                      	if (isset($music['for_player']['music_id'])) {
						
						$music_list[$list]['player'] = $music['for_player'];
						$music_list[$list]['thumbs'] = $music['for_thumbs'];
						$music_list[$list]['view_more'] = SK_Navigation::href($list.'_music');
					}
					else 
						$music_list[$list]['no_music'] = SK_Language::text('components.index_music.no_music');

					$music_list[$list]['is_active'] = ($this->active_list == $list) ? true : false;
				}

				$Layout->assign('music_menu_items', $menu_items);

				$Layout->assign('music', $music_list);
			}
		}
		else {
			$Layout->assign('service_msg', $service->permission_message['message']);
		}
		
		return parent::render($Layout);	
	}
}
