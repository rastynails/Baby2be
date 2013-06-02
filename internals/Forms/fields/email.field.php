<?php

class field_email extends fieldType_text
{
	public function __construct($name = 'email') {
		parent::__construct($name);
	}
	
	public function setup(SK_Form $Form) {
		
		$this->setRegExPatterns("/^([\w\-\.\+\%]+)@((?:[A-Za-z0-9\-]+\.)+[A-Za-z]{2,})$/");
		parent::setup($Form);
	}
}
