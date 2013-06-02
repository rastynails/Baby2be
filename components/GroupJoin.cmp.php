<?php

class component_GroupJoin extends SK_Component 
{
	private $group_id;
	
	private $display_type;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		if ( !app_Features::isAvailable(38) || !strlen($params['group_id']) || !SK_HttpUser::profile_id() )
		{
			$this->annul();
		}
		else
		{
			$this->group_id = intval($params['group_id']);
			
			$is_member = app_Groups::isGroupMember(SK_HttpUser::profile_id(), $this->group_id);
			$has_claimed = app_Groups::hasClaimedAccess(SK_HttpUser::profile_id(), $this->group_id); 
			
			if ( $is_member || $has_claimed )
				$this->annul();
				
			$this->display_type = $params['display_type'];
		}
		
		parent::__construct('group_join');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('GroupJoin');
		$this->frontend_handler->construct( 
			$this->group_id, 
			SK_Language::text('components.group_join.confirm'),
			SK_Language::text('components.group_join.claim_confirm')
		);
		
		parent::prepare($Layout, $Frontend);
	}
	
	
	public function render( SK_Layout $Layout )
	{
		$group = app_Groups::getGroupById($this->group_id);
		$Layout->assign('group', $group);
		
		$Layout->assign('display_type', $this->display_type);
		
		return parent::render( $Layout );
	}
	
	public static function ajax_JoinGroup( SK_HttpRequestParams $params = null, SK_ComponentFrontendHandler $handler) {
		
		$group_id = $params->group_id;
		$profile_id = SK_HttpUser::profile_id();
		
		if ($params->has('group_id') && $group_id)
		{	
			if (!app_Groups::canJoin($profile_id, $group_id))
			{
				$handler->error(SK_Language::text('components.group_join.cannot_join') );
				return array('result' => false);
			}
			elseif (app_Groups::addGroupMember(SK_HttpUser::profile_id(), $group_id))
			{
				$handler->message(SK_Language::text('components.group_join.join_successfull') );
				return array('result' => true);
			}
		}
		return array('result' => false);
	}
	
	public static function ajax_ClaimGroupAccess( SK_HttpRequestParams $params = null, SK_ComponentFrontendHandler $handler) 
	{	
		$profile_id = SK_HttpUser::profile_id();
		
		if ($params->has('group_id') && $profile_id)
		{
			$group_id = $params->group_id;
			
			$is_member = app_Groups::isGroupMember($profile_id, $group_id);
			$has_claimed = app_Groups::hasClaimedAccess($profile_id, $group_id); 
			
			if ( $is_member || $has_claimed )
			{
				$handler->error(SK_Language::text("%components.group_join.claim_error"));
				return array('result' => false);
			}	
				
			if (app_Groups::claimAccess($profile_id, $group_id))
			{
				$handler->message(SK_Language::text("%components.group_join.claim_success"));
				return array('result' => true);
			}
			else 
			{
				$handler->error(SK_Language::text("%components.group_join.claim_error"));
				return array('result' => false);
			}			
		}
		return array('result' => false);
	}
}