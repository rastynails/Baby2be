<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 27, 2008
 * 
 */


class httpdoc_EventEdit extends SK_HttpDocument
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
		parent::__construct('attendees');
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
		
		
		SK_Navigation::addBreadCrumbItem( $this->event->getTitle(), component_Event::getEventUrl( $this->event->getId() ) );
		
		SK_Navigation::addBreadCrumbItem( SK_Language::section( 'event' )->text( 'event_edit' ) );
		
		return parent::render($Layout);
	}
	
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		
		
	}

	
	/* static util methods */
	
	public static function getAttendeesUrl( $event_id )
	{
		return SK_Navigation::href( 'event_not_attendees', array( 'eventId' => $event_id ) );
	}
	
}