<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

$pointsPurchase = new component_PointsPurchase();

$Layout->display($pointsPurchase);
