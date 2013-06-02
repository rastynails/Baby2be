<?php

$file_key = 'payment_providers';
$active_tab = 'payment_providers';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

//require_once( DIR_INC.'fnc.custom.php' );
require_once( DIR_ADMIN_INC.'fnc.finance.php' );
//require_once( DIR_APP.'app.finance.php' );

$frontend = new AdminFrontend( );
require_once( 'inc.admin_menu.php' );

if ( ( @$_POST['command'] == 'save' ) )
{
	if( strlen($_FILES['icon']['name']) )
	{
		// get old image 
		$old_icon = getPaymentProviderInfo($_POST['provider_id']);
		
		$fileName = 'provider_' . $_POST['provider_id'] . '_' . rand( 1,999 ).'.png';
		
		try {
			app_Image::resize($_FILES['icon']['tmp_name'], 70, 70, false, DIR_USERFILES . $fileName);
			app_Image::convert(DIR_USERFILES . $fileName, IMAGETYPE_PNG);
		}
		catch (SK_ImageException $e) {
			$code = $e->getCode();

			switch ($code) {
				case app_Image::ERROR_WRONG_IMAGE_TYPE: 
					$frontend->RegisterMessage( 'Can not create undefined type image, only jpeg, gif and png allowed', 'notice' );
					break;
				case app_Image::ERROR_GD_LIB_NOT_INSTALLED: 
					$frontend->RegisterMessage( 'Your server does not support GD library', 'notice' );
					break;
				default:
					$frontend->RegisterMessage( 'System file upload error', 'notice' );
					break;
			}
			redirect( $_SERVER['REQUEST_URI'] );
		}
		
		$result = updatePaymentProvider( $_POST['provider_id'], $_POST['status'], $_POST['currency'], $_POST['fields'], $_POST['plans'], $_POST['packages'], $fileName );
		
		@unlink($_FILES['icon']['tmp_name']);
		@unlink(DIR_USERFILES . $old_icon['icon']);
	}
	else
	{
		$result = updatePaymentProvider( $_POST['provider_id'], $_POST['status'], $_POST['currency'], $_POST['fields'], @$_POST['plans'], $_POST['packages'] );
		component_PaymentSelection::clearCompile();
		component_PointsPurchase::clearCompile();
	}
	
	if( $result )
		$frontend->registerMessage( 'Payment system details updated' );
	else
		$frontend->registerMessage( 'Update failed', 'error' );
	
	$prov_qs = isset($_POST['provider_id']) ? '?provider_id='. $_POST['provider_id']: '';
	redirect( URL_ADMIN .'payment_providers.php'.$prov_qs );
}
elseif ( ( @$_POST['command'] == 'delete_icon' ) )
{
	deleteProviderIcon($_POST['provider_id']);
	redirect( $_SERVER['REQUEST_URI'] );
}

// get payment providers
$payment_providers = getPaymentProviders();
$providers_js = '';
foreach ( $payment_providers as $key => $provider )
{
	$payment_providers[$key]['currencies'] = explode( ',' ,$provider['currencies'] );
	
	$provider_js = '';
	foreach ( $provider['fields'] as $field )
	{
		if ( $field['regexp'] )
			$provider_js.= "if( !".$field['regexp'].".exec( $('field_{$provider['fin_payment_provider_id']}_{$field['name']}').value ) ) return fail_alert( $('field_{$provider['fin_payment_provider_id']}_{$field['name']}'), 'Invalid value for ".$field['name']."' ); \n";
	}
	
	if( $provider_js && $provider['is_available'] == 'y' )
	{
		$providers_js.= '$( \'provider_form_'.$provider['fin_payment_provider_id'].'\' ).onsubmit = function(){
			'.$provider_js.'
		}
		';
	}
}

$manuals = array(
    3  => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#paypal-ipn',
    4  => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#card-pay-using-ccbill',
    5  => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#online-pay-using-ccbill',
    6  => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#securepay',
    7  => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#authorize',
    8  => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#secpay',
    9  => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#card-pay-using-network-merchants',
    10 => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#virtual-card-services',
    18 => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#icepay',
    33 => 'http://www.skalfa.com/ca/docs/introduction_configuration_providers#modeybookers-skrill',
    37 => 'https://www.skalfa.com/ca/docs/introduction_configuration_providers#2checkout'
);

$frontend->assign('manuals', $manuals);

$frontend->registerOnloadJS($providers_js);
$frontend->assign( 'provider_arr', $payment_providers );
$frontend->assign('curr', SK_Language::text('%label.currency_sign'));

if ( isset($_GET['provider_id']) )
{
    $frontend->assign('expanded', $_GET['provider_id']);
}

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'opacity.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'payment_providers.js' );

$_page['title'] = "Payment Gateways";

$frontend->display( 'payment_providers.html' );

