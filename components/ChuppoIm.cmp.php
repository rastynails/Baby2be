<?php

class component_ChuppoIm extends SK_Component
{
	
	private $profile_key;
	
	private $opponent_key;
	
	private $no_permission;

    private $is_esd_session;
	
	/**
	 * Constructor.
	 */
	public function __construct( $params = null ) 
	{
		parent::__construct('chuppo_im');

	   if ( isset($params['userKey']) )
        {
            $this->profile_key = $params['userKey'] ;
        }
        
        if ( isset($params['oppUserKey']) )
        {
            $this->opponent_key = $params['oppUserKey'];
        }

        if ( isset($params['is_esd_session']) )
        {
            $this->is_esd_session = $params['is_esd_session'];
        }
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('ChuppoIm');
		
		$configs = new SK_Config_Section('chuppo');
		
		//define profile chuppo key 
		app_Profile::defineProfileChuppoKey( SK_HttpUser::profile_id() );

		$this->profile_key = empty($this->profile_key) ? SK_HttpRequest::$GET['userKey'] : $this->profile_key;
		$this->opponent_key = empty($this->opponent_key) ? SK_HttpRequest::$GET['oppUserKey'] : $this->opponent_key;		
		
		$profile_id = app_Profile::getProfileIdByUserKey($this->profile_key);
		$opponent_id = app_Profile::getProfileIdByUserKey($this->opponent_key);
		
		$im_session = app_ChuppoIM::getSession($profile_id, $opponent_id);
			
		if ( !$im_session ) {
            if ($this->is_esd_session)
            {
                app_ChuppoIM::createSession($profile_id , $opponent_id );
            }
            else
            {
                // checking permissions to Open Session
                $service = new SK_Service('initiate_im_session');
                if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
                {
                    $this->no_permission = $service->permission_message['message'];
                    return parent::prepare( $Layout, $Frontend );
                }
                else {
                    app_ChuppoIM::createSession($profile_id , $opponent_id );
                    $service->trackServiceUse();
                }
            }
		}
		else 
			app_ChuppoIM::deleteReceivedSession( $profile_id, $opponent_id );		
		
		$user_info = app_Profile::getFieldValues( $profile_id, array( 'username', 'sex', 'birthdate', 'email', 'country', 'state', 'city' ) );
		$opponent_info = app_Profile::getFieldValues($opponent_id, array( 'username', 'sex', 'birthdate', 'email', 'country', 'state', 'city','profile_id' ) );
		
		$user_info['sex'] = app_Profile::getProfileChuppoGender( $user_info['sex'] );
		$user_info['age'] = app_Profile::getAge( $user_info['birthdate'] );
		
		$opponent_info['sex'] = app_Profile::getProfileChuppoGender( $opponent_info['sex'] );
		$opponent_info['age'] = app_Profile::getAge( $opponent_info['birthdate'] );
		$opponent_info['url'] = SK_Navigation::href( 'profile', array( 'profile_id' => $opponent_info['profile_id'] ) );
			
		$siteUrl = parse_url( SITE_URL );
		 
		// General required parameters
		$chuppoImVars['swfHost'] = URL_CHUPPO_IM;
		$chuppoImVars['swfName'] = $configs->im_swf;
		$chuppoImVars['conStr'] = URL_CHUPPO_IM_SERVER;
		$chuppoImVars['siteUrl'] = $siteUrl['host'];
		$chuppoImVars['profileInfoUrl'] = SK_Navigation::href( 'profile', array( 'profile_id'=>$profile_id ) );
		$chuppoImVars['profileId'] = SK_HttpUser::profile_id();
		
		//design and assets
		$chuppoImVars['layout'] = $configs->im_skin;
		
		//required user Info
		$chuppoImVars['sessionId'] = SK_HttpUser::session_id(); 
		$chuppoImVars['userKey'] = $this->profile_key;
		$chuppoImVars['userName'] = $user_info['username'];
		$chuppoImVars["oppUserKey"] = $this->opponent_key;
		$chuppoImVars['oppUserName'] = $opponent_info['username'];	
		$chuppoImVars['oppUrl'] = SK_Navigation::href( 'profile', array( 'profile_id'=>$opponent_id ) );			
		$chuppoImVars['genderIcon'] = strtolower( $user_info['sex'] );
		
		//additional user info (key:value pairs separated with "|")
		$chuppoImVars['userInfo'] = 'Username:'.$user_info['username'].'|Age:'.$user_info['age'].'|Sex:'.$user_info['sex'].'|Country:'.$user_info['country'].'|City:'.$user_info['city'].';';		
				
		$Frontend->include_js_file( URL_CHUPPO_CHAT.'chuppo.js' );
		$Frontend->include_js_file( URL_CHUPPO_CHAT.'im.js' );
		
		$this->frontend_handler->construct(10000, $chuppoImVars);

	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout )  
	{
		$Layout->assign('no_permission', $this->no_permission);
	}
	
	
	public static function ajax_updateProfileActivity()
	{
		app_Profile::updateProfileActivity();
	}

}
