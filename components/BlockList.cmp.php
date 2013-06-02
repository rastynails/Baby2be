<?php

class component_BlockList extends component_ProfileList 
{
	public function __construct()
	{
		parent::__construct('block_list');
	}
	
	protected function profiles()
	{
		return app_Bookmark::BlockList(SK_HttpUser::profile_id());
	}
	
	protected function is_permitted()
	{
		if (!app_Features::isAvailable(19)) {
			$this->no_permission_msg = SK_Language::section('membership')->text("feature_inactive");
			return false;	
		}
		return true;
	}
	
}