<?php

class component_SignUp extends component_FieldForm
{
	
	public function __construct( array $params = null )
	{
		if (SK_Config::section('profile_registration')->type == 'invite') {
			$this->annul();
		}
		
		parent::__construct('sign_up');
		
	    if (!empty($params['type']))
        {
            $this->tpl_file = trim($params['type']) . '.tpl'; 
        }
		
		$this->enable_sections = false;
	}
	
	public function fields(){
		return array('username', 'password','email','i_agree_with_tos');
	}
		
}

