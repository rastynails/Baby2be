<?php

class component_UsersMapItem extends SK_Component
{
    protected $profile_details = array();

    public function __construct( array $params = array() )
	{
		parent::__construct('users_map_item');

        $this->profile_details = !empty($params['profile_details']) ? $params['profile_details'] : null;

        if ( empty($this->profile_details) || empty($this->profile_details['profile_id']) )
        {
            $this->annul();
        }

        $this->profile_details['profile_url'] = SK_Navigation::href('profile', array('profile_id' => $this->profile_details['profile_id']));
	}

	public function render( SK_Layout $Layout)
	{
        $Layout->assign('profile', $this->profile_details);
	}
}