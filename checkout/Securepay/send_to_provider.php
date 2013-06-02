<?php

require_once( '../../internals/Header.inc.php' );
if ( $_SESSION['__SALE_INFO_ARR']['sale_info_id'] )
{
    if ( $_SESSION['order_item'] == 'points_package' )
    {
        $_sale_info_arr = app_UserPoints::getUserPointPackageSaleByHash($_SESSION['__SALE_INFO_ARR']['sale_info_id']);
        $page = 'points_purchase.php';
        $amount = $_sale_info_arr['price'];
    }
    else
    {
        $_sale_info_arr = app_Finance::GetSaleInfoUsingId($_SESSION['__SALE_INFO_ARR']['sale_info_id']);
        $page = 'payment_selection.php';
        $amount = $_sale_info_arr['amount'];
    } 
    
	if ( !$_sale_info_arr )
	{
		header('Location:' . SITE_URL . 'member/' . $page);
	}
			
	$merchant_fields = app_Finance::getPaymentProviderFields(6);
?>
<html>
<body>
	<form name="purchase_form" action="https://www.securepay.com/secure18/index.cfm" method="post">
		<input type="hidden" name="SUCCESS_URL" value="<?=SITE_URL?>checkout/Securepay/display_result.php" />
		<input type="hidden" name="FAILURE_URL" value="<?=SITE_URL?>checkout/Securepay/display_result.php" />
		<input type="hidden" name="FMETHOD" value="POST" />
		<input type="hidden" name="SEND_MAIL" value="No" />
		<input type="hidden" name="AMOUNT" value="<?=$amount;?>" />
		<input type="hidden" name="MERCH_ID" value="<?=$merchant_fields['securepay_id']?>" />
		<input type="hidden" name="custom" value="<?=$_sale_info_arr['hash']?>" />
		<input type="hidden" name="Post_CallBack" value="<?=SITE_URL?>checkout/Securepay/post_callback.php" />
		<input type="hidden" name="Unique_ID" value="<?=$_sale_info_arr['hash']?>" />
		<input type="hidden" name="Tr_Type" value="SALE" />
		<input type="hidden" name="NAME" value="<?=$_SESSION['__SALE_INFO_ARR']['name']?>" />
		<input type="hidden" name="STREET" value="<?=$_SESSION['__SALE_INFO_ARR']['street']?>" />
		<input type="hidden" name="CITY" value="<?=$_SESSION['__SALE_INFO_ARR']['city']?>" />
		<input type="hidden" name="STATE" value="<?=$_SESSION['__SALE_INFO_ARR']['state']?>" />
		<input type="hidden" name="ZIP" value="<?=$_SESSION['__SALE_INFO_ARR']['zip']?>" />
		<input type="hidden" name="EMAIL" value="<?=$_SESSION['__SALE_INFO_ARR']['email']?>" />
	</form>
	<script language="JavaScript">
		document.forms["purchase_form"].submit();
	</script>
</body>
</html>
<?php
unset($_SESSION['__SALE_INFO_ARR']);
}
else {
	unset($_SESSION['__SALE_INFO_ARR']);
	header("Location: " . SITE_URL . "member/payment_selection.php");
}

?>