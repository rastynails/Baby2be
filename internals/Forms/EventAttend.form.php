<?php
require_once DIR_APPS.'appAux/EventProfile.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 23, 2008
 * 
 */

class form_EventAttend extends SK_Form 
{
	public function __construct()
	{
		parent::__construct( 'event_attend' );
	}
	
	/**
	 * @see SK_Form::setup()
	 *
	 */
	public function setup() 
	{
		$attend = new fieldType_select( 'attend' );
		$attend->setValues( array( 'not_sure', 'yes', 'no' ) );
		$this->registerField( $attend );
		
		$this->registerField( new fieldType_hidden( 'event_id' ) );
		
		$this->registerAction( new EventAttendAction() );	
	}

}


class EventAttendAction extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct( 'event_attend' );
	}
	
	/**
	 * @return app_EventService
	 */
	private function getEventService()
	{
		return app_EventService::newInstance();
	}
	
	/**
	 * @see SK_FormAction::process()
	 *
	 * @param array $data
	 * @param SK_FormResponse $response
	 * @param SK_Form $form
	 */
	public function process( array $data, SK_FormResponse $response, SK_Form $form )
	{	
		$event = $this->getEventService()->findById( $data['event_id'] );
		
		if( $event === null || !SK_HttpUser::is_authenticated() )
		{
			$response->addError( "No event to attend!" );
			return;
		}
		
		if( !in_array( $data['attend'], array( 'yes', 'no' ) ) )
		{	
			$event_profile = $this->getEventService()->findEventProfileEntry( $event->getId(), SK_HttpUser::profile_id() );
			
			if( $event_profile != null )
			{
				$this->getEventService()->deleteEventProfileByEventIdAndProfileId( $event->getId(), SK_HttpUser::profile_id() );
			}
			
		}
		else 
		{
			$event_profile = $this->getEventService()->findEventProfileEntry( $event->getId(), SK_HttpUser::profile_id() );
			
			if( $event_profile === null )
			{
				$event_profile = new EventProfile( $event->getId(), SK_HttpUser::profile_id(), ( $data['attend'] == 'yes' ? 1 : 0 ) );
			}
			else
			{
				$event_profile->setStatus( ( $data['attend'] == 'yes' ? 1 : 0 ) );
			}
            
            if ($data['attend'] == 'yes')
            {
                if ($event->getIs_speed_dating() && ($event->getProfile_id() != SK_HttpUser::profile_id() ))
                {
                    $service = new SK_Service( 'participate_speed_dating', SK_httpUser::profile_id() );

                    if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
                    {
                        $service->trackServiceUse();
                    }
                    else
                    {
                        $response->addError( $service->permission_message['message'] );
                        return;
                    }
                }
                
                if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_EVENT,
                            'entityType' => 'event_attend',
                            'entityId' => $event->getId(),
                            'userId' => SK_HttpUser::profile_id(),
                            'status' => ( $event->getAdmin_status() == 1 ) ? 'active' : 'approval'
                        )
                    );
                    app_Newsfeed::newInstance()->action($newsfeedDataParams);
                }
            }
			
			$this->getEventService()->saveOrUpdateEventProfile( $event_profile );
		}
        

			
		$response->addMessage( SK_Language::text( 'event.msg_attend_status' ) );
		
		$response->redirect( SK_Navigation::href( 'event', array( 'eventId' => $event->getId() ) ) );
	}
}




