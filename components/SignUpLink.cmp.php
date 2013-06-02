<?php

class component_SignUpLink extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('sign_up_link');
		
		if (SK_Config::section('profile_registration')->type == 'invite') {
			$this->annul();
		}
	}
}
