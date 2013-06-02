<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';
SK_Profiler::getInstance('app')->mark('header end');
$Layout = SK_Layout::getInstance();
SK_Profiler::getInstance('app')->mark('layout init');
$httpdoc = new component_MemberHome;
SK_Profiler::getInstance('app')->mark('httpdoc init');
$Layout->display($httpdoc);
