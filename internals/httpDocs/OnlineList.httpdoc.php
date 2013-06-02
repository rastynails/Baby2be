<?php

class httpdoc_OnlineList extends SK_HttpDocument
{
	public function __construct( array $params = null )
	{
		parent::__construct('online_list');
		
		$this->cache_lifetime = 5;
		
		
		
	}
	
	public function render( SK_Layout $Layout )
	{
			
		return parent::render($Layout);
	}
}
