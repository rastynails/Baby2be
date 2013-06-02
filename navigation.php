<?php
define("IS_NAVIGATION", true);
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

if ( @parse_url($_SERVER['REQUEST_URI']) === false )
{
    SK_HttpRequest::redirect(SK_Navigation::getDocument('not_found')->url);
}

$requestUri = sk_request_uri();

if (!SK_HttpRequest::prepare($requestUri)) {
	$real_url = SK_Navigation::getRealUrl($requestUri);

	$result = SK_HttpRequest::prepare($real_url);

	if (!isset($result)) {

		if (SK_HttpRequest::isXMLHttpRequest()) {
			exit("Request Error: responder not exists!");
		}

		$request_info = parse_url($requestUri);
		list(, $ext) = explode('.', $request_info['path']);

		if ( !empty($ext) && in_array($ext, array('js', 'css'))) {
			header("HTTP/1.1 404 Not Found", true, 404);
			exit();
		}

		SK_HttpRequest::redirect(SK_Navigation::getDocument('not_found')->url);
	}
}

$file_path = SK_HttpRequest::getRequarePath();
$file_path = isset($file_path) ? $file_path : SK_HttpRequest::getDocument()->path;

if(isset($file_path)){
	require_once($file_path);
}

