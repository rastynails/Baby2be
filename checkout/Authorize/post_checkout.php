<?php
require_once( '../../internals/Header.inc.php' );

// Receive data
$status = intval( $_REQUEST['x_response_code'] );
$trans_order = $_REQUEST['x_trans_id'];
$__CUSTOM = trim( $_REQUEST['custom_field'] );
$amount = $_REQUEST['x_amount'];
$md5_hash = $_REQUEST['x_MD5_Hash'];

// check sale info id:
if ( !strlen( $__CUSTOM ) )
	exit('Error.');

if ( !strlen($md5_hash) )
	exit('Error. Undefined md5_hash');

	
// TRACK USER POINTS PACKAGE SALE
$points_sale = app_UserPoints::getUserPointPackageSaleByHash($__CUSTOM);

if ( $points_sale )
{
    if ( $points_sale['pp_id'] != 7 )
        exit();
        
    app_UserPoints::logSale($__CUSTOM, print_r($_REQUEST, true), __FILE__, __LINE__);
    require_once 'uppc.php';
    exit();
}
// END of TRACK USER POINTS PACKAGE SALE


app_Finance::LogPayment( $_REQUEST, __FILE__, __LINE__ );

//security: verify md5 hash
$merchant_fields = app_Finance::getPaymentProviderFields( 7 );

app_Finance::LogPayment( $merchant_fields, __FILE__, __LINE__ );

// verify if MD5 Hash == md5(md5Hash.loginId.orderId.amount)
if ( $md5_hash != strtoupper(md5($merchant_fields['md5_hash'].$merchant_fields['login_id'].$trans_order.$amount)) )
{
	app_Finance::LogPayment( "Incorrect md5 hash: $md5_hash != ".strtoupper(md5($merchant_fields['md5_hash'].$merchant_fields['login_id'].$trans_order.$amount)), __FILE__, __LINE__, $__CUSTOM );
	exit();
}

/**
 * Possible values of order status returned from Authorize.net:
 * 1 - approved
 * 2 - declined
 * 3 - error
 * 4 - pending
 */
switch ( $status )
{
	case 1:
		if ( !is_numeric( $amount ) || !strlen( $trans_order ) )
		{
			app_Finance::LogPayment( "Incorrect data: amount=$amount OR order num: $trans_order", __FILE__, __LINE__, $__CUSTOM );
			app_Finance::SetTransactionResult( $__CUSTOM, 'error' );
			exit();
		}
		if ( app_Finance::SetTransactionResult( $__CUSTOM, 'approval', $amount, $trans_order ) )
		{
			// process transaction data
			require_once( '../post_checkout.php' );
		}
		break;
		
	case 2:
		app_Finance::SetTransactionResult( $__CUSTOM, 'declined' );
		break;
	
	case 4:
		app_Finance::SetTransactionResult( $__CUSTOM, 'processing' );
		break;
		
	default:
		app_Finance::SetTransactionResult( $__CUSTOM, 'error' );
}

app_Finance::LogPayment( "End. Look previous logs.", __FILE__, __LINE__, $__CUSTOM );

echo '<html><head><title>'.SK_Language::section('membership')->text('wait_redirecting').'</title></head>
	<body>
		<a href="'.SITE_URL.'checkout/Authorize/completed.php?custom='.$__CUSTOM.'">'.SK_Language::section('membership')->text('wait_redirecting').'</a>
		
		<script language="JavaScript">
		document.location=\''.SITE_URL.'checkout/Authorize/completed.php?custom='.$__CUSTOM.'\';
		</script>
	</body></html>';
?>