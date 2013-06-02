<?php

if ( $checksum != $IC_CheckSum )
{
    app_UserPoints::logSale($custom, 'Checksum mismatch', __FILE__, __LINE__);
    exit();
}

switch ( strtoupper($status) )
{
    case 'OK':
        if ( $reg = app_UserPoints::registerPackageSale($custom, trim($trans_order)) )
        {
            app_UserPoints::logSale($custom, 'APPROVAL', __FILE__, __LINE__);
        }
        break;

    case 'OPEN':
    case 'REFUND':
    case 'CBACK':
        // system
        exit();

    case 'ERR':
        app_UserPoints::logSale($custom, 'ERROR: ' . $statusCode);
        exit();

    default:
        app_UserPoints::logSale($custom, 'ERROR');
        exit();
}