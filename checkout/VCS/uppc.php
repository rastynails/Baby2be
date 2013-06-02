<?php

if ( $merchant_fields['merchant_pam'] != $verification_code )
{
    app_UserPoints::logSale($custom, 'Not verified', __FILE__, __LINE__);
    exit("<CallBackResponse>PAM not verified</CallBackResponse>");
}

/**
 * Possible codes returned from VCS
 * APPROVED -- Transaction Accepted
 * -- Transaction Declined
 */
switch ( $status )
{
    case 'APPROVED':
        if ( app_UserPoints::registerPackageSale($custom, trim($trans_order)) )
        {
            app_UserPoints::logSale($custom, 'approved');
            exit("<CallBackResponse>Accepted</CallBackResponse>");
        }        
        break;
        
    default:
        app_UserPoints::logSale($custom, 'declined');
        exit("<CallBackResponse>Declined</CallBackResponse>");
}