<?php

require_once '../../internals/Header.inc.php';

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

$fields = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields'];
$merchant_id = $fields['merchant_id'];
$product_id = $_SESSION['__SALE_INFO_ARR']['provider_plan_id'];
$country = getCountry();
$lang = langByCountry($country);
$custom = $_SESSION['__SALE_INFO_ARR']['custom'];
$return_url = SITE_URL . 'member/payment_selection.php';
$receipt_url = URL_CHECKOUT . 'checkout/2checkout/completed.php?custom=' . $custom;

function getCountry()
{
    $countryByIp = app_Location::getCountryByIp($_SERVER['REMOTE_ADDR']);
    if ( strlen($countryByIp) )
    {
        return $countryByIp;
    }

    $profile_id = SK_HttpUser::profile_id();
    $country = app_Profile::getFieldValues($profile_id, 'country_id');

    if ( strlen($country) )
    {
        return $country;
    }

    return "def";
}

function langByCountry( $country )
{
    $langs = array('def'=>'en','CN'=>'zh','DK'=>'da','NL'=>'nl','FR'=>'fr','DE'=>'gr','GR'=>'el',
        'IT'=>'it','JP'=>'jp','NO'=>'no','PT'=>'pt','SI'=>'sl','ES'=>'es_la','SE'=>'sv');
    return isset($langs[$country]) ? $langs[$country] : 'en';
}

?>

<html>
<body>
	<form name="purchase_form" action="https://www.2checkout.com/checkout/purchase" method="post">
		<input type="hidden" name="sid" value="<?=$merchant_id;?>" />
		<input type="hidden" name="product_id" value="<?=$product_id;?>" />
		<input type="hidden" name="quantity" value="1" />
        <input type="hidden" name="fixed" value="Y" />
<?php
if ( $fields['demo_mode'] == 'on' )
{
?>
		<input type="hidden" name="demo" value="Y" />
<?php
}
?>
        <input type="hidden" name="lang" value="<?=$lang;?>" />
		<input type="hidden" name="return_url" value="<?=$return_url;?>" />
		<input type="hidden" name="merchant_order_id" value="<?=$custom;?>" />		
		<input type="hidden" name="x_receipt_link_url" value="<?=$receipt_url;?>" />

		<script language="JavaScript">
			document.forms["purchase_form"].submit();
		</script>
	</form>
</body>
</html>

<?php 

unset($_SESSION['__SALE_INFO_ARR']);