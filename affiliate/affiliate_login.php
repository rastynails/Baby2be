<?php
$file_key = 'affiliate';
$active_tab = 'home';

require_once( '../internals/Header.inc.php' );
require_once( DIR_AFFILIATE_INC.'class.affiliate_frontend.php' );
require_once( DIR_ADMIN_INC.'fnc.affiliate.php' );
require_once( DIR_AFFILIATE_INC.'fnc.affiliate.php' );

$frontend = new AffiliateFrontend();
require_once( DIR_AFFILIATE_INC.'inc.affiliate_menu.php' );


if ( $_POST['affiliate_email'] && $_POST['affiliate_pass'] )
{
	if ( !LoginAffiliate( $_POST['affiliate_email'], $_POST['affiliate_pass'] ) )
		$frontend->registerMessage( 'Login failed. Please check data and try again.', 'error' );
	else
		SK_HttpRequest::redirect( URL_AFFILIATE.'index.php' );

	SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}

// if forgot:
if ( $_POST['forgot_email'] )
{
	 if ( !SendPassword( $_POST['forgot_email'] ) )
	 	$frontend->registerMessage( 'Sending password failed. Please check you email.', 'error' );
	 else
		$frontend->registerMessage( 'Your password was successfully sent to your email' );
	
	SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}

// include js modules
$frontend->IncludeJsFile( URL_AFFILIATE_JS.'affiliate.js' );

$_page['title'] = "Affiliate login - SkaDate";
$template = 'affiliate_login.html';


$frontend->display( $template );

?>