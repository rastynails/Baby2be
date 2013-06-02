<?php

if ( !is_verified_p() )
{
    app_UserPoints::logSale($custom, 'Not verified', __FILE__, __LINE__);
    exit();
}

switch ( $subscription_type )
{
    case 'subscr_signup':
    case 'subscr_eot':
        app_UserPoints::logSale($custom, 'txn_type: SIGNUP', __FILE__, __LINE__);
        break;
        
    case 'subscr_cancel':
        app_UserPoints::logSale($custom, 'txn_type: CANCEL', __FILE__, __LINE__);
        break;
        
    case 'subscr_failed':
        app_UserPoints::logSale($custom, 'txn_type: FAILED', __FILE__, __LINE__);
        break;
        
    default:
         
        switch ( strtoupper($status) )
        {
            // means approval
            case 'COMPLETED':       
                if ( app_UserPoints::registerPackageSale($custom, trim($trans_order)) )
                {
                    app_UserPoints::logSale($custom, 'approved');
                }                
                exit();
                break;

            // processing    
            case 'PENDING':
                app_UserPoints::logSale($custom, 'processing');
                exit();
                break;
                
            // declined
            case 'DENIED':
                app_UserPoints::logSale($custom, 'declined');
                exit();
                break;
        }
        break;
}


/**
 * Posts data back to PayPal to validate payment.
 * 
 */
function is_verified_p()
{
    foreach ($_POST as $key => $value )
    {
        $value = urlencode(stripslashes($value));
        $_posts.="$key=$value&";
    }
    $_posts.= 'cmd=_notify-validate';
    
    // post back to PayPal system to validate
    $pp_header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $pp_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $pp_header .= "Content-Length: ".strlen($_posts)."\r\n\r\n";
    $pp_fp = fsockopen ('www.paypal.com', 80, $pp_errno, $pp_errstr, 30);
    
    if ( !$pp_fp )
    {
        app_UserPoints::logSale($custom, "fsockopen error. Num: $pp_errno; Comment: $pp_errstr", __FILE__, __LINE__);
        return false;
    }
    
    fputs($pp_fp, $pp_header.$_posts);
    
    while ( !feof($pp_fp) )
        $_str .= trim( fgets($pp_fp, 2048) );
    
    fclose($pp_fp);

    app_UserPoints::logSale($custom, "payPal response: $_str", __FILE__, __LINE__);

    if ( strstr($_str, 'VERIFIED') !== false )
        return true;
    else
        return false;
}
