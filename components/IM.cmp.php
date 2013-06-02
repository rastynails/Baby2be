<?php

class component_IM extends SK_Component
{
	private $opponent;
	private $is_esd_session;
    private $enable_sound;
	
	public function __construct( array $params )
	{
		parent::__construct('im');
		$opponent_id = (int)$params['opponent_id'];
		$this->is_esd_session = (int)$params['is_esd_session'];

        $this->enable_sound = SK_Config::section('site')->Section('additional')->Section('im')->enable_sound;
		$_opponent = app_Profile::getFieldValues($opponent_id, array('sex'));
		
		$this->opponent = Object(array(
			'profile_id'	=>	$opponent_id,
			'username'		=>	app_Profile::username($opponent_id),
			'sex'			=>	$_opponent['sex'],
            'href'          =>  app_Profile::href($opponent_id)
		));
	}
	
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
        if (!$this->is_esd_session)
        {
            $im_is_available = true;
            $permission_message = null;
            try
            {
                $service = new SK_ServiceUse('instant_messenger');
                $service->track();
            }
            catch(SK_ServiceUseException $exService)
            {
                $im_is_available = false;

                $permission_message = $exService->getHtmlMessage();
            }

            
            $Layout->assign('im_is_available', $im_is_available);
            $Layout->assign('permission_message', $permission_message);
            
            $opponentService = new SK_Service('instant_messenger', $this->opponent->profile_id);
            if ($opponentService->checkPermissions() != SK_Service::SERVICE_FULL )
            {
                $Layout->assign('opponent_service_use_limited', true);    
            }
        }
        $Frontend->include_js_file(URL_STATIC.'swfobject.js');

		$this->frontend_handler = new SK_ComponentFrontendHandler('IM');
		$this->frontend_handler->construct($this->opponent->profile_id, $this->opponent->href, 3000, $this->is_esd_session, $this->enable_sound );
        $this->frontend_handler->swf_player_src = URL_FLASH_MEDIA_PLAYER.'mediaplayer.swf';
	}
	
	/**
	 * Render instant messenger.
	 *
	 * @param SK_Layout $Layout
	 * @return boolean
	 */
	public function render( SK_Layout $Layout ) {
		$Layout->assign('opponent', $this->opponent);
		if ($this->is_esd_session)
			$Layout->assign('session_length',  SK_Config::section('site')->Section('additional')->Section('speed_dating')->session_length * 60 );

        $Layout->assign('im_enable_sound', $this->enable_sound);

        return parent::render($Layout);
	}
	
	
	/**
	 * Ajax ping callback.
	 *
	 * @param integer $params->opponent_id
	 * @param string $params->last_message_id
	 * 
	 * @param SK_ComponentFrontendHandler $handler
	 * @param SK_AjaxResponse $response
	 */
	public static function ping( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		// checking entry params
		if ( !(
			$opponent_id = (int)$params->opponent_id
		) ) {
			throw new Exception('invalid argument "$params->opponent_id"');
		}
		if ( !$params->has("last_message_id") ) {
			throw new Exception('"$params->last_message_id" missing');
		}
		
		// checking user authentication
		if ( !SK_HttpUser::is_authenticated() ) {
			$response->onload_js(
<<<EOT
if (!component_IM.sign_in_box) {
	component_IM.sign_in_box = SK_SignIn();
	component_IM.sign_in_box.bind('close', function() {
		window.close();
	}); }
EOT
			);
			return;
		}

		// getting session
		try {
			$session = app_IM::getSession($opponent_id, (int)$params->has_countdown);

		}
		catch ( SK_ServiceUseException $e ) {
			$handler->stop($e->getHtmlMessage());
			return;
		}
		
		$session->ping();
		
		if ( @$msg_entries = (array)$params->msg_entries ) {
			$session->processMsgEntries($msg_entries);
		}
		
		// getting new messages
		$new_messages = app_IM::getNewMessages(
			$session->im_session_id(), $params->last_message_id
		);
		if ( count($new_messages) ) {
			$handler->drawMessages($new_messages);
		}

        if ( $params->has_countdown != null )
        {
            $elapsed_time = app_EventService::stGetEventSpeedDatingSessionEndTime($_SESSION['speed_dating_event']['event_id'], SK_HttpUser::profile_id(), (int)$params->opponent_id) - time();
            $handler->refreshCountdown($elapsed_time);
        }
	}
	
}
