<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 16, 2008
 * 
 */

class component_EventIndexCalendar extends SK_Component
{
	/**
	 * @var app_EventService
	 */
	private $event_service;
	
	/**
	 * @var array
	 */
	private $menu_array;
	
	/**
	 * @var integer
	 */
	private $count;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		parent::__construct( 'events_index_calendar' );		
		$this->event_service = app_EventService::newInstance();
		
		if( !empty( $params ) )
		{
			if( $params['view_mode'] )
			{
				$menu_array = explode( "|", $params['view_mode'] );
				
				foreach ( $menu_array as $value )
				{
					if( in_array( trim( $value ), array( 'list', 'calendar' ) ) )
					{
						$this->menu_array[] = trim( $value );
					}
				} 
			}
			
			if( ( isset( $params['count'] ) && (int)$params['count']) > 0 )
			{
				$this->count = (int)$params['count'];
			}
            else
            {
                $this->count = 5;
            }
		}
        
        if( empty($this->menu_array) )
        {
            $this->menu_array = array('calendar', 'list');
        }
		
		if(!app_Features::isAvailable(6))
			$this->annul();
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render( SK_Layout $Layout)
	{
		$year = date( 'Y', time() );
		$month = date( 'n', time() );
		$day = date( 'j', time() );
		
		$event_sect = SK_Language::section( 'event' );
		
		$current_date = array( 'year' => date('Y', time()), 'month' => date( 'n', time() ), 'day' => date( 'j', time() ) );
		
		$array = $this->event_service->getWeeksArray( $year, $month );
		
		foreach ( $array as $key => $value )
		{
			foreach ( $value as $key2 => $value2 )
			{
				if( !empty($value2['link']) && $value2['link'] )
				{
					$array[$key][$key2]['url'] = httpdoc_Events::getEventsUrl( $year, $month, $value2['date'] );
				}
				
				if( !empty($value2['date']) && $value2['date'] == $current_date['day'] )
				{
					$array[$key][$key2]['current'] = true;
				}
			}
		}
		
		$Layout->assign( 'month', $month );
		
		$Layout->assign( 'calendar_array', $array );

        $Layout->assign( 'index_label', $event_sect->text('cal_month_index_label', array('month' => SK_Language::text('%i18n.date.month_full_'.$month))));

		/**--------------------------------------------------------**/
		
		$events = $this->event_service->findDefaultMonthEventsForIndex($this->count);
			
		if( empty( $events ) )
			$Layout->assign( 'no_events', true );
		
		foreach ( $events as $key => $value )
		{
			if( $value['dto']->getImage() != null )
				$events[$key]['image_url'] = $this->event_service->getEventImageURL( 'event_icon_'.$value['dto']->getId().'.jpg' );
			else 
				$events[$key]['image_url'] = $this->event_service->getEventDefaultImageURL();	
				
			$events[$key]['event_url'] = component_Event::getEventUrl( $value['dto']->getId() );
			$events[$key]['profile_url'] = SK_Navigation::href( 'profile', array( 'profile_id' => $value['dto']->getProfile_id() ) );
			$events[$key]['description'] = app_TextService::stOutputFormatter( $value['dto']->getDescription() );
			
			if( $value['dto']->getCity_id() != null && trim( $value['dto']->getCity_id() ) )
			{
				$events[$key]['city_label'] = app_Location::CityNameById( $value['dto']->getCity_id() );
			}
		}
		
		$Layout->assign( 'events', $events );
		
		$Layout->assign( 'events_url', SK_Navigation::href( 'events' ) );

        $menu_array = array();
        
        foreach ( $this->menu_array as $menu_item )
        {
            if( $menu_item == 'calendar' )
            { 
                $mitem = array( 'label' => SK_Language::text('event.calendar'), 'active' => 1, 'href' => $this->auto_id.'calendar', 'class' => 'calendar' );
                
                if( !empty($menu_array) )
                {
                    $Layout->assign('cal_none', true);
                    $mitem['active'] = 0;
                }
                $menu_array[] = $mitem;
                $Layout->assign('calendar', true);
            }
            elseif( $menu_item == 'list' )  
            {
                $mitem = array( 'label' => SK_Language::text('event.events'), 'active' => 1, 'href' => $this->auto_id.'events', 'class' => 'events' );
                
                if( !empty($menu_array) )
                {
                    $Layout->assign('list_none', true);
                    $mitem['active'] = 0;
                }
                
                $menu_array[] = $mitem;
                $Layout->assign('list', true);
            }
        }
        if( count($menu_array) > 1 )
        {
            $Layout->assign('menu_array', $menu_array);
        }

		return parent::render( $Layout );
	}
	
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_FrontendHandler $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('EventIndexCalendar');
		
		$this->frontend_handler->construct();		
	}

	
}