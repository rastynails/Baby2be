<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$redirectUrl = sk_make_url(SITE_URL . 'google/popup.php', $_GET);
SK_HttpRequest::redirect($redirectUrl);