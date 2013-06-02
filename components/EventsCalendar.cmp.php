<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 16, 2008
 * 
 */

class component_EventsCalendar extends SK_Component
{
	/**
	 * @var app_EventService
	 */
	private $event_service;
	
	/**
	 * @var array
	 */
	private $page_params;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $page_params )
	{
		parent::__construct( 'events_calendar' );
		$this->page_params = $page_params;
		
		if( $this->page_params['year'] === null )
			$this->page_params['year'] = date('Y', SK_I18n::mktimeLocal());
			
		if( $this->page_params['month'] === null )
			$this->page_params['month'] = date( 'n', SK_I18n::mktimeLocal());
		
		$this->event_service = app_EventService::newInstance();
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render(  SK_Layout $Layout)
	{
		$current_date = array( 'year' => date('Y', SK_I18n::mktimeLocal()), 'month' => date( 'n', SK_I18n::mktimeLocal() ), 'day' => date( 'j', SK_I18n::mktimeLocal() ) );
		
		$array = $this->event_service->getWeeksArray( $this->page_params['year'], $this->page_params['month'] );
		
		foreach ( $array as $key => $value )
		{
			foreach ( $value as $key2 => $value2 )
			{
				if( $value2['link'] )
				{
					$array[$key][$key2]['url'] = httpdoc_Events::getEventsUrl( $this->page_params['year'], $this->page_params['month'], $value2['date'] );
				}
				
				if( $this->page_params['year'] == $current_date['year'] && $this->page_params['month'] == $current_date['month'] )
				{
					if( $value2['date'] == $current_date['day'] )
					{
						$array[$key][$key2]['current'] = true;
					}
					
					if( $this->page_params['day'] && $value2['date'] == $this->page_params['day'] )
					{
						$array[$key][$key2]['active'] = true;
					}
				}
			}
		}
		
		$date_section = SK_Language::section( 'i18n.date' );
		
		$Layout->assign( 'cap_label', $this->page_params['month'] );
		
		$Layout->assign( 'calendar_array', $array );
		
		/* ----- */
		
		$Layout->assign( 'next_array', array( 
			'url' => httpdoc_Events::getEventsUrl( ( $this->page_params['month'] == 12 ? $this->page_params['year'] + 1 : $this->page_params['year'] ), ( $this->page_params['month'] == 12 ? 1 : $this->page_params['month'] + 1 ) ), 
			'label' => SK_Language::text( 'event.cal_month_label', array( 'month' => $date_section->text( 'month_full_'.trim(( $this->page_params['month'] == 12 ? 1 : $this->page_params['month'] + 1 ))) ) ) ) );
		
		$Layout->assign( 'prev_array', array( 
			'url' => httpdoc_Events::getEventsUrl( ( $this->page_params['month'] == 1 ? $this->page_params['year'] - 1 : $this->page_params['year'] ), ( $this->page_params['month'] == 1 ? 12 : $this->page_params['month'] - 1 ) ), 
			'label' => SK_Language::text( 'event.cal_month_label', array( 'month' => $date_section->text( 'month_full_'.trim(( $this->page_params['month'] == 1 ? 12 : $this->page_params['month'] - 1 ))) ) ) ) );

        $Layout->assign('current_month_label', SK_Language::text('event.cal_month_label', array('month' => $date_section->text( 'month_full_'.trim($this->page_params['month'])))));

		return parent::render( $Layout );
	}
	
}