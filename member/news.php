<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

// force cond redirect
if(!app_Features::isAvailable(37))
	SK_HttpRequest::showFalsePage();

$Layout = SK_Layout::getInstance();

$httpdoc = new httpdoc_News;

$Layout->display($httpdoc);