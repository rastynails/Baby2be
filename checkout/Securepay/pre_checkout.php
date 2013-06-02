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
	<form name="purchase_form" action="<?=SITE_URL . 'member/checkout.php?provider=Securepay'?>" method="post">
		<input type="hidden" name="sale_info_id" value="<?=$_SESSION['__SALE_INFO_ARR']['sale_info_id']?>" />
	</form>

	<script language="JavaScript">
		document.forms["purchase_form"].submit();
	</script>
</body>
</html>

<?php
unset($_SESSION['__SALE_INFO_ARR']);
?>