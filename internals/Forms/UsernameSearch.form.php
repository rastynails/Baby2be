<?php

class form_UsernameSearch extends form_Search
{
	
	public function __construct() {
		parent::__construct('username_search');
	}
	
	public function setup() {
		$this->setSearchType('username');
		parent::setup();
	}
}

