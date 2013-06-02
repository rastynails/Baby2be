<?php

$file_key = 'affiliate_stats';
$active_tab = 'stats_tab';

require_once( '../internals/Header.inc.php' );

require_once( DIR_AFFILIATE_INC.'class.affiliate_frontend.php' );

require_once( DIR_ADMIN_INC.'fnc.affiliate.php' );
require_once( DIR_AFFILIATE_INC.'fnc.affiliate.php' );

$frontend = new AffiliateFrontend();
require_once( DIR_AFFILIATE_INC.'inc.affiliate_menu.php' );

if ($_GET['verification_code']) {
	app_Affiliate::processCheckVerificationCode();	
}

// get affiliate id
$affiliate_id = getAffiliateId();

$frontend->assign( 'affiliate_id', $affiliate_id );

if ($_POST['action'] == 'send_email')
{
	if ( app_Affiliate::addRequestEmailVerification( 0, $_POST['email'] ) )
		$frontend->registerMessage( 'Verification email has been sent' );
	else
		$frontend->registerMessage( 'Verification email has not been sent', 'error' );
		
	SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}

$verified = app_Affiliate::isAffiliateEmailVerified($affiliate_id);
$frontend->assign('is_verified', $verified);

//get and pass affiliate info
$traffic = getAffiliateTraffic($affiliate_id);
$registration = getAffiliateRegistration($affiliate_id);
$subscription = getAffiliateSubscription($affiliate_id);
$site = getAffiliateTrafficForSites($affiliate_id, 10);

$affiliate = array();
$affiliate['traffic'] = $traffic;
$affiliate['registration'] = $registration;
$affiliate['subscription'] = $subscription;
$affiliate['site'] = $site;
$affiliate['total'] = 
  ($traffic ? $traffic['amount'] : 0) + 
  ($subscription ? $subscription['amount'] : 0) + 
  ($registration ? $registration['amount'] : 0);

// get and pass affiliate payment list
$affiliate_payments = getAffiliatePaymentList( $affiliate_id );

$frontend->assign( 'affiliate_payments', $affiliate_payments );

// count balance
foreach ( $affiliate_payments as $_payment )
	$paid+= $_payment['amount'];

$affiliate['balance'] = $affiliate['total'] - $paid;
$frontend->assign( 'affiliate', $affiliate );

// include js modules
$frontend->IncludeJsFile( URL_AFFILIATE_JS.'affiliate.js' );

$_page['title'] = "Affiliate info - SkaDate";
$template = 'affiliate_stats.html';
$frontend->display( $template );

?>