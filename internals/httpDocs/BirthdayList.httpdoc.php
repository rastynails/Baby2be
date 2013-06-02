<?php

class httpdoc_BirthdayList extends SK_HttpDocument
{
	public function __construct( array $params = null )
	{
		parent::__construct('birthday_list');
		
		$this->cache_lifetime = 5;
	}
}
