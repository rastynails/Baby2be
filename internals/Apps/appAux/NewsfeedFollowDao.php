<?php
require_once DIR_APPS.'appAux/NewsfeedFollow.php';


final class NewsfeedFollowDao extends SK_BaseDao
{
    private static $classInstance;

    /**
     * Returns the only object of NewsfeedFollowDao class
     *
     * @return NewsfeedFollowDao
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
        return "NewsfeedFollow";
    }

    protected function getTableName()
    {
        return '`' . TBL_NEWSFEED_FOLLOW . '`';
    }

    public function addFollow( $userId, $feedType, $feedId, $permission )
    {
        $dto = $this->findFollow($userId, $feedType, $feedId);

        if ( $dto === null )
        {
            $dto = new NewsfeedFollow();
            $dto->setFeedId($feedId);
            $dto->setFeedType($feedType);
            $dto->setUserId($userId);
            $dto->setFollowTime(time());
            $dto->setPermission($permission);
        }
        
        $this->saveOrUpdate($dto);

        return $dto;
    }

    public function findFollow( $userId, $feedType, $feedId )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `userId`=? AND `feedId`=? AND `feedType`='?' ", $userId, $feedId, $feedType);

        return SK_MySQL::query($query)->mapObject($this->getDtoClassName());
    }

    public function findFollowForList( $userId, $feedType, $feedIdList )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `userId`=? AND `feedId` IN (?@) AND `feedType`='?' ", $userId, $feedIdList, $feedType);
        return SK_MySQL::queryForList($query);
    }

    public function removeFollow( $userId, $feedType, $feedId )
    {
        $query = SK_MySQL::placeholder("DELETE FROM " . $this->getTableName() . " WHERE `userId`=? AND `feedId`=? AND `feedType`='?' ", $userId, $feedId, $feedType);

        SK_MySQL::query($query);
    }
}