<?php

class component_MyCredits extends SK_Component
{

	public function __construct( array $params = null )
	{
        if ( !app_Features::isAvailable(44) )
        {
            $this->annul();
            return;
        }
	    
		parent::__construct('my_credits');
	}
			
	public function render( SK_Layout $Layout )
	{
	    $page = isset(SK_HttpRequest::$GET['page']) ? (int)SK_HttpRequest::$GET['page'] : 1;
	    
	    $profile_id = SK_HttpUser::profile_id();
        
	    $log = app_UserPoints::getActionHistory($profile_id, $page);

	    $Layout->assign('log', $log['list']);
	    
        $Layout->assign('paging',array(
            'total'     => $log['total'],
            'on_page'   => 20,
            'pages'     => 5,
        ));

	    $Layout->assign('credits_balance', app_UserPoints::getProfilePointsBalance($profile_id));
	    
		return parent::render($Layout);
	}
}
