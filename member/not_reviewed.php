<?php
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

$httpdoc = new httpdoc_ProfileNotReviewed;

$Layout->display($httpdoc);
