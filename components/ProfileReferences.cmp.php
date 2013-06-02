<?php

class component_ProfileReferences extends SK_Component
{
    private static $hiddenGiftsAdded = false;

    private $profile_id;

    private $references = array();

    private $mode = 'profile_list';

    public function __construct( array $params = null )
    {
        $this->profile_id = ( isset($params['profile_id']) && ($profile_id = intval($params['profile_id'])) )? $profile_id: 0;

        $this->mode = isset($params['mode']) ? $params['mode'] : 'profile_list';
        $blocked = app_Bookmark::isProfileBlocked($profile_id, SK_HttpUser::profile_id());

        switch ( $this->mode )
        {
            case 'sent_requests':
                $this->references = array('cancel_request');
                break;
            case 'got_requests':
                $this->references = array('accept', 'decline');
                break;
            case 'group_claims_list':
                $this->references = array('accept_claim', 'decline_claim');
                break;
            default:
                if ( $this->profile_id != SK_HttpUser::profile_id() )
                {
                    if ( $this->mode == 'group_list' && $this->displayGroupModeratorReferences() ) //TODO CST - optimize this call
                    {
                        if ( app_Features::isAvailable(38) )                        {
                            $this->references[] = 'delete_group';
                            $this->references[] = 'block_group';
                        }
                    }
                    else
                    {
                        if ( app_Features::isAvailable(19) )                        {
                            $this->references[] = 'block_profile';
                        }

                        if ( app_Features::isAvailable(18) && !$blocked )                        {
                            $this->references[] = 'bookmark';
                        }

                        if ( app_Features::isAvailable(14) && !$blocked )                        {
                            $this->references[] = 'friend_nw';
                        }

                        if ( app_Features::isAvailable(53) && !$blocked )                        {
                            $this->references[] = 'speed_dating_unbookmark';
                        }

                        if ( app_Features::isAvailable(59) && !$blocked )
                        {
                            $this->references[] = 'newsfeed_follow';
                            $this->references[] = 'newsfeed_unfollow';
                        }

                        if ( SK_HttpUser::is_authenticated() && app_Features::isAvailable(8) && !$blocked )
                        {
                            $this->references[] = 'send_gift';
                        }
                    }
                }
                break;
        }

        parent::__construct('profile_references');
    }


    /**
     * @see SK_Component::render()
     *
     * @param SK_Layout $Layout
     */
    public function render ( SK_Layout $Layout ) 
    {
        if ( !self::$hiddenGiftsAdded )
        {
           $Layout->renderHiddenComponent('send_virtual_gift', new component_SendVirtualGift(array('hidden' => true)));
            self::$hiddenGiftsAdded = true;
        }

        return parent::render( $Layout );
    }

