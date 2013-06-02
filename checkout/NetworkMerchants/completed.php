<?php

require_once( '../../internals/Header.inc.php' );

// Receive needed datas
$custom = $_REQUEST['order_id'];
$hash = $_REQUEST['hash'];
$trans_order = $_REQUEST['order_id'];
$status = intval( $_REQUEST['response'] );

// check sale info hash and provider hash
if ( !strlen($custom) || !$hash )
	exit();


// TRACK USER POINTS PACKAGE SALE
$points_sale = app_UserPoints::getUserPointPackageSaleByHash($custom);

if ( $points_sale )
{
    if ( $points_sale['pp_id'] != 9 )
        exit();
    
    app_UserPoints::logSale($custom, print_r($_REQUEST, true), __FILE__, __LINE__);
    require_once 'uppc.php';
    exit();
}
// END of TRACK USER POINTS PACKAGE SALE

	
app_Finance::LogPayment( $_REQUEST, __FILE__, __LINE__ );

$merchant_fields = app_Finance::getPaymentProviderFields( 9 );
$gw_merchantKeyId = $merchant_fields['key_id'];
$gw_merchantKeyText = $merchant_fields['security_text_key'];

if ( !verify($hash, $_REQUEST, $gw_merchantKeyText) )
{
	app_Finance::LogPayment( 'Not verified', __FILE__, __LINE__, $custom );
	exit('Transaction not verified');
}

/**
 * Possible codes returned from Network Merchants
 * 1 -- Transaction Accepted
 * 2 -- Transaction Declined
 * 3 -- Error in transaction data or system error
 */
switch ( $status )
{
	case 1:
		if ( app_Finance::SetTransactionResult( $custom, 'approval', '', $trans_order) )
		{
			app_Finance::LogPayment( "APPROVAL", __FILE__, __LINE__ );
			$__CUSTOM = $custom;
			
			require_once( '../post_checkout.php' );
		}
		break;
		
	case 2:
		app_Finance::LogPayment( "DENIED", __FILE__, __LINE__, $custom );
		app_Finance::SetTransactionResult( $custom, 'declined', $amount, $trans_order );
		break;
	
	case 3:
		app_Finance::LogPayment( "PENDING", __FILE__, __LINE__, $custom );
		app_Finance::SetTransactionResult( $custom, 'processing', '', $trans_order );
		break;
}

app_Finance::LogPayment( "END", __FILE__, __LINE__, $custom );

$__CUSTOM = $custom;
require_once( '../post_display_result.php' );


/**
 * Checks hash verification.
 * 
 */
function verify( $hash, $values, $gw_merchantKeyText )
{
	$fields = explode( '|', $hash );
	$stringToHash = '';
	for ( $i=0; $i<count($fields)-1; $i++)
		$stringToHash.= $_REQUEST[$fields[$i]].'|';
	
	$stringToHash.= $gw_merchantKeyText;
	
	return ( md5($stringToHash) == $fields[count($fields)-1] );
}
