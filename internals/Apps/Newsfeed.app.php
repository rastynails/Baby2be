<?php

require_once DIR_APPS . 'appAux/NewsfeedActionDao.php';
require_once DIR_APPS . 'appAux/NewsfeedActionFeedDao.php';
require_once DIR_APPS . 'appAux/NewsfeedFollowDao.php';
require_once DIR_APPS . 'appAux/NewsfeedLikeDao.php';

class app_Newsfeed
{
    const FEATURE_ID = 59;

    const VISIBILITY_SITE = 1;
    const VISIBILITY_FOLLOW = 2;
    const VISIBILITY_AUTHOR = 4;
    const VISIBILITY_FEED = 8;
    const VISIBILITY_FULL = 15;

    const PRIVACY_EVERYBODY = 'everybody';
    const PRIVACY_FRIENDS_ONLY = 'friends_only';

    const ACTION_STATUS_ACTIVE = 'active';
    const ACTION_STATUS_INACTIVE = 'inactive';

    /**
     *
     * @var NewsfeedActionDao
     */
    private $actionDao;

    /**
     *
     * @var NewsfeedFollowDao
     */
    private $followDao;

    /**
     *
     * @var NewsfeedActionFeedDao
     */
    private $actionFeedDao;

    /**
     *
     * @var NewsfeedLikeDao
     */
    private $likeDao;

    /**
     * @var app_Newsfeed
     */
    private static $classInstance;

    private function __construct()
    {
        $this->actionDao = new NewsfeedActionDao();
        $this->actionFeedDao = new NewsfeedActionFeedDao();
        $this->followDao = new NewsfeedFollowDao();
        $this->likeDao = new NewsfeedLikeDao();
    }

    /**
     * Returns the only instance of the class
     *
     * @return app_Newsfeed
     */
    public static function newInstance()
    {
        if ( self::$classInstance === null )
            self::$classInstance = new self;

        return self::$classInstance;
    }

    /**
     *  Add action to newsfeed
     *
     * @param array $dataParams  ('params' => array( 'feature', 'entityType', 'entityId', 'userId' ), 'data' => array(...))
     */
    public function action( $dataParams )
    {
        $params = $dataParams['params'];
        $data = (!empty($dataParams['data']) ) ? $dataParams['data'] : array();

        if ( $params['entityType'] != 'profile_join' )
        {
            $not_reviewed_profile_access = SK_Config::section('site')->Section('additional')->Section('profile')->not_reviewed_profile_access;
            if ( !app_Profile::reviewed($params['userId']) && !$not_reviewed_profile_access )
                return;

            $profile_status = app_Profile::getFieldValues($params['userId'], 'status');

            if ( $profile_status != 'active' )
                return;
        }

        if ($params['entityType'] == 'status_update')
        {
            if (isset($data['commentId']))
            {
                $originalAction = $this->getActionById($params['entityId']);
            }
        }
        else
        {
            if ($params['entityType'] != 'event_attend')
        	{
                $originalAction = $this->getAction($params['entityType'], $params['entityId']);
            }
        }

        if ( !empty($originalAction) )
        {
            $data['originalAction'] = $originalAction;
        }

        $params = $this->extractActionParams($params, $data);

        /* @var $action NewsfeedAction */

        if ( $data['originalAction'] !== null )
        {
            $params['feature'] = empty($params['feature']) ? $data['originalAction']->getFeature() : $params['feature'];

            $entityTypeData = $this->getEntityTypeData($params, $data);
            $params = $entityTypeData['params'];
            $data = $entityTypeData['data'];

            if ( empty($data['content']) && empty($data['string']) && empty($data['line']) )
            {
                return;
            }

            $action = $this->convertDataParamsToAction($params, $data);
            //$action->setId($data['originalAction']->getId());
            $action->setUpdateTime(time());
        }
        else
        {
            $entityTypeData = $this->getEntityTypeData($params, $data);

            $params = $entityTypeData['params'];
            $data = $entityTypeData['data'];

            if ( empty($data['content']) && empty($data['string']) && empty($data['line']) )
            {
                return;
            }

            $action = $this->convertDataParamsToAction($params, $data);
        }

        $actionId = $this->saveAction($action)->getId();

        if ( $params['postOnUserFeed'] )
        {
            $this->addActionToFeed('user', (int) $params['userId'], $actionId);
        }

        if ( !empty($params['feedType']) && !empty($params['feedId']) )
        {
            $this->addActionToFeed($params['feedType'], (int) $params['feedId'], $actionId);
        }
    }

