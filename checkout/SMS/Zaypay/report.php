<?php

// log
$fp = fopen('log.txt', 'w+');
fputs($fp, print_r($_REQUEST, true));

require_once dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

require_once 'Zaypay.class.php';

if ( !strlen($custom = trim($_GET['custom'])) )
{
    die("*fail*");
}
   
$sale = app_SMSBilling::getPaymentByHash($custom);

$price_setting_id = app_SMSBilling::getServiceField($sale['service_key'], 'price_setting_id');
$price_setting_key = app_SMSBilling::getServiceField($sale['service_key'], 'price_setting_key');
            
$Zaypay = new Zaypay($price_setting_id, $price_setting_key);
             
if ( isset($_GET['payment_id']) ) 
{
    $zaypay_info    = $Zaypay->show_payment($_GET['payment_id']);  
    
    $payment_id     = $zaypay_info['payment']['id'];
    $payment_status = $zaypay_info['payment']['status'];
    
    $service = app_SMSBilling::getService($sale['service_key']);
    
    if ( $payment_status == 'paid' )
    {
        $sale_info = array(
            'timestamp' => time(),
            'shortcode' => isset($zaypay_info['payment']['paycode']) ? $zaypay_info['payment']['paycode'] : 'undefined',
            'order_number' => $_GET['payment_id'],
            'amount' => $service['cost'],
            'msisdn' => isset($zaypay_info['payment']['number']) ? $zaypay_info['payment']['number'] : 'undefined',
        );
        
        fputs($fp, print_r($zaypay_info, true));
        fclose($fp);
        
        if ( app_SMSBilling::setPaymentStatusVerified($custom, $sale_info) ) 
        {
            die ('*ok*');              
        }
    }
    
    die ('*ok*');
}