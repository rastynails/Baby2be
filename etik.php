<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

$httpdoc = new httpdoc_CustomPages;

$Layout->display($httpdoc);