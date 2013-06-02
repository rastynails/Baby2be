<?php

class component_ForumGroupList extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('forum_group_list');
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
		
		if ( app_Forum::isProfileBanned(SK_HttpUser::profile_id()) ){
			SK_HttpRequest::redirect( SK_Navigation::href('forum_banned_profile_list', 
									  					  array('profile_id'=>SK_HttpUser::profile_id())) );
		}
		
		return parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$groups = app_Forum::getGroupForumList();
		foreach ($groups as $key=>$group)
		{
			if( !$group['forums'] )
				unset($groups[$key]);
		}
		$Layout->assign_by_ref('groups', $groups );
		$Layout->assign_by_ref('moderator', SK_HttpUser::isModerator() );
		return parent::render($Layout);
	}
}
