<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'init.php';

$backUri = empty($_GET['backUri']) ? '' : urldecode($_GET['backUri']);
$backUrl = substr(SITE_URL, 0, -1) . $backUri;

$service = FBC_Service::getInstance();

$fbUser = $service->fbRequireUser();

$authAdapter = new FBC_AuthAdapter($fbUser);

// Login and redirect if already registered
if ($authAdapter->isRegistered())
{
    $authAdapter->authenticate();

    SK_HttpRequest::redirect($backUrl);
}

//Register if not registered

$questions = $service->requestQuestionValueList($fbUser);

$questions['password'] = uniqid();

$profileId = FBC_Pofile::findProfileIdByEmail($questions['email']);

$userJoin = false;
if (empty($profileId))
{
    $profileId = FBC_Pofile::join($questions);
    $userJoin = true;
}

$authAdapter->register($profileId);

$authAdapter->authenticate();

if ( $userJoin )
{
    $redirect_document = SK_Config::section('navigation')->Section('settings')->join_document_redirect;
    SK_HttpRequest::redirect(SK_Navigation::href($redirect_document));
}
else
{
    SK_HttpRequest::redirect($backUrl);
}

