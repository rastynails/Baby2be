<?php

class component_SMSServices extends SK_Component
{
	private $service_name; 
	
	/**
	 * Component SMSServices constructor.
	 *
	 * @return component_SMSServices
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('sms_services');
		
		if (!app_Features::isAvailable(32)) 
			$this->annul();
		else {
			$this->service_name = trim($params['service']);
		}
	}
		
	public function render( SK_Layout $Layout )
	{		
		$provider = app_SMSBilling::getActiveProvider();
		
		if (!is_array($provider))
			$Layout->assign('message', 'No SMS provider activated');
			
		else {
			if ($this->service_name) {
				$service_info = app_SMSBilling::getService($this->service_name);
				$Layout->assign('service_info', $service_info);
			} else {
				$services = app_SMSBilling::getActiveServices();
				$Layout->assign('services', $services);
			}
			
			$showPrice = true;
			switch ($provider['name'])
			{
				case 'DaoPay':
					$Layout->assign("sms_PaymentProvider", new component_sms_DaoPay(array('service_key' => $this->service_name)));
					break;
					
                case 'Zaypay':
                    $Layout->assign("sms_PaymentProvider", new component_sms_Zaypay(array('service_key' => $this->service_name)));
                    $showPrice = false;
                    break;
                    
                case 'itelebill':
                    $Layout->assign("sms_PaymentProvider", new component_sms_Itelebill(array('service_key' => $this->service_name)));
                    break;
			}
			
			$Layout->assign('show_price', $showPrice);
		}
		
		return parent::render($Layout);
	}

}
