<?php

class httpdoc_CheckoutExtra extends SK_HttpDocument
{
	public function __construct( array $params = null )
	{
		parent::__construct('checkout_extra');
		
		//$this->cache_lifetime = 3600;
	}

	public function prepare( SK_Layout $Layout, SK_Frontend $handler )
	{
		parent::prepare($Layout, $handler);
		
		$provider = @SK_HttpRequest::$GET['provider'];
		
		if (isset($provider))
		{
			$check_provider = app_Finance::checkIfProviderExtraFieldsRequired($provider);
			
			if ($check_provider) {	
				$class = 'component_Provider'.$provider;
				$Layout->assign('PaymentProvider', new $class);	
			}
		}
	}
	
	public function render( SK_Layout $Layout )
	{
	
		return parent::render($Layout);
	}
}
