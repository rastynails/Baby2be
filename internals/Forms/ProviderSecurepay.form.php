<?php

class form_ProviderSecurepay extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('provider_securepay');
	}

	public function setup()
	{
		$sale_info_id = new fieldType_hidden('sale_info_id');
		parent::registerField($sale_info_id);

		$name = new fieldType_text('name');
		parent::registerField($name);
		$name->maxlength = 30;
		$name->setRegExPatterns('/^.+$/');
		 
		$state = new fieldType_text('state');
		parent::registerField($state);
		$state->maxlength = 30;
		$state->setRegExPatterns('/^.+$/');
		
		$city = new fieldType_text('city');
		parent::registerField($city);
		$city->maxlength = 30;
		$city->setRegExPatterns('/^.+$/');
						
		$street = new fieldType_text('street');
		parent::registerField($street);
		$street->maxlength = 30;
		$street->setRegExPatterns('/^.+$/');
		
		$zip = new fieldType_text('zip');
		parent::registerField($zip);
		$zip->maxlength = 10;
		$zip->setRegExPatterns('/^\d+$/');
		
		$email = new fieldType_text('email');
		parent::registerField($email);
		$email->maxlength = 30;
		$email->setRegExPatterns(SK_ProfileFields::get('email')->regexp);
		
		parent::registerAction('formProviderSecurepay_Checkout');
	}
	
	public function renderStart( array $params = null )
	{
		if ( !isset($params['sale_info_id']) ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() missing param "sale_info_id"', E_USER_WARNING);
		}
	
		$this->getField('sale_info_id')->setValue($params['sale_info_id']);
		
		return parent::renderStart($params); 
	}
}

class formProviderSecurepay_Checkout extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('checkout');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('sale_info_id', 'name', 'state', 'city', 'street', 'zip', 'email');
		
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		
		foreach ( $post_data as $key => $data )
			$_SESSION['__SALE_INFO_ARR'][$key] = $data;
		 
		$response->exec('window.location.href="' . URL_CHECKOUT . 'Securepay/send_to_provider.php";');
	
	}
}


