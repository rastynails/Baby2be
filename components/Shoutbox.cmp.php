<?php

class component_Shoutbox extends SK_Component
{
    private $title;

	public function __construct( array $params = null )
	{
		parent::__construct('shoutbox');

        if (!empty($params) && isset($params['title']))
        {
            $this->title = $params['title'];
        }
        else
        {
            $this->title = SK_Language::section('components.shoutbox')->text('label_shoutbox');
        }

        if(!app_Features::isAvailable(55))
        {
            $this->annul();
        }

	}


	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('Shoutbox');
		$this->frontend_handler->construct(6000, SK_Language::text('components.shoutbox.message'), SK_Language::text('components.shoutbox.guest') );
	}

	/**
	 * Render instant messenger.
	 *
	 * @param SK_Layout $Layout
	 * @return boolean
	 */
	public function render( SK_Layout $Layout )
    {
        $read_service = new SK_Service('read_shoutbox', SK_HttpUser::profile_id());
        if (!SK_HttpUser::isModerator())
        {
            if ($read_service->checkPermissions() == SK_Service::SERVICE_FULL)
            {
                $read_service->trackServiceUse();
                $service = new SK_Service('write_shoutbox', SK_HttpUser::profile_id());

                if($service->checkPermissions() == SK_Service::SERVICE_FULL)
                {
                    $Layout->assign('is_guest', !(bool) intval( SK_HttpUser::profile_id() ));
                    $labelEmbed = json_encode( SK_Language::text('components.shoutbox.guest') );
                    $Layout->assign('username_invitation', '<script language="javascript">SK_SetFieldInvitation(' . json_encode( $this->auto_id.'-'.'username' ) . ', ' . $labelEmbed . ' );</script>');
                }
                else
                {
                    $Layout->assign('write_permission_message', $service->permission_message['message']);
                }
            }
            else
            {
                $Layout->assign('read_permission_message', $read_service->permission_message['message']);
            }
        }

        $no_messages = app_Shoutbox::getMessagesCount();
        $no_messages = $no_messages > 0 ? false : true;
        $Layout->assign('no_messages', $no_messages);

        $Layout->assign('label_shoutbox', $this->title);

        $Layout->assign('isModerator', SK_HttpUser::isModerator());

        $labelEmbed = json_encode( SK_Language::text('components.shoutbox.message') );
        $Layout->assign('text_entry_invitation', '<script language="javascript">SK_SetFieldInvitation(' . json_encode( $this->auto_id.'-'.'input' ) . ', ' . $labelEmbed . ' );</script>');

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
	public static function ping( SK_HttpRequestParams $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
            // getting new messages
            $new_messages = app_Shoutbox::getNewMessages($params->lastMessageId);

            if ( $count = count($new_messages) )
            {
                $lastMessageId = $new_messages[$count - 1]['id'];
                $handler->drawMessages($lastMessageId, $new_messages);
            }
	}

    public static function addMessage( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
        $service = new SK_Service('write_shoutbox', SK_HttpUser::profile_id());

        if($service->checkPermissions() == SK_Service::SERVICE_FULL)
        {
            if ($params->username == '')
            {
                $username = app_Profile::username();
            }
            else
            {
                if (app_Profile::getProfileIdByUsername($params->username))
                {
                    $handler->error(SK_Language::text('components.shoutbox.error_profile_username_exist', array( 'url'=>  SK_Navigation::href('sign_in') )));
                    return;
                }
                $username = $params->username;
            }

            app_Shoutbox::addMessage(SK_HttpUser::profile_id(), $username, $params->text , $params->color);

            $service->trackServiceUse();
        }
        else
        {
            $handler->error($service->permission_message['message']);
        }

	}

    public static function deleteMessage( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
        if(SK_HttpUser::isModerator())
        {
            if (!empty($params->params['id']))
            {
                $id = str_replace("delete_", "", $params->params['id']);

                if (!empty($id))
                {
                    $id = (int)$id;
                    if ($id > 0)
                    {
                        app_Shoutbox::deleteMessage($id);
                        $handler->removeMessage("message_".$id);
                    }
                }
            }
        }
        else
        {
            $handler->error(SK_Language::text('components.shoutbox.error_not_moderator'));
        }

	}

}
