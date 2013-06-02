<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

if(!app_Features::isAvailable(32))
	SK_HttpRequest::showFalsePage();

$Layout = SK_Layout::getInstance();

$httpdoc = new component_SMSServices(array('service' => SK_HttpRequest::$GET['service']));

$Layout->display($httpdoc);
