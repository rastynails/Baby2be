<?php

$file_key = 'affiliate';
$active_tab = 'home_tab';

require_once( '../internals/Header.inc.php' );
require_once( DIR_AFFILIATE_INC.'class.affiliate_frontend.php' );
require_once( DIR_ADMIN_INC.'fnc.affiliate.php' );
require_once( DIR_AFFILIATE_INC.'fnc.affiliate.php' );
//require_once( DIR_APPS.'Affiliate.app.php' );

$frontend = new AffiliateFrontend();
require_once( DIR_AFFILIATE_INC.'inc.affiliate_menu.php' );


// get affiliate id
$affiliate_id = getAffiliateId();

if ( $_GET['delete-banner'] )
{
    if ( app_Affiliate::deleteBanner((int) $_GET['delete-banner'], $affiliate_id) )
    {
        $frontend->registerMessage( 'Banner was deleted' );
    }
    else {
        $frontend->registerMessage( 'Banner was not deleted', 'error' );
    }
    
    SK_HttpRequest::redirect(SITE_URL.'affiliate/affiliate_info.php');
}

if ($_POST['action'] == 'send_email')
{
	if ( app_Affiliate::addRequestEmailVerification( 0, $_POST['email'] ) )
		$frontend->registerMessage( 'Verification email has been sent' );
	else
		$frontend->registerMessage( 'Verification email has not been sent', 'error' );
		
	SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}
else if ( $_POST['action'] == 'add_image' )
{
    if ( app_Affiliate::addBanner($affiliate_id, $_FILES['banner_image']) )
    {
        $frontend->registerMessage('Banner image was successfully added');
    }
    else {
        $frontend->registerMessage('Banner image was not added. Unallowed file extension', 'error');
    }
    
    SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}

$verified = app_Affiliate::isAffiliateEmailVerified($affiliate_id);

$frontend->assign('is_verified', $verified);

// update affiliate info
if ( is_array( $_POST['aff_info'] ) )
{
	$_affiliate = checkAffiliateFields( $_POST['aff_info'] );
	// security
	if ( !$_affiliate || $_affiliate['active'] )
		$frontend->registerMessage( 'Updating failed!', 'error' );
	elseif ( !app_Affiliate::checkIsAffiliateEmailUnique( $_affiliate['email'], $affiliate_id ) )
		$frontend->registerMessage( 'This email is already used. Please enter other email address', 'error' );
	else
	{
		// check if email was changed
		processCheckEmailChange( $affiliate_id, $_affiliate['email'] );
		
		if ( updateAffiliateInfo( $affiliate_id, $_affiliate ) )
			$frontend->registerMessage( 'You successfully updated affiliate info' );
		else
			$frontend->registerMessage( 'Updating failed.', 'error' );
	}
	
	SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}


$frontend->assign( 'affiliate_id', $affiliate_id );

// get and pass affiliate info
$affiliate_info = app_Affiliate::getAffiliateInfo( $affiliate_id );
$frontend->assign( 'affiliate_info', $affiliate_info );

$banners = app_Affiliate::getAffiliateBanners($affiliate_id);
$frontend->assign('banners', $banners);


// include js modules
$frontend->IncludeJsFile( URL_AFFILIATE_JS.'affiliate.js' );

$_page['title'] = "Affiliate info - SkaDate";
$template = 'affiliate_info.html';
$frontend->display( $template );

?>