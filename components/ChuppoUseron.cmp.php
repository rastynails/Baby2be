<?php

class component_ChuppoUseron extends SK_Component
{
	private $user_info;
	
	private $configs;
	
	/**
	 * Constructor.
	 */
	public function __construct( $params = null ) 
	{
		parent::__construct('chuppo_useron');
		
		$this->configs = new SK_Config_Section('chuppo');	
			
		$this->user_info = app_Profile::getFieldValues( SK_HttpUser::profile_id(), array( 'username', 'sex', 'birthdate', 'email', 
																				    	  'country', 'state', 'city', 'status' ) );
		
		if ( !SK_HttpUser::profile_id() || $this->user_info['status'] == 'suspended' || !$this->configs->enable_chuppo_im )
		{
			$this->annul();
		}
		
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('ChuppoUseron');
		
		$this->user_info['sex'] = app_Profile::getProfileChuppoGender( $this->user_info['sex'] );
		$this->user_info['age'] = app_Profile::getAge( $this->user_info['birthdate'] );
		
		app_Profile::defineProfileChuppoKey( SK_HttpUser::profile_id() );
		
		$profile_key = app_ProfileField::getProfileUniqueId( SK_HttpUser::profile_id() );
		
		$siteUrl = parse_url( SITE_URL ); 
		
		$chuppoUseronVars['swfHost'] = URL_CHUPPO_USERON;
		$chuppoUseronVars['swfName'] = $this->configs->useron_swf;
		$chuppoUseronVars['conStr'] = URL_CHUPPO_USERON_SERVER;
		$chuppoUseronVars['siteUrl'] = $siteUrl['host'];
		$chuppoUseronVars['profileInfoUrl'] = SK_Navigation::href('profile', array( 'profile_id' => SK_HttpUser::profile_id()) );
		$chuppoUseronVars['profileId'] = SK_HttpUser::profile_id();
		$chuppoUseronVars['layout'] = $this->configs->useron_skin;
		$chuppoUseronVars['sessionId'] = SK_HttpUser::session_id(); 
		$chuppoUseronVars['userKey'] = $profile_key;
		$chuppoUseronVars['userName'] = $this->user_info['username'];
		$chuppoUseronVars['genderIcon'] = strtolower( $this->user_info['sex'] );
		$chuppoUseronVars['userInfo'] = 'Username:'.$this->user_info['username'].'|Age:'.$this->user_info['age'].'|Sex:'.$this->user_info['sex'].'|Country:'.$this->user_info['country'].'|City:'.$this->user_info['city'].';';		
	
		$Frontend->include_js_file( URL_CHUPPO_CHAT.'chuppo.js' );
		$Frontend->include_js_file( URL_CHUPPO_CHAT.'useronim.js' );	
		
		$this->frontend_handler->construct( SITE_URL, $chuppoUseronVars);

	}

}
