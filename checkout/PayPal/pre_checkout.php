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
?>

<html>
<body>
	<form name="purchase_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="return" value="<?=SITE_URL.'checkout/PayPal/completed.php'?>" />
		<input type="hidden" name="cancel_return" value="<?=SITE_URL.'checkout/PayPal/completed.php'?>" />
		<input type="hidden" name="notify_url" value="<?=SITE_URL.'checkout/PayPal/notify.php'?>" />
		<input type="hidden" name="rm" value="2" />
		<input type="hidden" name="business" value="<?=$_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['merchant_id'];?>" />
		<input type="hidden" name="item_name" value="<?=$_SESSION['__SALE_INFO_ARR']['membership_description'];?>" />
		<input type="hidden" name="no_note" value="1" />
		<input type="hidden" name="currency_code" value="<?=$_SESSION['__SALE_INFO_ARR']['currency'];?>" />
		<input type="hidden" name="custom" value="<?=$_SESSION['__SALE_INFO_ARR']['custom'];?>" />
		<input type="hidden" name="charset" value="utf-8" />
<?php
if ( $_SESSION['__SALE_INFO_ARR']['is_recurring'] == 'y' )
{
	$_units = ( $_SESSION['__SALE_INFO_ARR']['units'] == 'days' )? 'D' : 'M';
	$_period = intval( $_SESSION['__SALE_INFO_ARR']['period'] );

?>
		<input type="hidden" name="cmd" value="_xclick-subscriptions" />
		<input type="hidden" name="a3" value="<?=$_SESSION['__SALE_INFO_ARR']['price']?>" />
		<input type="hidden" name="p3" value="<?=$_period?>" />
		<input type="hidden" name="t3" value="<?=$_units?>" />
		<input type="hidden" name="src" value="1" />
<?php
}
else
{
?>
		<input type="hidden" name="cmd" value="_xclick" />
		<input type="hidden" name="bn" value="PP-BuyNowBF" />
		<input type="hidden" name="amount" value="<?=$_SESSION['__SALE_INFO_ARR']['price']?>" />
<?php
}
?>
		<script language="JavaScript">
			document.forms["purchase_form"].submit();
		</script>
	</form>
</body>
</html>

<?php
	unset($_SESSION['__SALE_INFO_ARR']);