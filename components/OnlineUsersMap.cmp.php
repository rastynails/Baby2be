<?php

class component_OnlineUsersMap extends SK_Component
{
	public function __construct()
	{
        if ( !app_Features::isAvailable(67) )
        {
            $this->annul();
        }

        parent::__construct('online_users_map');
	}
}