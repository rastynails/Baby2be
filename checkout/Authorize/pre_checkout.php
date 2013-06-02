<?php
session_start();
$provider_dir = dirname(dirname(dirname(__FILE__)));
define('DIR_INTERNALS', $provider_dir . DIRECTORY_SEPARATOR . 'internals' . DIRECTORY_SEPARATOR);

require_once DIR_INTERNALS . 'config.php';

if ( !isset($_SESSION['__SALE_INFO_ARR']) )
{
    if ( $_SESSION['order_item'] == 'points_package' )
    {
        $page = 'points_purchase.php';
    }
    else
    {
        $page = 'payment_selection.php';
    }
    header('Location:' . SITE_URL . 'member/' . $page );
	exit();
}

$loginid = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['login_id'];
$transaction_key = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['transaction_key'];

srand(time());
$sequence = rand(1, 1000);
$time_stamp = time();
$fingerprint = calculateFingerPrint( $loginid, $transaction_key, $_SESSION['__SALE_INFO_ARR']['price'], $sequence, $time_stamp, $_SESSION['__SALE_INFO_ARR']['currency'] );

?>
<html>
<body>
	<form name="purchase_form" action="https://secure.authorize.net/gateway/transact.dll" method="post">
		<input type="hidden" name="x_show_form" value="PAYMENT_FORM" />
		<input type="hidden" name="x_fp_hash" value="<?=$fingerprint;?>" />
		<input type="hidden" name="x_fp_sequence" value="<?=$sequence;?>" />
		<input type="hidden" name="x_fp_timestamp" value="<?=$time_stamp;?>" />
		<input type="hidden" name="x_login" value="<?=$loginid?>" />

		<input type="hidden" name="custom_field" value="<?=$_SESSION['__SALE_INFO_ARR']['custom'];?>" />
		<input type="hidden" name="x_amount" value="<?=$_SESSION['__SALE_INFO_ARR']['price'];?>" />
		<input type="hidden" name="x_currency_code" value="<?=$_SESSION['__SALE_INFO_ARR']['currency'];?>" />
		<input type="hidden" name="x_description" value="<?=$_SESSION['__SALE_INFO_ARR']['membership_description'];?>" />

		<input type="hidden" name="x_relay_response" value="TRUE" />
		<input type="hidden" name="x_relay_url" value="<?=SITE_URL?>checkout/Authorize/post_checkout.php" />

		<script language="JavaScript">
			document.forms["purchase_form"].submit();
		</script>
	</form>
</body>
</html>

<?php

unset($_SESSION['__SALE_INFO_ARR']);

/**
 * compute HMAC-MD5
 *
 */
function hmac($key, $data)
{
	$b = 64; // byte length for md5
	if (strlen($key) > $b)
		$key = pack("H*",md5($key));

	$key  = str_pad($key, $b, chr(0x00));
	$ipad = str_pad('', $b, chr(0x36));
	$opad = str_pad('', $b, chr(0x5c));
	$k_ipad = $key ^ $ipad ;
	$k_opad = $key ^ $opad;

	return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
}

/**
 * Calculate and return fingerprint
 *
 */
function calculateFingerPrint ($loginid, $x_tran_key, $amount, $sequence, $tstamp, $currency = "")
{
	return hmac( $x_tran_key, $loginid."^".$sequence."^".$tstamp."^".$amount."^".$currency );
}

?>