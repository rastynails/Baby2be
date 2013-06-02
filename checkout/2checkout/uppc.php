<?php

if ( $md5 == $md5calc || $fields['demo_mode'] == 'on' ) // MD5 hash fails on demo mode
{
    if ( $reg = app_UserPoints::registerPackageSale($custom, $sale_id) )
    {
        app_UserPoints::logSale($custom, 'APPROVAL', __FILE__, __LINE__);
    }
}
else
{
    app_UserPoints::logSale($custom, 'Checksum mismatch', __FILE__, __LINE__);
    exit();
}