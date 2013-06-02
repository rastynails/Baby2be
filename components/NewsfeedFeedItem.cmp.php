<?php

class component_NewsfeedFeedItem extends SK_Component
{
    /**
     *
     * @var $action NewsfeedAction
     */
    private $action;
    private $remove = false;
    private $sharedData = array();
    private $data;
    private $item;

    /**
     * Constructor.
     */
    public function __construct( array $params )
    {
        $this->action = $params['action'];
        $this->sharedData = $params['sharedData'];
        $this->data = $this->getActionData($params['action']);

        $this->remove = SK_HttpUser::is_authenticated()
            && ( $params['action']->getUserId() == SK_HttpUser::profile_id() || SK_HttpUser::isModerator() );

        $usersInfo = $this->sharedData['usersInfo'];

        $configs = $this->sharedData['configs'];

        if ( $this->action->getEntityType() == 'forum_add_topic' || $this->action->getEntityType() == 'friend_add' || $this->action->getEntityType() == 'profile_join'  || $this->action->getEntityType() == 'profile_avatar_change' )
        {
            $feature = FEATURE_NEWSFEED;
            $entityId = $this->action->getEntityId();
            $entityType = $this->action->getEntityType();
        }
        else if ($this->action->getEntityType() == 'profile_comment')
        {
            $feature = FEATURE_NEWSFEED;
            $entityId = $this->data['commentId'];
            $entityType = 'user_comment';
        }
        else if ( FEATURE_NEWSFEED == $this->action->getFeature() )
        {
            $feature = FEATURE_NEWSFEED;
            $entityId = $this->action->getId();
            $entityType = $this->action->getEntityType();
        }
        else
        {
            $feature = $this->action->getFeature();
            $entityId = $this->action->getEntityId();
            $entityType = $this->action->getEntityType();
        }

        if (!empty($this->data['content']))
        {
            $this->data['content']['text'] = app_TextService::stHandleSmiles($this->data['content']['text']);
        }

        $this->item = array(
            'item_auto_id' => $entityType . '_' . $entityId.'_'.$this->action->getId(),
            'id' => $this->action->getId(),
            'view' => $this->data['view'],
            'toolbar' => $this->data['toolbar'],
            'string' => app_TextService::stHandleSmiles($this->data['string']),
            'line' => $this->data['line'],
            'content' => $this->data['content'],
            'context' => $this->data['context'],
            'entityType' => $entityType,
            'entityId' => $entityId,
            'feature' => $this->action->getFeature(),
            'createTime' => SK_I18n::getSpecFormattedDate($this->action->getCreateTime()),
            'updateTime' => $this->action->getUpdateTime(),
            'featuresExpanded' => (bool) $configs['features_expanded'],
            'user' => array(
                'id' => $this->action->getUserId(),
                'avatarUrl' => $usersInfo['avatars'][$this->action->getUserId()],
                'url' => $usersInfo['urls'][$this->action->getUserId()],
                'name' => $usersInfo['names'][$this->action->getUserId()],
            ),
            'remove' => $this->remove,
            'cycle' => $cycle,
            'comments' => false,
            'likes' => false
        );

        if ( $configs['allow_comments'] && in_array('comments', $this->data['features']) )
        {
            $commentService = app_CommentService::newInstance($feature);
            $this->item['comments']['cmp'] = new component_NewsfeedAddComment($entityType, $entityId, $feature);
            $this->item['comments']['count'] = $commentService->findCommentsCount($entityId, $entityType);

            $service = new SK_Service('add_newsfeed_comment', SK_HttpUser::profile_id());
            $this->item['comments']['allow'] = $service->checkPermissions() == SK_Service::SERVICE_FULL;
        }

        if ( $configs['allow_likes'] && in_array('likes', $this->data['features']) )
        {
            $likes = app_Newsfeed::newInstance()->findEntityLikes($entityType, $entityId);

            $userLiked = false;
            foreach ( $likes as $like )
            {
                if ( $like->userId == SK_HttpUser::profile_id() )
                {
                    $userLiked = true;
                }
            }

            $this->item['likes']['count'] = count($likes);
            $this->item['likes']['liked'] = $userLiked;
            $this->item['likes']['allow'] = SK_HttpUser::is_authenticated() && $this->action->getUserId() != SK_HttpUser::profile_id();
            $this->item['likes']['cmp'] = app_Newsfeed::newInstance()->getLikes($entityType, $entityId);
        }


        parent::__construct('newsfeed_feed_item');
    }

