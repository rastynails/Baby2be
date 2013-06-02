<?php

function smarty_function_online_btn( array $params, SKM_Smarty $Layout )
{
	if ( !key_exists('profile_id', $params) ) {
		trigger_error('tpl function {online_btn ...} missing attribute "profile_id"', E_USER_WARNING);
		return;
	}
	if ( !($profile_id = (int)$params["profile_id"]) ) {
		trigger_error('tpl function {online_btn ...} invalid "profile_id" value', E_USER_WARNING);
		return;
	}
	
	$label = isset($params['label']) ? $params['label'] : SKM_Language::text("%interface.online_btn.label");
	$feature_enabled = app_Features::isAvailable(11);
	$viewer_id = SKM_User::profile_id();
	$as_btn = $params['display'] == 'btn'; 
	
	$blocked = app_Bookmark::isProfileBlocked($profile_id, $viewer_id);
	
	if ( !$feature_enabled || !$viewer_id || $profile_id == $viewer_id ) {
		return '<span class="highlight' . ($as_btn ? ' btn' : '') . '">' . $label . '</span>';
	}
	
	$blocked = app_Bookmark::isProfileBlocked($profile_id, $viewer_id);
	
	if ($blocked) {
		return '<span class="highlight' . ($as_btn ? ' btn' : '') . '">' . $label . '</span>';
	}
		
	if (app_ProfilePreferences::get('my_profile', 'hide_im_btn', $profile_id)) {
		return '<span class="highlight' . ($as_btn ? ' btn' : '') . '">' . $label . '</span>';
	}
	
	$username = app_Profile::username($profile_id);
	return '<span class="highlight' . ($as_btn ? ' btn' : '') . '"><a href="'.url::base().'im/'.$username.'">' . $label . '</a></span>'; 
}
