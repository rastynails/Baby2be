<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

if ( !isset($_GET['event_id']) ) {
    SK_HttpRequest::showFalsePage();
	//trigger_error('missing parameter `event_id`', E_USER_ERROR);
}

if ( !(
	$event_id = (int)$_GET['event_id']
) ) {
    SK_HttpRequest::showFalsePage();
	//trigger_error('invalid parameter `event_id`', E_USER_ERROR);
}

if ( !isset($_GET['opponent_id']) ) {
    SK_HttpRequest::showFalsePage();
	//trigger_error('missing parameter `opponent_id`', E_USER_ERROR);
}

if ( !(	$opponent_id = (int)$_GET['opponent_id'] ) ) {
    SK_HttpRequest::showFalsePage();
	//trigger_error('invalid parameter `opponent_id`', E_USER_ERROR);
}

$httpdoc = new httpdoc_ProfileNote( array('event_id'=>$event_id, 'opponent_id' => $opponent_id) );

SK_Layout::getInstance()->display($httpdoc);
