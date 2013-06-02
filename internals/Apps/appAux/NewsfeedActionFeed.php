<?php

final class NewsfeedActionFeed extends SK_Entity
{
    /**
     * 
     * @var string
     */
    public $feedType;
    /**
     * 
     * @var int
     */
    public $feedId;
    /**
     *
     * @var int
     */
    public $actionId;


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

    public function getActionId()
    {
        return $this->actionId;
    }

    public function setActionId( $actionId )
    {
        $this->actionId = $actionId;
    }
}