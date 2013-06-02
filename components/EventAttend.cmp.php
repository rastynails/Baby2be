<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 23, 2008
 * 
 */

class component_EventAttend extends SK_Component
{
	/**
	 * @var app_EventService
	 */
	private $event_service;
	
	/**
	 * @var integer
	 */
	private $event_id;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( $event_id )
	{
		parent::__construct( 'event_attend' );
		
		if( !SK_HttpUser::is_authenticated() )
			$this->annul();		
		
		$this->event_id = $event_id;	
			
		$this->event_service = app_EventService::newInstance();
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		return parent::render( $Layout );		
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form ) 
	{
		$event_profile = $this->event_service->findEventProfileEntry( $this->event_id, SK_HttpUser::profile_id() );
		
		$form->getField( 'attend' )->setValue( ( $event_profile === null ? 'not_sure' : ( ( $event_profile->getStatus() == 1 ) ? 'yes' : 'no' ) )  );;
		
		$form->getField( 'event_id' )->setValue( $this->event_id );
	}

	
}