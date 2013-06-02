<?php

class component_ProfileViewsList extends component_ProfileList 
{
	public function __construct() {
		parent::__construct('who_viewed_me');
	}
	
	
	protected function profiles()
	{
		$period = isset(SK_HttpRequest::$GET['period']) ? SK_HttpRequest::$GET['period'] : 'month';
		
		switch($period)
		{			
			case 'week':
			
				$dayTime = time() - mktime(0, 0, 0);
                $start_stamp = time() - $dayTime - date('w') * 60 * 60 * 24;
				
				break;
			case 'day':
				$start_stamp = mktime(0,0,0,date('m'), date('d'), date('Y'));
				break;
				
			case 'month':
			default:
				$start_stamp = mktime(0,0,0, date('m'), 0, date('Y'));
				break;			
		}
		
		
		$list = app_ProfileViewHistory::getList(SK_HttpUser::profile_id(), $start_stamp, time());
		
		return $list;
	}
	
	protected function is_permitted()
	{
		if (!app_Features::isAvailable(29)) {
			$this->no_permission_msg = SK_Language::section('membership')->text("feature_inactive");
			return false;	
		}
		
		$service = new SK_Service("profile_view_history");
		if ($service->checkPermissions() != SK_Service::SERVICE_FULL ) {
			$this->no_permission_msg = $service->permission_message["message"];
			return false;
		}
		
		$service->trackServiceUse();
		
		return true;
	}
	
}