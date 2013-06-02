<?php
class component_FeaturedList extends component_ProfileList 
{
	public function __construct()
	{
		parent::__construct('featured_list');
	}
	
	protected function profiles()
	{
		return app_ProfileList::FeaturedList();
	}
	
	public function setup()
	{
		//$this->cache_lifetime = 60*5;
	}
	
}