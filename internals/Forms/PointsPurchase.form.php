<?php

class form_PointsPurchase extends SK_Form 
{
	public function __construct()
	{
		parent::__construct('points_purchase');
	}

	public function setup()
	{
		$package_id = new fieldType_custom_select('package_id');
		parent::registerField($package_id);

		$provider = new fieldType_select('provider_id');
		$provider->setType('select');
		parent::registerField($provider);
		
		$payment_providers = app_Finance::GetPaymentProviders();
		foreach ($payment_providers as $prov_id)
			$providers[] = $prov_id['fin_payment_provider_id'];
			
		$provider->setValues($providers);
		
		parent::registerAction('formPointsPurchase_Purchase');
		
		if (isset($_SESSION['order_item']))
            unset($_SESSION['order_item']);
	}
}

class formPointsPurchase_Purchase extends SK_FormAction 
{
	public $provider_id;
	public $package_id;
	public $is_recurring; 
	
	public function __construct()
	{
		parent::__construct('purchase');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('package_id', 'provider_id');
		
		parent::setup($form);
	}

	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form )
	{
	    $provider_id = (int)$post_data['provider_id'];
	    $package_id = (int)$post_data['package_id'];
	    
	    $custom = md5(rand(100, 100000000));
	    $__SALE_INFO_ARR['custom'] = $custom;
	    
	    $provider = app_Finance::GetPaymentProviderInfo($provider_id);
	    $__SALE_INFO_ARR['provider_info'] = $provider;	    
	    $__SALE_INFO_ARR['provider_name'] = $provider['name'];
	    $__SALE_INFO_ARR['currency'] = $provider['active_currency'];
	    $__SALE_INFO_ARR['membership_type_id'] = $package_id;
	    $__SALE_INFO_ARR['membership_type_plan_id'] = $package_id;
	    
	    $package = app_UserPoints::getPackageById($package_id);
	    
	    $__SALE_INFO_ARR['price'] = $package['price'];
	    $__SALE_INFO_ARR['period'] = 30;
	    $__SALE_INFO_ARR['units'] = 'days';
	    $__SALE_INFO_ARR['is_recurring'] = 'n';
	    
        $__SALE_INFO_ARR['membership_type_name'] = SK_Language::text('%user_points.package', array('points' => $package['points'], 'price' => SK_Language::text('%label.currency_sign').$package['price']));
        $__SALE_INFO_ARR['membership_description'] = SK_Language::text('%user_points.packages.package_'.$package_id);
        
        $provider_product = app_UserPoints::getProviderProductIdForUserPointPackage($provider_id, $package_id);
        
        if ( $provider['is_required_plan_synchronizing'] == 'y' &&  !$provider_product )
        {
            $response->addMessage(SK_Language::section('forms.payment_selection.error_msg')->text('plan_not_configured'));
            return;
        }
        
        $__SALE_INFO_ARR['provider_plan_id'] = $provider_product;
        
        // store unverified payment in DB
        $sale = app_UserPoints::preparePackageSale(SK_HttpUser::profile_id(), $package_id, $provider_id, $custom);
		
        if ( $sale )
        {
            $__SALE_INFO_ARR['sale_info_id'] = $custom;
            
            $__SALE_INFO_ARR['site_name'] = SK_Config::section('site.official')->get('site_name');
            
            $_SESSION['__SALE_INFO_ARR'] = $__SALE_INFO_ARR;
            $_SESSION['order_item'] = 'points_package';
            
            $response->exec('window.location.href="' . URL_CHECKOUT . $provider['name'] . '/pre_checkout.php";');
        }
        else
            $response->exec('window.location.reload()');

	}
}
