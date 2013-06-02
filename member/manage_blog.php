<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

if( 
	(!app_Features::isAvailable(23) && !app_Features::isAvailable(37)) || 
	( !app_Features::isAvailable(23) && !app_Profile::isProfileModerator(SK_HttpUser::profile_id()) ) 
   )
	SK_HttpRequest::showFalsePage();

$Layout = SK_Layout::getInstance();

$httpdoc = new httpdoc_BlogWorkshop();

$Layout->display($httpdoc);