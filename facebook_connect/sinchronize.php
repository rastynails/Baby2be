<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'init.php';

$backUri = empty($_GET['backUri']) ? '' : urldecode($_GET['backUri']);
$backUrl = substr(SITE_URL, 0, -1) . $backUri;

$service = FBC_Service::getInstance();

$fbUser = $service->fbRequireUser();

$questions = $service->requestQuestionValueList($fbUser);
unset($questions['email']);
unset($questions['username']);
$profileId = SK_HttpUser::profile_id();
FBC_Pofile::edit($profileId, $questions);

SK_HttpRequest::redirect($backUrl);
