<?php

class field_looking_for extends fieldType_set
{
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'looking_for' ) {
		parent::__construct($name);
	}
	
	
	public function setup( SK_Form $from )
	{
		// TODO: getting values from database
		$this->values = array(1, 2, 4, 8);
		
		parent::setup($from);
	}
	
}
