<?php

class component_ProfileLocationMap extends component_ProfileListLocationMap
{
    protected $profile_id;

	public function __construct( array $params = null )
	{
        if ( !app_Features::isAvailable(67) )
        {
            $this->annul();
            return;
        }

        if ( empty($params['profile_id']) )
        {
            $this->annul();
            return;
        }

        $this->profile_id = (int)$params['profile_id'];

		parent::__construct(array());
        //$this->namespace = 'profile_location_map';

        $this->options['zoom'] = 10;
        
        if( empty($this->points) )
        {
            $this->options['zoom'] = 1;

            $this->map_center_lat = 30;
            $this->map_center_lng = -30;

            if( !$this->isSetMapCenter )
            {
                $this->isSetMapCenter = true;
            }
        }

        foreach ( $this->points as $point )
        {
            $this->map_center_lat = $point['latitude'];
            $this->map_center_lng = $point['longitude'];

            if( !$this->isSetMapCenter )
            {
                $this->isSetMapCenter = true;
            }
        }
	}

    protected function profiles()
	{
        $list = app_ProfileList::getProfileListByUserIdList( array( $this->profile_id ) );
        return $list;
    }
}