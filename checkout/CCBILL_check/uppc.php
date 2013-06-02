<?php

if ( !defined('DIR_INTERNALS') )
    exit("No direct access allowed");

if ( !check_ip($_SERVER['REMOTE_ADDR']) )
{
    app_UserPoints::logSale($custom, 'IP MISMATCH: ' . $_SERVER['REMOTE_ADDR']);
    exit();
}

if ( (int) $_REQUEST['reasonForDeclineCode'] )
{
    app_UserPoints::logSale($custom, 'Declined: ' . $_REQUEST['reasonForDecline']);
    exit();
}
    
$merchant_fields = app_Finance::getPaymentProviderFields(5);
    
//check merchant info
if ( $merchant_fields['account_number'] != $client_accnum || $merchant_fields['subaccount_cred'] != $client_subaccnum )
{
    app_UserPoints::logSale($custom, 'Incorrect data');
    exit();
}

if ( app_UserPoints::registerPackageSale($custom, trim($trans_order)) )
{
    app_UserPoints::logSale($custom, 'approved');
}
