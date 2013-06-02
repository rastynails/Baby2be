<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

logRequest(time() . ": " . print_r($_REQUEST, true));

define('BILL_METHOD_TEMP', 5); // start of the transaction
define('BILL_METHOD_PART', 7); // on every successful delivery receipt
define('BILL_METHOD_FULL', 1); // all delivery receipts have been received

$method = $_GET['pay_method'];
$service = $_GET['service'];
$trans_id = $_GET['trans'];

if ( !strlen($custom = trim($_GET['custom'])) )
{
    exit("FAIL");
}

switch ( $method )
{
	case BILL_METHOD_FULL:
		$sale = app_SMSBilling::getPaymentByHash($custom);
		if ( !$sale )
		{
			exit("ERROR");
		}

		if ( $sale['verified'] == 1 ) {
		    exit('COMPLETED');
		}
		
		$service = app_SMSBilling::getService($service);
		if ( !$service )
		{
			exit("ERROR");
		}

		$sale_info = array(
		    'timestamp' => time(),
		    'shortcode' => 'undefined',
		    'order_number' => $trans_id,
		    'amount' => $service['cost'],
		    'msisdn' => 'undefined',
		);

		if ( app_SMSBilling::setPaymentStatusVerified($custom, $sale_info) )
		{
		    exit('COMPLETED');
		}

		break;

	case BILL_METHOD_PART:
		exit("PARTIAL");
		break;

	case BILL_METHOD_TEMP:
		exit("STARTED");
		break;

	default:
		header('HTTP/1.1 500 Internal Server Error');
		echo "500";
		break;
}


function logRequest( $string )
{
	$file = DIR_SITE_ROOT.'checkout/SMS/itelebill/log.txt';
	if ( file_exists($file) )
		$mode = (filesize($file) > 500000) ? "w" : "a";
	else
		$mode = "w";
	
	if ( !$fr = @fopen($file, $mode) )
		return false;
	
	@fwrite($fr, $string);
	fclose($fr);
	
	return true;
}
