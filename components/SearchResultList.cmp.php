<?php

class component_SearchResultList extends component_ProfileList 
{
	public function __construct()
	{
		parent::__construct('search_result');
	}
	
	protected function profiles()
	{
            $result = app_ProfileSearch::Profiles();
            $this->search_type = $result['search_type']; 
            return $result;
	}
	
	protected function is_permitted()
	{
		$service = new SK_Service('search');
		if ($service->checkPermissions() != SK_Service::SERVICE_FULL ) {
			$this->no_permission_msg = $service->permission_message['message'];
			return false;
		}
		
		return true;
	}
	
}