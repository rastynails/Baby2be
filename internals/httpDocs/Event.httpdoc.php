<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 10, 2008
 * 
 */


class httpdoc_Event extends SK_HttpDocument
{
	/**
	 * @var app_EventService
	 */
	private $event_service;

	/**
	 * @var Event
	 */
	private $event;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct('event');
		$this->event_service = app_EventService::newInstance();
		
		$event = $this->event_service->findById( (int)SK_HttpRequest::$GET['eventId'] );
		
		if( $event === null )
		{
			SK_HttpRequest::showFalsePage();
		}
		
		$this->event = $event; 
	}
	
	public function render( SK_Layout $Layout )
	{
		$event_info = $this->event_service->findEventInfo( $this->event->getId() );
		
		if( $this->event->getImage() != null )
		{
			$Layout->assign( 'image_url', $this->event_service->getEventImageURL( $this->event->getImage() ) );
		}
		
		$event_info['title'] = app_TextService::stCensor( $event_info['dto']->getTitle(), FEATURE_EVENT, true );
		$event_info['desc'] = app_TextService::stCensor( $event_info['dto']->getDescription(), FEATURE_EVENT );
		
		$Layout->assign( 'event', $event_info );
        
		if( $this->event->getZip() != null && trim( $this->event->getZip() ) )
		{
			$event_info['city_label'] = app_Location::getCityLabelByZip( $this->event->getZip() );
		}
		
		if( !$this->event_service->isEventExpired( $this->event ) )
		{
			$Layout->assign( 'event_attend', 1 );
			
			$Layout->assign( 'event_attend', new component_EventAttend( $this->event->getId() ) );
			
			$Layout->assign( 'event_attendees', new component_EventAttendees( $this->event ) );
			
			$Layout->assign( 'event_not_attendees', new component_EventNotAttendees( $this->event ) );
		}
		
		$Layout->assign( 'event_comments', new component_AddComment( $this->event->getId(), 'event', 'event_add' ) );
		
		if( SK_HttpUser::is_authenticated() && ( SK_HttpUser::isModerator() || $this->event->getProfile_id() == SK_HttpUser::profile_id() ) )
		{
			$Layout->assign( 'edit_url', component_EventEdit::getEventEditURL( $this->event->getId() ) );
		}
		
		SK_Navigation::removeBreadCrumbItem();
		
		SK_Navigation::addBreadCrumbItem( app_TextService::stCensor($this->event->getTitle(),FEATURE_EVENT,true) );
		
		SK_Language::defineGlobal( array('eventtitle' => app_TextService::stCensor($this->event->getTitle(),FEATURE_EVENT,true) ) );
		
		$service = new SK_Service( 'event_view', SK_httpUser::profile_id() );
		
		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
		{
			$service->trackServiceUse();
		}
		else 
		{
			$Layout->assign( 'err_message', $service->permission_message['message'] );
		}
		
		return parent::render($Layout);
	}
	
	/* static util methods */
	
	public static function getEventUrl( $event_id )
	{
		return SK_Navigation::href( 'event', array( 'eventId' => $event_id ) );
	}
	
}