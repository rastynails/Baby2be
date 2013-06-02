<?php

class component_ForumBannedProfileList extends SK_Component
{
	private $profiles_list;
	private $profile_id;
	
	public function __construct( array $params = null )
	{
		parent::__construct('forum_banned_profile_list');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{	
		if (!app_Features::isAvailable(22)) {
			SK_HttpRequest::showFalsePage();
		}
		
		$this->profiles_list = app_Forum::getBannedProfiles();	
		//get profile_ids array
		if ($this->profiles_list['profiles'])
		{
			foreach ( $this->profiles_list['profiles'] as $profile )
				$profile_arr[] = $profile['profile_id'];
		}
		
		$handler = new SK_ComponentFrontendHandler('ForumBannedProfileList');
		
		$this->profile_id = SK_HttpRequest::$GET['profile_id'];
		if( $this->profile_id ){
			$error_msg = SK_Language::section('components.forum_banned_profile_list.error_msg');
			$handler->error( $error_msg->text('you_are_banned') );
		}		
		
		if ($profile_arr) {
			$handler->construct($profile_arr);
		}
		
		$this->frontend_handler = $handler;
		
		parent::prepare($Layout, $Frontend);
	}

	
	public function render( SK_Layout $Layout )
	{
		SK_Navigation::removeBreadCrumbItem();
		$label = SK_Language::section('components.forum_banned_profile_list')->text('banned_profiles');
		SK_Navigation::addBreadCrumbItem($label);

		$Layout->assign('profile_list', $this->profiles_list);
		$Layout->assign('moderator', SK_HttpUser::isModerator());
		$Layout->assign('profile_id', $this->profile_id);
			
		return parent::render($Layout);
	}
	
	public static function ajax_RemoveBan($params, SK_ComponentFrontendHandler $handler )
	{	
		$lang_msg = SK_Language::section('components.forum_banned_profile_list.messages') ;
		
		app_Forum::RemoveProfileBan( $params->profile_id );
		$handler->message( $lang_msg->text('profile_was_approved') );
		$handler->removeBlock( $params->profile_id );
	}

}
