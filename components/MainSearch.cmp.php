<?php

class component_MainSearch extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('main_search');
	}

	public function render( SK_Layout $Layout )
	{
		
		$fields = app_ProfileField::getPageFields( 'search', 1);
		
		$fields = array_diff($fields, array( 'country_id', 'state_id', 'city_id', 'zip', 'custom_location','mcheck_country' ));
		
		$_fields = array();
		$system_fields = array();
		
		foreach ($fields as $field){
			
			try {
				$pr_field = SK_ProfileFields::get($field);		
			} catch( SK_ProfileFieldException $e) {
				continue;
			}
			
			$sect_id = $pr_field->profile_field_section_id;
			if (!in_array($field,array('search_online_only', 'search_with_photo_only'))){
				$_fields[$sect_id][$pr_field->name] = $pr_field->profile_field_id;	
			}
			else {
				$system_fields[$pr_field->name] = $pr_field->profile_field_id; 
			}
			
		}
			
		$Layout->assign('fields',$_fields);
		$Layout->assign('sys_fields',$system_fields);
		
		return parent::render($Layout);
	}
	
}
