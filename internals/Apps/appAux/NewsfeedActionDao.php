<?php

require_once DIR_APPS . 'appAux/NewsfeedAction.php';

final class NewsfeedActionDao extends SK_BaseDao
{
    private static $classInstance;

    /**
     * Returns the only object of NewsfeedActionDao class
     *
     * @return NewsfeedActionDao
     */
    public static function newInstance()
    {
        if ( self::$classInstance === null )
            self::$classInstance = new self;

        return self::$classInstance;
    }

    /**
     * Class constructor
     *
     */
    public function __construct()
    {

    }

    protected function getDtoClassName()
    {
        return "NewsfeedAction";
    }

    protected function getTableName()
    {
        return '`' . TBL_NEWSFEED_ACTION . '`';
    }

    public function findByFeature( $feature )
    {

    }

    public function getProfileSiteAccessCondition()
    {
        $allow_emailverify_no_access = SK_Config::section('site.additional.profile')->allow_emailverify_no_access;
        $allow_emailverify_undefined_access = SK_Config::section('site.additional.profile')->allow_emailverify_undefined_access;
        $not_reviewed_profile_access = SK_Config::section('site.additional.profile')->not_reviewed_profile_access;


        $condition = "( profile.email_verified='yes' ";
        //$condition = "1";

        if ($allow_emailverify_no_access)
        {
            $condition .= " OR profile.email_verified='no' ";
        }

        if ($allow_emailverify_undefined_access)
        {
            $condition .= " OR profile.email_verified='undefined' ";
        }

        $condition .= ") ";

        if (!$not_reviewed_profile_access)
        {
            $condition .= " AND profile.reviewed='y' ";
        }
        
        return $condition;
    }