    private function getActionData( NewsfeedAction $action )
    {
        $data = json_decode($action->getData(), true);

        $data = empty($data) ? array() : $data;

        $view = array('iconClass' => 'ow_ic_add', 'class' => '', 'style' => '');
        $defaults = array(
            'line' => null, 'string' => null, 'content' => array(), 'toolbar' => array(), 'context' => array(),
            'features' => array('comments', 'likes')
        );

        foreach ( $defaults as $key => $value )
        {
            if ( !isset($data[$key]) )
            {
                $data[$key] = $value;
            }
        }

        if ( !isset($data['view']) || !is_array($data['view']) )
        {
            $data['view'] = array();
        }

        $data['view'] = array_merge($view, $data['view']);

        return $data;
    }

    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $handler = new SK_ComponentFrontendHandler('NewsfeedFeedItem');
        $this->frontend_handler = $handler;

        /* @var $this->action NewsfeedAction  */
        $data = array(
            'item_auto_id' => $this->item['entityType'].'_'.$this->item['entityId'].'_'.$this->item['id'],
            'entityType' => $this->item['entityType'],
            'entityId' => $this->item['entityId'],
            'id' => $this->item['id'],
            'updateStamp' => $this->item['updateTime'],
            'likes' => $this->item['likes'] ? $this->item['likes']['count'] : 0,
            'comments' => $this->item['comments'] ? $this->item['comments']['count'] : 0,
            'cycle' => $this->item['cycle'],
            'profile_id' => SK_HttpUser::profile_id()
        );

        $handler->construct($data);
    }

    public function render( SK_Layout $Layout )
    {
        $Layout->assign('item', $this->item);

        parent::render($Layout);
    }

    public static function ajax_Like( $params, SK_ComponentFrontendHandler $handler )
    {
        if ( !SK_HttpUser::is_authenticated() )
        {
            return;
        }

        app_Newsfeed::newInstance()->addLike(SK_HttpUser::profile_id(), $params->entityType, $params->entityId);

        $likes = app_Newsfeed::newInstance()->getLikes($params->entityType, $params->entityId);
        $count = $likes['count'];
        $markup = $likes['markup'];
        $handler->updateLikes($count, $markup);
    }

    public static function ajax_Unlike( $params, SK_ComponentFrontendHandler $handler )
    {
        if ( !SK_HttpUser::is_authenticated() )
        {
            return;
        }

        app_Newsfeed::newInstance()->removeLike(SK_HttpUser::profile_id(), $params->entityType, $params->entityId);

        $likes = app_Newsfeed::newInstance()->getLikes($params->entityType, $params->entityId);
        $count = $likes['count'];
        $markup = $likes['markup'];
        $handler->updateLikes($count, $markup);
    }

    /**
     * Ajax method for action deleting
     *
     * @param $params
     * @param SK_ComponentFrontendHandler $handler
     */
    public static function ajax_Delete( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
    {
        $actionId = $params->actionId;
        $profile_id = $params->profile_id;
        if ( !empty($actionId) && !empty($profile_id) )
        {
            if ( $profile_id == SK_HttpUser::profile_id() )
            {
                app_Newsfeed::newInstance()->removeActionById($actionId);
                $handler->hide();
            }
        }
    }

    public static function ajax_LoadProfilesInfo( $params, SK_ComponentFrontendHandler $handler )
    {
        $users = $params->users;

        $users = json_decode($users, true);

        $markup = "";
        foreach($users as $userId)
        {
            $markup .= "<div class='newsfeed_userlist_info' style='float: left;'><div class='newsfeed_userlist_picture'><img src='".app_ProfilePhoto::getThumbUrl($userId)."' /></div><div class='newsfeed_userlist_data'><a href='".app_Profile::getUrl($userId)."'>".app_Profile::username($userId)."</a></div></div>";
        }

        $handler->showLikesUserList( $markup );
    }
}
