<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 * 
 * Desc: Events Page httpDoc
 */


class httpdoc_Events extends SK_HttpDocument
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
		parent::__construct('events');
		$this->event_service = app_EventService::newInstance();
	}
	
	public function render( SK_Layout $Layout )
	{
		// Getting page params
		$page_params = array (
			'page' => ( isset( SK_HttpRequest::$GET['page'] ) && (int)SK_HttpRequest::$GET['page'] > 0 ? (int)SK_HttpRequest::$GET['page'] : 1 ),
			'year' => ( isset( SK_HttpRequest::$GET['year'] ) && (int)SK_HttpRequest::$GET['year'] > 0 ? (int)SK_HttpRequest::$GET['year'] : null ),
			'month' => ( isset( SK_HttpRequest::$GET['month'] ) && in_array( (int)SK_HttpRequest::$GET['month'], array(1,2,3,4,5,6,7,8,9,10,11,12) ) ? (int)SK_HttpRequest::$GET['month'] : null ),
			'day' => ( isset( SK_HttpRequest::$GET['day'] ) && (int)SK_HttpRequest::$GET['day'] > 0 && (int)SK_HttpRequest::$GET['day'] <= 31 ? (int)SK_HttpRequest::$GET['day'] : null )
		);
		
		// Registering components to display
		$Layout->assign( 'event_list', new component_EventsList( $page_params ) );	
		$Layout->assign( 'calendar', new component_EventsCalendar( $page_params ) );
		
		// Speed Dating event add component
		if (SK_HttpUser::isModerator())
			$Layout->assign( 'eventSpeedDatingAdd', new component_EventSpeedDatingAdd() );
			
		
		return parent::render( $Layout );
	}
	
	
	/**
	 * Generates events page urls
	 *
	 * @param integer $year
	 * @param integer $month
	 * @param integer $day
	 * @return string
	 */
	public static function getEventsUrl( $year, $month, $day = null )
	{
		if( $day != null )
			return SK_Navigation::href( 'events', array( 'year' => $year, 'month' => $month, 'day' => $day ) );
			
		return SK_Navigation::href( 'events', array( 'year' => $year, 'month' => $month ) );
	}
}