<?php

class component_sms_Zaypay extends SK_Component 
{
	private $service_key;
		
	public function __construct( array $params = null ) 
	{
		if ( !isset($params['service_key']) ) {
			$this->annul();
		}
		else {
			$this->service_key = trim($params['service_key']);
		}

		$this->frame_width = isset($params['width']) ? trim($params['width']) : 560;
		$this->frame_height = isset($params['height']) ? trim($params['height']) : 415;
		
		parent::__construct('sms_zaypay');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{	
		$handler = new SK_ComponentFrontendHandler('sms_Zaypay');
		$success_url = sk_make_url();
		$hash = md5(time());
		
		$handler_params = array(
			'service_key' => $this->service_key,
			'width' => $this->frame_width, 
			'height' => $this->frame_height,
			'url' => $success_url,
            'widget_url' => URL_SMS_PROVIDERS . 'Zaypay/widget_iframe.php?service_key='.$this->service_key.'&custom='.$hash,
			'custom' => $hash,			
		);
		
		$handler->construct($handler_params);			
		$this->frontend_handler = $handler;
		
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{				
		return parent::render($Layout);
	}
	
	public static function ajax_registerPaymentAttempt( $params, SK_ComponentFrontendHandler $handler )
	{
		$sale_info = array();
		foreach ( $params as $key => $item )
			$sale_info[$key] = $item;		
		
		$sale_info['profile_id'] = SK_HttpUser::profile_id();
		$sale_info['timestamp'] = time();
		
		if ( isset($sale_info['hash']) )
		{
			app_SMSBilling::registerPaymentAttempt($sale_info);
		}
	}		
}
