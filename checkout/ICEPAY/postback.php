<?php

require_once '../../internals/Header.inc.php';

// Receive data
$status = trim($_REQUEST['Status']);
$statusCode = trim($_REQUEST['StatusCode']);
$merchant = trim($_REQUEST['Merchant']);
$orderId = trim($_REQUEST['OrderID']);
$paymentId = trim($_REQUEST['PaymentID']);
$custom = trim($_REQUEST['Reference']);
$trans_order = trim($_REQUEST['TransactionID']);
$amount = $_REQUEST['Amount'];
$currency = trim($_REQUEST['Currency'] );
$duration = trim($_REQUEST['Duration']);
$consumerIP = trim($_REQUEST['ConsumerIPAddress']);
$checksum = trim($_REQUEST['Checksum']);

if ( !strlen($custom) )
{
    exit();
}

$merchant_fields = app_Finance::getPaymentProviderFields("ICEPAY");
$enc_code = $merchant_fields['encryption_code'];

$IC_CheckSum = sha1(
    $enc_code . '|' . $merchant  . '|' . $status . '|' . $statusCode . '|' . $orderId . '|' . $paymentId . '|' . 
    $custom . '|' . $trans_order .'|' . $amount . '|' . $currency . '|' . $duration . '|' . $consumerIP
);

// TRACK USER POINTS PACKAGE SALE
$points_sale = app_UserPoints::getUserPointPackageSaleByHash($custom);

if ( $points_sale )
{
    app_UserPoints::logSale($custom, 'TIME: '.date("Y-m-d H:i:s", time())."; \n".print_r($_REQUEST, true), __FILE__, __LINE__);
    require_once 'uppc.php';
    exit();
}
// END of TRACK USER POINTS PACKAGE SALE

app_Finance::LogPayment( 'TIME: '.date("Y-m-d H:i:s", time())."; \n".print_r($_REQUEST, true), __FILE__, __LINE__ );


if ( $checksum != $IC_CheckSum )
{
    app_Finance::LogPayment('Checksum not matches', __FILE__, __LINE__, $custom);
    exit();
}

switch ( strtoupper($status) )
{
    case 'OK':       
        if ( app_Finance::SetTransactionResult($custom, 'approval', '', $trans_order, $currency) )
        {
            app_Finance::LogPayment('APPROVAL', __FILE__, __LINE__);
            $__CUSTOM = $custom;

            require_once('../post_checkout.php');
        }
        app_Finance::LogPayment('END', __FILE__, __LINE__, $custom);
        exit();

    case 'OPEN':
    case 'REFUND':
    case 'CBACK':
        // system
        break;
        
    case 'ERR':
        app_Finance::LogPayment('ERROR: ' . $statusCode, __FILE__, __LINE__, $custom);
        app_Finance::SetTransactionResult($custom, 'error', $amount/100, $trans_order);
        exit();
        
    default:
        app_Finance::SetTransactionResult($custom, 'error');
        exit();
} 
