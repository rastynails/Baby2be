<?php

if ( $_REQUEST['Status'] == 'ERR' )
{
    require_once '../../internals/Header.inc.php';

    $msg = $_REQUEST['StatusCode'] == 'Cancelled' ? 'Payment cancelled' : 'Payment processing error: ' . trim($_REQUEST['ErrCode']);
 
    echo 
        '<html>
            <head><title>Status</title></head>
            <body>
                ' . $msg . 
                '<br /><a href="' . SITE_URL . 'member/payment_selection.php">Return to site</a>      
            </body>
        </html>';
}
else 
{
    $__CUSTOM = trim($_REQUEST['Reference']);

    require '../post_display_result.php';
}