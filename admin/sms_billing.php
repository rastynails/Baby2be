<?php

$file_key = 'payment_providers';
$active_tab = 'sms_billing';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.sms_billing.php' );

$frontend = new AdminFrontend( );
require_once( 'inc.admin_menu.php' );

switch ($_POST['command'])
{
	case 'activate':
		if (intval($_POST['provider_id']))
			if (SMS_Billing::activateProvider(intval($_POST['provider_id'])))
				$frontend->RegisterMessage( 'Provider activated', 'message' );
		redirect( $_SERVER['REQUEST_URI'] );
		break;

	case 'disable':
		if (intval($_POST['provider_id']))
			if (SMS_Billing::disableProvider(intval($_POST['provider_id'])))
				$frontend->RegisterMessage( 'Provider disabled', 'message' );
		redirect( $_SERVER['REQUEST_URI'] );
		break;
		
	case 'save':
		if (SMS_Billing::updatePaymentProvider($_POST['provider_id'], $_POST['merchant_key'], $_POST['fields']))
			$frontend->RegisterMessage( 'Provider fields were updated', 'message' );
		redirect( $_SERVER['REQUEST_URI'] );
		break;
		
	case 'save_services':
		if (SMS_Billing::updateSMSServices($_POST['services']))
			$frontend->RegisterMessage( 'Services were updated', 'message' );
		else 
			$frontend->RegisterMessage( 'Please activate SMS payment provider', 'notice' );
		redirect( $_SERVER['REQUEST_URI'] );
		break;
		
	case 'save_services_fields':
		if (SMS_Billing::updateServiceFields($_GET['service'], $_POST['sf']))
			$frontend->RegisterMessage( 'Service fields were updated', 'message' );
		redirect( URL_ADMIN . 'sms_billing.php?service='.$_GET['service'] );
		break;
}

// get payment providers
$payment_providers = SMS_Billing::getSMSProviders();

// get SMS services
$services = SMS_Billing::getSMSServices();

if ($_GET['service']) {
	$serv = trim($_GET['service']);
	$frontend->assign('service_info', SMS_Billing::getServiceInfo($serv));
	$services_fields = SMS_Billing::getServiceFields($serv);
	$frontend->assign('services_fields', $services_fields);
}
	
$frontend->assign('provider_arr', $payment_providers);
$frontend->assign('service_arr', $services);

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'opacity.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'payment_providers.js' );

$_page['title'] = "SMS Billing";

$frontend->display( 'sms_billing.html' );
