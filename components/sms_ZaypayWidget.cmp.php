<?php

require_once 'Zaypay.class.php';


class component_sms_ZaypayWidget extends SK_Component 
{
	private $service_key;
	
	private $custom;
	
	private $Zaypay;
		
	public function __construct( array $params = null ) 
	{
		if ( !isset($params['service_key']) || !isset($params['custom']) ) 
		{
			$this->annul();
		}
		else 
		{
			$this->service_key = trim($params['service_key']);
			$this->custom = trim($params['custom']);
			
			$price_setting_id = app_SMSBilling::getServiceField($this->service_key, 'price_setting_id');
            $price_setting_key = app_SMSBilling::getServiceField($this->service_key, 'price_setting_key');
            
            $this->Zaypay = New Zaypay($price_setting_id, $price_setting_key);
		}
		
		parent::__construct('sms_zaypay_widget');
	}
	
	
    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {   
        $handler = new SK_ComponentFrontendHandler('sms_ZaypayWidget');

        $handler->construct();           
        $this->frontend_handler = $handler;

        parent::prepare($Layout, $Frontend);
    }

    
	public function render( SK_Layout $Layout )
	{
	    // step 4: check payment

        if ( isset($_POST['action']) && $_POST['action'] == 'paid' && isset($_POST['paymentid']) ) 
        {
            $zaypay_info = $this->Zaypay->show_payment($_POST['paymentid']);
        
            $status = $zaypay_info['payment']['status'];
        
            if ( isset($zaypay_info['payment']['verification-needed']) 
                    and $zaypay_info['payment']['verification-needed'] == 'true' 
                    and isset($_POST['verification_code']) ) 
            {
                if ( $zaypay_info = $this->Zaypay->verification_code($_POST['paymentid'], $_POST['verification_code']) ) 
                {
                    $status = $zaypay_info['payment']['status'];
                }
            }
            
            $Layout->assign('zaypay_info', $zaypay_info);
        
            if ( $status == 'paid')
            {
                $Layout->assign('page', 3); // paid
                $this->Zaypay->mark_payload_provided($_POST['paymentid']);
            }
            elseif ( $status == 'prepared' or $status == 'in_progress' or $status == 'paused' ) 
            {
                $Layout->assign('page', 2); // pay
            }
            else
            {
                $Layout->assign('error', "An error has occured [{$status}]");
            }
        }
        
        // step 3: let consumer pay
        elseif ( isset($_POST['action']) && $_POST['action'] == 'pay' && isset($_POST['locale']) && isset($_POST['paymentmethod']) ) 
        {
            if ( !($zaypay_info = $this->Zaypay->create_payment($_POST['locale'], $_POST['paymentmethod'], $this->custom)) )
            {
                $Layout->assign('error', $this->Zaypay->getError());
                return;
            }
            
            $Layout->assign('zaypay_info', $zaypay_info);
            
            $sale_info = array(
                'profile_id' => SK_HttpUser::profile_id(),
                'hash' => $this->custom,
                'service_key' => $this->service_key,
                'timestamp' => time(),
                'order_number' => $this->Zaypay->getPaymentId()
            );
                
            app_SMSBilling::registerPaymentAttempt($sale_info);
        
            $Layout->assign('page', 2); // pay
        }
        
        // step 1: let consumer choose country and language
        else 
        {
            if ( !($locales = $this->Zaypay->list_locales()) ) 
            {
                $Layout->assign('error', $this->Zaypay->getError());
                return;
            }
        
            if ( isset($_POST['locale_country']) and isset($_POST['locale_language']) ) 
            {
                $this->Zaypay->setLocale($_POST['locale_language'] . '-' . $_POST['locale_country']);
            }
            else 
            {
                $this->Zaypay->locale_for_ip($_SERVER['REMOTE_ADDR']);
            }
        
            if ( !($payment_methods = $this->Zaypay->list_payment_methods($this->Zaypay->getLocale())) )
            {
                $Layout->assign('error', $this->Zaypay->getError());
                return;
            }
            
            $Layout->assign('locales', $locales);
            $Layout->assign('payment_methods', $payment_methods);
            $Layout->assign('page', 1); // choose method
        }
        
        $Layout->assign('Zaypay', $this->Zaypay);
        
        $Layout->assign('ps_id', app_SMSBilling::getServiceField($this->service_key, 'price_setting_id'));
	    
		return parent::render($Layout);
	}
}
