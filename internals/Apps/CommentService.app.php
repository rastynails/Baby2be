<?php

require_once DIR_APPS . 'appAux/CommentDao.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 04, 2008
 *
 * Desc: Comment Feature Service Class
 */
final class app_CommentService
{
    const ENTITY_TYPE_BLOG_POST_ADD = 'blog_post_add';
    const ENTITY_TYPE_NEWS_POST_ADD = 'news_post_add';
    const ENTITY_TYPE_FRIEND_ADD =  'friend_add';
    const ENTITY_TYPE_PROFILE_AVATAR_CHANGE = 'profile_avatar_change';
    const ENTITY_TYPE_PROFILE_EDIT = 'profile_edit';
    const ENTITY_TYPE_PROFILE_JOIN = 'profile_join';
    const ENTITY_TYPE_PROFILE_COMMENT = 'profile_comment';
    const ENTITY_TYPE_USER_COMMENT = 'user_comment';
    const ENTITY_TYPE_POST_CLASSIFIEDS_ITEM = 'post_classifieds_item';
    const ENTITY_TYPE_EVENT_ATTEND = 'event_attend';
    const ENTITY_TYPE_EVENT_ADD = 'event_add';
    const ENTITY_TYPE_GROUP_ADD = 'group_add';
    const ENTITY_TYPE_GROUP_JOIN = 'group_join';
    const ENTITY_TYPE_PHOTO_UPLOAD = 'photo_upload';
    const ENTITY_TYPE_MEDIA_UPLOAD = 'media_upload';
    const ENTITY_TYPE_MUSIC_UPLOAD = 'music_upload';
    const ENTITY_TYPE_FORUM_ADD_TOPIC = 'forum_add_topic';
    /**
     * @var CommentDao
     */
    private $commentDao;
    /**
     * @var array
     */
    private $configs;
    /**
     * @var array
     */
    private static $classInstance;

