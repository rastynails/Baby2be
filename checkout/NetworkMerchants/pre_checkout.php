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

$gw_merchantKeyId = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['key_id'];
$gw_merchantKeyText = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['security_text_key'];

function gw_printField($name, $value = "", $gw_merchantKeyText = '')
{
	static $fields;

	// Generate the hash
	if($name == "hash")
	{
		$stringToHash = implode('|', array_values($fields))."|".$gw_merchantKeyText;


		$value = implode("|", array_keys($fields)) . "|" . md5($stringToHash);
	}
	else
	{
		$fields[$name] = $value;
	}
	print "<input type=\"hidden\" name=\"$name\" VALUE=\"$value\">\n";
}

?>
<html>
<body>
	<form name="purchase_form" action="https://secure.networkmerchants.com/cart/cart.php" method="POST">
	<input type="hidden" name="key_id" value="<?=$gw_merchantKeyId?>">
	<input type="hidden" name="language" value="en" />
	<input type="hidden" name="url_finish" value="<?=SITE_URL.'checkout/NetworkMerchants/completed.php'?>" />
	<input type="hidden" name="order_id" value="<?=$_SESSION['__SALE_INFO_ARR']['custom'];?>" />
	<?php gw_printField("action", "process_fixed"); ?>
	<?php gw_printField("order_description", $_SESSION['__SALE_INFO_ARR']['custom']); ?>
	<?php gw_printField("amount", $_SESSION['__SALE_INFO_ARR']['price']); ?>
	<?php gw_printField("hash", '', $gw_merchantKeyText); ?>

	<script language="JavaScript">
		document.forms["purchase_form"].submit();
	</script>
	</form>
</body>
</html>

<?php
unset($_SESSION['__SALE_INFO_ARR']);
