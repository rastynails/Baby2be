<?php

/**
 * N - declined
 * Y - approved
 */
switch ( strtoupper($status) )
{
    case 'Y':
        if ( app_UserPoints::registerPackageSale($custom, trim($trans_order)) )
        {
            app_UserPoints::logSale($custom, 'approved');
        }
        break;
        
    case 'N':
        app_UserPoints::logSale($custom, 'declined');
        break;
        
    // error
    default:
        app_UserPoints::logSale($custom, 'error');
}