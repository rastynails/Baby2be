<?php
class component_MatchList extends component_ProfileList 
{
	public function __construct()
	{
		parent::__construct('match_list');
	}
	
	protected function profiles()
	{
		return app_ProfileList::ProfileMatcheList( SK_HttpUser::profile_id());
	}
	
	protected function is_permitted()
	{
		
		return true;
	}
	
}