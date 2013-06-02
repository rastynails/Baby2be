<?php

class httpdoc_ProfileViewsList extends SK_HttpDocument
{
	public function __construct( array $params = null )
	{
		parent::__construct('profile_views_list');
	}
	
	public function prepare(SK_Layout $Layout, SK_Frontend $Frontend){
		$period = SK_HttpRequest::$GET['period'];
		$Layout->assign('period', $period);
	}
}
