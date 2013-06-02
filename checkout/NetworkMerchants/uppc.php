<?php

$merchant_fields = app_Finance::getPaymentProviderFields(9);
$gw_merchantKeyId = $merchant_fields['key_id'];
$gw_merchantKeyText = $merchant_fields['security_text_key'];

if ( !verify($hash, $_REQUEST, $gw_merchantKeyText) )
{
    app_UserPoints::logSale($custom, 'Not verified', __FILE__, __LINE__);
    exit();
}

switch ( $status )
{
    // Transaction Accepted
    case 1:
        if ( app_UserPoints::registerPackageSale($custom, trim($trans_order)) )
        {
            app_UserPoints::logSale($custom, 'approved');
        }
        break;
        
    // Transaction Declined
    case 2:
        app_UserPoints::logSale($custom, 'declined');
        break;
    
    // Error in transaction data or system error
    case 3:
        app_UserPoints::logSale($custom, 'processing');
        break;
}

$__CUSTOM = $custom;
require_once '../post_display_result.php';


/**
 * Checks hash verification.
 * 
 */
function verify( $hash, $values, $gw_merchantKeyText )
{
    $fields = explode( '|', $hash );
    $stringToHash = '';
    for ( $i=0; $i<count($fields)-1; $i++)
        $stringToHash.= $_REQUEST[$fields[$i]].'|';
    
    $stringToHash.= $gw_merchantKeyText;
    
    return ( md5($stringToHash) == $fields[count($fields)-1] );
}
