<?php

if ( !isGenuine($hash, $digestKey, $trans_order) )
{
    app_UserPoints::logSale($custom, 'Not verified', __FILE__, __LINE__);
    exit();
}

// approval
if ( strtoupper($status) == 'A' && !$resp_code ) 
{
    if ( app_UserPoints::registerPackageSale($custom, trim($trans_order)) )
    {
        app_UserPoints::logSale($custom, 'approved');
    }
}
// processing
else 
{
    app_UserPoints::logSale($custom, 'processing');
} 


/**
 * Check if the request was sent by SecPay
 * 
 */
function isGenuine( $hash, $digestKey, $trans_id )
{
    return (md5('trans_id='.$trans_id.'&callback='.SITE_URL.'checkout/SecPay/completed.php&'.$digestKey) == $hash);
}

?>
<html>
<body>
    <a href="<?=SK_Navigation::href('points_purchase')?>"><?=SK_Language::section('membership')->text('wait_redirecting');?></a>
    
    <script>
        document.location='<?=SK_Navigation::href('points_purchase');?>';
    </script>
</body>
</html>