    private function prepareReferences( $handler )
    {
        $lang_section = SK_Language::section('components.profile_references.labels');

        foreach ( $this->references as $reference )
        {
            switch ( $reference )
            {
                case 'block_profile':
                    $handler->registerReference(array('name' => 'block_profile', 'label' => $lang_section->text('block'), 'backend_func' => 'ajax_Handler'));
                    $handler->registerReference(array('name' => 'unblock_profile', 'label' => $lang_section->text('unblock'), 'backend_func' => 'ajax_Handler'));
                    break;

                case 'block_group':
                    $handler->registerReference(
                        array(
                            'name' => 'block_group_profile',
                            'label' => $lang_section->text('block_group_access'),
                            'group_id' => SK_HttpRequest::$GET['group_id'],
                            'backend_func' => 'ajax_Handler'
                        )
                    );
                    $handler->registerReference(
                        array(
                            'name' => 'unblock_group_profile',
                            'label' => $lang_section->text('unblock_group_access'),
                            'group_id' => SK_HttpRequest::$GET['group_id'],
                            'backend_func' => 'ajax_Handler'
                        )
                    );
                    break;

                case 'delete_group':
                    $handler->registerReference(
                        array(
                            'name' => 'delete_group_profile',
                            'label' => $lang_section->text('delete_from_group'),
                            'group_id' => SK_HttpRequest::$GET['group_id'],
                            'backend_func' => 'ajax_Handler'
                        )
                    );
                    break;

                case 'bookmark':
                    $handler->registerReference(array('name' => 'bookmark', 'label' => $lang_section->text('bookmark'), 'backend_func' => 'ajax_Handler'));
                    $handler->registerReference(array('name' => 'unbookmark', 'label' => $lang_section->text('unbookmark'), 'backend_func' => 'ajax_Handler'));
                    break;
                case 'friend_nw':
                    $handler->registerReference(array('name' => 'send_friend_request', 'label' => $lang_section->text('send_friend_request'), 'backend_func' => 'ajax_Handler'));
                    $handler->registerReference(array('name' => 'sent_friend_requests', 'label' => $lang_section->text('sent_friend_requests'), 'backend_func' => 'ajax_Handler'));
                    $handler->registerReference(array('name' => 'remove_friend', 'label' => $lang_section->text('remove_friend'), 'backend_func' => 'ajax_Handler'));
                    break;

                case 'cancel_request':
                    $handler->registerReference(array('name' => 'cancel_request', 'label' => $lang_section->text('cancel_request'), 'backend_func' => 'ajax_Handler'));
                    break;
                case 'accept':
                    $handler->registerReference(array('name' => 'accept', 'label' => $lang_section->text('accept'), 'backend_func' => 'ajax_Handler'));
                    break;
                case 'decline':
                    $handler->registerReference(array('name' => 'decline', 'label' => $lang_section->text('decline'), 'backend_func' => 'ajax_Handler'));
                    break;

                case 'accept_claim':
                    $handler->registerReference(
                        array(
                            'name' => 'accept_claim',
                            'label' => $lang_section->text('accept'),
                            'group_id' => SK_HttpRequest::$GET['group_id'],
                            'backend_func' => 'ajax_Handler'
                        )
                    );
                    break;

                case 'decline_claim':
                    $handler->registerReference(
                        array(
                            'name' => 'decline_claim',
                            'label' => $lang_section->text('decline'),
                            'group_id' => SK_HttpRequest::$GET['group_id'],
                            'backend_func' => 'ajax_Handler'
                        )
                    );
                    break;
                case 'speed_dating_unbookmark':
                    $handler->registerReference(array('name' => 'speed_dating_unbookmark', 'label' => $lang_section->text('speed_dating_unbookmark'), 'backend_func' => 'ajax_Handler'));
                    break;
                case 'newsfeed_follow':
                    $handler->registerReference(array('name' => 'newsfeed_follow', 'label' => $lang_section->text('newsfeed_follow'), 'backend_func' => 'ajax_Handler'));
                    break;
                case 'newsfeed_unfollow':
                    $handler->registerReference(array('name' => 'newsfeed_unfollow', 'label' => $lang_section->text('newsfeed_unfollow'), 'backend_func' => 'ajax_Handler'));
                    break;

                case 'send_gift':
                    $handler->registerReference(
                        array(
                            'name' => 'send_gift',
                            'label' => SK_Language::text('%components.send_virtual_gift.gift_btn'),
                            'click_func' => 'send_gift_fb'
                        )
                    );
                    break;
            }
        }
    }



    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $handler = new SK_ComponentFrontendHandler('ProfileReferences');

        $handler->construct($this->profile_id, $this->mode);

        $this->prepareReferences($handler);

        if ( in_array('speed_dating_unbookmark', $this->references) && app_EventService::isSpeedDatingProfileBookmarked(SK_HttpUser::profile_id(), $this->profile_id) )        {
            $handler->displayReference('speed_dating_unbookmark');
        }

        if ( $blocked = (in_array('block_profile', $this->references) && app_Bookmark::isProfileBlocked(SK_HttpUser::profile_id(), $this->profile_id)) )        {
            $handler->displayReference('unblock_profile');
        }
        else
        {
            $handler->displayReference('block_profile');
        }

        if ( in_array('friend_nw', $this->references) && app_FriendNetwork::isProfileFriend(SK_HttpUser::profile_id(), $this->profile_id) )        {

            $handler->displayReference('remove_friend');
        }
        elseif ( in_array('friend_nw', $this->references) && app_FriendNetwork::isMemberSentRequest(SK_HttpUser::profile_id(), $this->profile_id) )        {
            $handler->displayReference('sent_friend_requests');
        }
        else        {
            $handler->displayReference('send_friend_request');
        }

