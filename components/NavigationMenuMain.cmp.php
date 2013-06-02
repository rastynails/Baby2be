<?php

class component_NavigationMenuMain extends SK_Component
{
	
	public function __construct( array $params = null )
	{
		parent::__construct('navigation_menu_main');
	}
	
	
	public function render( SK_Layout $Layout )
	{
		$main =  SK_Navigation::menu('main');
		$Layout->assign('main_menu_items', $main);
		return parent::render($Layout);
	}
	
}

