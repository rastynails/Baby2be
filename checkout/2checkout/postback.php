<?php

switch( $_SERVER['REQUEST_METHOD'] )
{
    case 'GET': $data = $_GET; break;
    case 'POST': $data = $_POST; break;
    default: exit;
}

require_once( '../../internals/Header.inc.php' );

$type = trim($data['message_type']);
$custom = trim($data['vendor_order_id']);
$sale_id = trim($data['sale_id']);
$invoice_id = trim($data['invoice_id']);
$md5 = trim($data['md5_hash']);
$recurring = $data['recurring'];
$total = $data['invoice_list_amount'];

logRequest(print_r($data, true));

if ( !in_array($type, array('ORDER_CREATED', 'RECURRING_INSTALLMENT_SUCCESS')) )
{
    exit();
}

if ( !strlen($custom) )
{
	exit();
}

$fields = app_Finance::getPaymentProviderFields('2checkout');
$md5calc = strtoupper(md5($sale_id . $fields['merchant_id'] . $invoice_id . $fields['secret']));

// TRACK USER POINTS PACKAGE SALE
$points_sale = app_UserPoints::getUserPointPackageSaleByHash($custom);

if ( $points_sale )
{
    app_UserPoints::logSale($custom, print_r($data, true), __FILE__, __LINE__);
    require_once 'uppc.php';

    exit();
}
// END of TRACK USER POINTS PACKAGE SALE
	
app_Finance::LogPayment( 'TIME: '.date("Y-m-d H:i:s", time())."; \n".print_r($data, true), __FILE__, __LINE__ );

if ( $md5 == $md5calc || $fields['demo_mode'] == 'on' ) // MD5 hash fails on demo mode
{
    if ( $recurring )
    {
        $res = app_Finance::SetTransactionResult($custom, 'approval', '', $sale_id, '', $invoice_id);
        logRequest("Recurring order: Custom = ".$custom." | Sale ID = ".$sale_id." | Invoice ID = ".$invoice_id." | Result: ".$res);
    }
    else
    {
        $res = app_Finance::SetTransactionResult($custom, 'approval', '', $sale_id);
        logRequest("Order: Custom = ".$custom." | Sale ID = ".$sale_id." | Invoice ID = ".$invoice_id." | Result: ".$res);
    }

    if ( $res )
    {
        app_Finance::LogPayment('Registered.', __FILE__, __LINE__);
        $__CUSTOM = $custom;

        include( DIR_CHECKOUT . 'post_checkout.php' );
    }

    app_Finance::LogPayment('END', __FILE__, __LINE__, $custom);
    exit();
}
else
{
    app_Finance::SetTransactionResult($custom, 'error');
    app_Finance::LogPayment("Data not verified", __FILE__, __LINE__, $custom);

    exit('Data not verified');
}

function logRequest( $string )
{
    $log_file = DIR_SITE_ROOT.'checkout/2checkout/log.txt';
    $mode = file_exists($log_file) ? (filesize($log_file) > 500000 ? "w" : "a") : "w";

	if ( !$fp = @fopen($log_file, $mode) )
    {
		return false;
    }

    @fwrite($fp, $string);
    fclose($fp);

    return true;
}
