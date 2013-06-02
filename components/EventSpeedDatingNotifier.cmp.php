<?php
/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 29, 2008
 *
 */

class component_EventSpeedDatingNotifier extends SK_Component
{
	/**
	 * @var integer
	 */
	private $profile_id;

	/**
	 * @var array
	 */
	private $cmp_list;

	/**
	 * Class constructor
	 *
	 * @param array $params
	 */
	public function __construct( array $params = null )
	{
        if(!app_Features::isAvailable(6))
        	$this->annul();

        if(!app_Features::isAvailable(53))
        	$this->annul();

		parent::__construct( 'event_speed_dating_notifier' );
		$this->profile_id = $params['profile_id'];

	}
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('EventSpeedDatingNotifier');
		$this->frontend_handler = $handler;

		$js_array = array();

		$handler->construct();
	}
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout )
	{
		if ( isset($_SESSION['speed_dating_event']) )
			$display = true;
		else
			$display = false;

		$Layout->assign( 'display', $display );
		return parent::render( $Layout );
	}


	/**
     * EXPERIMENTAL! reopen popup window method
     *
     * @param SK_HttpRequestParams $params
     * @param SK_ComponentFrontendHandler $handler
     * @param SK_AjaxResponse $response
     */
    public static function ajax_StartDating(SK_HttpRequestParams $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
    {
        $new_opponent = $_SESSION['speed_dating_event']['opponent_id'];
        if (SK_Config::section("chuppo")->enable_chuppo_im)
        {
            $opp_key = app_ProfileField::getProfileUniqueId($new_opponent);
            $pr_key = app_ProfileField::getProfileUniqueId(SK_HttpUser::profile_id());

            $chat = "window.open( '" . SK_Navigation::href("private_chat", array("userKey"=>$pr_key, "oppUserKey"=>$opp_key, 'is_esd_session'=>1 )) . "', '', 'height=540,width=415,left=100,top=100,scrollbars=no,resizable=no' );";

            $handler->startDating($new_opponent, $chat );
        }
        else if( (boolean) SK_Config::section('123_wm')->enable_123wm )
        {
            $u = app_Profile::getFieldValues($new_opponent, 'username');

            $handler->popup123FlashChatDating( $u );
        }
        else
        {
            $chat = 'SK_openIM(' . $new_opponent . ', 1);';
            $handler->startDating($new_opponent, $chat );
        }
    }


    /**
	 * Ajax method that stops session with current member
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_StopDating(SK_HttpRequestParams $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		if( (boolean) SK_Config::section('123_wm')->enable_123wm )
			$handler->stop123FlashChatDating($_SESSION['speed_dating_event']['event_id'], $_SESSION['speed_dating_event']['opponent_id'] );
		else
		{
			$handler->stopDating($_SESSION['speed_dating_event']['event_id'], $_SESSION['speed_dating_event']['opponent_id'] );
		}

		app_EventService::stStopEventSpeedDatingSession(  $_SESSION['speed_dating_event']['event_id'],  SK_HttpUser::profile_id(), $_SESSION['speed_dating_event']['opponent_id'] );
		unset( $_SESSION['speed_dating_event']['session_end_timestamp'] );
		$_SESSION['speed_dating_event']['ping_type'] = 'idle';
	}

		/**
	 * Ajax method that starting search of match for current member
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_StartSearching(SK_HttpRequestParams $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		if (isset($_SESSION['speed_dating_event']))
		{
            $handler->drawProfileStatus( SK_Language::text("components.event.speed_dating.searching_match") , "" );
			app_EventService::stUpdateDatingSession( $_SESSION['speed_dating_event']['event_id'], SK_HttpUser::profile_id(), 1 );
			$_SESSION['speed_dating_event']['ping_type'] = 'search';
		}
	}


/**
	 * Ajax ping callback.
	 *
	 * @param array $params->drawn_invitations
	 * @param SK_ComponentFrontendHandler $handler
	 * @param SK_AjaxResponse $response
	 */
	public static function ping(
		SK_HttpRequestParams $params,
		SK_ComponentFrontendHandler $handler,
		SK_AjaxResponse $response
	) {

		// There is no speed_dating event, check if one has been started
		if ( !isset($_SESSION['speed_dating_event']) || empty( $_SESSION['speed_dating_event'] ) )
		{
			$new_speed_dating_events = app_EventService::stFindSpeedDatingEventForProfile( SK_HttpUser::profile_id() );
			if (!empty($new_speed_dating_events))
			{
				//app_EventService::stTruncateSpeedDatingEvent( SK_HttpUser::profile_id() ); //TODO: Comment before commit

				$_SESSION['speed_dating_event'] = $new_speed_dating_events;

				app_EventService::stAddDatingSession( $_SESSION['speed_dating_event']['event_id'], SK_HttpUser::profile_id());

				$_SESSION['speed_dating_event']['ping_type'] = 'search';
				$_SESSION['speed_dating_event']['drawn_opponents'] = array();


                if (SK_Config::section("chuppo")->enable_chuppo_im)
                {
                    $chat_type_notification = "components.event.speed_dating.started_title_chuppo";
                }
                else if ( (boolean) SK_Config::section('123_wm')->enable_123wm )
                {
                    $chat_type_notification = "components.event.speed_dating.started_title_123wm";
                }
                else
                {
                    $chat_type_notification = "components.event.speed_dating.started_title";
                }

                $notification = SK_Language::text( $chat_type_notification ,
                                                    array('title'=>$_SESSION['speed_dating_event']['title'],
                                                          'description'=>$_SESSION['speed_dating_event']['description'],
                                                          'start_date'=>date( "h:i d/m/Y", $_SESSION['speed_dating_event']['start_date'] ) ) );

				$handler->drawSpeedDatingNotifications($new_speed_dating_events, $notification );
				$handler->showNotifier();
			}
            //$handler->hideNotifier();
		} // Check if speed dating event has already been completed
		else if (isset($_SESSION['speed_dating_event']) && $_SESSION['speed_dating_event']['end_date'] <= time() )
		{

			$handler->drawSpeedDatingNotifications($_SESSION['speed_dating_event'], SK_Language::text("components.event.speed_dating.finished_title" , array( 'title'=>$_SESSION['speed_dating_event']['title'], 'bookmark_list_link'=>SK_Navigation::href( 'event_speed_dating_bookmark_list', array('event_id'=>$_SESSION['speed_dating_event']['event_id']) ) ) )  );
			$handler->hideNotifier();
			if ( isset($_SESSION['speed_dating_event']['session_end_timestamp']) )
			{
				app_EventService::stUpdateDatingSession( $_SESSION['speed_dating_event']['event_id'], SK_HttpUser::profile_id(), 1 );

				self::ajax_StopDating( $params, $handler, $response );

			}

			unset($_SESSION['speed_dating_event']);
			//app_EventService::stTruncateSpeedDatingEvent(); //TODO: Comment before commit
		} // Speed dating in process
		else
		{
				switch ( $_SESSION['speed_dating_event']['ping_type'] )
				{
					case 'search':
						// Search match
						$is_invitation = true;
						$new_opponent = app_EventService::stFindSpeedDatingEventInvitation($_SESSION['speed_dating_event']['event_id'], SK_HttpUser::profile_id(), $_SESSION['speed_dating_event']['drawn_opponents'] );
						//Check if someone has already invited this profile
						if ( empty($new_opponent) )
						{
							$is_invitation = false;
                            $search_by_location = app_EventService::stSpeedDatingSearchByLocation( $_SESSION['speed_dating_event']['event_id'] );
							$new_opponent = app_EventService::stFindSpeedDatingEventProfileMatches( $_SESSION['speed_dating_event']['event_id'], SK_HttpUser::profile_id(), $_SESSION['speed_dating_event']['drawn_opponents'], $search_by_location );

							if ( !empty($new_opponent) )
							{
								$session_length = SK_Config::section('site')->Section('additional')->Section('speed_dating')->get("session_length");
								app_EventService::stAddDatingSessionOpponents( $_SESSION['speed_dating_event']['event_id'], SK_HttpUser::profile_id(), $new_opponent, time(), time() + $session_length * 60 );
							}
						}
						// Invite someone
						if ( !empty($new_opponent) )
						{
							if (SK_Config::section("chuppo")->enable_chuppo_im) {
								$opp_key = app_ProfileField::getProfileUniqueId($new_opponent);
								$pr_key = app_ProfileField::getProfileUniqueId(SK_HttpUser::profile_id());

								$chat = "window.open( '" . SK_Navigation::href("private_chat", array("userKey"=>$pr_key, "oppUserKey"=>$opp_key, 'is_esd_session'=>1 )) . "', '', 'height=540,width=415,left=100,top=100,scrollbars=no,resizable=no' );";

								if (!$is_invitation )
									$handler->startDating($new_opponent, $chat );
							}
							else if( (boolean) SK_Config::section('123_wm')->enable_123wm )
							{
								$u = app_Profile::getFieldValues($new_opponent, 'username');

								if (!$is_invitation )
								{
									$handler->start123FlashChatDating( $u );
								}
								else
								{
									$handler->popup123FlashChatDating( $u );
								}
							}
							else
							{
								$chat = 'SK_openIM(' . $new_opponent . ', 1);';
								$handler->startDating($new_opponent, $chat );
							}

							$_SESSION['speed_dating_event']['ping_type'] = 'wait';
							$_SESSION['speed_dating_event']['drawn_opponents'][$new_opponent] = true;
							$_SESSION['speed_dating_event']['opponent_id'] = $new_opponent;

						}
						$handler->drawProfileStatus( SK_Language::text("components.event.speed_dating.searching_match") , "" );
						break;
					case 'wait':
						// User has private session with some profile
						if (!$params->im_closed)
						{
							$_SESSION['speed_dating_event']['session_end_timestamp'] = app_EventService::stGetEventSpeedDatingSessionEndTime(  $_SESSION['speed_dating_event']['event_id'],  SK_HttpUser::profile_id(), $_SESSION['speed_dating_event']['opponent_id'] );

							if( $_SESSION['speed_dating_event']['session_end_timestamp'] <= time() )
							{
								self::ajax_StopDating($params, $handler, $response);
							}

							$username = app_Profile::getFieldValues( $_SESSION['speed_dating_event']['opponent_id'], 'username' );
							$time = $_SESSION['speed_dating_event']['session_end_timestamp'] - time();
							$handler->drawProfileStatus( SK_Language::text("components.event.speed_dating.profile_in_session", array( 'username'=>$username )),  SK_Language::text("components.event.speed_dating.session_elapsed_time", array( 'time'=>date('i:s', $time) )));
						}
						else
						{
							self::ajax_StopDating($params, $handler, $response);
						}
						break;
					case 'idle':
						// Private session has been completed and system waiting while user is making notes and bookmarks another profile
						if (!$params->note_closed)
						{
							$handler->drawProfileStatus(  SK_Language::text("components.event.speed_dating.waiting_note"), "" );
						}
						else
						{
							self::ajax_StartSearching($params, $handler, $response);
						}
						break;
					default:
						break;
				}

		}
		//unset($_SESSION['speed_dating_event']);
	}

}