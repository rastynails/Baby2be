<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

$httpdoc = new component_VideoEdit( array('video_id' => SK_HttpRequest::$GET['video_id']) );

$Layout->display($httpdoc);
