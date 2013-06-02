<?php

require_once( '../../internals/Header.inc.php' );

$custom = trim($_REQUEST['custom']);

$trans_order = $_REQUEST['rec_payment_id'] ? trim($_REQUEST['rec_payment_id']) : trim($_REQUEST['mb_transaction_id']);
$status = trim($_REQUEST['status']);

$amount = ( $_REQUEST['amount'] != '' )? $_REQUEST['amount'] : $_REQUEST['rec_amount'];
$sig = trim($_REQUEST['md5sig']);

//logRequest(print_r($_REQUEST, true), 'log.txt');

$merchant_fields = app_Finance::getPaymentProviderFields("Moneybookers");
$merchant_id = $merchant_fields['merchant_email'];
$secret_word = $merchant_fields['secret_word'];

$slug = strtoupper(md5($merchant_id . $_REQUEST['mb_transaction_id'] . strtoupper(md5($secret_word)) . $_REQUEST['mb_amount'] . $_REQUEST['mb_currency'] . $status));

//logRequest($slug, 'log.txt');

// check sale info id:
if ( !strlen($custom) )
    exit();

app_Finance::LogPayment( 'TIME: '.date("Y-m-d H:i:s", time())."; \n".print_r($_REQUEST, true), __FILE__, __LINE__ );

if ( !is_verified($slug, $sig) )
{
    app_Finance::LogPayment( 'Not verified', __FILE__, __LINE__, $custom );
    exit();
}


switch ( $status )
{
    case '2':
        if ( !is_numeric($amount) || !strlen($trans_order) )
        {
            app_Finance::SetTransactionResult( $custom, 'error' );
            app_Finance::LogPayment( "Incorrect data: amount: $amount OR order num: $trans_order", __FILE__, __LINE__, $custom );
            exit();
        }
        
        if ( app_Finance::SetTransactionResult( $custom, 'approval', $amount, $trans_order ) )
        {
            app_Finance::LogPayment( 'APPROVAL', __FILE__, __LINE__ );
            $__CUSTOM = $custom;

            require_once( '../post_checkout.php' );
        }
        app_Finance::LogPayment( 'END', __FILE__, __LINE__, $custom );
        exit();
        break;
        
    case '0':
        app_Finance::SetTransactionResult( $custom, 'processing', $amount, $trans_order );
        app_Finance::LogPayment( 'PENDING', __FILE__, __LINE__, $custom );
        exit();
        break;

    case '-1':
        app_Finance::LogPayment( 'CANCELLED', __FILE__, __LINE__, $custom );
        app_Finance::SetTransactionResult( $custom, 'declined', $amount, $trans_order );
        exit();
        break;

    case '-2':
        app_Finance::LogPayment( 'DENIED', __FILE__, __LINE__, $custom );
        app_Finance::SetTransactionResult( $custom, 'declined', $amount, $trans_order );
        exit();
        break;

    default:
        app_Finance::SetTransactionResult( $custom, 'error' );
        exit();
} 

function is_verified( $slug, $sig )
{
    return $slug == $sig;
}


function logRequest($string, $filename)
{
    $file = DIR_SITE_ROOT.'checkout/Moneybookers/'.$filename;
    
    if ( file_exists($file) )
        $mode = filesize($file) > 500000 ? "w" : "a";
    else
        $mode = "w";
    
    if ( !$fr = @fopen($file, $mode) )
        return false;
    
    @fwrite($fr, $string);
    
    fclose($fr);
    
    return true;
}

