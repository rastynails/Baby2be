<?php

function smarty_function_profile_username( array $params, SK_Layout $Layout )
{
	if ( !key_exists('profile_id', $params) ) {
		trigger_error('{profile_username...} missing attribute "profile_id"', E_USER_WARNING);
		return;
	}
	
	if ( !(
		$profile_id = (int)$params['profile_id'])
	) {
		trigger_error('{profile_username...} incorrect value for attribute "profile_id"', E_USER_WARNING);
		return;
	}
	
	$deleted = app_Profile::isProfileDeleted( $profile_id );
	$username = ( $deleted ) ? SK_Language::text("label.deleted_member") : app_Profile::username( $profile_id );
	
	if (@$params['href']===false || $deleted) {
		$out = '<span>'.$username.'</span>';
	}
	else {
		$href = ( @$params['href'] ) ? @$params['href'] : SK_Navigation::href('profile', array('profile_id' => $profile_id));
		
		$out = '<a href="'.$href.'">'.$username.'</a>';
	}
	
	return $out;
}
