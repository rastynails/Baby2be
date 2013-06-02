<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$chuppo_im_enabled = SK_Config::section('chuppo')->get('enable_chuppo_im');

if ( !$chuppo_im_enabled ) {
	if ( !isset($_GET['opponent_id']) ) {
		trigger_error('missing parameter `opponent_id`', E_USER_ERROR);
	}
	
	if ( !(
		$opponent_id = (int)$_GET['opponent_id']
	) ) {
		trigger_error('invalid parameter `opponent_id`', E_USER_ERROR);
	}	
	
	$httpdoc = SK_Component('IM', array('opponent_id' => $opponent_id, 'is_esd_session'=>(int)$_GET['is_esd_session']));
}
else {
    $params = array();
    if ( isset($_GET['opponent_id']) && empty($_GET['oppUserKey']) && SK_HttpUser::is_authenticated() )
    {
        $params['userKey'] = app_Profile::getUserKeyByProfileId( SK_HttpUser::profile_id() );
        $params['oppUserKey'] = app_Profile::getUserKeyByProfileId( $_GET['opponent_id'] );
        
    }
    $params['is_esd_session'] = (int)$_GET['is_esd_session'];
	$httpdoc = new component_ChuppoIm($params);
}

SK_Layout::getInstance()->display($httpdoc);