        if ( in_array('bookmark', $this->references) && !app_Bookmark::isProfileBookmarked(SK_HttpUser::profile_id(), $this->profile_id) )        {
            $handler->displayReference('bookmark');
        }
        else        {
            $handler->displayReference('unbookmark');
        }

        if ( in_array('cancel_request', $this->references) && app_FriendNetwork::isMemberSentRequest(SK_HttpUser::profile_id(), $this->profile_id) )        {
            $handler->displayReference('cancel_request');
        }

        if ( in_array('accept', $this->references) && in_array('decline', $this->references) && app_FriendNetwork::isProfileHasGotRequest(SK_HttpUser::profile_id(), $this->profile_id) )        {
            $handler->displayReference('accept');
            $handler->displayReference('decline');
        }

        if ( in_array('newsfeed_follow', $this->references) && in_array('newsfeed_unfollow', $this->references) && app_Newsfeed::newInstance()->isFollow(SK_HttpUser::profile_id(), 'user', $this->profile_id) )
        {

            $handler->displayReference('newsfeed_unfollow');
        }
        else
        {
            $handler->displayReference('newsfeed_follow');
        }

        //==============================================================================================

        if ( $blocked )        {
            $handler->hideOther('unblock_profile');
        }        elseif ( app_Bookmark::isProfileBlocked($this->profile_id, SK_HttpUser::profile_id()) )
        {
            $handler->hideOther('block_profile');
        }

        $is_group_member = app_Groups::isGroupMember($this->profile_id, SK_HttpRequest::$GET['group_id']);
        $is_group_creator = app_Groups::isGroupCreator($this->profile_id, SK_HttpRequest::$GET['group_id']);
        $is_group_blocked = app_Groups::isBlocked($this->profile_id, SK_HttpRequest::$GET['group_id']);

        if ( $blocked = (in_array('block_group', $this->references) && $is_group_blocked) )        {
            $handler->displayReference('unblock_group_profile');
        }
        else if ( !$is_group_creator )
        {
            $handler->displayReference('block_group_profile');
        }

        if ( in_array('decline_claim', $this->references) && !$is_group_member )
            $handler->displayReference('decline_claim');
        if ( in_array('accept_claim', $this->references) && !$is_group_member )
            $handler->displayReference('accept_claim');

        if ( in_array('delete_group', $this->references) && !$is_group_creator )        {
            $handler->displayReference('delete_group_profile');
        }

        $handler->displayReference('send_gift');
        
        $this->frontend_handler = $handler;

