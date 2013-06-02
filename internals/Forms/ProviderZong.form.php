<?php

require_once DIR_CHECKOUT . 'SMS' . DIRECTORY_SEPARATOR . 'Zong' . DIRECTORY_SEPARATOR . 'PayByZong.class.php';

class form_ProviderZong extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('provider_zong');
	}

	public function setup()
	{	
		$code = new fieldType_text('code');
		$code->setRegExPatterns("/\+[0-9]{5,15}$/");
		parent::registerField($code);
		
		$pin = new fieldType_text('pin');
		parent::registerField($pin);

		$transaction_id = new fieldType_hidden('transaction_id');
		parent::registerField($transaction_id);
		
		$service_id = new fieldType_hidden('service_id');
		parent::registerField($service_id);
		
		$hash = new fieldType_hidden('hash');
		parent::registerField($hash);
		
		$service_key = new fieldType_hidden('service_key');
		parent::registerField($service_key);
		
		$market_id = new fieldType_hidden('market_id');
		parent::registerField($market_id);

		parent::registerAction('form_ProviderZong_Send');
		parent::registerAction('form_ProviderZong_CheckPin');
	}
	
	public function renderStart( array $params = null )
	{			
		if ( isset($params['market_id']) ) {
			$this->getField('market_id')->setValue($params['market_id']);
		}
	
		return parent::renderStart($params); 
	}
}

class form_ProviderZong_Send extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('send');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('code', 'service_id', 'service_key', 'market_id');
				
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$phone_number = trim($post_data['code']);
		$service_id = trim($post_data['service_id']);
		$service_key = trim($post_data['service_key']);
		$market_id = trim($post_data['market_id']);
		$api_key = app_SMSBilling::getProviderMerchantKey('Zong');
				
		if (!strlen($phone_number) || !strlen($api_key) || !strlen($service_id) || !strlen($service_key) || !strlen($market_id))
			$response->addError("Error. Required parameter not specified");
			
		try {
			$sms_provider = new PayByZong($api_key, $service_id);
			
			if ($sms_provider->zong_validatePhoneNumber($phone_number)) {
				
				$hash = md5(time());
				$service = app_SMSBilling::getService($service_key);
				$sale_info = array(
					'hash' => $hash,
					'service_key' => $service_key,
					'amount' => $service['cost'],
					'profile_id' => SK_HttpUser::profile_id(),
					'msisdn' => $phone_number,
					'timestamp' => time()
				);
				
				if (app_SMSBilling::registerPaymentAttempt($sale_info)) {
					$transaction = $sms_provider->zong_getTransactionId($market_id, $phone_number, $hash, $service['description']);
					
					// for debug: ---
					/*$transaction = array(
						'transaction_id' => 1241215,
						'pincode' => 'pincode'
					);*/
					// ---
					
					$transaction['status'] = 'checked';
					$transaction['hash'] = $hash;

					return $transaction;
				}
			}
			
		} catch (SK_PayByZongException $e) {
			$response->addError(SK_Language::section('components.sms_zong.msg')->text($e->getErrorKey()));
		}
	}
}

class form_ProviderZong_CheckPin extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('ok');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('pin', 'transaction_id', 'service_id' , 'hash');
				
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$pin = trim($post_data['pin']);
		$transaction_id = trim($post_data['transaction_id']);
		$service_id = trim($post_data['service_id']);
		$hash = trim($post_data['hash']);
		$api_key = app_SMSBilling::getProviderMerchantKey('Zong');

		if (!strlen($pin) || !strlen($transaction_id) || !strlen($api_key) || !strlen($service_id) || !strlen($hash))
			$response->addError("Error. Required parameter not specified");
			
		try {
			$sms_provider = new PayByZong($api_key, $service_id);
			
			if ($sms_provider->zong_confirmPincode($transaction_id, $pin)) {
				$response->addMessage(SK_Language::section('components.sms_zong.msg')->text('pincode_confirmed'));
				return array('status' => 'confirmed', 'hash' => $hash);
			}
			
		} catch (SK_PayByZongException $e) {
			$response->addError(SK_Language::section('components.sms_zong.msg')->text($e->getErrorKey()));
		}
	}
}
