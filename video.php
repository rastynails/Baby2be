<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

if (! app_Features::isAvailable(4))
	SK_HttpRequest::showFalsePage();
	
$Layout = SK_Layout::getInstance();

$httpdoc = new component_VideoList(array('list_type'=>'tags'));

$Layout->display($httpdoc);
