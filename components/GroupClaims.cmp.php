<?php

class component_GroupClaims extends SK_Component 
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

			if ( !$this->group['group_id'] )
			{
                SK_HttpRequest::showFalsePage();
			}
		}
		
		parent::__construct('group_claims');
	}
	
	public function render( SK_Layout $Layout )
	{
		SK_Navigation::removeBreadCrumbItem();
		$bc_item_1 = app_TextService::stCensor($this->group['title'], FEATURE_GROUP, true);
		SK_Navigation::addBreadCrumbItem($bc_item_1, SK_Navigation::href('group', array('group_id'=>$this->group_id)));
		
		$lang_key = SK_Language::text('%components.group_claims.page_header');
		SK_Navigation::addBreadCrumbItem($lang_key);
		SK_Language::defineGlobal('group_edit_page', $lang_key);
		
		$is_creator = app_Groups::isGroupCreator(SK_HttpUser::profile_id(), $this->group_id);
		$is_moderator = app_Groups::isGroupModerator(SK_HttpUser::profile_id(), $this->group_id);
		
		if ($is_creator || $is_moderator)
		{
			$Layout->assign('claims', new component_GroupClaimsList( array('group_id' => $this->group_id)));
		}
		else { 
			$Layout->assign('error', SK_Language::text('components.group_claims.no_access'));
		}
				
		return parent::render( $Layout );
	}
}