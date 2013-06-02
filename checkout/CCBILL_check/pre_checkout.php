<?php
session_start();
$provider_dir = dirname(dirname(dirname(__FILE__)));
define('DIR_INTERNALS', $provider_dir . DIRECTORY_SEPARATOR . 'internals' . DIRECTORY_SEPARATOR);

require_once DIR_INTERNALS . 'config.php';

$merchant_fields = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields'];

if ( $_SESSION['order_item'] == 'points_package' )
{
    $page = 'points_purchase.php';
    $subaccount = $merchant_fields['subaccount_cred'];
    $form_name = $merchant_fields['form_name_cred'];
}
else
{
    $page = 'payment_selection.php';
    $subaccount = $merchant_fields['subaccount'];
    $form_name = $merchant_fields['form_name'];
}

if ( !isset($_SESSION['__SALE_INFO_ARR']) )
{
    header('Location:' . SITE_URL . 'member/' . $page );
    exit();
}

if ( !strlen($_SESSION['__SALE_INFO_ARR']['provider_plan_id']) )
	header("Location: " . SITE_URL . "member/payment_selection.php");

$_username = substr( $_SESSION['__SALE_INFO_ARR']['custom'], 0, 16 );
$_password = substr( $_SESSION['__SALE_INFO_ARR']['custom'], 16 );
$currencies = array('USD' => '840', 'EUR' => '978', 'AUD' => '036', 'CAD' => '124', 'GBP' => '826', 'JPY' => '392');

?>

<html>
<body>
	<form action='https://bill.ccbill.com/jpost/signup.cgi' method="post" name="purchase_form">
		<input type="hidden" name="clientAccnum" value="<?=$merchant_fields['account_number']?>" />
        <input type="hidden" name="clientSubacc" value="<?=$subaccount?>" />
        <input type="hidden" name="formName" value="<?=$form_name;?>" />
		<input type="hidden" name="language" value="<?=$merchant_fields['language']?>" />
		<input type="hidden" name="allowedTypes" value="<?=$_SESSION['__SALE_INFO_ARR']['provider_plan_id']?>" />
		<input type="hidden" name="subscriptionTypeId" value="<?=$_SESSION['__SALE_INFO_ARR']['provider_plan_id']?>" />
		<input type="hidden" name="custom" value="<?=$_SESSION['__SALE_INFO_ARR']['custom'];?>" />
		<input type="hidden" name="username" value="<?=$_username?>" />
		<input type="hidden" name="password" value="<?=$_password?>" />
        <input type="hidden" name="allowedCurrencies" value="<?=$currencies[$_SESSION['__SALE_INFO_ARR']['currency']]?>" />
        <input type="hidden" name="currencyCode" value="<?=$currencies[$_SESSION['__SALE_INFO_ARR']['currency']]?>" />

		<script language="JavaScript">
			document.forms["purchase_form"].submit();
		</script>
	</form>
</body>
</html>

<?php
unset($_SESSION['__SALE_INFO_ARR']);
