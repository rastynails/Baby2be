<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

$httpdoc = new component_MusicEdit( array('music_id' => SK_HttpRequest::$GET['music_id']) );

$Layout->display($httpdoc);
