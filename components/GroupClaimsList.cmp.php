<?php

class component_GroupClaimsList extends component_ProfileList 
{
	private $group_id;
	
	public function __construct( array $params )
	{
		$this->group_id = $params['group_id'];
		parent::__construct('group_claims_list');
		
	}
	
	protected function setup()
	{
		$this->view_mode = 'details';
		$this->change_view_mode = false;
	}
	
	protected function profiles()
	{
		$items = app_Groups::getClaimsList($this->group_id, SK_HttpRequest::$GET['page'] );
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