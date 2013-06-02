<?php

$file_key = 'affiliate';
$active_tab = 'affiliate';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'fnc.affiliate.php' );
require_once( DIR_AFFILIATE_INC.'fnc.affiliate.php' );

$frontend = new AdminFrontend();
require_once( 'inc.admin_menu.php' );

$affiliate_id = intval( $_GET['affiliate_id'] );
if ( !$affiliate_id )
{
	redirect( URL_ADMIN.'affiliate_list.php' );
	exit;
}

if ( $_GET['delete-banner'] )
{
    if ( app_Affiliate::deleteBanner((int) $_GET['delete-banner'], $affiliate_id) )
    {
        $frontend->registerMessage( 'Banner was deleted' );
    }
    else {
        $frontend->registerMessage( 'Banner was not deleted', 'error' );
    }

    redirect(URL_ADMIN . 'affiliate.php?affiliate_id='.$affiliate_id);
}


// update affiliate info
if ( isset( $_POST['aff_info'] ) )
{
	$_affiliate = checkAffiliateFields( $_POST['aff_info'] );
	if ( !$_affiliate )
		$frontend->registerMessage( 'Update failed', 'error' );
	elseif ( !app_Affiliate::checkIsAffiliateEmailUnique( $_affiliate['email'], $affiliate_id ) )
		$frontend->registerMessage( 'Email is already used. Please enter another email address', 'error' );
	else 
	{
		// check if email was changed
		processCheckEmailChange( $affiliate_id, $_affiliate['email'] );
		
		if ( updateAffiliateInfo( $affiliate_id, $_affiliate ) )
			$frontend->registerMessage( 'Affiliate info updated' );
		else
			$frontend->registerMessage( 'Update failed', 'error' );
	}	
	redirect( $_SERVER['REQUEST_URI'] );
}

// update and insert affiliate payments
if ( isset( $_POST['pay_sum'] ) )
{
	if ( isset( $_POST['payment_id'] ) )
	{
		switch ( updateAffiliatePaymentInfo( $affiliate_id, $_POST['payment_id'], $_POST['pay_sum'] ) )
		{
			case 1:
				$frontend->registerMessage( 'Affiliate payment amount updated' );
				break;
			case -2:
				$frontend->registerMessage( 'You can update only last payment', 'notice' );
				break;
			default:
				$frontend->registerMessage( 'Update failed', 'error' );
		}
	}
	else
	{
		if ( addAffiliatePayment( $affiliate_id, $_POST['pay_sum'] ) && $_POST['pay_sum'] )
			$frontend->registerMessage( 'New payment record added' );
		else
			$frontend->registerMessage( 'Payment record add failed', 'error' );
	}

	redirect( $_SERVER['REQUEST_URI'] );
}

if ( isset($_POST['add_banner']) )
{
    if ( app_Affiliate::addBanner($affiliate_id, $_FILES['banner_image']) )
    {
        $frontend->registerMessage('Banner image was successfully added');
    }
    else {
        $frontend->registerMessage('Banner image was not added. Unallowed file extension', 'error');
    }

    redirect( $_SERVER['REQUEST_URI'] );
}

$frontend->assign( 'affiliate_id', $affiliate_id );

// get and pass affiliate info
$affiliate_info = app_Affiliate::getAffiliateInfo( $affiliate_id );
$frontend->assign( 'affiliate_info', $affiliate_info );

// get and pass affiliate payment list
$affiliate_payments = getAffiliatePaymentList( $affiliate_id );

$frontend->assign( 'affiliate_payments', $affiliate_payments );

// get and pass affiliate payment info if it is selected
$payment_id = intval( @$_GET['payment_id'] );
if ( $payment_id )
	$frontend->assign( 'payment_info', getAffiliatePaymentInfo( $affiliate_id, $payment_id ) );
$total_paid = 0;
foreach ( $affiliate_payments as $payment )
	$total_paid += $payment['amount'];
	
$frontend->assign( 'total_paid', $total_paid );
$frontend->assign( 'total_balance', getAffiliateTotalAmount( $affiliate_id ) - $total_paid );

$banners = app_Affiliate::getAffiliateBanners($affiliate_id);
$frontend->assign('banners', $banners);

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'affiliate.js' );

$_page['title'] = 'Affiliates';
$template = 'affiliate.html';

$frontend->display( $template );
