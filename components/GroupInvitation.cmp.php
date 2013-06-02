<?php

class component_GroupInvitation extends SK_Component 
{
	private $group_id;
	
	private $group;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		if ( !SK_HttpRequest::$GET['group_id'] )
		{
			SK_HttpRequest::showFalsePage();
		}
		else
		{
			$this->group_id = intval(SK_HttpRequest::$GET['group_id']);
			$this->group = app_Groups::getGroupById($this->group_id); 
		}
		
		parent::__construct('group_invitation');
	}
	
	public function render( SK_Layout $Layout )
	{
		// adjust breadcrumb & header
		SK_Navigation::removeBreadCrumbItem();
		$bc_item_1 = app_TextService::stCensor($this->group['title'], FEATURE_GROUP, true);
		SK_Navigation::addBreadCrumbItem($bc_item_1, SK_Navigation::href('group', array('group_id'=>$this->group_id)));
		$lang_key = SK_Language::text('%components.group_invitation.invite_members'); 
		SK_Navigation::addBreadCrumbItem($lang_key);
		SK_Language::defineGlobal('group_edit_page', $lang_key );
		
		$is_creator = app_Groups::isGroupCreator(SK_HttpUser::profile_id(), $this->group_id);
		$is_moderator = app_Groups::isGroupModerator(SK_HttpUser::profile_id(), $this->group_id);
		
		if ( $is_creator || $is_moderator)
		{
			$Layout->assign('members', app_Groups::getGroupMembers($this->group_id));
			$Layout->assign('members_count', app_Groups::getGroupMembersCount($this->group_id));
			$Layout->assign('invitations', app_Groups::getInvitedMembers($this->group_id));
		}
		else {
			$Layout->assign('error', SK_Language::text('components.group_claims.no_access'));
		}
		
		return parent::render( $Layout );
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		$form->getField('group_id')->setValue( $this->group_id );
	}
}