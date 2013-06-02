<?php

class component_NewMembersList extends component_ProfileList 
{
	
	public function __construct()
	{
		parent::__construct('new_members_list');
	}
	
	protected function profiles()
	{
		return app_ProfileList::NewMembersList();
	}
	
	protected function is_permitted()
	{
		return true;
	}
	
}