    public function findByFeed( $feedType, $feedId, $count = null, $startTime = null )
    {
        $limit = '';
        if ( !empty($count) )
        {
            $limit = "LIMIT 0, " . intval($count);
        }

        $condition = $this->getProfileSiteAccessCondition();
        $query = SK_MySQL::placeholder("SELECT action.* FROM " . $this->getTableName() . " action
            INNER JOIN `" . TBL_NEWSFEED_ACTION_FEED . "` action_feed ON action.id=action_feed.actionId
            INNER JOIN `".TBL_PROFILE."` as profile ON action.userId=profile.profile_id
            WHERE {$condition} AND action.status='?' AND action_feed.feedType='?' AND action_feed.feedId=? AND action.updateTime<? AND action.visibility & ? AND action.privacy='?'
            ORDER BY action.updateTime DESC, action.id DESC  " . $limit, app_Newsfeed::ACTION_STATUS_ACTIVE, $feedType, $feedId, empty($startTime) ? time() : $startTime, app_Newsfeed::VISIBILITY_FEED, app_Newsfeed::PRIVACY_EVERYBODY);

        return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
    }

    public function findCountByFeed( $feedType, $feedId )
    {
        $condition = $this->getProfileSiteAccessCondition();
        $query = SK_MySQL::placeholder("SELECT COUNT(action.id) FROM " . $this->getTableName() . " action
            INNER JOIN `" . TBL_NEWSFEED_ACTION_FEED . "` action_feed ON action.id=action_feed.actionId
            INNER JOIN `".TBL_PROFILE."` as profile ON action.userId=profile.profile_id
            WHERE {$condition} AND action.status='?' AND action_feed.feedType='?' AND action_feed.feedId=? AND action.visibility & ? AND action.privacy='?' ", app_Newsfeed::ACTION_STATUS_ACTIVE, $feedType, $feedId, app_Newsfeed::VISIBILITY_FEED, app_Newsfeed::PRIVACY_EVERYBODY);

        return SK_MySQL::query($query)->fetch_cell();
    }

    /**
     *
     * @param $entityType
     * @param $entityId
     * @return NEWSFEED_BOL_Action
     */
    public function findAction( $entityType, $entityId, $userId = null )
    {
        if ($userId != null)
        {
            $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `entityType`='?' AND `entityId`=? AND `userId`=? ", $entityType, $entityId, $userId);
        }
        else
        {
            $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `entityType`='?' AND `entityId`=? ", $entityType, $entityId);
        }

        return SK_MySQL::query($query)->mapObject($this->getDtoClassName());
    }

    public function deleteByUserIdEntityTypeEntityIds( $entityType, $entityId, $userId )
    {
        $query = SK_MySQL::placeholder("DELETE FROM " . $this->getTableName() . " WHERE `entityType`='?' AND `entityId`=? AND `userId`=? ", $entityType, $entityId, $userId);

        SK_MySQL::query($query);
        return SK_MySQL::affected_rows();
    }


    public function findByUser( $userId, $count = null, $startTime = null )
    {
        $limit = '';
        if ( !empty($count) )
        {
            $limit = "LIMIT 0, " . intval($count);
        }

        $condition = $this->getProfileSiteAccessCondition();
        $query = SK_MySQL::placeholder("SELECT DISTINCT action.* FROM " . $this->getTableName() . " action
            INNER JOIN `" . TBL_NEWSFEED_ACTION_FEED . "` action_feed ON action.id=action_feed.actionId
            INNER JOIN `".TBL_PROFILE."` profile ON action.userId=profile.profile_id
            LEFT JOIN `" . TBL_NEWSFEED_FOLLOW . "` follow ON action_feed.feedId = follow.feedId AND action_feed.feedType = follow.feedType
            WHERE {$condition} AND action.status='?' AND action.updateTime<? AND ( action.privacy='?' OR action.privacy=follow.permission ) AND (
                ( follow.userId=? AND action.visibility & ? )
                OR
                ( action.userId=? AND action.visibility & ? )
                OR
                ( action_feed.feedId=? AND action_feed.feedType='user' AND action.visibility & ? )
            )
            ORDER BY action.updateTime DESC, action.id DESC " . $limit,
            app_Newsfeed::ACTION_STATUS_ACTIVE,
            empty($startTime) ? time() : $startTime,
            app_Newsfeed::PRIVACY_EVERYBODY,
            $userId,  app_Newsfeed::VISIBILITY_FOLLOW,
            $userId,  app_Newsfeed::VISIBILITY_AUTHOR,
            $userId,  app_Newsfeed::VISIBILITY_FEED
        );
//printArr($query);
        return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
    }

    public function findCountByUser( $userId )
    {
        $condition = $this->getProfileSiteAccessCondition();
        $query = SK_MySQL::placeholder("SELECT COUNT(DISTINCT action.id) FROM " . $this->getTableName() . " action
            INNER JOIN `" . TBL_NEWSFEED_ACTION_FEED . "` action_feed ON action.id=action_feed.actionId
            INNER JOIN `".TBL_PROFILE."` profile ON action.userId=profile.profile_id
            LEFT JOIN `" . TBL_NEWSFEED_FOLLOW . "` follow ON action_feed.feedId = follow.feedId AND action_feed.feedType = follow.feedType
            WHERE {$condition} AND action.status='?' AND ( action.privacy='?' OR action.privacy=follow.permission ) AND (
                ( follow.userId=? AND action.visibility & ? )
                OR
                ( action.userId=? AND action.visibility & ? )
                OR
                ( action_feed.feedId=? AND action_feed.feedType='user' AND action.visibility & ? )
            )", app_Newsfeed::ACTION_STATUS_ACTIVE, app_Newsfeed::PRIVACY_EVERYBODY, $userId, app_Newsfeed::VISIBILITY_FOLLOW, $userId, app_Newsfeed::VISIBILITY_AUTHOR, $userId, app_Newsfeed::VISIBILITY_FEED);
        return SK_MySQL::query($query)->fetch_cell();
    }

    public function findSiteFeed( $count = null, $startTime = null )
    {
        $limit = '';
        if ( !empty($count) )
        {
            $limit = "LIMIT 0, " . intval($count);
        }

        $condition = $this->getProfileSiteAccessCondition();
        $query = SK_MySQL::placeholder("SELECT DISTINCT `na`.* FROM " . $this->getTableName() . " AS `na`
            LEFT JOIN `".TBL_PROFILE."` AS `profile` ON ( `na`.`userId` = `profile`.`profile_id` )
            WHERE {$condition} AND `profile`.`status`='active' AND `na`.`status`='?' AND `na`.`privacy`='?' AND `na`.`visibility` & ? AND `na`.`updateTime` < ? ORDER BY `na`.`updateTime` DESC, `na`.`id` DESC " . $limit,
            app_Newsfeed::ACTION_STATUS_ACTIVE, app_Newsfeed::PRIVACY_EVERYBODY, app_Newsfeed::VISIBILITY_SITE, empty($startTime) ? time() : $startTime);

        return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
    }

    public function findSiteFeedCount()
    {
        $condition = $this->getProfileSiteAccessCondition();
        $query = SK_MySQL::placeholder("SELECT COUNT(DISTINCT `na`.`id`) FROM " . $this->getTableName() . " AS `na`
            LEFT JOIN `".TBL_PROFILE."` AS `profile` ON ( `na`.`userId` = `profile`.`profile_id` )
            WHERE {$condition} AND `profile`.`status`='active' AND `na`.`status`='?' AND `na`.`visibility` & ? AND `na`.`privacy`='?' ORDER BY `na`.`updateTime` DESC, `na`.`id` DESC", app_Newsfeed::ACTION_STATUS_ACTIVE, app_Newsfeed::VISIBILITY_SITE, app_Newsfeed::PRIVACY_EVERYBODY);

        return SK_MySQL::query($query)->fetch_cell();
    }

    public function findExpired( $inactivePeriod )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `updateTime`<? ", time() - (int) $inactivePeriod);

        return SK_MySQL::query($query)->mapObject($this->getDtoClassName());
    }

    public function findListByUserId( $userId )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `userId`=? ", $userId);

        return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
    }

    public function setStatusByFeature( $feature, $status )
    {
        $query = SK_MySQL::placeholder("UPDATE " . $this->getTableName() . " SET `status`='?' WHERE `feature`='?' ", $status, $feature);
        SK_MySQL::query($query);
    }
}