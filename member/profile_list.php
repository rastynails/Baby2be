<?php
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

if ( !empty(SK_HttpRequest::$GET['page']) && SK_HttpRequest::$GET['page'] > 1)
{
    $service = new SK_Service('unlimited_search_result');
    $perm = $service->checkPermissions();
    
    if ($perm != SK_Service::SERVICE_FULL)
    {
        if ( SK_HttpUser::is_authenticated() )
        {
            SK_HttpRequest::redirect(SK_Navigation::href('payment_selection'));    
        }
        
        $httpdoc = new httpdoc_SignIn;        
    }
    else
    {
        $httpdoc = new httpdoc_SearchResult;
    }
}
else 
{
    $httpdoc = new httpdoc_SearchResult;    
}

$Layout->display($httpdoc);
