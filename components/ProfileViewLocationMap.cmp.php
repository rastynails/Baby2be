<?php

class component_ProfileViewLocationMap extends SK_Component
{
    protected $profile_id;
    protected $viewer_id;
    protected $distance;
    protected $displayDistance = false;

	public function __construct( array $params = null )
	{
        if ( empty($params['profile_id']) )
        {
            $this->annul();
            return;
        }

        $this->profile_id = (int)$params['profile_id'];
        $this->viewer_id = SK_HttpUser::profile_id();

        $list = app_Profile::getFieldValues( $params['profile_id'], array('city_id', 'city') );
        $cityIdList = array();

        if ( !empty( $list['city_id'] ) )
        {
            $cityIdList[$profile_id] = $list['city_id'];
        }

        if ( empty($cityIdList) )
        {
            
        }

		parent::__construct('profile_view_location_map');
	}
    
    public function count_distance()
    {
        if ( !empty($this->viewer_id) && $this->profile_id != $this->viewer_id )
        {
            $fields = app_Profile::getFieldValuesForUsers( array($this->profile_id, $this->viewer_id), array('country_id','city_id', 'zip') );

            $cityIdList = array();
            $zipList = array();

            $viewerZip = !empty($fields[$this->viewer_id]['zip']) ? $fields[$this->viewer_id]['zip'] : null;
            $viewerCityId = !empty($fields[$this->viewer_id]['city_id']) ? $fields[$this->viewer_id]['city_id'] : null;
            $viewerCoordinates = array();
            $profileZip = !empty($fields[$this->profile_id]['zip']) ? $fields[$this->profile_id]['zip'] : null;
            $profileCityId = !empty($fields[$this->profile_id]['city_id']) ? $fields[$this->profile_id]['city_id'] : null;
            $profileCoordinates = array();

            if ( empty($viewerZip) && empty($viewerCityId) || empty($profileZip) && empty($profileCityId) )
            {
                return;
            }

            $zipList = array( $viewerCityId => $viewerZip, $profileCityId => $profileZip );

            if ( !empty($zipList) )
            {
                $zipCoordinateList = app_Location::getCoordinatesByZipList($zipList);

                if ( !empty($zipCoordinateList) && $profileZip && !empty( $zipCoordinateList[$profileCityId."_".$profileZip] ) )
                {
                    $zipCoordinates = $zipCoordinateList[$profileCityId."_".$profileZip];

                    if ( empty($zipCoordinates['latitude']) || !empty($zipCoordinates['longitude']) )
                    {
                        $profileCoordinates['latitude'] = $zipCoordinates['latitude'];
                        $profileCoordinates['longitude'] = $zipCoordinates['longitude'];
                    }
                }

                if ( !empty($zipCoordinateList) && $viewerZip && !empty( $zipCoordinateList[$viewerCityId."_".$viewerZip] ) )
                {
                    $zipCoordinates = $zipCoordinateList[$viewerCityId."_".$viewerZip];

                    if ( empty($zipCoordinates['latitude']) || !empty($zipCoordinates['longitude']) )
                    {
                        $viewerCoordinates['latitude'] = $zipCoordinates['latitude'];
                        $viewerCoordinates['longitude'] = $zipCoordinates['longitude'];
                    }
                }
            }

            $cityIdList = array( $viewerCityId => $viewerCityId, $profileCityId => $profileCityId );

            if( (!$viewerCoordinates || !$profileCoordinates) && !empty( $cityIdList ) )
            {
                $cityCoordinateList = app_Location::getCoordinatesByCityIdList($cityIdList);

                if ( !$profileCoordinates )
                {
                    if ( !empty($cityCoordinateList) && $profileCityId && !empty( $cityCoordinateList[$profileCityId] ) )
                    {
                        $cityCoordinates = $cityCoordinateList[$profileCityId];

                        if ( empty($cityCoordinates['latitude']) || !empty($cityCoordinates['longitude']) )
                        {
                            $profileCoordinates['latitude'] = $cityCoordinates['latitude'];
                            $profileCoordinates['longitude'] = $cityCoordinates['longitude'];
                        }
                    }
                }

                if ( !$viewerCoordinates )
                {
                    if ( !empty($cityCoordinateList) && $viewerCityId && !empty( $cityCoordinateList[$viewerCityId] ) )
                    {
                        $cityCoordinates = $cityCoordinateList[$viewerCityId];

                        if ( empty($cityCoordinates['latitude']) || !empty($cityCoordinates['longitude']) )
                        {
                            $viewerCoordinates['latitude'] = $cityCoordinates['latitude'];
                            $viewerCoordinates['longitude'] = $cityCoordinates['longitude'];
                        }
                    }
                }
            }

            if ( empty( $viewerCoordinates)  || empty($profileCoordinates) )
            {
                return;
            }

            $this->distance = app_Location::getDistanceByCoordinates($profileCoordinates['latitude'], $profileCoordinates['longitude'], $viewerCoordinates['latitude'], $viewerCoordinates['longitude'] );
        }
    }
    
    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        parent::prepare($Layout, $Frontend);

        $this->count_distance();
    }

	public function render( SK_Layout $Layout)
	{
        $Layout->assign('profile_id', $this->profile_id);
        $Layout->assign('distance', $this->distance);
        $Layout->assign('unit', SK_Config::section('site')->Section('additional')->Section('profile_list')->search_distance_unit);
        $Layout->assign('opponent_username', app_Profile::username($this->profile_id));
	}
}