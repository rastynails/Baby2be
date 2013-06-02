<?php

class component_QuickSearch extends SK_Component
{
	/**
	 * Component QuickSearch constructor.
	 *
	 * @return component_QuickSearch
	 */
	public function __construct( array $params = null )
	{
	    if (!empty($params['type']))
	    {
	        $this->tpl_file = trim($params['type']) . '.tpl';
	    }

		parent::__construct('quick_search');
	}

	public function render( SK_Layout $Layout )
	{
	    if ( !app_Features::isAvailable(50) )
	    {
	        return false;
	    }

		$_fields = app_ProfileSearch::SearchTypeFields('quick_search');
		$fields = array();
		foreach ($_fields as $field){
			if ( in_array( $field, array( 'country_id', 'state_id', 'city_id', 'zip', 'custom_location' ) ) )
				continue;

			$fields[$field] = SK_ProfileFields::get($field)->profile_field_id;
		}

                if (in_array('location', $_fields) )
                {
                    $fields['location'] = SK_ProfileFields::get('location')->profile_field_id;
                }

		$Layout->assign('fields', $fields);

		return parent::render($Layout);
	}


	public static function clearCompile($tpl_file = null, $compile_id = null)
	{
		$cmp = new self;
		if ( $tpl_file !== null )
		{
                    $cmp->tpl_file = $tpl_file;
		}

		return $cmp->clear_compiled_tpl();
	}
}
