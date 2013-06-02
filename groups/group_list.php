<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

$httpdoc = new component_GroupList( array('page' => SK_HttpRequest::$GET['page']));

$Layout->display($httpdoc);