<?php

class component_NavigationSubMenu extends SK_Component
{
	private $level;
	
	public function __construct( array $params = null )
	{
		parent::__construct('navigation_sub_menu');
		
		$this->level = ( isset($params['level']) && intval($params['level']) ) ? $params['level'] : 1 ;
		
		//$this->cache_lifetime=360;
	}
	
	public function render( SK_Layout $Layout )
	{
		$sub = SK_Navigation::menu('main',SK_Navigation::MENU_ACTIVE_BRANCH, 1 );
				
		$Layout->assign('sub_menu_items', $sub);
		
		return parent::render($Layout);
	}
	
}
