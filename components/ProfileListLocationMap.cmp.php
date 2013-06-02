<?php

abstract class component_ProfileListLocationMap extends component_ProfileList
{
    public $div_id;
    public $points = array();
    public $isSetBounds = false;
    public $options = array();
    public $southWestLat;
    public $southWestLng;
    public $northEastLat;
    public $northEastLng;
    public $width = '100%';
    public $height = '300px';
    public $profileDetails = array();

    public $map_center_lat = 0;
    public $map_center_lng = 0;
    public $isSetMapCenter = false;

	public function __construct( array $params = null )
	{
        if ( !app_Features::isAvailable(67) )
        {
            $this->annul();
            return;
        }

		parent::__construct('profile_list_location_map');
        $this->namespace = 'profile_list_location_map';
        
        $this->div_id = uniqid('google_map_'. rand(0, 9999999));
        $this->isSetBounds = false;
        
        $this->width = !empty($params['width']) ? $params['width'] : $this->width;
        $this->height = !empty($params['height']) ? $params['height'] : $this->height;

        $this->profile_list = $this->profiles();

        $this->prepare_list();
        $this->prepare_markers();
	}

    protected function prepare_markers()
    {
        $profileIdList = array();
        $this->profileDetails = array();

        foreach( $this->list as $profile )
        {
            $profileIdList[$profile['profile_id']] = $profile['profile_id'];
            $this->profileDetails[$profile['profile_id']] = $profile;
        }

        $list = app_Profile::getFieldValuesForUsers( $profileIdList, array('city_id', 'zip', 'country') );
        $cityIdList = array();
        $zipList = array();
        $locationToProfile = array();

        foreach( $list as $profile_id => $profileField )
        {
            if ( !empty( $profileField['city_id'] ) )
            {
                $cityIdList[$profile_id] = $profileField['city_id'];

                if ( !empty($profileField['zip']) )
                {
                    $zipList[$profileField['city_id']] = $profileField['zip'];
                    $locationToProfile[$profile_id]['zip'] = $profileField['zip'];
                }

                $locationToProfile[$profile_id]['city_id'] = $cityIdList[$profile_id];
            }
        }

        if ( empty($cityIdList) )
        {
            return;
        }

        app_Profile::getUsernamesForUsers($profileIdList);
        app_ProfilePhoto::getThumbUrlList($profileIdList);
        app_Profile::getOnlineStatusForUsers($profileIdList);
        $birthdate_fields = app_ProfileField::getProfileListBirthdateFields();
        $profileFieldData = app_Profile::getFieldValuesForUsers($profileIdList, array_merge($birthdate_fields, array('country', 'state', 'city', 'zip', 'custom_location')));
        //$profileStatusData = app_Profile::getUserStatusForList($profileIdList);

        //-------------------//
        if( SK_HttpUser::is_authenticated() )
        {
            app_Bookmark::initUserBlockCacheForValues(SK_HttpUser::profile_id(), $profileIdList);
            app_Bookmark::initProfileBookmarkedCache(SK_HttpUser::profile_id(), $profileIdList);
            app_FriendNetwork::initUserRelationCacheForValues(SK_HttpUser::profile_id(), $profileIdList);
            app_EventService::initSpeedDatingProfileBookmarks(SK_HttpUser::profile_id(), $profileIdList);
            app_Newsfeed::newInstance()->initCacheForFollow(SK_HttpUser::profile_id(), 'user', $profileIdList);
        }

        $locationCityList = app_Location::getCoordinatesByCityIdList($cityIdList);
        $locationZipList = app_Location::getCoordinatesByZipList($zipList);

        $this->points = array();
        $uniqueCoordinates = array();

        foreach( $locationToProfile as $profileId => $value )
        {
            $lat = null;
            $lng = null;
            $location = null;
            
            $cityId = !empty($value['city_id']) ? $value['city_id'] : null;
            $zip = !empty($value['zip']) ? $value['zip'] : null;
            
            if ( $cityId && $zip )
            {
                if( !empty($locationZipList[$cityId . '_' . $zip]) )
                {
                    $location = $locationZipList[$cityId . '_'. $zip];
                    $lat = $location['latitude'];
                    $lng = $location['longitude'];
                }
            }

            if (  empty($zip) && !empty($cityId) && !empty( $locationCityList[$cityId] ) && isset($locationCityList[$cityId]['latitude']) && isset($locationCityList[$cityId]['longitude']) )
            {
                $location = $locationCityList[$cityId];

                if ( !empty($profileId) )
                {
                    $lat = $location['latitude'];
                    $lng = $location['longitude'];
                }
            }
            
            if( isset($lat) && isset($lng) )
            {
                
                $key = $lat . "_" .$lng;
            

                if( !empty($uniqueCoordinates[$key]) )
                {
                    $lat += .0001 * rand(-200, 200);
                    $lng += .0001 * rand(-200, .200);

                    $key = $lat . "_" .$lng;
                }

                $uniqueCoordinates[$key] = $profileId;

                $this->points[$profileId]['latitude'] = $lat;
                $this->points[$profileId]['longitude'] = $lng;
                $this->points[$profileId]['title'] = $list[$profileId]['city'];
                $this->points[$profileId]['content'] = '';
                $this->points[$profileId]['isOpen'] = false;
            }
        }
	}

