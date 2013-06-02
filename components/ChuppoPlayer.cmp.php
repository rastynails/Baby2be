<?php

class component_ChuppoPlayer extends SK_Component
{
	private $no_permission;
	
	private $record_owner_id;
	
	private $profile_id;
	
	/**
	 * Constructor.
	 */
	public function __construct( $params = null ) 
	{
		parent::__construct('chuppo_player');

		$this->record_owner_id = $params['profile_id'];
		$this->profile_id = SK_HttpUser::profile_id();
		
		$chuppo_recorder = SK_Config::section('chuppo')->get('enable_chuppo_recorder');
		
		if ( !$chuppo_recorder ) {
			$this->annul();
		}
		
		$service = new SK_Service('view_record');
		
		// check service availability
		if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
			$this->no_permission = $service->permission_message['message'];
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout )  
	{
		
		// define profile chuppo key 
		app_Profile::defineProfileChuppoKey( SK_HttpUser::profile_id() );
		
		$profile_key = app_ProfileField::getProfileUniqueId( $this->profile_id );
		
		$record_owner_key = app_ProfileField::getProfileUniqueId( $this->record_owner_id );		
		
		$recorder_obj_url = URL_CHUPPO_PLAYER.SK_Config::section('chuppo')->get('player_swf').'.swf';
		
		$site_url = parse_url( SITE_URL );
		
		$query_string = 'playingUserKey='.$record_owner_key.'&userKey='.$profile_key.'&conStr='.URL_CHUPPO_PLAYER_SERVER.'&siteUrl='.$site_url['host'].'&sessionId='.SK_HttpUser::session_id();				
		
		$Layout->assign('no_permission', $this->no_permission);
		$Layout->assign('recorder_obj_url', $recorder_obj_url);
		$Layout->assign('query_string', $query_string);
		
	}
}
