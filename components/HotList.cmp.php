<?php

class component_HotList extends SK_Component
{
    private $hot_list;

    private $use_sms = false;
    
	public function __construct( array $params = null )
	{
	    parent::__construct('hot_list');
	    
	    if ( !app_Features::isAvailable(52) )
	    {
	        $this->annul();
	        return;
	    }
	    
	    $srv = new SK_Service('view_hot_list'); 
	    if ( $srv->checkPermissions() != SK_Service::SERVICE_FULL )
	    {
            $this->annul();
            return;
	    }
        
		$this->hot_list = app_HotList::getHotList();
	}

	
    /**
     * @see SK_Component::prepare()
     *
     * @param SK_Layout $Layout
     * @param SK_Frontend $Frontend
     */

    function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $handler = new SK_ComponentFrontendHandler('HotList');
        
        $this->frontend_handler = $handler;
        
        $sms_cond = app_Features::isAvailable(32) && app_SMSBilling::serviceIsActive('hot_list');
        
        $auth_msg = '';
        $srv = new SK_Service('hot_list');
        if ( $srv->checkPermissions() == SK_Service::SERVICE_FULL )
        {
            $auth_msg = '';
            $this->use_sms = false;
        }
        else if ( $srv->checkPermissions() != SK_Service::SERVICE_FULL && !$sms_cond )
        {
            $auth_msg = $srv->permission_message['message'];
            $this->use_sms = $sms_cond;
        }
        else if ( $sms_cond )
        {
            $this->use_sms = true;
        }
        
        $handler->construct(SK_Language::text('%components.hot_list.confirm'), $auth_msg);
    }
    
    
	public function render ( SK_Layout $Layout )
	{
		$Layout->assign('hot_list', $this->hot_list);

        $tempDataToAssign = array();
        
        foreach ( $this->hot_list as $item )
        {
            $tempDataToAssign[$item['profile_id']] = array( 'sex' => $item['sex'], 'profile_id' => $item['profile_id'] );
        }

        $profileIdList = array_keys($tempDataToAssign);

        app_Profile::getUsernamesForUsers($profileIdList);
        app_ProfilePhoto::getThumbUrlList($profileIdList);
        app_Profile::getOnlineStatusForUsers($profileIdList);
        
		$Layout->assign('use_sms', $this->use_sms);
		
		//return parent::render($Layout);
        return NULL;
	}
	
	public static function ajax_AddToHotList ( SK_HttpRequestParams $params = null, SK_ComponentFrontendHandler $handler ) 
    {
        $profile_id = SK_HttpUser::profile_id();
        
        $service = new SK_Service('hot_list');
        
        if ( $service->checkPermissions() == SK_Service::SERVICE_FULL && $profile_id )
        {
            if ( app_HotList::addProfile($profile_id) )
            {
                $service->trackServiceUse();
                
                $handler->message(SK_Language::text('%components.hot_list.added'));
                return array('result' => true);
            }
        }
        else 
        {
            $handler->error($service->permission_message['message']);
            return array('result' => false);    
        }        
    }
}
