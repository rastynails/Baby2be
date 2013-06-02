<?php
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

if(!app_Features::isAvailable(54))
	SK_HttpRequest::showFalsePage();

$Layout = SK_Layout::getInstance();

$httpdoc = new component_Game();

$Layout->display($httpdoc);
