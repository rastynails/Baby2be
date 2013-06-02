<?php

class component_GroupMembers extends SK_Component 
{
	private $group_id;
	
	private $group;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		if (!SK_HttpRequest::$GET['group_id'])
			SK_HttpRequest::showFalsePage();
		else { 
			$this->group_id = SK_HttpRequest::$GET['group_id'];
			$this->group = app_Groups::getGroupById($this->group_id);
		}
		
		parent::__construct('group_members');
	}
	
	public function render( SK_Layout $Layout )
	{		
		$bc_item_1 = app_TextService::stCensor($this->group['title'], FEATURE_GROUP, true);
		SK_Navigation::removeBreadCrumbItem();
		SK_Navigation::addBreadCrumbItem($bc_item_1, SK_Navigation::href('group', array('group_id'=>$this->group_id)));
		SK_Navigation::addBreadCrumbItem(SK_Language::text('nav_doc_item.group_members'));
		
		$Layout->assign('group_members', new component_GroupMembersList( array('group_id' => $this->group_id)));
		
		$Layout->assign('is_member', app_Groups::isGroupMember( SK_HttpUser::profile_id(), $this->group_id));
		$Layout->assign('is_blocked', app_Groups::isBlocked(SK_HttpUser::profile_id(), $this->group_id));
		
		$Layout->assign('show_invitation', 
			app_Groups::profileIsInvited(SK_HttpUser::profile_id(), $this->group_id) 
			&& !app_Groups::isGroupMember(SK_HttpUser::profile_id(), $this->group_id) );
		
		$Layout->assign('group', $this->group);
		
		return parent::render( $Layout );
	}
}