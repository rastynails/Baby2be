<?php

$file_key = 'affiliate';
$active_tab = 'settings';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'fnc.affiliate.php' );
require_once( DIR_AFFILIATE_INC.'fnc.affiliate.php' );

$frontend = new AdminFrontend();
require_once( 'inc.admin_menu.php' );

if ( isset( $_POST['settings'] ) )
{
	if ( !is_array( $_POST['settings'] ) || !$_POST['settings'] )
		$frontend->registerMessage( 'Incorrect data. Please check and try again.' );
	
	$result = true;
	foreach ( $_POST['settings'] as $key=>$value )
	{
		switch ( $key )
		{
			case 'subscription_type_value':
			case 'subscription_amount':
			case 'subscription_percent':
			case 'period':
			case 'registration_amount':
			case 'traffic_amount':
				if ( !updateAffiliateConfig( $key, $value ) )
					$result = false;
		}
	}
	
	if ( $result )
		$frontend->registerMessage( 'Affiliate settings updated' );
	else
		$frontend->registerMessage( 'Update failed', 'error' );
	
	redirect( $_SERVER['REQUEST_URI'] );
}

// get and pass affiliate configs
$interval = app_Affiliate::getAffiliateConfig( 'period' );
$frontend->assign( 'interval', $interval );

$traffic_amount = app_Affiliate::getAffiliateConfig( 'traffic_amount' );
$frontend->assign( 'traffic_amount', $traffic_amount );

$signup_amount = app_Affiliate::getAffiliateConfig( 'registration_amount' );
$frontend->assign( 'registration_amount', $signup_amount );

$subscription_amount = app_Affiliate::getAffiliateConfig( 'subscription_amount' );
$frontend->assign( 'subscription_amount', $subscription_amount );

$subscription_percent = app_Affiliate::getAffiliateConfig( 'subscription_percent' );
$frontend->assign( 'subscription_percent', $subscription_percent );

$subscription_type = app_Affiliate::getAffiliateConfig( 'subscription_type_value' );
$frontend->assign( 'subscription_type', $subscription_type );

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'affiliate.js' );

$_page['title'] = "Affiliates Settings";
$template = 'affiliate_configure.html';

$frontend->display( $template );

?>