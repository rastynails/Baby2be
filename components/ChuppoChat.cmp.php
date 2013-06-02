<?php

class component_ChuppoChat extends SK_Component
{

	private $no_permission; 
	
	/**
	 * Constructor.
	 */
	public function __construct( $params = null ) 
	{
		parent::__construct('chuppo_chat');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('ChuppoChat');
		
		$service = new SK_Service('chat');

		if ($service->checkPermissions()!=SK_Service::SERVICE_FULL) {
			$this->no_permission = $service->permission_message['message'];
			return parent::prepare( $Layout, $Frontend );
		}
	    else 
        {
            $service->trackServiceUse();
        }
		
		$configs = new SK_Config_Section('chuppo');
		
		//define profile chuppo key 
		app_Profile::defineProfileChuppoKey( SK_HttpUser::profile_id() );
		
		$user_info = app_Profile::getFieldValues( SK_HttpUser::profile_id(), array( 'username', 'sex', 'birthdate', 'email', 
																				    'country', 'state', 'city' ) );
		$user_info['sex'] = app_Profile::getProfileChuppoGender( $user_info['sex'] );
		$user_info['age'] = app_Profile::getAge( $user_info['birthdate'] );
		
		$siteUrl = parse_url( SITE_URL ); 
		
		$chuppoChatVars['swfHost'] = URL_CHUPPO_CHAT;
		$chuppoChatVars['swfName'] = $configs->chat_swf;
		$chuppoChatVars['conStr'] = URL_CHUPPO_CHAT_SERVER;
		$chuppoChatVars['siteUrl'] = $siteUrl['host'];
		$chuppoChatVars['profileInfoUrl'] = SK_Navigation::href('profile', array( 'profile_id' => SK_HttpUser::profile_id()) );
		$chuppoChatVars['profileId'] = SK_HttpUser::profile_id();
		$chuppoChatVars['layout'] = $configs->chat_skin;
		$chuppoChatVars['sessionId'] = SK_HttpUser::session_id(); 
		$chuppoChatVars['userKey'] = app_ProfileField::getProfileUniqueId( SK_HttpUser::profile_id() );
		$chuppoChatVars['userName'] = $user_info['username'];
		$chuppoChatVars['genderIcon'] = strtolower( $user_info['sex'] );
		$chuppoChatVars['userInfo'] = 'Username:'.$user_info['username'].'|Age:'.$user_info['age'].'|Sex:'.$user_info['sex'].'|Country:'.$user_info['country'].'|City:'.$user_info['city'].';';		
		
		$Frontend->include_js_file( URL_CHUPPO_CHAT.'chuppo.js' );
		$Frontend->include_js_file( URL_CHUPPO_CHAT.'chat.js' );
		
		$this->frontend_handler->construct(10000, $chuppoChatVars);

	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout )  {
		$Layout->assign('no_permission', $this->no_permission);
	}

	
	public static function ajax_updateProfileActivity()
	{
		app_Profile::updateProfileActivity();
	}

}
