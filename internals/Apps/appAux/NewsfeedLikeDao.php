<?php
require_once DIR_APPS.'appAux/NewsfeedLike.php';


final class NewsfeedLikeDao extends SK_BaseDao
{
    private static $classInstance;

    /**
     * Returns the only object of NewsfeedLikeDao class
     *
     * @return NewsfeedLikeDao
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
        return "NewsfeedLike";
    }

    protected function getTableName()
    {
        return '`' . TBL_NEWSFEED_LIKE . '`';
    }

    public function addLike( $userId, $entityType, $entityId )
    {
        $dto = $this->findLike($userId, $entityType, $entityId);

        if ( $dto !== null )
        {
            return $dto;
        }

        $dto = new NewsfeedLike();
        $dto->setEntityType($entityType);
        $dto->setEntityId($entityId);
        $dto->setUserId($userId);
        $dto->setTimeStamp(time());

        $this->saveOrUpdate($dto);

        return $dto;
    }

    public function findLike( $userId, $entityType, $entityId )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `userId`=? AND `entityId`=? AND `entityType`='?' ", $userId, $entityId, $entityType);

        return SK_MySQL::query($query)->mapObject($this->getDtoClassName());
    }

    public function removeLike( $userId, $entityType, $entityId )
    {
        $query = SK_MySQL::placeholder("DELETE FROM " . $this->getTableName() . " WHERE `userId`=? AND `entityId`=? AND `entityType`='?' ", $userId, $entityId, $entityType);

        SK_MySQL::query($query);
    }

    public function deleteByEntity( $entityType, $entityId )
    {
        $query = SK_MySQL::placeholder("DELETE FROM " . $this->getTableName() . " WHERE `entityId`=? AND `entityType`='?' ", $entityId, $entityType);

        SK_MySQL::query($query);
    }

    public function findByUserId( $userId )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `userId`=? ", $userId);

        return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
    }

    public function findByEntity( $entityType, $entityId )
    {
        if( !isset(self::$likesCache[$entityType][$entityId]['list']) )
        {
            $query = SK_MySQL::placeholder("SELECT * FROM " . $this->getTableName() . " WHERE `entityId`=? AND `entityType`='?' ", $entityId, $entityType);
            self::$likesCache[$entityType][$entityId]['list'] = SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
        }

        return self::$likesCache[$entityType][$entityId]['list'];
    }

    public function findCountByEntity( $entityType, $entityId )
    {
        if( !isset(self::$likesCache[$entityType][$entityId]['count']) )
        {
            $query = SK_MySQL::placeholder("SELECT COUNT(*) FROM " . $this->getTableName() . " WHERE `entityType`='?' AND `entityId`=? ", $entityType, $entityId);
            self::$likesCache[$entityType][$entityId]['count'] = (int)MySQL::fetchField($query);
        }

        return self::$likesCache[$entityType][$entityId]['count'];
    }

    private static $likesCache = array();

    public function findListForEntities( array $entities )
    {
        $count = 0;

        $queryAdd = '';

        foreach ( $entities as $entityType => $list )
        {
            foreach ( $list as $entityId )
            {
                if( !isset(self::$likesCache[$entityType]) )
                {
                    self::$likesCache[$entityType] = array();
                }

                if( !isset(self::$likesCache[$entityType][$entityId]) )
                {
                    self::$likesCache[$entityType][$entityId] = array();
                }

                self::$likesCache[$entityType][$entityId]['list'] = array();

                $queryAdd .= " (`entityId`=".  intval($entityId)." AND `entityType`='".  mysql_real_escape_string($entityType)."') OR";

                $count++;
            }
        }

        if( $count == 0 )
        {
            return array();
        }

        $queryAdd = substr($queryAdd, 0, -2);

        $query = "SELECT * FROM " . $this->getTableName() . " WHERE " . $queryAdd;

        $result = SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());

        $resultArray = array();

        /* @var $item NewsfeedLike */
        foreach ( $result as $item )
        {
            self::$likesCache[$item->getEntityType()][$item->getEntityId()]['list'][] = $item;
        }

        $returnArray = array();

        foreach ( $entities as $entityType => $list )
        {
            foreach ( $list as $entityId )
            {
                 if( !isset($returnArray[$entityType]) )
                {
                    $returnArray[$entityType] = array();
                }

                $returnArray[$entityType][$entityId] = self::$likesCache[$entityType][$entityId]['list'];
            }
        }
        
        return $returnArray;
    }

    public function findCountListForEntities( array $entities )
    {
        $count = 0;
        $queryAdd = '';

        foreach ( $entities as $entityType => $list )
        {
            foreach ( $list as $entityId )
            {
                if( !isset(self::$likesCache[$entityType]) )
                {
                    self::$likesCache[$entityType] = array();
                }

                if( !isset(self::$likesCache[$entityType][$entityId]) )
                {
                    self::$likesCache[$entityType][$entityId] = array();
                }

                self::$likesCache[$entityType][$entityId]['count'] = 0;

                $queryAdd .= " (`entityId`=".  intval($entityId)." AND `entityType`='".  mysql_real_escape_string($entityType)."') OR";
                $count++;
            }
        }

        if( $count == 0 )
        {
            return array();
        }

        $queryAdd = substr($queryAdd, 0, -2);

        $query = "SELECT *, COUNT(*) as c FROM " . $this->getTableName() . " WHERE " . $queryAdd . " GROUP BY `entityType`, `entityId`";

        $result = SK_MySQL::queryForList($query);

        foreach ( $result as $item )
        {
            self::$likesCache[$item['entityType']][$item['entityId']]['count'] = $item['c'];
        }

        $returnArray = array();

        foreach ( $entities as $entityType => $list )
        {
            foreach ( $list as $entityId )
            {
                 if( !isset($returnArray[$entityType]) )
                {
                    $returnArray[$entityType] = array();
                }

                $returnArray[$entityType][$entityId] = self::$likesCache[$entityType][$entityId]['count'];
            }
        }

        return $returnArray;
    }
}