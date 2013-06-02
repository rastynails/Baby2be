<?php

class httpdoc_BlockList extends SK_HttpDocument
{
	public function __construct( array $params = null )
	{
		parent::__construct('block_list');
		
		$this->cache_lifetime = 5;
		
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
