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
    header('Location:' . SITE_URL . 'member/' . $page);
    exit();
}

require_once DIR_CHECKOUT . 'ICEPAY/api/icepay.php';

$fields = $_SESSION['__SALE_INFO_ARR']['provider_info']['fields'];
$merchantId = $fields['merchant_id'];
$encryptionCode = $fields['encryption_code'];
$country = getCountry();
$currency = $_SESSION['__SALE_INFO_ARR']['currency'];
$price = $_SESSION['__SALE_INFO_ARR']['price'] * 100;
$description = $_SESSION['__SALE_INFO_ARR']['membership_description'];
$custom = $_SESSION['__SALE_INFO_ARR']['custom'];

$sale = app_Finance::GetSaleInfo($custom);

$icepay = new ICEPAY($merchantId, $encryptionCode);
$icepay->SetOrderID($sale['fin_sale_info_id']);
$icepay->SetReference($custom);

$url = $icepay->Pay($country, langByCountry($country), $currency, $price, $description);

unset($_SESSION['__SALE_INFO_ARR']);

header("Location: " . $url);

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
    
    return "00";
}

function langByCountry( $country )
{
    $langs = array('00'=>'EN','AT'=>'DE','AU'=>'EN','BE'=>'NL','CA'=>'EN','CH'=>'DE','CZ'=>'CZ','DE'=>'DE','ES'=>'ES','FR'=>'FR','GB'=>'EN','IT'=>'IT','LU'=>'DE','NL'=>'NL','PL'=>'PL','PT'=>'PT','SK'=>'SK','US'=>'EN');
    return isset($langs[$country]) ? $langs[$country] : 'EN';
}
