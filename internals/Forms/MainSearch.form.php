<?php

class form_MainSearch extends form_Search  
{
	public function __construct()
	{
		$this->setSearchType('main');
		
		parent::__construct('main_search');
	}
			
}

