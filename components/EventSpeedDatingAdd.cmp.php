<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 16, 2008
 * 
 */

class component_EventSpeedDatingAdd extends SK_Component
{
	/**
	 * @var app_EventService
	 */
	private $event_service;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'event_speed_dating_add' );
		
		if( !SK_HttpUser::is_authenticated() )
			$this->annul();		

        if(!app_Features::isAvailable(53))
        	$this->annul();

		$this->event_service = app_EventService::newInstance();
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		/*
		$service = new SK_Service( 'event_submit', SK_httpUser::profile_id() );
		
		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
		{
			//TODO remove tracking
		}
		else 
		{
			$Layout->assign( 'err_message', $service->permission_message['message'] );
		}*/
		
		
		return parent::render( $Layout );		
	}
	
}