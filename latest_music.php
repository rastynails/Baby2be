<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

if (! app_Features::isAvailable(40))
	SK_HttpRequest::showFalsePage();

$Layout = SK_Layout::getInstance();

$httpdoc = new component_MusicList(array('list_type'=>'latest'));

$Layout->display($httpdoc);
