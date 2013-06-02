<?php

require_once( '../../internals/Header.inc.php' );

// Receive needed datas
$custom = trim( $_REQUEST['custom'] );
$amount = ( $_REQUEST['mc_gross'] != '' )? $_REQUEST['mc_gross'] : $_REQUEST['amount3'];
$trans_order = trim( $_REQUEST['txn_id'] );
$status = trim( $_REQUEST['payment_status'] );
$currency = trim( $_REQUEST['mc_currency'] );
$subscription_type = trim( $_REQUEST['txn_type'] );

// check sale info id:
if ( !strlen($custom) )
	exit();


// TRACK USER POINTS PACKAGE SALE
$points_sale = app_UserPoints::getUserPointPackageSaleByHash($custom);

if ( $points_sale )
{
    if ( $points_sale['pp_id'] != 3 )
        exit();
    
    app_UserPoints::logSale($custom, print_r($_REQUEST, true), __FILE__, __LINE__);
    require_once 'uppc.php';
    exit();
}
// END of TRACK USER POINTS PACKAGE SALE
	
	
app_Finance::LogPayment( 'TIME: '.date("Y-m-d H:i:s", time())."; \n".print_r($_REQUEST, true), __FILE__, __LINE__ );

if ( !is_verified() )
{
	app_Finance::LogPayment( 'Not verified', __FILE__, __LINE__, $custom );
	exit();
}

/**
 * Possible values of txn_type:
 * subscr_cancel - cancellation
 * subscr_failed - payment failure
 * subscr_eot - notify about eot
 * subscr_signup - notify about signup
 * subscr_modify - notify about modify
 * other - payment
 * 
 */

if ( $subscription_type == 'subscr_signup' || $subscription_type == 'subscr_eot' )
{
	app_Finance::LogPayment( 'txn_type: SIGNUP', __FILE__, __LINE__, $custom );
	exit();
}
elseif ( $subscription_type == 'subscr_cancel' )
{
	app_Finance::LogPayment( 'txn_type: CANCEL', __FILE__, __LINE__, $custom );
	app_Finance::SetTransactionResult( $custom, 'cancel' );
	exit();
}
elseif ( $subscription_type == 'subscr_failed' )
{
	app_Finance::LogPayment( 'txn_type: FAILED', __FILE__, __LINE__, $custom );
	app_Finance::SetTransactionResult( $custom, 'error' );
	exit();
}
else
{
	/**
	 * Possible codes returned from PayPal.
	 * Pending -- Payment is still processing.
	 * Completed -- Payment has been approved by buyer's bank.
	 * Denied -- Payment has been declined by payment provider or buyer's bank.
	 * -- System error occured during payment processing.
	 */
	switch ( strtoupper( $status ) )
	{
		case 'COMPLETED':		// means approval
			if ( !is_numeric($amount) || !strlen($trans_order) )
			{
				app_Finance::SetTransactionResult( $custom, 'error' );
				app_Finance::LogPayment( "Incorrect data: amount: $amount OR order num: $trans_order", __FILE__, __LINE__, $custom );
				exit();
			}
			
			if ( app_Finance::SetTransactionResult( $custom, 'approval', '', $trans_order ) )
			{
				app_Finance::LogPayment( 'APPROVAL', __FILE__, __LINE__ );
				$__CUSTOM = $custom;

				require_once( '../post_checkout.php' );
			}
			app_Finance::LogPayment( 'END', __FILE__, __LINE__, $custom );
			exit();
			break;
			
		case 'PENDING':		// processing
			app_Finance::SetTransactionResult( $custom, 'processing', $amount, $trans_order );
			app_Finance::LogPayment( 'PENDING', __FILE__, __LINE__, $custom );
			exit();
			break;
		case 'DENIED':		// declined
			app_Finance::LogPayment( 'DENIED', __FILE__, __LINE__, $custom );
			app_Finance::SetTransactionResult( $custom, 'declined', $amount, $trans_order );
			exit();
			break;
		default:		// error
			app_Finance::SetTransactionResult( $custom, 'error' );
			exit();
	} 
}


/**
 * Posts data back to PayPal to validate payment.
 * 
 */
function is_verified()
{
	foreach ($_POST as $key => $value )
	{
		$value = urlencode(stripslashes($value));
		$_posts.="$key=$value&";
	}
	$_posts.= 'cmd=_notify-validate';
	
	// post back to PayPal system to validate
	$pp_header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
	$pp_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$pp_header .= "Content-Length: ".strlen($_posts)."\r\n";
	$pp_header .= "Host: www.paypal.com\r\n";
    $pp_header .= "Connection: close\r\n\r\n";
	$pp_fp = fsockopen ('www.paypal.com', 80, $pp_errno, $pp_errstr, 30);
	
	if (!$pp_fp)
	{
		app_Finance::LogPayment( "fsockopen error. Num: $pp_errno; Comment: $pp_errstr", __FILE__, __LINE__ );
		return false;
	}
	
	fputs($pp_fp, $pp_header.$_posts);
	
	while( !feof($pp_fp) )
		$_str.= trim( fgets($pp_fp, 2048) );
	
	fclose( $pp_fp );

	app_Finance::LogPayment( "payPal response: $_str", __FILE__, __LINE__ );
	if ( strstr($_str, 'VERIFIED') !== false )
		return true;
	else
		return false;
}
