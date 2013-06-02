<?php

class component_123Chat extends SK_Component
{
	private $profile_id;
	private $no_permission; 
	
	/**
	 * Constructor.
	 */
	public function __construct( $params = null ) 
	{
		$this->profile_id = SK_HttpUser::profile_id();

		if ( !$this->profile_id ) {
			SK_HttpRequest::redirect('index');
		}

		parent::__construct('123_chat');
	}

	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$Frontend->include_js_file( URL_123CHAT.'123flashchat.js' );

		$service = new SK_Service('chat');
        $allowd = $service->checkPermissions();
        
        $dissalowd_msg = '';
        
		if ( $allowd == SK_Service::SERVICE_FULL) {
            $service->trackServiceUse();
		}
        else if($allowd == SK_Service::SERVICE_NO_CREDITS)
        {
            $dissalowd_msg = $service->permission_message['message'];
            $Layout->assign('dissalowd_msg', $dissalowd_msg);
			return parent::prepare( $Layout, $Frontend );            
        }		
	    else 
	    {
			$this->no_permission = $service->permission_message['message'];
			return parent::prepare( $Layout, $Frontend );            
        }
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout )  
	{
		$url = SITE_URL;
		
		if(preg_match('/.*\\/$/', $url))
			$url .= '123flashchat/123flashchat.php';
		else
			$url .= '/123flashchat/123flashchat.php';

		$Layout->assign('chatUrl', $url);
	}

}
