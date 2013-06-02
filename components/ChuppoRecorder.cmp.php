<?php

class component_ChuppoRecorder extends SK_Component
{	
	/**
	 * Constructor.
	 */
	public function __construct( $params = null ) 
	{
		parent::__construct('chuppo_recorder');

		$chuppo_recorder = SK_Config::section('chuppo')->get('enable_chuppo_recorder');
		
		if ( !$chuppo_recorder ) {
			$this->annul();
		}
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout )  
	{		
		$service = new SK_Service('record_media');
		
		// check service availability
		if ( $service->checkPermissions()!=SK_Service::SERVICE_FULL )
			$no_permission = $service->permission_message['message'];
		
		// define profile chuppo key 
		app_Profile::defineProfileChuppoKey( SK_HttpUser::profile_id() );
		
		$profile_key = app_ProfileField::getProfileUniqueId( SK_HttpUser::profile_id() );
		
		$recorder_obj_url = URL_CHUPPO_RECORDER.SK_Config::section('chuppo')->get('recorder_swf').'.swf';
		
		$site_url = parse_url( SITE_URL );
		
		$query_string = 'userKey='.$profile_key.'&conStr='.URL_CHUPPO_RECORDER_SERVER.'&siteUrl='.$site_url['host'].'&sessionId='.SK_HttpUser::session_id();
						
		$Layout->assign('no_permission', $no_permission);
		$Layout->assign('recorder_obj_url', $recorder_obj_url);
		$Layout->assign('query_string', $query_string);
	
	}

}
