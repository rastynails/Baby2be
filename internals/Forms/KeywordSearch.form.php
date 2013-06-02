<?php

class form_KeywordSearch extends form_Search 
{
	
	public function __construct()
	{
		$this->setSearchType('keyword');
		
		parent::__construct('keyword_search');
		
	}
}

