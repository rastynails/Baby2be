<?php
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();
$profile_id = SK_HttpRequest::$GET['profile_id'];
$httpdoc = new component_PhotoAlbums(array('profile_id' => $profile_id));

$Layout->display($httpdoc);
