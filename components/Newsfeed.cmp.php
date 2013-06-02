<?php

abstract class component_Newsfeed extends SK_Component
{
    private static $feedCounter = 0;
    protected $actionList = array();
    protected $count = false;
    protected $data = array();

    /**
     * Constructor.
     */
    public function __construct( array $params = null )
    {
        if ( !app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $this->annul();
            return;
        }

        self::$feedCounter++;

        $this->data['class'] = $params['class'];
        $this->data['feedType'] = $params['feedType'];
        $this->data['feedId'] = $params['feedId'];
        $this->data['startTime'] = time();
        $this->data['displayCount'] = SK_Config::section('newsfeed')->display_count;
        $this->data['viewMore'] = true; //TODO depend on settings


        parent::__construct('newsfeed');
    }

    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $service = new SK_Service('newsfeed_view', SK_HttpUser::profile_id());
        $allowed = $service->checkPermissions() == SK_Service::SERVICE_FULL;
        if ( !$allowed )
        {
            $Layout->assign('no_permission_msg', $service->permission_message['message']);
            $this->tpl_file = 'no_permission.tpl';
            return;
        }

        $handler = new SK_ComponentFrontendHandler('Newsfeed');
        $this->frontend_handler = $handler;
        $handler->construct($this->getActionCount(), array('data' => $this->data));
    }

    public function render( SK_Layout $layout )
    {
        $layout->assign('feed_list', new component_NewsfeedFeedList($this->getActionList(), $this->data));
        $layout->assign('data', $this->data);
        $viewMore = $this->data['viewMore'] && $this->getActionCount() > $this->data['displayCount'];
        $layout->assign('viewMore', $viewMore);


        return parent::render($layout);
    }

    protected function getActionList( $params = array() )
    {
        if (!empty($params))
        {
            if (isset($params['startTime']))
            {
                $this->data['startTime'] = $params['startTime'];
            }
        }
        $actionList = $this->findActionList($this->data);

        if ( empty($actionList) )
        {
            $this->count = 0;

            return array();
        }

        $this->count = $this->getActionCount();

        foreach ( $actionList as $action )
        {
            $this->actionList[$action->getId()] = $action;
        }

        return $this->actionList;
    }

    public function getActionCount()
    {
        if ( $this->count === false )
        {
            return $this->findActionCount($this->data);
        }

        return $this->count;
    }


    abstract protected function findActionList( $params );

    abstract protected function findActionCount( $params );

    public static function ajax_LoadItem( $params, SK_ComponentFrontendHandler $handler )
    {
        $params = json_decode($_GET['p'], true);

        $feedData = $params['feedData'];

        $driverClass = $feedData['driver']['class'];
        /* @var $driver NEWSFEED_CLASS_Driver */
        $driver = new $driverClass;
        $driver->setup($feedData['driver']['params']);

        if ( isset($params['actionId']) )
        {
            $action = $driver->getActionById($params['actionId']);
        }
        else if ( isset($params['entityType']) && isset($params['entityId']) )
        {
            $action = $driver->getAction($params['entityType'], $params['entityId']);
        }
        else
        {
            throw new InvalidArgumentException('Invalid paraeters: `entityType` and `entityId` or `actionId`');
        }

        if ( $action === null )
        {
            throw new InvalidArgumentException('Action not found');
        }

        $data = $feedData['data'];

        $sharedData['feedAutoId'] = $data['feedAutoId'];
        $sharedData['feedType'] = $data['feedType'];
        $sharedData['feedId'] = $data['feedId'];

        $sharedData['configs'] = OW::getConfig()->getValues('newsfeed');

        $sharedData['usersInfo'] = array(
            'avatars' => BOL_AvatarService::getInstance()->getAvatarsUrlList(array($action->getUserId())),
            'urls' => BOL_UserService::getInstance()->getUserUrlsForList(array($action->getUserId())),
            'names' => BOL_UserService::getInstance()->getDisplayNamesForList(array($action->getUserId())),
            'roleLabels' => BOL_AvatarService::getInstance()->getDataForUserAvatars(array($action->getUserId()), false, false, false)
        );

        $cmp = new NEWSFEED_CMP_FeedItem($action, $sharedData);
        //$html = $cmp->renderMarkup($params['cycle']);

        $markup = array();
        $markup['html'] = SK_Layout::getInstance()->renderComponent($cmp);
        
        //$this->echoMarkup($html);
    }

    /**
     * If $viewMore == true
     *
     * @param type $params
     * @param SK_ComponentFrontendHandler $handler
     * @param SK_AjaxResponse $response
     */
    public static function ajax_LoadItemList( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
    {
        $class = $params->p['data']['class'];
        $component = new $class($params->p['data']);

        $actionList = $component->getActionList( array('startTime'=>$params->p['startTime']) );

        $list = new component_NewsfeedFeedList($actionList, $params->p['data']);

        $markup = array();
        $markup['html'] = SK_Layout::getInstance()->renderComponent($list);

        $handler->appendList($markup);
    }

}
