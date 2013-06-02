<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 23, 2008
 * 
 */

class component_EventNotAttendees extends SK_Component
{
	/**
	 * @var Event
	 */
	private $event;
	
	/**
	 * @var app_EventService
	 */
	private $event_service;
	
	/**
	 * Class constructor
	 *
	 * @param Event $event
	 */
	public function __construct( $event )
	{
		parent::__construct( 'event_not_attendees' );
		
		$this->event_service = app_EventService::newInstance();
		
		$this->event = $event;
		
		if( $this->event === null )
		{
			$this->annul();
		}
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		$profiles = $this->event_service->findNotAttendingProfileIdsForEvent( $this->event->getId(), true );
		
		$Layout->assign( 'count', $this->event_service->findNotAttendingProfilesCount( $this->event->getId() ) );
		
		if( $profiles === null || empty( $profiles ) )
		{
			$Layout->assign( 'false_label', SK_Language::section( 'event' )->text( 'no_profile' ) );
			return parent::render( $Layout );
		}
		
		$configs = $this->event_service->getConfigs();
		
		if( sizeof( $profiles ) > $configs['event_thumb_list_limit'] )
		{
			$Layout->assign( 'view_all_link', httpdoc_Attendees::getAttendeesUrl( $this->event->getId() ) );
		}
		
		$Layout->assign( 'profile_list1', new component_ThumbedProfileList( $profiles ) );
		
		return parent::render( $Layout );		
	}
}