    /**
     * Class constructor
     *
     * @param string $feature
     */
    private function __construct( $table_name, $add_service )
    {
        $this->commentDao = new CommentDao($table_name);

        $this->configs['comments_on_page'] = 10;
        $this->configs['add_service'] = $add_service;
        $this->configs['avail_tags'] = "<b><u><i><span><font><a>";
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * Returns the only instance of the class
     *
     * @param string $feature ( blog | event | photo | video | profile | classifieds )
     *
     * @return app_CommentService
     */
    public static function newInstance( $feature )
    {
        switch ( $feature )
        {
            case FEATURE_BLOG:
                $table_name = TBL_BLOG_POST_COMMENT;
                $add_service = 'add_blog_post_comment';
                break;

            case FEATURE_EVENT:
                $table_name = TBL_EVENT_COMMENT;
                $add_service = 'add_event_comment';
                break;

            case FEATURE_PHOTO:
                $table_name = TBL_PHOTO_COMMENT;
                $add_service = 'add_photo_comment';
                break;

            case FEATURE_VIDEO:
                $table_name = TBL_VIDEO_COMMENT;
                $add_service = 'add_video_comment';
                break;
            case FEATURE_PROFILE:
                $table_name = TBL_PROFILE_COMMENT;
                $add_service = 'add_profile_comment';
                break;

            case FEATURE_CLASSIFIEDS:
                $table_name = TBL_CLASSIFIEDS_COMMENT;
                $add_service = 'add_classifieds_comment';
                break;

            case FEATURE_GROUP:
                $table_name = TBL_GROUP_COMMENT;
                $add_service = 'add_group_comment';
                break;

            case 'news':
                $table_name = TBL_BLOG_POST_COMMENT;
                $add_service = 'add_news_comment';
                break;
            case FEATURE_MUSIC:
                $table_name = TBL_MUSIC_COMMENT;
                $add_service = 'add_music_comment';
                break;
            case FEATURE_NEWSFEED:
                $table_name = TBL_NEWSFEED_COMMENT;
                $add_service = 'add_newsfeed_comment';
                break;
            default:
                return null;
        }

        if ( !isset(self::$classInstance[$feature]) && self::$classInstance[$feature] === null )
            self::$classInstance[$feature] = new self($table_name, $add_service);

        return self::$classInstance[$feature];
    }

    /**
     * Gets comment list for entity
     *
     * @param integer $entityId
     * @param integer $first
     * @param integer $count
     * @return array
     */
    public function findCommentList( $entity_id, $page, $mode, $entityType = null )
    {
        $first = ( $page - 1 ) * (int) $this->configs['comments_on_page'];
        $count = (int) $this->configs['comments_on_page'];

        if ( $mode )
            $return_arr = $this->commentDao->getCommentListWL($entity_id, $entityType);
        else
            $return_arr = $this->commentDao->getCommentList($entity_id, $first, $count, $entityType);

        //foreach ( $return_arr as $key => $value )
        //$return_arr[$key]['comment_text'] = strip_tags( app_TextService::stCensor( $value['dto']->getText(), FEATURE_COMMENT ), $this->configs['avail_tags']);

        return $return_arr;
    }

    /**
     * Gets comments count for entity
     *
     * @param integer $entityId
     * @return integer
     */
    public function findCommentsCount( $entity_id, $entityType = null )
    {
        if( $entityType != null && isset(self::$commentsCountCache[$entityType][$entity_id]) )
        {
            return self::$commentsCountCache[$entityType][$entity_id];
        }

        return $this->commentDao->getCommentsCount($entity_id, $entityType);
    }

    /**
     * Saves and updates
     *
     * @param Comment $comment
     */
    public function saveOrUpdate( Comment $comment )
    {
        $this->commentDao->saveOrUpdate($comment);
    }

    /**
     * Deletes comment item by id
     *
     * @param integer $id
     */
    public function deleteComment( $id )
    {
        $this->commentDao->deleteById($id);
    }

    /**
     * Deletes all entity item comments
     *
     * @param integer $entity_id
     */
    public function deleteEntityComments( $entity_id, $entityType = null )
    {
        $this->commentDao->deleteEntityComments($entity_id, $entityType);
    }

    /**
     * Returns comment item by id
     *
     * @param integer $id
     * @return Comment
     */
    public function findCommentById( $id )
    {
        return $this->commentDao->findById($id);
    }

    /**
     * Hardcoded method, returns entity owner Id
     *
     * @param integer $entity_id
     * @param string $feature
     */
    public function findEntityOwnerId( $entity_id, $feature, $entityType = null )
    {
        switch ( $feature )
        {
            case 'news':
            case 'blog':
                $post = app_BlogService::newInstance()->findBlogPostById($entity_id);
                return $post ? $post->getProfile_id() : null;

            case 'event':
                $event = app_EventService::newInstance()->findById($entity_id);
                return $event ? $event->getProfile_id() : null;

            case 'photo':
                $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE_PHOTO . "` WHERE `photo_id`=? ", $entity_id);
                break;

            case 'video':
                $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE_VIDEO . "` WHERE `video_id`=? ", $entity_id);
                break;

            case 'profile':
                return $entity_id;

            case 'classifieds':
                $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_CLASSIFIEDS_ITEM . "` WHERE `id`=? ", $entity_id);
                break;

