<?php

define("IS_CRON", true);

// Detect DOCUMENT_ROOT var
$document_root = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

require_once $document_root.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

// Video convertation
app_ProfileVideo::runCronVideoConvert();

// Sending mail queue
app_Mail::QueueProcess();

// Photo convertation
require_once(DIR_ADMIN_INC . "fnc.config_photo.php");
cron_RedrawPhotos();

// Remove expired SMS payments
app_SMSBilling::deleteExpiredPayments();

// SEO-Sitemap ping Search Engines (Google, Yahoo)
app_SEOSitemap::cronDoJob();

// Remove tmp files
app_TempFileManager::removeExpiredTmpFiles();

// remove expired user points package sales
app_UserPoints::deleteExpiredPackageSales();

// remove old records from credits log table
app_UserPoints::deleteExpiredCreditsHistory();

// notify before membership expiration
app_Membership::sendExpirationNotifications();

// notify about upcoming speed dating event
app_EventService::sendSpeedDatingNotifications();

app_UserActivities::cronJob();

// remove old posts from shoutbox
app_Shoutbox::clearPosts();

// remove old IM messages and sessions
app_IM::cronJob();

app_Security::getInstance()->update();