    public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
        parent::prepare( $Layout, $Frontend );

        foreach( $this->profileDetails as $profileDetails )
        {
            $cmp = new component_UsersMapItem( array( 'profile_details' => $profileDetails ) );

            if ( !empty($cmp) && !empty($this->points[$profileDetails['profile_id']]) )
            {
                /* @var $Layout SK_Layout */
                $this->points[$profileDetails['profile_id']]['content'] = $Layout->renderComponent($cmp);
            }
        }

        $points = "";
        foreach ( $this->points as $point )
        {
            $points .= " window.map[".(json_encode($this->div_id))."].addPoint(".((float)$point['latitude']).", ".((float)$point['longitude']).", ".  json_encode($point['title']).", ".  json_encode($point['content']).", ".  json_encode($point['isOpen'])."); \n";
        }

        $bounds = "";
        if( $this->isSetBounds )
        {
            $bounds = "
                var sw = new google.maps.LatLng(".(float)$this->southWestLat.",".(float)$this->southWestLng.");
                var ne = new google.maps.LatLng(".(float)$this->northEastLat.",".(float)$this->northEastLng.");

                var bounds = new google.maps.LatLngBounds(sw, ne);
                window.map[".(json_encode($this->div_id))."].fitBounds(bounds); ";
        }

        $mapOptions = $this->options;

        $mapOptionsString = " {
                zoom: 1,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP ";

        unset($mapOptions['zoom']);

        if ( isset($mapOptions['center']) )
        {
            unset($mapOptions['center']);
        }

        if ( isset($mapOptions['mapTypeId']) )
        {
            unset($mapOptions['mapTypeId']);
        }

        foreach( $this->options as $key => $value )
        {
            if ( !empty($value) )
            {
                $mapOptionsString .=  ", $key: $value \n";
            }
        }

        $mapOptionsString .= "}";

        $closeButtonUrl = URL_LAYOUT_IMG . 'loc_close.png';

        $script = " $( document ).ready(function(){
            var latlng = new google.maps.LatLng(".(float)$this->map_center_lat.",".(float)$this->map_center_lng.");

            var options = $mapOptionsString;

            if( window.map == undefined )
            {
                window.map = [];
            }

            window.map[".(json_encode($this->div_id))."] = new GoogleMap(".json_encode($this->div_id).");
            window.map[".(json_encode($this->div_id))."].initialize(options, '{$closeButtonUrl}');
            {$bounds}
            {$points}
           }); ";

        $key = SK_Config::section('site')->Section('additional')->Section('googlemaps')->google_map_api_key;

        if ( !empty($key) )
        {
            $key = "&key=".$key;
        }
        
        /* @var $Frontend SK_Frontend */
        $Frontend->include_js_file('http://maps.google.com/maps/api/js?sensor=false' . $key);
        $Frontend->include_js_file(URL_STATIC . 'info_bubble.js');
        $Frontend->include_js_file(URL_COMPONENTS . 'ProfileListLocationMap.cmp.js');
        
        $Frontend->onload_js($script);

        $Layout->template_dir;
        
        $this->tpl_file = 'default.tpl';
	}

	public function render( SK_Layout $Layout)
	{
        $Layout->assign('div_id', $this->div_id);
        $Layout->assign('width', $this->width);
        $Layout->assign('height', $this->height);
	}

}