<?php

require_once DIR_CHECKOUT . 'SMS' . DIRECTORY_SEPARATOR . 'DaoPay' . DIRECTORY_SEPARATOR . 'DaoPay.class.php';

class component_sms_DaoPay extends SK_Component 
{
	private $service_key;
	
	private $appcode;
	
	private $prodcode;
	
	private $frame_width;
	private $frame_height;
	
	public function __construct( array $params = null ) 
	{
		if ( !isset($params['service_key']) ) {
			$this->annul();
		}
		else {
			$this->service_key = trim($params['service_key']);
		}

		$this->frame_width = isset($params['width']) ? trim($params['width']) : 415;
		$this->frame_height = isset($params['height']) ? trim($params['height']) : 530;	
		
		$this->appcode = app_SMSBilling::getProviderMerchantKey('DaoPay');
		$this->prodcode = app_SMSBilling::getServiceField($this->service_key, 'prodcode');
		
		parent::__construct('sms_daopay');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{	
		$handler = new SK_ComponentFrontendHandler('sms_DaoPay');
		$success_url = sk_make_url();
		$hash = md5(time());
		
		$handler_params = array(
			'appcode' => $this->appcode,
			'prodcode' => $this->prodcode,
			'width' => $this->frame_width, 
			'height' => $this->frame_height,
			'url' => $success_url,
			'hash' => $hash,
			'service_key' => $this->service_key
		);
		
		$handler->construct($handler_params);			
		$this->frontend_handler = $handler;
		
		// handle payment
		if (strlen(SK_HttpRequest::$GET['custom'])) {
			$status = DaoPay::handlePayment(SK_HttpRequest::$GET, $this->service_key, $this->appcode);
			$text_ns = SK_Language::section('components.sms_daopay'); 
			
			switch ($status)
			{
				case 1:
					$service_info = app_SMSBilling::getService($this->service_key);
					$sale_info = array(
						'amount' => $service_info['cost'],
						'order_number' => isset(SK_HttpRequest::$GET['orderno']) ? SK_HttpRequest::$GET['orderno'] : 'undefined'
					);
					
					if (app_SMSBilling::setPaymentStatusVerified(trim(SK_HttpRequest::$GET['custom']), $sale_info))
						$Frontend->onload_js('SK_drawMessage("'.$text_ns->text('completed').'","message");');
						$Frontend->onload_js('window.location="'.SK_Navigation::href('home').'";');
					break;
				case -1:
					$Frontend->onload_js('SK_drawMessage("'.$text_ns->text('invalid_prodcode').'","notice");');
					break;
				case -2:
					$Frontend->onload_js('SK_drawMessage("'.$text_ns->text('invalid_pin').'","notice");');
					break;	
				case -3:
					$Frontend->onload_js('SK_drawMessage("'.$text_ns->text('connection_error').'","notice");');
					break;
			}
		}

		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign('appcode', $this->appcode);
		$Layout->assign('prodcode', $this->prodcode);
					
		return parent::render($Layout);
	}
	
	public static function ajax_registerPaymentAttempt($params, SK_ComponentFrontendHandler $handler)
	{
		$sale_info = array();
		foreach ($params->params as $key => $item)
			$sale_info[$key] = $item;		
		
		$sale_info['profile_id'] = SK_HttpUser::profile_id();
		$sale_info['timestamp'] = time();
		
		if (isset($sale_info['hash']) )
			app_SMSBilling::registerPaymentAttempt($sale_info);
	}
		
}
