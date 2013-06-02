<?php

class component_GroupMembersList extends component_ProfileList 
{
	private $group_id;
	
	public function __construct()
	{
		$this->group_id = SK_HttpRequest::$GET['group_id'];
		parent::__construct('group_list');
		
	}
	
	protected function setup()
	{
		$this->view_mode = 'details';
		$this->change_view_mode = false;
		$this->list_name = 'group_list';
	}
	
	protected function profiles()
	{
		$items = app_Groups::getMembersList($this->group_id, SK_HttpRequest::$GET['page'] );
		return $items;
	}
		
	protected function prepare_list()
	{
		parent::prepare_list();

		if (count($this->list)) {
			foreach ( array_keys($this->list) as $key )
			{
				$profile = &$this->list[$key];
			}
		} 
	}
	
}