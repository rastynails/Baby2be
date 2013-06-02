<?php

class fieldType_custom_set extends fieldType_set
{
	/**
	 * Constructor.
	 *
	 * @param string $field_name
	 */
	public function __construct( $field_name ) {
		parent::__construct($field_name);
	}
	
	
	public function validate( $value ) {
		return (array)$value;
	}
	
	
	public function renderItem( array $params )
	{
		$attrs = 'name="'.$this->getName().'[]" type="checkbox"';
		
		if ( isset($params['value']) ) {
			$attrs .= ' value="'.SK_Language::htmlspecialchars($params['value']).'"';
		}
		
		if ( isset($params['checked']) && $params['checked'] ) {
			$attrs .= ' checked="checked"';
		}
		
		return "<input $attrs />";
	}
	
}
