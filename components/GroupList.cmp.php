<?php

class component_GroupList extends SK_Component 
{
	private $page;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		if (!app_Features::isAvailable(38))
			SK_HttpRequest::showFalsePage();
		else	
			$this->page = $params['page'];
						
		parent::__construct('group_list');
	}
	
	public function render( SK_Layout $Layout )
	{
		if ( isset(SK_HttpRequest::$GET['member_id']) && $member_id = (int)SK_HttpRequest::$GET['member_id'])
		{
			$page = (int)SK_HttpRequest::$GET['page'] ? (int)SK_HttpRequest::$GET['page'] : 1; 
			$group_arr = app_Groups::getGroupsProfileParticipates($member_id, $page);			
			$count = $group_arr['total'];
			$groups = $group_arr['list'];
		}
		elseif ( isset(SK_HttpRequest::$GET['owner_id']) && $owner_id = (int)SK_HttpRequest::$GET['owner_id'])
		{
			$page = (int)SK_HttpRequest::$GET['page'] ? (int)SK_HttpRequest::$GET['page'] : 1; 
			$group_arr = app_Groups::getGroupsProfileCreated($owner_id, $page);			
			$count = $group_arr['total'];
			$groups = $group_arr['list'];
		}	
		else 
		{
			$groups = app_Groups::getGroupList($this->page);
			$count = app_Groups::getGroupsCount();
		}
		
		if ($count > 0) {
			$Layout->assign('paging', array(
				'total'		=> $count,
				'on_page'	=> SK_Config::Section('site')->Section('additional')->Section('groups')->result_per_page,
				'pages'		=> 5
			));
			
			$Layout->assign('groups', $groups);
		} 
		else 
			$Layout->assign('no_groups', true);
			
		return parent::render( $Layout );
	}
}