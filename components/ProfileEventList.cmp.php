<?php
/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 29, 2008
 *
 */

class component_ProfileEventList extends SK_Component
{
	/**
	 * @var integer
	 */
	private $profile_id;

	/**
	 * @var app_EventService
	 */
	private $event_service;

	/**
	 * Class constructor
	 *
	 * @param array $params
	 */
	public function __construct( array $params = null )
	{
		parent::__construct( 'profile_event_list' );
		$this->profile_id = $params['profile_id'];

		$this->event_service = app_EventService::newInstance();

		if(!app_Features::isAvailable(6))
			$this->annul();

	}

	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout )
	{
        $service = new SK_Service( 'event_view', SK_httpUser::profile_id() );

		$event_list = $this->event_service->findProfileEvents( $this->profile_id );

		$assign_list = array();

		foreach ( $event_list as $key => $value )
		{
            if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
            {
                 $event_list[$key]->setDescription('');//mode
            }
			$assign_list[] = array( 'title' => app_TextService::stCensor( $value->getTitle(),FEATURE_EVENT, true ), 'date' => $value->getStart_date(),
				'desc' =>  app_TextService::stOutputFormatter( app_TextService::stCensor($value->getDescription(), FEATURE_EVENT )), 'url' => component_Event::getEventUrl( $value->getId() ),
				'image_url' => ( $value->getImage() != null ? $this->event_service->getEventImageURL( 'event_icon_'.$value->getId().'.jpg' ) :
					$this->event_service->getEventDefaultImageURL() ) );
		}

		$Layout->assign( 'events', ( empty( $assign_list ) ? false : $assign_list ) );
	}

}