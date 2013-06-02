<?php

class component_SiteNewsfeed extends component_Newsfeed
{

    public function __construct( array $params = null )
    {
        if ( empty($params) )
        {
            $params = array( 'class'=>__CLASS__, 'feedType'=>'site', 'feedId' => null );
        }
        parent::__construct($params);
    }

    protected function findActionList( $params )
    {
        return app_Newsfeed::newInstance()->findSiteFeed($params['displayCount'], $params['startTime']);
    }

    protected function findActionCount( $params )
    {
        return app_Newsfeed::newInstance()->findSiteFeedCount();
    }

}