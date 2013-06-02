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

$merchant_id = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['username'];
$digestKey = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['digest_key'];
$remotePassword = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['remote_password'];

//generate digest code for SecPay authentication
$digest = md5(substr($_SESSION['__SALE_INFO_ARR']['custom'], 0, 8).$_SESSION['__SALE_INFO_ARR']['price'].$remotePassword);

if ( $_SESSION['__SALE_INFO_ARR']['is_recurring'] == 'y' )
{
	$days = ( $_SESSION['__SALE_INFO_ARR']['units'] == 'days' )? intval($_SESSION['__SALE_INFO_ARR']['period']) : intval($_SESSION['__SALE_INFO_ARR']['period'])*30;
	switch ($days)
	{
		case 1:
			$units = 'daily';
			break;
		case 7:
			$units = 'weekly';
			break;
		case 30:
			$units = 'monthly';
			break;
		case 90:
			$units = 'quarterly';
			break;
		case 360:
		case 365:
			$units = 'yearly';
			break;
		default:
			header("Location: " . SITE_URL . "member/payment_selection.php");
	}

	$repeat = date("Ymd", time()).'/'.$units.'/-1';
	$recurringHtmlCode = '<input type="hidden" name="repeat" value="'.$repeat.'" />
		<input type="hidden" name="repeat_callback" value="'.SITE_URL.'checkout/SecPay/completed.php" />';
}

?>
<html>
<body>
	<form name="purchase_form" action="https://www.secpay.com/java-bin/ValCard" method="post">
		<input type="hidden" name="merchant" value="<?=$merchant_id;?>" />
		<input type="hidden" name="trans_id" value="<?=substr($_SESSION['__SALE_INFO_ARR']['custom'], 0, 8);?>" />
		<input type="hidden" name="amount" value="<?=$_SESSION['__SALE_INFO_ARR']['price']?>" />
		<input type="hidden" name="callback" value="<?=SITE_URL.'checkout/SecPay/completed.php'?>" />
		<input type="hidden" name="currency" value="<?=$_SESSION['__SALE_INFO_ARR']['currency'];?>" />
		<input type="hidden" name="cb_flds" value="customfield" />
		<input type="hidden" name="customfield" value="<?=$_SESSION['__SALE_INFO_ARR']['custom'];?>" />
		<input type="hidden" name="digest" value="<?=$digest;?>" />
		<input type="hidden" name="cb_post" value="true" />
		<input type="hidden" name="md_flds" value="trans_id:callback" />

		<?php
			if (isset($recurringHtmlCode))
				echo $recurringHtmlCode;
		?>

		<script language="JavaScript">
			document.forms["purchase_form"].submit();
		</script>
	</form>
</body>
</html>
