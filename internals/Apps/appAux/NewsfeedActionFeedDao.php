<?php

require_once DIR_APPS . 'appAux/NewsfeedActionFeed.php';

final class NewsfeedActionFeedDao extends SK_BaseDao
{
    private static $classInstance;

    /**
     * Returns the only object of NewsfeedActionFeedDao class
     *
     * @return NewsfeedActionFeedDao
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
        return "NewsfeedActionFeed";
    }

    protected function getTableName()
    {
        return '`' . TBL_NEWSFEED_ACTION_FEED . '`';
    }

    public function findByFeedTypeFeedIdActionId( $feedType, $feedId, $actionId )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `feedType`='?' AND `feedId`=? AND `actionId`=? ", $feedType, $feedId, $actionId);

        return SK_MySQL::query($query)->mapObject($this->getDtoClassName());
    }

    public function deleteByActionId( $actionId )
    {
        $query = SK_MySQL::placeholder("DELETE FROM " . $this->getTableName() . " WHERE `actionId`=? ", $actionId);
        SK_MySQL::query($query);
    }
}