        parent::prepare($Layout, $Frontend);
    }


    private function displayGroupModeratorReferences()
    {
        if ( $this->profile_id && $group_id = (int) SK_HttpRequest::$GET['group_id'] )
        {
            $is_creator = app_Groups::isGroupCreator(SK_HttpUser::profile_id(), $group_id);
            $is_moderator = app_Groups::isGroupModerator(SK_HttpUser::profile_id(), $group_id)
                || app_Profile::isProfileModerator(SK_HttpUser::profile_id());

            if ( $is_creator || $is_moderator )
                return true;
        }
        return false;
    }

    public static function ajax_Handler( $params, SK_ComponentFrontendHandler $handler )
    {
        $profile_id = (int) $params->profile_id;
        $reference = $params->reference;
        $list = $params->mode;

        $error_section = SK_Language::section('components.profile_references.messages.error');
        $msg_section = SK_Language::section('components.profile_references.messages.success');

        switch ( $reference )
        {

            case 'bookmark':

                $service = new SK_Service('bookmark_members', SK_HttpUser::profile_id());
                if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )                {
                    $handler->error($service->permission_message['message']);
                    break;
                }

                if ( !app_Bookmark::BookmarkProfile(SK_HttpUser::profile_id(), $profile_id) )                {
                    $handler->error($error_section->text('bookmark'));
                    break;
                }

                $service->trackServiceUse();
                $handler->changeReference('bookmark', 'unbookmark');
                $handler->message($msg_section->text('bookmark'));
                break;

            case 'unbookmark':
                if ( !app_Bookmark::UnbookmarkProfile(SK_HttpUser::profile_id(), $profile_id) )                {
                    $handler->error($error_section->text('unbookmark'));
                    break;
                }

                if ( $list == 'bookmark_list' )                {
                    $handler->redirect();
                }
                else                {
                    $handler->changeReference('unbookmark', 'bookmark');
                    $handler->message($msg_section->text('unbookmark'));
                }

                break;
            case 'speed_dating_unbookmark':
                if ( !app_EventService::UnbookmarkSpeedDatingProfile(SK_HttpUser::profile_id(), $profile_id) )                {
                    $handler->error($error_section->text('unbookmark'));
                    break;
                }
                $handler->message($msg_section->text('speed_dating_unbookmark'));
                $handler->redirect();

                break;
            case 'block_profile':
                $service = new SK_Service('block_members', SK_HttpUser::profile_id());
                if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )                {
                    $handler->error($service->permission_message['message']);
                    break;
                }

                if ( !app_Bookmark::BlockProfile(SK_HttpUser::profile_id(), $profile_id) )                {
                    $handler->error($error_section->text('block'));
                    break;
                }
                $service->trackServiceUse();

                $handler->changeReference('block_profile', 'unblock_profile');
                $handler->changeReference('unbookmark', 'bookmark');
                $handler->changeReference('remove_friend', 'send_friend_request');
                $handler->changeReference('sent_friend_requests', 'send_friend_request');
                $handler->hideOther('unblock_profile');
                $handler->message($msg_section->text('block'));
                $handler->displayReference('block_group_profile');
                break;

            case 'unblock_profile':
                if ( !app_Bookmark::UnblockProfile(SK_HttpUser::profile_id(), $profile_id) )                {
                    $handler->error($error_section->text('unblock'));
                    break;
                }

                if ( $list == 'block_list' || $list == 'friends' || $list == 'bookmark_list' )                {
                    $handler->redirect();
                }
                else                {
                    $handler->changeReference('unblock_profile', 'block_profile');
                    if ( !app_Bookmark::isProfileBlocked($profile_id, SK_HttpUser::profile_id()) )                    {
                        $handler->showOther('block_profile');
                    }

                    $handler->message($msg_section->text('unblock'));
                }

                break;

            case 'block_group_profile':
                $group_id = $params->group_id;
                if ( !app_Groups::blockMember($profile_id, $group_id) )                {
                    $handler->error($error_section->text('group_block'));
                    break;
                }

                $handler->changeReference('block_group_profile', 'unblock_group_profile');
                $handler->message($msg_section->text('group_block'));
                $handler->displayReference('unblock_group_profile');
                break;

            case 'unblock_group_profile':
                $group_id = $params->group_id;
                if ( !app_Groups::unblockMember($profile_id, $group_id) )                {
                    $handler->error($error_section->text('group_unblock'));
                    break;
                }

                $handler->changeReference('unblock_group_profile', 'block_group_profile');
                $handler->message($msg_section->text('group_unblock'));

                break;

            case 'delete_group_profile':
                $group_id = $params->group_id;
                if ( app_Groups::removeGroupMember($profile_id, $group_id) )                {
                    $handler->message($msg_section->text('gelete_profile'));
                    $handler->redirect();
                }
                break;

            case 'decline_claim':
                $group_id = $params->group_id;
                if ( app_Groups::declineClaim($profile_id, $group_id) )                {
                    $handler->message($msg_section->text('decline_claim'));
                    $handler->redirect();
                }
                break;

            case 'accept_claim':
                $group_id = $params->group_id;
                if ( app_Groups::acceptClaim($profile_id, $group_id) )                {
                    $handler->message($msg_section->text('accept_claim'));
                    $handler->redirect();
                }
                break;

            case 'send_friend_request':
                $service = new SK_Service('creat_friends_network', SK_HttpUser::profile_id());
                if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )                {
                    $handler->error($service->permission_message['message']);
                    break;
                }

                if ( !app_FriendNetwork::SendFriendRequest(SK_HttpUser::profile_id(), $profile_id) )                {
                    $handler->error($error_section->text('send_request'));
                    break;
                }

                $service->trackServiceUse();

                $handler->changeReference('send_friend_request', 'sent_friend_requests');

                app_FriendNetwork::sendFriendNetworkMessage($profile_id, 'request_got');

                $handler->message($msg_section->text('send_request'));

                break;

            case 'sent_friend_requests':

                if ( $url = app_FriendNetwork::FriendNetworkTabUrl('sent_requests', $profile_id) )                {
                    $handler->redirect($url);
                }
                break;

            case 'accept':
                if ( app_FriendNetwork::confirmRequest(SK_HttpUser::profile_id(), $profile_id) )                {

                    app_FriendNetwork::sendFriendNetworkMessage($profile_id, 'request_accepted');
                    if ( app_FriendNetwork::countGotRequests(SK_HttpUser::profile_id()) )                    {
                        $handler->redirect();
                    } else {
                        $handler->redirect(SK_Navigation::href("profile_friend_list"));
                    }

                }

                break;

            case 'decline':
                app_FriendNetwork::declineRequest(SK_HttpUser::profile_id(), $profile_id);

                app_FriendNetwork::sendFriendNetworkMessage($profile_id, 'request_declined');

                $handler->redirect();
                break;

            case 'remove_friend':
                if ( !app_FriendNetwork::DeleteFromFriendList(SK_HttpUser::profile_id(), $profile_id) )                {
                    $handler->error($error_section->text('remove_friend'));
                    break;
                }

                app_FriendNetwork::sendFriendNetworkMessage($profile_id, 'profile_deleted');

                if (app_Features::isAvailable(app_Newsfeed::FEATURE_ID))
                {
                    app_Newsfeed::newInstance()->removeFollow(SK_HttpUser::profile_id(), 'user', $profile_id);
                    app_Newsfeed::newInstance()->removeFollow($profile_id, 'user', SK_HttpUser::profile_id());
                }

                if ( $list == 'friends' ){
                    $handler->redirect();
                }
                else{
                    $handler->changeReference('remove_friend', 'send_friend_request');
                    $handler->message($msg_section->text('remove_friend'));
                }
                break;

            case 'cancel_request':
                if ( app_FriendNetwork::cancelRequest(SK_HttpUser::profile_id(), $profile_id) )                {
                    $handler->redirect();
                }
                break;
            case 'newsfeed_follow':

                if ( !SK_HttpUser::is_authenticated() )
                {
                    $str = SK_Language::section('membership')->text('service_no_permission_for_guest');
                    $permission_message = str_replace( array('{$service}', '<url>', '</url>'), array(SK_Language::section('membership.services')->text('follow_members'), '<a href="#" onclick="SK_SignIn(); return false">', '</a>'), $str );
                    $handler->error( $permission_message );
                    break;
                }

                $permission = app_FriendNetwork::isProfileFriend(SK_HttpUser::profile_id(), $profile_id) ? app_Newsfeed::PRIVACY_FRIENDS_ONLY : app_Newsfeed::PRIVACY_EVERYBODY;
                $follow = app_Newsfeed::newInstance()->addFollow(SK_HttpUser::profile_id(), 'user', $profile_id, $permission);
                if ( !empty($follow) )
                {
                    $handler->changeReference('newsfeed_follow', 'newsfeed_unfollow');
                    $handler->message($msg_section->text('newsfeed_follow', array('username' => app_Profile::username($profile_id))));
                }
                else
                {
                    $handler->message($error_section->text('newsfeed_follow', array('username' => app_Profile::username($profile_id))));
                }

                break;
            case 'newsfeed_unfollow':

                app_Newsfeed::newInstance()->removeFollow(SK_HttpUser::profile_id(), 'user', $profile_id);
                $handler->changeReference('newsfeed_unfollow', 'newsfeed_follow');
                $handler->message($msg_section->text('newsfeed_unfollow', array('username' => app_Profile::username($profile_id))));
                break;
        }

        return true;
    }
}
