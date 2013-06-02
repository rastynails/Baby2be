<?php

class component_ForumNewTopic extends SK_Component
{
	private $group_id;
	
	private $group;
	
	public function __construct( array $params = null )
	{
		if (intval(SK_HttpRequest::$GET['group_id']) 
			&& app_Features::isAvailable(38)
			&& app_Groups::isGroupMember(SK_HttpUser::profile_id(), SK_HttpRequest::$GET['group_id'] ))
		{
			$this->group_id = intval(trim(SK_HttpRequest::$GET['group_id']));
			$this->group = app_Groups::getGroupById($this->group_id);
		}
			
		parent::__construct('forum_new_topic');		
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
		
		$Frontend->onload_js('$("select[name=\'forum_id\'] option[value=\'\']").attr("disabled", "0");');
		
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$service = new SK_Service('forum_write');
		if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
			$no_permission = $service->permission_message['message'];
		
		if (isset($this->group))
		{
			$Layout->assign('group_title', $this->group['title']);
		}
		
		$Layout->assign( 'no_permission', $no_permission );
	}	
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form ) 
	{
		if (!$this->group_id)
		{
			$forums = app_Forum::getForumsList();

			$form->getField('forum_id')->setValues( $forums );
		}
		else 
		{
			$forum_id = app_Groups::getGroupForumId( $this->group_id );	
			
			$form->getField('group_forum_id')->setValue( $forum_id );
			$form->getField('group_id')->setValue( $this->group_id );
		}
	}
	
}