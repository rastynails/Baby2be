<?php

class field_i_am extends fieldType_select
{
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'i_am' ) {
		parent::__construct($name);
	}
	
	
	public function setup( SK_Form $from )
	{
		// TODO: getting values from database
		$this->values = array(1, 2, 4, 8);
		
		$this->setValue(current($this->values));
		
		parent::setup($from);
	}
	
}
