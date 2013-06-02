<?php

class fieldType_custom_select extends fieldType_select
{
	/**
	 * Constructor.
	 *
	 * @param string $field_name
	 */
	public function __construct( $field_name ) {
		parent::__construct($field_name);
	}
	
	/**
	 * Validator.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function validate( $value ) {
		return (string)$value;
	}
	
	
	public function renderItem( array $params )
	{
		$attrs = 'name="'.$this->getName().'" type="radio"';
		
		if ( isset($params['value']) ) {
			$attrs .= ' value="'.SK_Language::htmlspecialchars($params['value']).'"';
		}
		
		if ( isset($params['checked']) && $params['checked'] ) {
			$attrs .= ' checked="checked"';
		}
		
		return "<input $attrs />";
	}
	
}
