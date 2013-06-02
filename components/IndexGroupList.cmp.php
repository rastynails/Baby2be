<?php

class component_IndexGroupList extends SK_Component 
{
	private $count;
	
	private $lists;
	
	private $active_list;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		$this->count = isset($params['count']) && intval($params['count']) ? intval($params['count']) : 8;

		if (! app_Features::isAvailable(38))
			$this->annul();
		else {
			$valid_lists = array('latest', 'most_popular');
			
			$param_lists = explode('|', $params['lists']);
			
			$this->active_list = in_array($params['active'], $valid_lists) ? $params['active'] : 'most_popular';
			
			foreach ($param_lists as $list_name)
			{
				if (in_array($list_name, $valid_lists))
					$this->lists[] = $list_name;
			}			
		}
		
		parent::__construct('index_group_list');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('IndexGroupList');
		
		$this->frontend_handler->construct();
		
		parent::prepare($Layout, $Frontend);
	}
	
	
	public function render( SK_Layout $Layout )
	{
		$count = app_Groups::getGroupsCount();
		
		if ($count)
		{
			foreach ($this->lists as $list)
			{
				$menu_items[] = array(
					'href'   => $list,
					'label'  => SK_Language::section('components.index_group_list')->text('submenu_item_'.$list),
					'active' => ($this->active_list == $list) ? true : false,
				    'class' => $list
				);
				$groups[$list]['groups'] = app_Groups::getIndexGroupList($list, $this->count);
				$groups[$list]['is_active'] = ($this->active_list == $list) ? true : false;
			}
	
			$Layout->assign('groups_menu_items', $menu_items);
			
			$Layout->assign('groups', $groups );
		}
		else 
		{
			$Layout->assign('no_groups', true);	
		}
		
		return parent::render( $Layout );
	}
}