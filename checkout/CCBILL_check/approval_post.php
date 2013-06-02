<?php

require_once( '../../internals/Header.inc.php' );

//gather data
$client_accnum = $_REQUEST['clientAccnum'];
$client_subaccnum = $_REQUEST['clientSubacc'];
$amount = ( $_REQUEST['recurringPrice'] )? $_REQUEST['recurringPrice'] : $_REQUEST['initialPrice'];
$custom = $_REQUEST['username'].$_REQUEST['password'];
$trans_order = $_REQUEST['subscription_id'];

if ( !strlen($custom) || !strlen($trans_order) )
    exit();

// TRACK USER POINTS PACKAGE SALE
$points_sale = app_UserPoints::getUserPointPackageSaleByHash($custom);

if ( $points_sale )
{
    if ( $points_sale['pp_id'] != 5 )
        exit();
    
    app_UserPoints::logSale($custom, print_r($_REQUEST, true), __FILE__, __LINE__);
    require_once 'uppc.php';
    exit();
}
// END of TRACK USER POINTS PACKAGE SALE

if ( !check_ip($_SERVER['REMOTE_ADDR']) )
{
    app_Finance::LogPayment('IP MISMATCH: ' . $_SERVER['REMOTE_ADDR'], __FILE__, __LINE__, $custom);
    exit();
}

if ( (int) $_REQUEST['reasonForDeclineCode'] )
{
    app_Finance::LogPayment('DECLINED' . print_r($_REQUEST, true), __FILE__, __LINE__, $custom);
    app_Finance::SetTransactionResult( $custom, 'declined');
    exit();
}

$merchant_fields = app_Finance::getPaymentProviderFields(5);

app_Finance::LogPayment( 'TIME: '.date("Y-m-d H:i:s", time())."; \n".print_r($_REQUEST, true), __FILE__, __LINE__ );
    
//check merchant info
if ( $merchant_fields['account_number'] != $client_accnum || $merchant_fields['subaccount'] != $client_subaccnum )
{
    app_Finance::LogPayment( 'Incorrect data', __FILE__, __LINE__, $custom );
    exit();
}

if ( app_Finance::SetTransactionResult( $custom, 'approval', $amount, $trans_order ) )
{
    app_Finance::LogPayment( 'Registered.', __FILE__, __LINE__ );
    $__CUSTOM = $custom;
    require_once( '../post_checkout.php' );
}

app_Finance::LogPayment( 'Finish', __FILE__, __LINE__, $custom );

function check_ip( $ip )
{
    $ipv4 = ip2long($ip);
    
    // Array of valid IPv4 CCBill ranges
    // 64.38.240.0 - 64.38.240.255
    // 64.38.241.0 - 64.38.241.255
    
    $valid_ip[0]['start'] = 1076293632;
    $valid_ip[0]['end'] =   1076293887;
    $valid_ip[1]['start'] = 1076293888;
    $valid_ip[1]['end'] =   1076294143;
    
    $in_range[0] = ($ipv4 >= $valid_ip[0]['start'] && $ipv4 <= $valid_ip[0]['end']);
    $in_range[1] = ($ipv4 >= $valid_ip[1]['start'] && $ipv4 <= $valid_ip[1]['end']);
    
    return $in_range[0] || $in_range[1];
}
