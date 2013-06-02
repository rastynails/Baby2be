<?php

class httpdoc_FeaturedList extends SK_HttpDocument
{
	public function __construct( array $params = null )
	{
		parent::__construct('featured_list');
		
		$this->cache_lifetime = 5;
		
		
		
	}
	
	public function render( SK_Layout $Layout )
	{
			
		return parent::render($Layout);
	}
}
