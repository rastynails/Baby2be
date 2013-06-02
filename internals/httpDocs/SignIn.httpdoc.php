<?php

class httpdoc_SignIn extends SK_HttpDocument
{
	public function __construct( array $params = null )
	{
		parent::__construct('sign_in');
	}
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign('sign_up', (SK_Config::section('profile_registration')->type == 'free'));
		
		return parent::render($Layout);
	}
}
