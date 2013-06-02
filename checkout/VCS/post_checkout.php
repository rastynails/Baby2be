<?php

require_once( '../../internals/Header.inc.php' );

$custom = $_REQUEST['m_1'];
$amount = $_REQUEST['p6'];
$trans_order = $_REQUEST['p2'];
$status = trim(substr($_REQUEST['p3'],6));
$verification_code = $_REQUEST['pam'];

$merchant_fields = app_Finance::getPaymentProviderFields(10);

// check sale info hash and provider hash
if ( !strlen($custom) )
	exit();

	
// TRACK USER POINTS PACKAGE SALE
$points_sale = app_UserPoints::getUserPointPackageSaleByHash($custom);

if ( $points_sale )
{
    if ( $points_sale['pp_id'] != 10 )
        exit();
    
    app_UserPoints::logSale($custom, print_r($_REQUEST, true), __FILE__, __LINE__);
    require_once 'uppc.php';
    exit();
}
// END of TRACK USER POINTS PACKAGE SALE
	

app_Finance::LogPayment( $_REQUEST, __FILE__, __LINE__ );

if ( $merchant_fields['merchant_pam'] != $verification_code )
{
	app_Finance::LogPayment( 'Not verified', __FILE__, __LINE__, $custom );
	exit("<CallBackResponse>PAM not verified</CallBackResponse>");
}

/**
 * Possible codes returned from VCS
 * APPROVED -- Transaction Accepted
 * -- Transaction Declined
 */
switch ( $status )
{
	case 'APPROVED':
		if ( app_Finance::SetTransactionResult( $custom, 'approval', $amount, $trans_order) )
		{
			$__CUSTOM = $custom;

			app_Finance::LogPayment( 'APPROVAL', __FILE__, __LINE__ );
			require_once( '../post_checkout.php' );
			
			app_Finance::LogPayment( 'END', __FILE__, __LINE__, $custom );
			exit("<CallBackResponse>Accepted</CallBackResponse>");
		}
		else
			app_Finance::LogPayment( 'Incorrect data', __FILE__, __LINE__ );
			exit("<CallBackResponse>Accepted</CallBackResponse>");
		
	default:
		app_Finance::LogPayment( 'DECLINED', __FILE__, __LINE__ );
		app_Finance::SetTransactionResult( $custom, 'declined', $amount, $trans_order );
		
		exit("<CallBackResponse>Accepted</CallBackResponse>");
}
