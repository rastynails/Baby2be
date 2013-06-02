<?php

class component_NeighbourhoodList extends component_ProfileList
{
	public function __construct()
	{
		parent::__construct('neighbourhood_list');
	}
	
	protected function profiles()
	{
		$mode = @SK_HttpRequest::$GET['mode'];
		$distance = @SK_HttpRequest::$GET['distance'];
		return app_NeighbourhoodList::getList($mode , SK_HttpUser::profile_id(),false, $distance);
	}
	
	
	
	protected function is_permitted()
	{
		return true;
	}
	
}
