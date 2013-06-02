<?php

final class NewsfeedFollow extends SK_Entity
{
    /**
     * 
     * @var int
     */
    public $feedId;
    /**
     * 
     * @var string
     */
    public $feedType;
    /**
     * 
     * @var int
     */
    public $userId;
    /**
     * 
     * @var int
     */
    public $followTime;
    /**
     *
     * @var string 
     */
    public $permission;


    public function getFeedId()
    {
        return $this->feedId;
    }

    public function setFeedId( $feedId )
    {
        $this->feedId = $feedId;
    }

    public function getFeedType()
    {
        return $this->feedType;
    }

    public function setFeedType( $feedType )
    {
        $this->feedType = $feedType;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId( $userId )
    {
        $this->userId = $userId;
    }

    public function getFollowTime()
    {
        return $this->followTime;
    }

    public function setFollowTime( $followTime )
    {
        $this->followTime = $followTime;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function setPermission( $permission )
    {
        $this->permission = $permission;
    }
}