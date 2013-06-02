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

$referenceId = substr( $_SESSION['__SALE_INFO_ARR']['custom'], 0, 16 );
?>

<html>
<body>
	<form name="purchase_form" method="POST" action="https://www.vcs.co.za/vvonline/ccform.asp">
		<input type="hidden" name="p1" value="<?=$_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['terminal_id']?>" />
		<input type="hidden" name="p2" value="<?=$referenceId;?>" />
		<input type="hidden" name="p3" value="<?=$_SESSION['__SALE_INFO_ARR']['membership_description']?>" />
		<input type="hidden" name="p4" value="<?=$_SESSION['__SALE_INFO_ARR']['price'];?>" />
		<input type="hidden" name="m_1" value="<?=$_SESSION['__SALE_INFO_ARR']['custom'];?>" />
	</form>

	<script language="JavaScript">
		document.forms["purchase_form"].submit();
	</script>
</body>
</html>