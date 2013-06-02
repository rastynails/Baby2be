<?php

if ( !defined('DIR_INTERNALS') )
    exit("No direct access allowed");

$merchant_fields = app_Finance::getPaymentProviderFields(7);

// verify if MD5 Hash == md5(md5Hash.loginId.orderId.amount)
$slug = strtoupper(md5($merchant_fields['md5_hash'].$merchant_fields['login_id'].$trans_order.$amount));
if ( $md5_hash !=  $slug)
{
    app_UserPoints::logSale($__CUSTOM, "Incorrect md5 hash: $md5_hash != " . $slug, __FILE__, __LINE__);
    exit("1");
}

switch ( $status )
{
    // approved
    case 1:
        if ( $reg = app_UserPoints::registerPackageSale($__CUSTOM, trim($trans_order)) )
        {
            app_UserPoints::logSale($__CUSTOM, 'approved');
            //require_once( '../uppc.php' );
        }
        break;

    // declined
    case 2:
        app_UserPoints::logSale($__CUSTOM, 'declined');
        break;
    
    // pending
    case 4:
        app_UserPoints::logSale($__CUSTOM, 'processing');
        break;
        
    default: 
        app_UserPoints::logSale($__CUSTOM, 'error');
        break;
}

echo '<html><head><title>'.SK_Language::section('membership')->text('wait_redirecting').'</title></head>
    <body>
        <a href="'.SITE_URL.'checkout/Authorize/completed.php?custom='.$__CUSTOM.'">'.SK_Language::section('membership')->text('wait_redirecting').'</a>
        
        <script language="JavaScript">
        document.location=\''.SITE_URL.'checkout/Authorize/completed.php?custom='.$__CUSTOM.'\';
        </script>
    </body></html>';
