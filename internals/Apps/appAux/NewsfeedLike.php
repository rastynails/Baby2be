<?php

final class NewsfeedLike extends SK_Entity
{
    /**
     * 
     * @var int
     */
    public $entityId;
    /**
     * 
     * @var string
     */
    public $entityType;
    /**
     * 
     * @var int
     */
    public $userId;
    /**
     * 
     * @var int
     */
    public $timeStamp;

    public function getEntityId()
    {
        return $this->entityId;
    }

    public function setEntityId( $entityId )
    {
        $this->entityId = $entityId;
    }

    public function getEntityType()
    {
        return $this->entityType;
    }

    public function setEntityType( $entityType )
    {
        $this->entityType = $entityType;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId( $userId )
    {
        $this->userId = $userId;
    }

    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    public function setTimeStamp( $timeStamp )
    {
        $this->timeStamp = $timeStamp;
    }
}