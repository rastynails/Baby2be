<?php

class component_ProviderSecurepay extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('provider_securepay');
		
	}
		
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign('sale_info_id', $_POST['sale_info_id']);	
		return parent::render($Layout);
	}
	
}

