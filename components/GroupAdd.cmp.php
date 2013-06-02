<?php

class component_GroupAdd extends SK_Component 
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{					
		parent::__construct('group_add');
	}
	
	public function render( SK_Layout $Layout )
	{
		$service = new SK_Service('create_group');

		if ( $service->checkPermissions() != SK_Service::SERVICE_FULL)
			$Layout->assign('error_msg', $service->permission_message['message']);
		
		return parent::render( $Layout );
	}
}