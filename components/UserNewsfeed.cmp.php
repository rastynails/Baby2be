<?php

class component_UserNewsfeed extends component_Newsfeed
{

    public function __construct( array $params = null )
    {
        $profile_id = !empty($params['profile_id']) ? $params['profile_id'] : SK_HttpUser::profile_id();
        $params['feedId'] = $profile_id;
        $params['feedType'] = 'user';
        $params['class'] = __CLASS__;
        parent::__construct($params);
        
        if( !SK_HttpUser::is_authenticated() || $profile_id != SK_HttpUser::profile_id() )
        {
           $this->annul();
        }
    }

    protected function findActionList( $params )
    {
        return app_Newsfeed::newInstance()->findUserFeed($params['feedId'], $params['displayCount'], $params['startTime']);
    }

    protected function findActionCount( $params )
    {
        return app_Newsfeed::newInstance()->findUserFeedCount($params['feedId']);
    }
}