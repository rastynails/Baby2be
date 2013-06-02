<?php

// Disable error reporting
//error_reporting(0);

define("IS_CRON", true);

// Detect DOCUMENT_ROOT var
$document_root = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

require_once $document_root.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

require_once DIR_ADMIN_INC.'AntiBruteforce.class.php';
AntiBruteforce::cron_Process();

/* --- Profile Timeout Logout --- */
$query = "SELECT `profile_id` FROM `".TBL_PROFILE_ONLINE."` WHERE `expiration_time`<".time()."";
$res = SK_MySQL::query($query);

$profile_id_list = array();
while ($profile = $res->fetch_assoc() )
{
    $profile_id_list[$profile['profile_id']] = $profile['profile_id'];
}

app_Profile::LogoffByUserIdList($profile_id_list, false);

/* --- For sending birthday congratulations  --- */
if ( time() == mktime( 0, 0 ) )
{
    if ( SK_Config::section('site.additional.profile')->birthday_congratulation_email )
    {
        app_Profile::sendBirthdayCgts();
    }
}

/* --- Delete expired Profile register invitations --- */
$query = "DELETE FROM `".TBL_PROFILE_REGISTER_INVITE_CODE."` WHERE `expiration_date`<'".time()."'";
SK_MySQL::query($query);

/* --- Delete expired email verifications --- */
$query = "DELETE FROM `".TBL_PROFILE_EMAIL_VERIFY_CODE."` WHERE `expiration_date`<'".time()."'";
SK_MySQL::query($query);

/* --- Delete expired Captcha table --- */
$query = "DELETE FROM `".TBL_CAPTCHA."` WHERE `expiration_time`<'".time()."'";
SK_MySQL::query($query);

/* --- Delete expired profiles memberships --- */
app_Membership::checkSubscriptionTrialMembership();

/* --- Deletes expired affiliate email verifications --- */
$query = "DELETE FROM `".TBL_AFFILIATE_EMAIL_VERIFY_CODE."` WHERE `expiration_stamp`<'".time()."'";
SK_MySQL::query($query);

/* --- Deletes expired search results --- */

app_TempProfileList::deleteExpiredResults();

/* --- Delete old rows from fin sale info table --- */
app_Finance::clear_FinSaleInfoTable();

/* --- Delete expired membership service track --- */
app_Membership::deleteExpiredServiceTrack();

app_Forum::deleteReplaceTopics();

/* ---- Clear forum ban list ---- */
$query = "DELETE FROM `".TBL_FORUM_BANNED_PROFILE."` WHERE `expiration_stamp`<".time();
SK_MySQL::query($query);


/* --- Run activity scheduler --- */
app_ActivityScheduler::runScheduler();

require_once( DIR_SITE_ROOT.'checkout'.DIRECTORY_SEPARATOR.'cron_checkout.php' );

$query = SK_MySQL::placeholder("DELETE FROM `".TBL_PROFILE_VIEW_HISTORY."` WHERE `time_stamp` < ?", (time()-31*24*60*60));
SK_MySQL::query($query);

app_Chat::clearGarbage();

app_Invitation::cron_UpdateInviteLimit();

SK_Cache::clearExpired();

app_Passwords::deleteExpiredKeys();