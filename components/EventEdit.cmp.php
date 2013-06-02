<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 27, 2008
 * 
 */


class component_EventEdit extends SK_Component
{
	/**
	 * @var app_EventService
	 */
	private $event_service;

	/**
	 * @var Event
	 */
	private $event;
	
	
//	/**
//	 * @var string
//	 */
//	private $buttonId;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct('event_edit');
		
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
		SK_Navigation::removeBreadCrumbItem();
		
		SK_Navigation::removeBreadCrumbItem();
		
		SK_Navigation::addBreadCrumbItem( $this->event->getTitle(), component_Event::getEventUrl( $this->event->getId() ) );
		
		SK_Navigation::addBreadCrumbItem( SK_Language::section( 'event' )->text( 'event_edit' ) );
		
		if( $this->event->getImage() !== null )
			$Layout->assign( 'img_url', $this->event_service->getEventImageURL( $this->event->getImage() ) );
		
			
		return parent::render($Layout);
	}
	
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{	
		$handler = new SK_ComponentFrontendHandler('EventEdit');
		$this->frontend_handler = $handler;
		
		$event_id = ( $this->event->getImage() !== null ) ? $this->event->getId() : false;
		
		$handler->construct( $event_id );
	}
	
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		$form->getField( 'event_title' )->setValue( $this->event->getTitle() );
		$form->getField( 'address' )->setValue( $this->event->getAddress() );
		$form->getField( 'event_desc' )->setValue( $this->event->getDescription() );
		
		$location_array = array();
		$location_array['country_id'] = $this->event->getCountry_id();
		if( $this->event->getState_id() !== null ) $location_array['state_id'] = $this->event->getState_id();
		if( $this->event->getCity_id() !== null ) $location_array['city_id'] = $this->event->getCity_id();
		if( $this->event->getZip() !== null ) $location_array['zip'] = $this->event->getZip();
		if( $this->event->getCustom_location() !== null ) $location_array['custom_location'] = $this->event->getCustom_location();
		
		$form->getField( 'date' )->setValue( 
			array( 
				'year' => date( 'Y', $this->event->getStart_date() ),
				'month' => date( 'n', $this->event->getStart_date() ),
				'day' => date( 'j', $this->event->getStart_date() )
			) 
		);

        $form->getField( 'endDate' )->setValue(
			array(
				'year' => date( 'Y', $this->event->getEnd_date() ),
				'month' => date( 'n', $this->event->getEnd_date() ),
				'day' => date( 'j', $this->event->getEnd_date() )
			)
		);

		$form->getField( 'start_time' )->setValue( $this->event->getStart_date() );
		$form->getField( 'end_time' )->setValue( $this->event->getEnd_date() );
		$form->getField( 'i_am_attending' )->setValue( $this->event->getI_am_attand() ? true : false );
		$form->getField( 'location' )->setValue( $location_array );
		$form->getField( 'event_id' )->setValue( $this->event->getId() );
	}
	
	
	/**
	 * Ajax method for updating rates
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_deleteImage( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_EventService::newInstance();
		
		$event_item = $service->findById( $params->id );
		
		if( $event_item === null )
			return;

		if( !SK_HttpUser::is_authenticated() || ( !SK_HttpUser::isModerator() && $event_item->getProfile_id() != SK_HttpUser::profile_id() ) )
			return;	
			
		$service->deleteEventImage( $event_item );
	} 
	
	
	/* static util methods */
	
	public static function getEventEditURL( $event_id )
	{
		return SK_Navigation::href( 'event_edit', array( 'eventId' => $event_id ) );
	}
	
}