    public function addActionToFeed( $feedType, $feedId, $actionId )
    {
        $actionFeed = $this->actionFeedDao->findByFeedTypeFeedIdActionId(trim($feedType), (int) $feedId, (int) $actionId);
        if ( empty($actionFeed) )
        {
            $actionFeed = new NewsfeedActionFeed();
            $actionFeed->setFeedType(trim($feedType));
            $actionFeed->setFeedId((int) $feedId);
            $actionFeed->setActionId((int) $actionId);

            $this->actionFeedDao->saveOrUpdate($actionFeed);
        }

        return $actionFeed;
    }

    public function addFollow( $userId, $feedType, $feedId, $permission )
    {
        return $this->followDao->addFollow($userId, $feedType, $feedId, $permission);
    }

    public function addLike( $userId, $entityType, $entityId )
    {
        if ( !app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            return;

        $dataParams = array(
            'params' => array(
                'entityType' => $entityType,
                'entityId' => $entityId,
                'userId' => $userId,
                'time' => time()
            )
        );
        $this->action($dataParams);

        return $this->likeDao->addLike($userId, $entityType, $entityId);
    }

    /**
     *
     * @param array $params
     * @param array $data
     * @return NewsfeedAction
     */
    private function convertDataParamsToAction( $params, $data )
    {
        if ( !isset($data['originalAction']) || $data['originalAction'] === null || $params['replace'] )
        {
            $action = new NewsfeedAction();

            $action->setEntityType($params['entityType']);
            $action->setEntityId($params['entityId']);
            $action->setFeature($params['feature']);
            $action->setStatus(empty($params['status']) ? 'active' : $params['status']);
            if (!empty($params['create_time']))
            {
                $action->setCreateTime($params['create_time']);
            }
            else
            {
                $action->setCreateTime(empty($params['time']) ? time() : $params['time']);
            }

            $action->setUpdateTime(empty($params['time']) ? time() : $params['time']);
            $action->setUserId(empty($data['ownerId']) ? $params['userId'] : $data['ownerId']);
        }
        else
        {
            $action = $data['originalAction'];
        }

        if ( !empty($params['time']) )
        {
            //$action->setCreateTime($params['time']);
            $action->setUpdateTime($params['time']);
        }

        if ( !empty($params['feature']) )
        {
            $action->setFeature($params['feature']);
        }

        $action->setVisibility($params['visibility']);
        $action->setPrivacy($params['privacy']);

        unset($data['originalAction']);
        unset($data['ownerId']);

        $actionData = array();
        foreach ( $data as $name => $value )
        {
            $actionData[$name] = $value;
        }

        $action->setData(json_encode($actionData));

        return $action;
    }

    /**
     *  Merge existing and default paramters for feeds
     *
     * @param array $params Parameters for feed (visibility, privacy, etc.)
     * @param array $data Data of action
     * @return array() Array with default and action custom parameters
     */
    private function extractActionParams( $params, $data )
    {
        if ( empty($params['userId']) )
        {
            $params['userId'] = SK_HttpUser::profile_id();
        }

        $visibility = ( app_ProfilePreferences::get('my_profile', 'is_profile_private', $params['userId']) ) ? app_Newsfeed::VISIBILITY_AUTHOR + app_Newsfeed::VISIBILITY_FOLLOW : app_Newsfeed::VISIBILITY_FULL;

        $privacy = ( app_ProfilePreferences::get('my_profile', 'is_profile_private', $params['userId']) ) ? app_Newsfeed::PRIVACY_FRIENDS_ONLY : app_Newsfeed::PRIVACY_EVERYBODY;

        $defaultParams = array(
            'postOnUserFeed' => true,
            'visibility' => $visibility,
            'privacy' => $privacy,
            'replace' => false
        );

        if ( isset($data['params']) && is_array($data['params']) )
        {
            $params = array_merge($params, $data['params']);
            unset($data['params']);
        }

        return array_merge($defaultParams, $params);
    }

    /**
     *
     * @param string $entityType
     * @param int $entityId
     * @return NewsfeedAction
     */
    public function findAction( $entityType, $entityId )
    {
        $dto = $this->actionDao->findAction($entityType, $entityId);

        return $dto;
    }

    public function findActionsByUserId( $userId )
    {
        return $this->actionDao->findListByUserId($userId);
    }

    /**
     *
     * @param int $actionId
     * @return NewsfeedAction
     */
    public function findActionById( $actionId )
    {
        $dto = $this->actionDao->findById($actionId);

        return $dto;
    }

    public function findEntityLikes( $entityType, $entityId )
    {
        return $this->likeDao->findByEntity($entityType, $entityId);
    }

    public function findEntityLikesCount( $entityType, $entityId )
    {
        return $this->likeDao->findCountByEntity($entityType, $entityId);
    }

    public function findEntityLikeUserIds( $entityType, $entityId )
    {
        $likes = $this->findEntityLikes($entityType, $entityId);
        $out = array();

        foreach ( $likes as $like )
        {
            /* @var $like NewsfeedLike */
            $out[] = $like->userId;
        }

        return $out;
    }

    public function findExpiredActions( $inactivePeriod )
    {
        $this->actionDao->findExpired($inactivePeriod);
    }

    public function findByFeed( $feedType, $feedId )
    {
        return $this->actionDao->findByFeed($feedType, $feedId);
    }

    public function findFeedCount( $feedType, $feedId )
    {
        return $this->actionDao->findCountByFeed($feedType, $feedId);
    }

    public function findSiteFeed( $count, $startTime )
    {
        return $this->actionDao->findSiteFeed($count, $startTime);
    }

    public function findSiteFeedCount()
    {
        return $this->actionDao->findSiteFeedCount();
    }

    public function findUserFeed( $userId, $count = null, $startTime = null )
    {
        return $this->actionDao->findByUser($userId, $count, $startTime);
    }

    public function findUserFeedCount( $userId )
    {
        return $this->actionDao->findCountByUser($userId);
    }

    public function findUserLikes( $userId )
    {
        return $this->likeDao->findByUserId($userId);
    }

    /**
     *
     * @param string $entityType
     * @param int $entityId
     * @return NewsfeedAction
     */
    public function getAction( $entityType, $entityId, $userId = null )
    {
        return $this->actionDao->findAction($entityType, $entityId, $userId);
    }

    /**
     *
     * @param int $id
     * @return NewsfeedAction
     */
    public function getActionById( $id )
    {
        return $this->actionDao->findById($id);
    }

    public function getLikes( $entityType, $entityId )
    {
        $likes = $this->findEntityLikes($entityType, $entityId);
        $count = $this->findEntityLikesCount($entityType, $entityId);
        if ( $count == 0 )
        {
            return array('count' => 0, 'markup' => '');
        }
        $userIds = array();
        foreach ( $likes as $like )
        {
            $userIds[] = $like->getUserId();
        }

        if ( $count <= 3 )
        {
            $langVars = array();

            foreach ( $userIds as $id => $userId )
            {
                $langVars['user' . ($id + 1)] = '<a href="' . app_Profile::getUrl($userId) . '">' . app_Profile::username($userId) . '</a>';
            }

            return array('count' => $count, 'markup' => SK_Language::text('components.newsfeed_feed_item.feed_likes_' . $count . '_label', $langVars));
        }
        else
        {
            $url_inner = " class='showLikesUserlist' href='javascript://' ";
            return array('count' => $count, 'markup' => "<input type='hidden' class='showLikesUserlistValue' value='" . json_encode($userIds) . "' />" . SK_Language::text('components.newsfeed_feed_item.feed_likes_list_label', array('count' => $count, 'url' => $url_inner)));
        }
    }

    public function getEntityTypeData( $params, $data )
    {
        switch ( $params['entityType'] )
        {
            case 'status_update':
                if (isset($data['originalAction']))
                {
                    $originalActionData = json_decode($data['originalAction']->getData(), true);
                    $data['string'] = $originalActionData['string'];
                }

                break;

            case 'blog_post_add':
            case 'news_post_add':

                $blogPost = app_BlogService::newInstance()->findBlogPostById($params['entityId']);

                $data['content'] = array('href' => SK_Navigation::href('blog_post_view', array('postId' => $blogPost->getId())),
                    'title' => $blogPost->getTitle(),
                    'text' => $this->truncate( str_replace("\n", "", strip_tags( $blogPost->getText() )) )
                    );

                $data['ownerId'] = $blogPost->getProfile_id();

                $params['create_time'] = $blogPost->getCreate_time_stamp();
                $params['feature'] = FEATURE_BLOG;
                break;

            case 'friend_add':

                if ($data['originalAction'] !== null)
                {
                    $originalActionData = json_decode($data['originalAction']->getData(), true);
                    $data['friend_profile_id'] = $originalActionData['content']['friend_id'];
                    $data['profile_id'] = $originalActionData['content']['profile_id'];
                }

                $data['line'] = 'friend_add';
                $data['content'] = array(
                    'friend_id' => $data['friend_profile_id'],
                    'profile_id' => $data['profile_id']
                );

                $params['feedType'] = 'user';
                $params['feedId'] = $data['friend_profile_id'];

                $this->addFollow($data['friend_profile_id'], 'user', $data['profile_id'], app_Newsfeed::PRIVACY_FRIENDS_ONLY);
                $this->addFollow($data['profile_id'], 'user', $data['friend_profile_id'], app_Newsfeed::PRIVACY_FRIENDS_ONLY);

                unset($data['friend_profile_id']);
                unset($data['profile_id']);
                $params['feature'] = FEATURE_NEWSFEED;
                break;

            case 'profile_avatar_change':
            case 'profile_edit':
            case 'profile_join':
                $data['string'] = $params['entityType'];
                $params['feature'] = FEATURE_NEWSFEED;

                //$this->removeAction($params['entityType'], $params['entityId']);

                break;

            case 'profile_comment':

                $comment = app_CommentService::newInstance(FEATURE_PROFILE)->findCommentById($data['commentId']);

                $data['context'] = array('url' => app_Profile::getUrl($params['entityId']),
                    'label' => app_Profile::username($params['entityId'])
                );
                $data['string'] = $this->truncate($comment->getText());

                $params['feedType'] = 'user';
                $params['feedId'] = $params['entityId'];
                $params['feature'] = FEATURE_PROFILE;
                break;

            case 'user_comment':

                $comment = app_CommentService::newInstance(FEATURE_PROFILE)->findCommentById($params['entityId']);

                $data['context'] = array('url' => app_Profile::getUrl($comment->getAuthor_id()),
                    'label' => app_Profile::username($comment->getAuthor_id())
                );
                $data['string'] = $this->truncate($comment->getText());

                $params['feedType'] = 'user';
                $params['feedId'] = $params['entityId'];
                $params['feature'] = FEATURE_NEWSFEED;
                break;


            case 'post_classifieds_item':

                $classifiedsItem = app_ClassifiedsItemService::stGetItemInfo($params['entityId']);

                if ( !$classifiedsItem['allow_comments'] )
                {
                    $data['features'] = array('likes');
                }

                $data['content'] = array('href' => SK_Navigation::href('classifieds_item', array('item_id' => $classifiedsItem['item_id'])),
                    'title' => $classifiedsItem['title'],
                    'text' => $this->truncate( $classifiedsItem['description'] ),
                    'entity' => $classifiedsItem['entity']
                );

                $cls_files = app_ClassifiedsFileService::stGetItemFiles($classifiedsItem['item_id']);

                if ( count($cls_files) > 0 )
                {
                    $data['content']['img_src'] = $cls_files[0]['file_url'];
                }

                $data['ownerId'] = $classifiedsItem['profile_id'];
                $params['create_time'] = $classifiedsItem['create_stamp'];
                $params['feature'] = FEATURE_CLASSIFIEDS;
                break;
            case 'event_attend':
            case 'event_add':

                $event = app_EventService::stFindEventInfo($params['entityId']);

                $data['content'] = array('href' => SK_Navigation::href('event', array('eventId' => $params['entityId'])),
                    'title' => $event['dto']->getTitle(),
                    'text' =>  $this->truncate( $event['dto']->getDescription() )
                );

                if ( $event['items_count'] > 0 )
                {
                    $data['toolbar'] = array(
                        'event_attendees' => array(
                            'count' => $event['items_count']
                        )
                    );
                }

                if ( $event['dto']->getIs_speed_dating() )
                {
                    $data['content']['is_speed_dating'] = true;
                }

                $event_image = $event['dto']->getImage();
                if ( !empty($event_image) )
                {
                    $data['content']['img_src'] = app_EventService::newInstance()->getEventImageURL($event['dto']->getImage());
                }

                $params['time'] = $event['dto']->getCreate_date();
                //$params['create_time'] = $event['dto']->getCreate_date();
                //$data['ownerId'] = $event['dto']->getProfile_id();
                $params['feature'] = FEATURE_EVENT;
                break;
            case 'group_add':
            case 'group_join':

                $group = app_Groups::getGroupById($params['entityId']);

                $visibility = app_Newsfeed::VISIBILITY_FULL;
                if ( $group['browse_type'] == 'private' )
                {
                    $visibility = app_Newsfeed::VISIBILITY_AUTHOR + app_Newsfeed::VISIBILITY_FEED;
                }

                $data['content'] = array(
                    'group_id' => $params['entityId'],
                    'title' => $group['title'],
                    'text' => $group['description'],
                    'img_src' => app_Groups::getGroupImageURL($params['entityId'])
                );

                $data['features'] = array('likes');

                if ( $group['browse_type'] == 'private' && $group['join_type'] == 'closed' && !$group['allow_claim'] )
                {
                    return array('data' => array(), 'params' => array());
                }

                $params['status'] = $group['status'];
                //$data['ownerId'] = $group['owner_id'];
//print_r($params);
                $params['visibility'] = $visibility;
                $params['feature'] = FEATURE_GROUP;
                break;

            case 'photo_upload':

                $photo = app_ProfilePhoto::getPhoto($params['entityId']);
                $photo_info = app_ProfilePhoto::getPhotoInfo($params['entityId']);

                $visibility = app_Newsfeed::VISIBILITY_FULL;
                $privacy = app_Newsfeed::PRIVACY_EVERYBODY;

                if ( $photo->publishing_status == 'friends_only' )
                {
                    $visibility = app_Newsfeed::VISIBILITY_AUTHOR + app_Newsfeed::VISIBILITY_FOLLOW;
                    $privacy = app_Newsfeed::PRIVACY_FRIENDS_ONLY;
                }
                $album_id = app_PhotoAlbums::getPhotoAlbum($params['entityId']);
                if ( !empty($album_id) )
                {
                    $album = app_PhotoAlbums::getAlbum($album_id);
                    if ( $album->getPrivacy() != 'public' )
                    {
                        $visibility = app_Newsfeed::VISIBILITY_AUTHOR + app_Newsfeed::VISIBILITY_FOLLOW;
                        $privacy = app_Newsfeed::PRIVACY_FRIENDS_ONLY;
                    }
                    $data['string'] = $params['entityType'];
                    $data['toolbar'] = array(
                        'album_link' => array(
                            'href' => $album->getUrl(false),
                            'label' => $album->getView_label()
                        )
                    );

                    $data['content'] = array(
                        'photo_url' => app_ProfilePhoto::getPermalink($params['entityId']),
                        'photo_src' => app_ProfilePhoto::getUrl($params['entityId'], app_ProfilePhoto::PHOTOTYPE_THUMB),
                    );
                }
                else
                {
                    $data['string'] = $params['entityType'];
                    $data['content'] = array(
                        'photo_url' => app_ProfilePhoto::getPermalink($params['entityId']),
                        'photo_src' => app_ProfilePhoto::getUrl($params['entityId'], app_ProfilePhoto::PHOTOTYPE_THUMB),
                    );
                }

                $data['ownerId'] = $photo->profile_id;
                $params['visibility'] = $visibility;
                $params['privacy'] = $privacy;
                $params['create_time'] = $photo->added_stamp;
                $params['feature'] = FEATURE_PHOTO;
                break;

            case 'media_upload':

                $video_hash = app_ProfileVideo::getVideoHash($params['entityId']);

                $videoOwnerId = app_ProfileVideo::getVideoOwnerById($params['entityId']);
                $video_info = app_ProfileVideo::getVideoInfo($videoOwnerId, $video_hash);

                $data['content'] = array(
                    'url' => SK_Navigation::href('profile_video_view', array('videokey' => $video_hash)),
                    'title' => $video_info['title'],
                    'text' => $video_info['description']
                );

                $params['status'] = $video_info['status'];
                $params['time'] = $video_info['upload_stamp'];
                $data['ownerId'] = $video_info['profile_id'];

                if ( $video_info['privacy_status'] == 'friends_only' )
                {
                    $params['visibility'] = app_Newsfeed::VISIBILITY_AUTHOR + app_Newsfeed::VISIBILITY_FOLLOW;
                    $params['privacy'] = app_Newsfeed::PRIVACY_FRIENDS_ONLY;
                }
                $params['feature'] = FEATURE_VIDEO;
                break;

            case 'music_upload':

                $music_hash = app_ProfileMusic::getMusicHash($params['entityId']);
                $musicOwnerId = app_ProfileMusic::getMusicOwnerById($params['entityId']);
                $music_info = app_ProfileMusic::getMusicInfo($musicOwnerId, $music_hash);

                $data['content'] = array(
                    'url' => SK_Navigation::href('music_view', array('musickey' => $music_hash)),
                    'title' => $music_info['title'],
                    'text' => $music_info['description']
                );

                $params['status'] = $music_info['status'];
                $params['time'] = $music_info['upload_stamp'];
                $data['ownerId'] = $music_info['profile_id'];

                if ( $music_info['privacy_status'] == 'friends_only' )
                {
                    $params['visibility'] = app_Newsfeed::VISIBILITY_AUTHOR + app_Newsfeed::VISIBILITY_FOLLOW;
                    $params['privacy'] = app_Newsfeed::PRIVACY_FRIENDS_ONLY;
                }
                $params['feature'] = FEATURE_MUSIC;
                break;

            case 'forum_add_topic':

                $topic = app_Forum::getTopicHtml($params['entityId']);
                $post = app_Forum::getTopicMainPost($params['entityId']);

                $group = app_Groups::getGroupByForumTopicID($params['entityId']);
                if (!empty($group))
                {
                    return array('data' => array(), 'params' => array());
                }

                $data['content'] = array(
                    'url' => app_Forum::getPostURL($post['forum_post_id']),
                    'title' => $topic['title'],
                    'text' => $this->truncate( $topic['text'])
                );

                $data['ownerId'] = $topic['profile_id'];
                $params['time'] = $post['create_stamp'];
                $params['feature'] = FEATURE_NEWSFEED;
                break;

        }

        return array('data' => $data, 'params' => $params);
    }

    private static $isFollowCache = array();

    public function isFollow( $userId, $feedType, $feedId )
    {
        if( (int)$userId == 0 )
        {
            return false;
        }

        if( isset(self::$isFollowCache[$feedType][$userId.'_'.$feedId]) )
        {
            return self::$isFollowCache[$feedType][$userId.'_'.$feedId];
        }

        return $this->followDao->findFollow($userId, $feedType, $feedId) !== null;
    }

    public function initCacheForFollow( $userId, $feedType, array $feedIdList )
    {
        if( (int)$userId == 0 || empty($feedIdList))
        {
            return false;
        }

        $feedType = trim($feedType);

        if( !isset(self::$isFollowCache[$feedType]) )
        {
            self::$isFollowCache[$feedType] = array();
        }

        foreach ( $feedIdList as $id )
        {
            self::$isFollowCache[$feedType][$userId.'_'.$id] = false;
        }

        $result = $this->followDao->findFollowForList($userId, $feedType, $feedIdList);

        foreach ( $result as $item )
        {
            self::$isFollowCache[$feedType][$item['userId'].'_'.$item['feedId']] = true;
        }
    }

    public function isLiked( $userId, $entityType, $entityId )
    {
        return $this->likeDao->findLike($userId, $entityType, $entityId) !== null;
    }

    public function saveAction( NewsfeedAction $action )
    {
        $this->actionDao->saveOrUpdate($action);

        return $action;
    }

    public function setActionStatusByFeature( $feature, $status )
    {
        $this->actionDao->setStatusByFeature($feature, $status);
    }

    public function removeAction( $entityType, $entityId )
    {
        $dto = $this->actionDao->findAction($entityType, $entityId);

        if ( $dto === null )
        {
            return;
        }

        $this->likeDao->deleteByEntity($dto->getEntityType(), $dto->getEntityId());
        $this->actionDao->deleteById($dto->getId());

        //app_CommentService::stDeleteEntityComments(FEATURE_NEWSFEED, $dto->getId());
    }

    public function removeActionById( $id )
    {
        /* @var $dto NewsfeedAction */
        $dto = $this->actionDao->findById($id);

        if ( $dto === null )
        {
            return;
        }

        $this->likeDao->deleteByEntity($dto->getEntityType(), $dto->getEntityId());
        $this->actionDao->deleteById($dto->getId());

        //app_CommentService::stDeleteEntityComments(FEATURE_NEWSFEED, $dto->getId());
    }

    public function removeActionListByFeature( $feature )
    {
        $list = $this->actionDao->findByFeature($feature);

        foreach ( $list as $dto )
        {
            /* @var $dto NewsfeedAction */
            $this->removeAction($dto->getEntityType(), $dto->getEntityId());
        }
    }

    public function removeFollow( $userId, $feedType, $feedId )
    {
        return $this->followDao->removeFollow($userId, $feedType, $feedId);
    }

    public function removeLike( $userId, $entityType, $entityId )
    {
        if ( !app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            return;

        return $this->likeDao->removeLike($userId, $entityType, $entityId);
    }

    public function updateStatus( $newsfeedDataParams )
    {
        $action = $this->getAction($newsfeedDataParams['params']['entityType'], $newsfeedDataParams['params']['entityId']);
        if ( !empty($action) )
        {
            $action->setStatus($newsfeedDataParams['params']['status']);
            $this->saveAction($action);
        }
        else
        {
            $this->action($newsfeedDataParams);
        }
    }

    public function updateVisibility( $newsfeedDataParams )
    {
        $action = $this->getAction($newsfeedDataParams['params']['entityType'], $newsfeedDataParams['params']['entityId']);

        if ( !empty($action) )
        {
            $action->setVisibility($newsfeedDataParams['params']['visibility']);
            $this->saveAction($action);
        }
        else
        {
            $this->action($newsfeedDataParams);
        }
    }

    public function truncate($text, $lettersCount = 200)
    {
        return (strlen($text) > $lettersCount ) ? substr($text, 0, $lettersCount).'...' : $text;
    }

    public function findLikestForEntities( $entities )
    {
        return $this->likeDao->findListForEntities($entities);
    }

    public function findLikesCountListForEntities( $entities )
    {
        return $this->likeDao->findCountListForEntities($entities);
    }
}

