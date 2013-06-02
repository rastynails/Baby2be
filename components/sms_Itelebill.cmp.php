<?php

require_once DIR_CHECKOUT . 'SMS' . DIRECTORY_SEPARATOR . 'itelebill' . DIRECTORY_SEPARATOR . 'Itelebill.class.php';

class component_sms_Itelebill extends SK_Component
{
    private $service_key;

    private $customer;

    private $zone;

    private $price;

    private $secret;

    private $frame_width;
    
    private $frame_height;

    public function __construct( array $params = null )
    {
        if ( !isset($params['service_key']) ) 
        {
            $this->annul();
        }
        else 
        {
            $this->service_key = trim($params['service_key']);
        }

        $service = app_SMSBilling::getService($this->service_key);

        $this->frame_width = isset($params['width']) ? trim($params['width']) : 415;
        $this->frame_height = isset($params['height']) ? trim($params['height']) : 530;

        $this->customer = app_SMSBilling::getProviderMerchantKey('itelebill');
        $this->zone = app_SMSBilling::getServiceField($this->service_key, 'zone_id', 'itelebill');
        $this->price = $service['cost'];
        $this->secret = app_SMSBilling::getProviderField('api_key', 'itelebill');

        parent::__construct('sms_itelebill');
    }

    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $handler = new SK_ComponentFrontendHandler('sms_Itelebill');
        $success_url = sk_make_url();
        $hash = md5(time());

        $country = self::getCountry();
        
        $handler_params = array(
			'src' => Itelebill::getIframeSrc($this->zone, $this->customer, $this->secret, $this->price, $hash, $this->service_key, $country),
			'width' => $this->frame_width, 
			'height' => $this->frame_height,
			'url' => $success_url,
			'hash' => $hash,
			'service_key' => $this->service_key
        );

        $handler->construct($handler_params);
        $this->frontend_handler = $handler;

        // handle payment
        if ( strlen(SK_HttpRequest::$GET['custom']) ) 
        {
            if ( app_SMSBilling::paymentIsVerified(SK_HttpRequest::$GET['custom']) )
            {
                $Frontend->onload_js('SK_drawMessage("'.SK_Language::text('%membership.transaction_approval').'","message");');
                $Frontend->onload_js('window.location="'.SK_Navigation::href('home').'";');
            }
            else
            {
                $Frontend->onload_js('SK_drawMessage("'.SK_Language::text('%membership.transaction_processing').'","notice");');
            }
        }

        parent::prepare($Layout, $Frontend);
    }

    public function render( SK_Layout $Layout )
    {
        $Layout->assign('customer', $this->customer);
        $Layout->assign('site', $this->zone);
         
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
    
    private static function getCountry()
    {
        $countryByIp = app_Location::getCountryByIp($_SERVER['REMOTE_ADDR']);
        if ( strlen($countryByIp) )
        {
            return strtolower($countryByIp);
        }
    
        $profile_id = SK_HttpUser::profile_id();
        $country = app_Profile::getFieldValues($profile_id, 'country_id');
        
        if ( strlen($country) )
        {
            return strtolower($country);
        }
        
        return "gb";
    }
}
