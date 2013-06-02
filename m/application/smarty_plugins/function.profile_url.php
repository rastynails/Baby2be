<?php

function smarty_function_profile_url( array $params, SKM_Smarty $Layout )
{
	/*if ( !key_exists('profile_id', $params) ) {
		trigger_error('tpl function {profile_url ...} missing attribute "profile_id"', E_USER_WARNING);
		return;
	}
	
	if ( !($profile_id = (int)$params["profile_id"]) ) {
		trigger_error('tpl function {profile_url ...} invalid "profile_id" value', E_USER_WARNING);
		return;
	}*/
	
	$username = app_Profile::username($params["profile_id"]);
		
	if (strlen($username))
	{
		$output = url::base().'profile/'.$username;
		return $output;
	}
	else 
		return ''; 
}
