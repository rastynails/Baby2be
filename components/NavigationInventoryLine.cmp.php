<?php

class component_NavigationInventoryLine extends SK_Component
{
	
	public function __construct( array $params = null )
	{
		parent::__construct('navigation_inventory_line');
		
		//$this->cache_lifetime = 60;
	}
	
	
	public function render( SK_Layout $Layout )
	{
		$inventory = SK_Navigation::menu('inventory_line');
		if ( empty($inventory) )
		{
		    return false;
		}
		$Layout->assign('inv_line_items', $inventory);
		
		return parent::render($Layout);
	}
	
}

