<?php

class component_FeedNewsfeed extends component_Newsfeed
{

    private $feedId;
    private $feedType;


    public function __construct( array $params = null )
    {
        $params['feedId'] = !empty($params['profile_id']) ? $params['profile_id'] : SK_HttpUser::profile_id();
        $params['feedType'] = !empty($params['feedType']) ? $params['feedType'] : 'user';
        
        $this->feedId = !empty($params['feedId']) ? $params['feedId'] : $params['profile_id'];
        $this->feedType = 

        parent::__construct($params);
    }

    protected function findActionList( $params )
    {
        return app_Newsfeed::newInstance()->findByFeed($params['feedType'], $params['feedId']);
    }

    protected function findActionCount( $params )
    {
        return app_Newsfeed::newInstance()->findFeedCount($params['feedType'], $params['feedId']);
    }
}