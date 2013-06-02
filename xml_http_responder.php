<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals/Header.inc.php';

if ( !SK_HttpRequest::isXMLHttpRequest() || !isset($_POST['COM_node']) || !isset($_POST['apply_func']) )
{
	exit('http request error');
}

$ajax_response = new SK_AjaxResponse();

$ajax_response->process();