            case 'music':
                $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE_MUSIC . "` WHERE `music_id`=? ", $entity_id);
                break;

            case 'group':
                $query = SK_MySQL::placeholder("SELECT `owner_id` FROM `" . TBL_GROUP . "` WHERE `group_id`=? ", $entity_id);
                break;
            case 'newsfeed':
                switch ( $entityType )
                {
                    case 'profile_avatar_change':
                    case 'profile_edit':
                    case 'profile_join':
                    case 'user_comment':
                        return $entity_id;
                        break;
                    case 'forum_add_topic':
                        $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_FORUM_TOPIC . "` WHERE `forum_topic_id`=? ", $entity_id);
                        break;
                    case 'friend_add':
                    default:
                        return -1;
                }
                break;
            default:
                return -1;
        }

        return (int) SK_MySQL::query($query)->fetch_cell();
    }

    public function findProfilesLastCommentList( $profile_id, $count )
    {
        $query = '';

        if ( app_Features::isAvailable(23) )
        {
            $query .= SK_MySQL::placeholder("
                SELECT `bpc`.*, 'blog' as `feature` FROM `" . TBL_BLOG_POST_COMMENT . "` AS `bpc`
                LEFT JOIN `" . TBL_BLOG_POST . "` AS `bp` ON (`bpc`.`entity_id` = `bp`.`id`)
                    WHERE `bp`.`profile_id`=? ", $profile_id);

            $query .= " UNION ALL ";
        }

        if ( app_Features::isAvailable(31) )
        {
            $query .= SK_MySQL::placeholder("
            SELECT `cc`.*, 'classifieds' as `feature` FROM `" . TBL_CLASSIFIEDS_COMMENT . "` AS `cc`
            LEFT JOIN `" . TBL_CLASSIFIEDS_ITEM . "` AS `c` ON (`cc`.`entity_id` = `c`.`id`)
                WHERE `c`.`profile_id`=? ", $profile_id);

            $query .= " UNION ALL ";
        }

        if ( app_Features::isAvailable(6) )
        {
            $query .= SK_MySQL::placeholder("
            SELECT `ec`.*, 'event' as `feature` FROM `" . TBL_EVENT_COMMENT . "` AS `ec`
            LEFT JOIN `" . TBL_EVENT . "` AS `e` ON (`ec`.`entity_id` = `e`.`id`)
                WHERE `e`.`profile_id`=? ", $profile_id);

            $query .= " UNION ALL ";
        }

        if ( app_Features::isAvailable(38) )
        {
            $query .= SK_MySQL::placeholder("
            SELECT `gc`.*, 'group' as `feature` FROM `" . TBL_GROUP_COMMENT . "` AS `gc`
            LEFT JOIN `" . TBL_GROUP . "` AS `g` ON (`gc`.`entity_id` = `g`.`group_id`)
                WHERE `g`.`owner_id`=? ", $profile_id);

            $query .= " UNION ALL ";
        }

        if ( app_Features::isAvailable(58) )
        {
            $query .= SK_MySQL::placeholder("
            SELECT `mc`.*, 'music' as `feature` FROM `" . TBL_MUSIC_COMMENT . "` AS `mc`
            LEFT JOIN `" . TBL_PROFILE_MUSIC . "` AS `m` ON (`mc`.`entity_id` = `m`.`music_id`)
                WHERE `m`.`profile_id`=? ", $profile_id);

            $query .= " UNION ALL ";
        }

        if ( app_Features::isAvailable(30) )
        {
            $query .= SK_MySQL::placeholder("
            SELECT `pc`.*, 'photo' as `feature` FROM `" . TBL_PHOTO_COMMENT . "` AS `pc`
            LEFT JOIN `" . TBL_PROFILE_PHOTO . "` AS `pp` ON (`pc`.`entity_id` = `pp`.`photo_id`)
                WHERE `pp`.`profile_id`=? ", $profile_id);

            $query .= " UNION ALL ";
        }

        if ( app_Features::isAvailable(25) )
        {
            $query .= SK_MySQL::placeholder("
            SELECT *, 'profile' as `feature` FROM `" . TBL_PROFILE_COMMENT . "` WHERE `entity_id`=? ", $profile_id);

            $query .= " UNION ALL ";
        }

        if ( app_Features::isAvailable(5) )
        {
            $query .= SK_MySQL::placeholder("
            SELECT `vc`.*, 'video' as `feature` FROM `" . TBL_VIDEO_COMMENT . "` AS `vc`
            LEFT JOIN `" . TBL_PROFILE_VIDEO . "` AS `pv` ON (`vc`.`entity_id` = `pv`.`video_id`)
                WHERE `pv`.`profile_id`=? ", $profile_id);

            $query .= " UNION ALL ";
        }

        if ( substr($query, -11) == " UNION ALL " )
        {
            $query = substr($query, 0, -11);
        }

        if ( strlen($query) )
        {
            $query .= " ORDER BY 5 DESC LIMIT " . $count;
            return MySQL::fetchArray($query);
        }
        else
        {
            return array();
        }
    }

    public function findProfilesCommentListByFeature( $profile_id, $feature, $count = 20 )
    {
        switch ( $feature )
        {
            case 'news':
            case 'blog':
                $query = SK_MySQL::placeholder("
                    SELECT `bpc`.*, '" . $feature . "' as `feature` FROM `" . TBL_BLOG_POST_COMMENT . "` AS `bpc`
                    LEFT JOIN `" . TBL_BLOG_POST . "` AS `bp` ON (`bpc`.`entity_id` = `bp`.`id`)
                        WHERE `bp`.`profile_id`=? ORDER BY `bpc`.`create_time_stamp` DESC ", $profile_id);
                break;

            case 'event':
                $query = SK_MySQL::placeholder("
                    SELECT `ec`.*, '" . $feature . "' as `feature` FROM `" . TBL_EVENT_COMMENT . "` AS `ec`
                    LEFT JOIN `" . TBL_EVENT . "` AS `e` ON (`ec`.`entity_id` = `e`.`id`)
                        WHERE `e`.`profile_id`=? ORDER BY `ec`.`create_time_stamp` DESC", $profile_id);
                break;

            case 'photo':
                $query = SK_MySQL::placeholder("
                    SELECT `pc`.*, '" . $feature . "' as `feature` FROM `" . TBL_PHOTO_COMMENT . "` AS `pc`
                    LEFT JOIN `" . TBL_PROFILE_PHOTO . "` AS `pp` ON (`pc`.`entity_id` = `pp`.`photo_id`)
                        WHERE `pp`.`profile_id`=? ORDER BY `pc`.`create_time_stamp` DESC", $profile_id);
                break;

            case 'video':
                $query = SK_MySQL::placeholder("
                    SELECT `vc`.*, '" . $feature . "' as `feature` FROM `" . TBL_VIDEO_COMMENT . "` AS `vc`
                    LEFT JOIN `" . TBL_PROFILE_VIDEO . "` AS `pv` ON (`vc`.`entity_id` = `pv`.`video_id`)
                        WHERE `pv`.`profile_id`=? ORDER BY `vc`.`create_time_stamp` DESC", $profile_id);
                break;

            case 'profile':
                $query = SK_MySQL::placeholder("
                    SELECT *, '" . $feature . "' as `feature` FROM `" . TBL_PROFILE_COMMENT . "` WHERE `entity_id`=? ORDER BY `create_time_stamp` DESC", $profile_id);
                break;

            case 'classifieds':
                $query = SK_MySQL::placeholder("
                    SELECT `cc`.*, '" . $feature . "' as `feature` FROM `" . TBL_CLASSIFIEDS_COMMENT . "` AS `cc`
                    LEFT JOIN `" . TBL_CLASSIFIEDS_ITEM . "` AS `c` ON (`cc`.`entity_id` = `c`.`id`)
                        WHERE `c`.`profile_id`=? ORDER BY `cc`.`create_time_stamp` DESC", $profile_id);
                break;
            case 'group':
                $query = SK_MySQL::placeholder("
                    SELECT `gc`.*, '" . $feature . "' as `feature` FROM `" . TBL_GROUP_COMMENT . "` AS `gc`
                    LEFT JOIN `" . TBL_GROUP . "` AS `g` ON (`gc`.`entity_id` = `g`.`group_id`)
                        WHERE `g`.`owner_id`=? ORDER BY `gc`.`create_time_stamp` DESC", $profile_id);
                break;
            case 'music':
                $query = SK_MySQL::placeholder("
                    SELECT `mc`.*, '" . $feature . "' as `feature` FROM `" . TBL_MUSIC_COMMENT . "` AS `mc`
                    LEFT JOIN `" . TBL_PROFILE_MUSIC . "` AS `m` ON (`mc`.`entity_id` = `m`.`music_id`)
                        WHERE `m`.`profile_id`=? ORDER BY `mc`.`create_time_stamp` DESC", $profile_id);
                break;
            default:
                return -1;
        }
        $query .= " LIMIT " . $count;

        return MySQL::fetchArray($query);
    }

    /**
     * Returns most commented entity ids with comment count ( optional )
     *
     * @param boolean $count
     * @return array
     */
    public function findMostCommentedEntityIds( $count = false, $entityType = null )
    {
        $result_array = $this->commentDao->findMostCommentedEntities($entityType);

        $return_array = array();

        foreach ( $result_array as $value )
        {
            $return_array[$value['dto']->getEntity_id()] = array('id' => $value['dto']->getEntity_id(), 'comments_count' => $value['items_count']);
        }

        if ( $count )
        {
            return $return_array;
        }
        else
        {
            return array_keys($return_array);
        }
    }

    private static $commentsCountCache = array();

    /**
     * Returns comments count for entity group
     *
     * @param array $entities
     * @return array
     */
    public function findCommentsCountForEntities( array $entities, $entityType )
    {
        if ( empty($entities) || empty($entityType) )
        {
            return array();
        }

        if( !isset(self::$commentsCountCache[$entityType]) )
        {
            self::$commentsCountCache[$entityType] = array();
        }

        $entities = array_unique($entities);

        $entities = array_diff(array_unique($entities), array_keys(self::$commentsCountCache[$entityType]));

        if( !empty($entities) )
        {
            $result_array = $this->commentDao->findCommentsCountForEntityIds($entities, $entityType);
          
            foreach ( $result_array as $value )
            {
                self::$commentsCountCache[$entityType][$value['dto']->getEntity_id()] = $value['items_count'];
            }

            foreach ( $entities as $id )
            {
                if( !isset(self::$commentsCountCache[$entityType][$id] ) )
                {
                    self::$commentsCountCache[$entityType][$id] = 0;
                }
            }

        }

        $returnArray = array();

        foreach ( $entities as $id )
        {
            $returnArray[$id] = self::$commentsCountCache[$entityType][$id];
        }
        
        return $returnArray;
    }

    /**
     * Returns comments pages count
     *
     * @param integer $entity_id
     * @return integer
     */
    public function findCommentPagesCount( $entity_id, $entityType = null)
    {
        $on_page = $this->configs['comments_on_page'];

        $comments_count = $this->findCommentsCount($entity_id, $entityType);

        if ( $comments_count == 0 )
            return 1;

        return ( ( $comments_count - ( $comments_count % $on_page ) ) / $on_page ) + ( ( $comments_count % $on_page > 0 ) ? 1 : 0 );
    }

    /**
     * Deletes profile comments
     *
     * @param integer $profile_id
     */
    public function deleteProfileComments( $profile_id, $entityType = null )
    {
        $this->commentDao->deleteProfileComments($profile_id, $entityType);
    }

    /**
     * Gets comment list for classifieds
     *
     * @param integer $entityId
     * @return array
     */
    public function findClassifiedsCommentList( $entity_id )
    {
        $comments = $this->commentDao->findClassifiedsComments($entity_id);

        return $comments;
    }

    /**
     * Deletes classifieds comments
     *
     * @param int $entity_id
     */
    public function deleteClassifiedsComments( $entity_id )
    {
        $comments = $this->commentDao->findClassifiedsComments($entity_id);

        //delete comments bids
        foreach ( $comments as $comment )
        {
            app_ClassifiedsBidService::stDeleteEntityBid($comment['dto']->getId());
        }

        $this->deleteEntityComments($entity_id, ENTITY_TYPE_POST_CLASSIFIEDS_ITEM);
    }
    /* ---------------------------------------------------
      Static Interface
      --------------------------------------------------- */

    /**
     * Deletes all comments for entity item
     *
     * @param string $feature ( blog | event | photo | video | profile )
     *
     * @return boolean
     */
    public static function stDeleteEntityComments( $feature, $entity_id, $entityType )
    {
        $service = self::newInstance($feature);

        if ( $service === null )
            return false;

        $service->deleteEntityComments($entity_id, $entityType);

        return true;
    }

    /**
     * Returns comments count for entity item
     *
     * @param string $feature ( blog | event | photo | video | profile )
     *
     * @return integer
     */
    public static function stGetCommentsCount( $feature, $entity_id, $entityType = null )
    {
        $service = self::newInstance($feature);

        if ( $service === null )
            return false;

        return $service->findCommentsCount($entity_id, $entityType);
    }

    /**
     * Returns most commented entity ids with comment count ( optional )
     *
     * @param boolean $count
     * @return array
     */
    public static function stFindMostCommentedEntityIds( $feature, $count = false, $entityType = null )
    {
        $service = self::newInstance($feature);

        if ( $service === null )
            return false;

        return $service->findMostCommentedEntityIds($count, $entityType);
    }

    /**
     * Returns comments count for entity group
     *
     * @param array $entities
     * @return array
     */
    public static function stFindCommentsCountForEntities( array $entities, $feature, $entityType)
    {
        $service = self::newInstance($feature);

        if ( $service === null )
            return false;

        return $service->findCommentsCountForEntities($entities, $entityType);
    }

    /**
     * Deletes classifieds comments
     *
     * @param int $entity_id
     */
    public static function stDeleteClassifiedsComments( $entity_id )
    {
        $service = self::newInstance(FEATURE_CLASSIFIEDS);

        return $service->deleteClassifiedsComments($entity_id);
    }
}