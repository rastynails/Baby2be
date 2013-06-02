<?php

class component_PointsPurchase extends SK_Component
{
		
	/**
	 * Component PointsPurchase constructor.
	 *
	 * @return component_PointsPurchase
	 */
	public function __construct( array $params = null )
	{
		if ( !SK_HttpUser::profile_id() ) 
		{
			$this->annul();
		}
		
		parent::__construct('points_purchase');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{	    
		if (isset($_SESSION['messages']))
		{
			foreach ( $_SESSION['messages'] as $msg )
				$Frontend->onload_js('SK_drawMessage("'.$msg['message'].'","'.$msg['type'].'");');
			
			unset($_SESSION['messages']);
		}
	}
	
	public static function clearCompile( $tpl_file = null, $compile_id = null) 
	{
		$cmp = new self;
		
		return $cmp->clear_compiled_tpl();
	}
		
	public function render( SK_Layout $Layout )
	{
		$profile_id = SK_HttpUser::profile_id(); 
		
		$providers = app_Finance::GetPaymentProviders();
		$Layout->assign_by_ref('providers', $providers);
		
		$packages = app_UserPoints::getPackages();
		$Layout->assign('packages', $packages);
		
		$Layout->assign('cur', SK_Language::text('%label.currency_sign'));
		
		$services = app_UserPoints::getServicesList();
		$Layout->assign('services', $services);
					
		return parent::render($Layout);
	}
	
}

