<?php

final class NewsfeedAction extends SK_Entity
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
     * @var string
     */
    public $feature;
    /**
     * 
     * @var int
     */
    public $userId;
    /**
     * 
     * @var string
     */
    public $data;
    /**
     * 
     * @var string
     */
    public $status = 'active';
    /**
     * 
     * @var int
     */
    public $createTime;
    /**
     * 
     * @var int
     */
    public $updateTime;
    /**
     * 
     * @var int
     */
    public $visibility;
    /**
     *
     * @var string
     */
    public $privacy;
    

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

    public function getFeature()
    {
        return $this->feature;
    }

    public function setFeature( $feature )
    {
        $this->feature = $feature;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId( $userId )
    {
        $this->userId = $userId;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData( $data )
    {
        $this->data = $data;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus( $status )
    {
        $this->status = $status;
    }

    public function getCreateTime()
    {
        return $this->createTime;
    }

    public function setCreateTime( $createTime )
    {
        $this->createTime = $createTime;
    }

    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    public function setUpdateTime( $updateTime )
    {
        $this->updateTime = $updateTime;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function setVisibility( $visibility )
    {
        $this->visibility = $visibility;
    }

    public function getPrivacy()
    {
        return $this->privacy;
    }

    public function setPrivacy( $privacy )
    {
        $this->privacy = $privacy;
    }
}