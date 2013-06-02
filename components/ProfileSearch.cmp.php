<?php

class component_ProfileSearch extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('profile_search');
	}
	
	
	public function prepare( SK_Layout $Layout, &$display_params )
	{
		parent::prepare($Layout, $display_params);
	}
	
	public function render( SK_Layout $Layout )
	{
		return parent::render($Layout);
	}
}
