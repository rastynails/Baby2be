<?php

function smarty_function_membership_icon(  array $params, SK_Layout $Layout )
{
	if(!($profile_id = intval($params['profile_id']))){
		trigger_error( __CLASS__.'::'.__FUNCTION__.'() undefined profile id', E_USER_WARNING);
		return '';
	}

    if ( isset($Layout->_tpl_vars['thumbInfo'][$profile_id]['membership_type_id']) )
    {
        $membership_id = $Layout->_tpl_vars['thumbInfo'][$profile_id]['membership_type_id'];
    }
    else
    {
        $membership_id = app_Profile::getFieldValues($profile_id,'membership_type_id');
    }
	
	$src = URL_USERFILES.'membership_type_icon_'.$membership_id.'.png';
        
        if(!file_exists( $src )) return '';
	
	$label = SK_Language::section('membership.types')->text($membership_id);
		
	$out = "<img src='$src' title='$label' alt='$label' height='29px'>";
	
	return $out;
}

