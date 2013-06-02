<?php

class component_Chat extends SK_Component
{

    /**
     * @var string
     */
    private $no_permission;

    /**
     * Constructor.
     */
    public function __construct()
    {
            parent::__construct('chat');
    }

    /**
     * Chat javascript handler preparing.
     *
     * @param SK_Layout $Layout
     * @param SK_Frontend $Frontend
     */
    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $service = new SK_Service('chat');

        if ($service->checkPermissions()!=SK_Service::SERVICE_FULL)
        {
            $this->no_permission = $service->permission_message['message'];
            return parent::prepare( $Layout, $Frontend );
        }
        else
        {
            $service->trackServiceUse();
        }

        $this->frontend_handler = new SK_ComponentFrontendHandler('Chat');

        $rooms = app_Chat::getRooms();
        $this->frontend_handler->drawRooms($rooms);

        if( (boolean) SK_Config::section('123_wm')->enable_123wm && SK_HttpUser::is_authenticated() )
        {
            $Layout->assign('is_123wm', true );
        }

        $this->frontend_handler->construct(3000, SK_HttpUser::profile_id());

        $default_room_id = current($rooms)->chat_room_id;
        $this->frontend_handler->selectRoom($default_room_id, true);
    }

    /**
     * Ajax ping callback.
     *
     * @param integer $params->room_id
     * @param string $params->userlist_hash
     * @param string $params->last_message_id
     * @param SK_ComponentFrontendHandler $handler
     * @param SK_AjaxResponse $response
     */
    public static function ping( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
    {
        if ( !($room_id = (int)$params->room_id) )
        {
            throw new Exception('"$params->room_id" missing');
        }

        if ( !is_numeric($params->last_message_id) )
        {
            throw new Exception('"$params->last_message_id" missing');
        }

        if ( !SK_HttpUser::is_authenticated() )
        {
            $handler->stop();
            $response->onload_js(
                'SK_SignIn()
                    .bind("close", function() {
                            window.location.reload();
                    });
            ');

            return false;
        }

        if ( !app_Chat::traceUserPing($room_id) )
        {
                $handler->error('ping failed');
        }

        $userlist_hash = app_Chat::getRoomUsersHash($room_id);
        if ( $params->userlist_hash != $userlist_hash )
        {
            $room_users = app_Chat::getRoomUsers($room_id);
            $handler->drawRoomUsers($room_id, $room_users, $userlist_hash);
        }

        $user_count_list = app_Chat::getCountUsersInRooms();
        $handler->drawRoomsUsersCounter($user_count_list);

        if ( @$params->msg_entries )
        {
            app_Chat::processMsgEntries($room_id, $params->msg_entries);
        }

        $new_messages = app_Chat::getNewMessages($room_id, $params->last_message_id);
        if ( count($new_messages) )
        {
            $handler->drawMessages("$room_id", $new_messages);
        }
    }

    /**
     * @see SK_Component::render()
     *
     * @param SK_Layout $Layout
     * @return boolean
     */
    public function render ( SK_Layout $Layout )
    {
        $Layout->assign('no_permission', $this->no_permission);
    }

}