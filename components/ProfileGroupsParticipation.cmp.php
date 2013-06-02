<?php

class component_ProfileGroupsParticipation extends SK_Component 
{
	private $profile_id;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		$this->profile_id = $params["profile_id"];
						
		parent::__construct('profile_groups_participation');
	}
	
	public function render( SK_Layout $Layout )
	{	
		$Layout->assign('groups', app_Groups::getGroupsProfileParticipates($this->profile_id));
		$Layout->assign("username", app_Profile::username($this->profile_id));
		$Layout->assign("profile_id", $this->profile_id);
				
		return parent::render( $Layout );
	}
}