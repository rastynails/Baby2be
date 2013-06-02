<?php

$file_key = 'finance';
$active_tab = 'finance';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile.php' );
require_once( DIR_ADMIN_INC.'fnc.finance.php' );

$frontend = new AdminFrontend();
require_once( 'inc.admin_menu.php' );

if ( $_POST )
{
	if ( is_numeric( $_POST['amount'] ) && is_numeric( $_POST['sale_id'] ) )
	{
		if ( AdminMembership::refundTransaction( $_POST['sale_id'], $_POST['amount'], $_POST['comment'] ) )
			$frontend->RegisterMessage( 'Order refunded' );
		else
			$frontend->RegisterMessage( 'Order not refunded. Check your input data.', 'notice' );
	}
	redirect( $_SERVER['REQUEST_URI'] );
}


$trans_info = GetTransactionInfo( $_GET['order'], $_GET['provider_id'] );
$trans_info['is_deleted'] = AdminMembership::IsExistMembershipType( $trans_info['membership_type_id'] );
$trans_info['type'] = ( $trans_info['expiration_stamp'] - $trans_info['start_stamp']  < 2 )? 'credits' : 'subscription';

if ( strlen($trans_info['coupon']) )
{
    $ccode = app_CouponCodes::getCode($trans_info['coupon']);
    if ( $ccode )
    {
        $trans_info['discount'] = floatval($ccode['percent']);
    }
}

$frontend->assign( 'trans', $trans_info );
$frontend->assign( 'provider_id', $_GET['provider_id'] );

$new_obj = new adminProfile();

$frontend->register_function( 'profile_url', array( &$new_obj, 'frontendGetProfileURL') );

$_page['title'] = "Finance";

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'affiliate.js' );

$template = 'transaction.html';

$frontend->display( $template );
