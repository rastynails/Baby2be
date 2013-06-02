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

$merchant_email = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['merchant_email'];
$language = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['language'];
$sandboxMode = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields']['sandbox_mode'];

$action = $sandboxMode ? 'http://www.moneybookers.com/app/test_payment.pl' : 'https://www.moneybookers.com/app/payment.pl';

$custom = $_SESSION['__SALE_INFO_ARR']['custom'];
$price = $_SESSION['__SALE_INFO_ARR']['price'];
$currency = $_SESSION['__SALE_INFO_ARR']['currency'];
$desc = $_SESSION['__SALE_INFO_ARR']['membership_description'];
$site_name = $_SESSION['__SALE_INFO_ARR']['site_name'];

?>

<html>
<body>
    <form name="purchase_form" action="<?=$action;?>" method="post">
        <input type="hidden" name="pay_to_email" value="<?=$merchant_email;?>" />
        <input type="hidden" name="recipient_description" value="<?=$site_name;?>" />
        <input type="hidden" name="return_url" value="<?=SITE_URL.'checkout/Moneybookers/completed.php?custom='.$custom;?>" />
        <input type="hidden" name="cancel_url" value="<?=SITE_URL.'checkout/Moneybookers/completed.php?custom='.$custom;?>" />
        <input type="hidden" name="status_url" value="<?=SITE_URL.'checkout/Moneybookers/notify.php'?>" />
        <input type="hidden" name="language" value="<?=$language;?>" />
        <input type="hidden" name="merchant_fields" value="custom" />
        <input type="hidden" name="custom" value="<?=$custom;?>" />
        <input type="hidden" name="currency" value="<?=$currency;?>" />
        <input type="hidden" name="detail1_description" value="Description:" />
        <input type="hidden" name="detail1_text" value="<?=$desc;?>" />
        <input type="hidden" name="confirmation_note" value="" />
        <input type="hidden" name="charset" value="utf-8" />
<?php
if ( $_SESSION['__SALE_INFO_ARR']['is_recurring'] == 'y' )
{
    $rec_cycle = ( $_SESSION['__SALE_INFO_ARR']['units'] == 'days' ) ? 'day' : 'month';
    $rec_period = intval( $_SESSION['__SALE_INFO_ARR']['period'] );
?>
        <input type="hidden" name="rec_amount" value="<?=$price;?>" />
        <input type="hidden" name="rec_cycle" value="<?=$rec_cycle?>" />
        <input type="hidden" name="rec_period" value="<?=$rec_period?>" />
<?php
}
else
{
?>
        <input type="hidden" name="amount" value="<?=$price;?>" />
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
?>