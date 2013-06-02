<?php

class component_KeywordSearch extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('keyword_search');
	}
	
	public function render( SK_Layout $Layout)
	{
					
		$field_name = SK_ProfileFields::get('keyword_search')->name;
		$field_id = SK_ProfileFields::get('keyword_search')->profile_field_id;
		
		$Layout->assign('field',$field_name);
		$Layout->assign('field_id',$field_id);
		
			
		return parent::render($Layout);
	}
	
}
