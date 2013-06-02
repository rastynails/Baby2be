<?php

class component_UsernameSearch extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('username_search');
	}
	
	public function render( SK_Layout $Layout)
	{
		$fields = app_ProfileSearch::SearchTypeFields('username');

		$field['name'] = array_shift($fields);
		$field['id'] = SK_ProfileFields::get($field['name'])->profile_field_id;
		
			
		$Layout->assign('field',$field);
			
		return parent::render($Layout);
	}
	
}
