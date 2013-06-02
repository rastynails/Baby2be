<?php

class component_NewsfeedFeedList extends SK_Component
{
    private $feed = array();
    private $sharedData = array();

    /**
     * Constructor.
     */
    public function __construct( $actionList, $data )
    {
        $this->feed = $actionList;

        $userIds = array();
        $commentsEntityList = array();
        $likesEntityList = array();

        foreach ( $this->feed as $action )
        {
            /* @var $action NewsfeedAction */
            $userIds[] = $action->getUserId();

            if( !isset($commentsEntityList[$action->getEntityType()]) )
            {
                $commentsEntityList[$action->getEntityType()] = array( 'feature' => $action->getFeature(), 'ids' => array() );
            }

            if( !isset($likesEntityList[$action->getEntityType()]) )
            {
                $likesEntityList[$action->getEntityType()] = array();
            }

            $data = json_decode($action->getData(), true);

            if( !isset($data['features']['comments']) || (int)$data['features']['comments'] )
            {
                $commentsEntityList[$action->getEntityType()]['ids'][] = $action->getEntityId();
            }

            if( !isset($data['features']['likes']) || (int)$data['features']['likes'] )
            {
                $likesEntityList[$action->getEntityType()][] = $action->getEntityId();
            }
        }

        $this->sharedData['feedType'] = $data['feedType'];
        $this->sharedData['feedId'] = $data['feedId'];
        $configs = array();

        foreach (SK_Config::section('newsfeed')->getConfigsList() as $config )
        {
            $configs[$config->name] = $config->value;
        }
        
        $this->sharedData['configs'] = $configs;

        // batch optimize
        if( $this->sharedData['configs']['allow_comments'] )
        {
            foreach ( $commentsEntityList as $key => $item )
            {
                app_CommentService::newInstance($item['feature'])->findCommentsCountForEntities($item['ids'], $key);
            }
        }

        if( $this->sharedData['configs']['allow_likes'] )
        {
            app_Newsfeed::newInstance()->findLikesCountListForEntities($likesEntityList);
            app_Newsfeed::newInstance()->findLikestForEntities($likesEntityList);
        }

        app_Profile::getUsernamesForUsers($userIds);
        app_ProfilePhoto::getThumbUrlList($userIds);
        app_Profile::getOnlineStatusForUsers($userIds);


        $avatars = array();
        $urls = array();
        $names = array();
        foreach ( $userIds As $id )
        {
            $avatars[$id] = app_ProfilePhoto::getThumbUrl($id);
            $urls[$id] = app_Profile::getUrl($id);
            $names[$id] = app_Profile::username($id);
        }

        $this->sharedData['usersInfo'] = array(
            'avatars' => $avatars,
            'urls' => $urls,
            'names' => $names,
        );

        parent::__construct('newsfeed_list');
    }

    public function render( SK_Layout $layout )
    {

        $out = array();
        foreach ( $this->feed as $action )
        {
            if ( $action->getEntityType() == 'user_comment' )
            {
                continue;
            }
            /* @var $action NewsfeedAction */
            $updateTime = $action->getUpdateTime();
            $sectionKey = date('dmY', $updateTime);

            if ( empty($out[$sectionKey]) )
            {
                $out[$sectionKey] = array(
                    'date' => SK_I18n::getSpecFormattedDate( $updateTime, true ),
                    'list' => array()
                );
            }

            $out[$sectionKey]['list'][] = $action;
        }

        $layout->assign('feed', $out);
        $layout->assign('sharedData', $this->sharedData);
        
        return parent::render($layout);
    }
}