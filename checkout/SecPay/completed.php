<?php

require_once( '../../internals/Header.inc.php' );

$custom = $_REQUEST['customfield'];
$trans_order = trim( $_REQUEST['trans_id'] );
$amount = $_REQUEST['amount'];
$status = $_REQUEST['code'];
$resp_code = $_REQUEST['resp_code'];
$hash = $_REQUEST['hash'];

//get merchant info
$merchant_fields = app_Finance::getPaymentProviderFields( 8 );
$merchant_id = $merchant_fields['username'];
$digestKey = $merchant_fields['digest_key'];
$remotePassword = $merchant_fields['remote_password'];

//check sale hash:
if ( !strlen($custom) )
	exit();


// TRACK USER POINTS PACKAGE SALE
$points_sale = app_UserPoints::getUserPointPackageSaleByHash($custom);

if ( $points_sale )
{
    if ( $points_sale['pp_id'] != 8 )
        exit();
    
    app_UserPoints::logSale($custom, print_r($_REQUEST, true), __FILE__, __LINE__);
    require_once 'uppc.php';
    exit();
}
// END of TRACK USER POINTS PACKAGE SALE

	
app_Finance::LogPayment( 'Log. Time: '.date( "Y-m-d H:i:s", time()).";\n".$_REQUEST, __FILE__, __LINE__ );

if ( !isGenuine($hash, $digestKey, $trans_order) )
{
	app_Finance::LogPayment( 'Not verified', __FILE__, __LINE__, $custom );
	exit();
}

//check status
if ( strtoupper($status) == 'A' && !$resp_code ) //means approval
{
	if ( !is_numeric($amount) || !strlen($trans_order) )
	{
		app_Finance::SetTransactionResult( $custom, 'error' );
		app_Finance::LogPayment( "Incorrect data: amount: $amount OR order number: $trans_order", __FILE__, __LINE__ );
	}
	elseif ( app_Finance::SetTransactionResult( $custom, 'approval', $amount, $trans_order ) )
	{
		app_Finance::LogPayment( "APPROVAL", __FILE__, __LINE__ );
		$__CUSTOM = $custom;

		require_once( '../post_checkout.php' );
	}
}
else // processing
{
	app_Finance::SetTransactionResult( $custom, 'processing', $amount, $trans_order );
	app_Finance::LogPayment( "PENDING", __FILE__, __LINE__ );
} 

app_Finance::LogPayment( "END", __FILE__, __LINE__, $custom );


/**
 * Check if the request was sent by SecPay
 * 
 */
function isGenuine( $hash, $digestKey, $trans_id )
{
	return ( md5('trans_id='.$trans_id.'&callback='.SITE_URL.'checkout/SecPay/completed.php&'.$digestKey) == $hash );
}

?>
<html>
<body>
	<a href="<?=SK_Navigation::href('payment_selection')?>"><?=SK_Language::section('membership')->text('wait_redirecting');?></a>
	
	<script>
		document.location='<?=SK_Navigation::href('payment_selection');?>';
	</script>
</body>
</html>