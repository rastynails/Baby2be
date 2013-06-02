<?php
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

$profileId = SK_HttpUser::profile_id();

if ( SK_HttpRequest::$GET['command'] == 'send-request' )
{
    app_PhotoAuthenticate::sendRequest($profileId);
    
    SK_HttpRequest::redirect( sk_make_url(null, array('command' => null)) );
}

$request = app_PhotoAuthenticate::findRequest($profileId);

if ( $request )
{
    $httpdoc = new component_PhotoAuthenticate($request);    
}
else
{
    $httpdoc = new component_PhotoAuthenticateRequest($profileId);
}



$Layout->display($httpdoc);
