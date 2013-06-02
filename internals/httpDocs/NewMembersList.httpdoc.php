<?php

class httpdoc_NewMembersList extends SK_HttpDocument
{
	public function __construct( array $params = null )
	{
		parent::__construct('new_members_list');
		
		$this->cache_lifetime = 5;
	}
}
