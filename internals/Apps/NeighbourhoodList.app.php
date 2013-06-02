<?php

class app_NeighbourhoodList
{
    public static function setDefaultNeighLocation($location,$profile_id,$distance = false)
	{
		SK_MySQL::query(SK_MySQL::placeholder("UPDATE `".TBL_PROFILE."` SET `neigh_location`='?', `neigh_location_distance`=? WHERE `profile_id`=?",$location,(!$distance ? null : $distance),$profile_id));
		return (bool)SK_MySQL::affected_rows();
	}
	
	
	public static function getDefaultDistance($profile_id)
	{
		return SK_MySQL::query(SK_MySQL::placeholder("SELECT `neigh_location_distance` FROM `".TBL_PROFILE."` WHERE `profile_id`=?",$profile_id))->fetch_cell();
	}

    public static function getAvailableLocationLists( $profile_id )
	{
        if( (int)$profile_id < 0 )
        {
            return array();
        }

        static $cache = array();

        if( isset($cache[$profile_id]) )
        {
            return $cache[$profile_id];
        }

		$query = SK_MySQL::placeholder( "SELECT `country_id` AS `country`, `state_id` AS `state`, `city_id` AS `city`, `zip` FROM `".TBL_PROFILE."` WHERE `profile_id`=?", $profile_id );
		
		$return_array = array();
		
 		foreach ( SK_MySQL::query($query)->fetch_assoc() as $key => $value )
			if ( $value )
				$return_array[] = $key;

        $cache[$profile_id] = $return_array;

		return $return_array;
	}
	
	public static function getMinLocationItem( $profile_id )
	{
		$array = self::getAvailableLocationLists( $profile_id );
		
		if(in_array('zip',$array)) 
			return 'zip';
			
		if(in_array('city',$array))
			return 'city';
			
		if(in_array('state',$array))
			return 'state';
			
		return 'country';
			
	}
	
	public static function getProfileSelectedLocationforNeigh( $profile_id )
	{
		
		$query = SK_MySQL::placeholder("SELECT `neigh_location` FROM `".TBL_PROFILE."` WHERE `profile_id`=?",$profile_id);
		$selected_loc = SK_MySQL::query($query)->fetch_cell();
		
		if($selected_loc && in_array($selected_loc, self::getAvailableLocationLists($profile_id)))
			return $selected_loc;
		
		$min_location = self::getMinLocationItem($profile_id);	
		SK_MySQL::query(
						SK_MySQL::placeholder("UPDATE `".TBL_PROFILE."` SET `neigh_location`='?' WHERE `profile_id`=?",$min_location,$profile_id)
						);
		return $min_location;	
	}
	
	public static function getList( $mode, $profile_id, $only_count = false, $distance = false )
	{
			
		$config_section = SK_Config::section('site')->Section('additional')->Section('profile_list');
		
		// detect online list result page
		$page = ( isset( SK_HttpRequest::$GET['page'] ) && intval( SK_HttpRequest::$GET['page'] ) ) ? SK_HttpRequest::$GET['page'] : 1;
		
		if ( !in_array( $mode, self::getAvailableLocationLists( $profile_id ) ) )
			$mode = 'country';
		
		$profile_info = app_Profile::getFieldValues( $profile_id, array( 'country_id', 'state_id', 'city_id', 'zip' ) );	
			
		switch ( $mode )
		{
			case 'country':
				$query_add = " AND `profile`.`country_id`='".$profile_info['country_id']."' AND `profile`.`profile_id` !='".$profile_id."' ";
				break;
				
			case 'state':
				$query_add = " AND `profile`.`state_id`='".$profile_info['state_id']."' AND `profile`.`profile_id` !='".$profile_id."' ";
				break;
				
			case 'city':
				$query_add = " AND `profile`.`city_id`='".$profile_info['city_id']."' AND `profile`.`profile_id` !='".$profile_id."' ";
				break;
				
			case 'zip':
				$query_add = " AND `profile`.`zip`='".$profile_info['zip']."' AND `profile`.`profile_id` !='".$profile_id."' ";
				break;
		}
		
		if($distance && ($mode == 'zip' || $mode == 'city') && intval($distance) > 0)
		{
			$_search_distance_unit = ( $config_section->search_distance_unit == 'mile' ) ? '3963.0' : '6378.8';
			
			if($mode == 'city')
			{			
				$_city_info = SK_MySQL::query(
							 				SK_MySQL::placeholder("
							 					SELECT `Feature_dec_lat` AS `lat`, `Feature_dec_lon` AS `lon` 
												FROM `".TBL_LOCATION_CITY."` WHERE `Feature_int_id`='?'",
							 					$profile_info['city_id'] ) )->fetch_assoc();
				
//				$query_add_dis = SK_MySQL::placeholder("
//													 AND `city_id` IN ( SELECT `Feature_int_id` FROM `".TBL_LOCATION_CITY."`
//													WHERE ( $_search_distance_unit*acos( sin( ".( $_city_info['lat']/57.29577951 ).") * sin(`Feature_dec_lat`/57.29577951) + cos(".( $_city_info['lat']/57.29577951 ).") * cos(`Feature_dec_lat`/57.29577951) *	cos(`Feature_dec_lon`/57.29577951 -".$_city_info['lon']/57.29577951.") ) ) <= ? )"
//													, $distance);

				$query_add_dis = SK_MySQL::placeholder( " INNER JOIN `".TBL_LOCATION_CITY."` AS `ct`
									ON( `profile`.`city_id` = `ct`.`Feature_int_id` AND ( $_search_distance_unit*acos( sin( ".( $_city_info['lat']/57.29577951 ).") * sin(`Feature_dec_lat`/57.29577951) + cos(".( $_city_info['lat']/57.29577951 ).") * cos(`Feature_dec_lat`/57.29577951) *	cos(`Feature_dec_lon`/57.29577951 -".$_city_info['lon']/57.29577951.") ) ) <= ? )"
														, $distance);									
											
							
			}
			else 
			{
				$_zip_info = SK_MySQL::query(
									SK_MySQL::placeholder( "SELECT `latitude` AS `lat`, `longitude` AS `lon` 
										FROM `".TBL_LOCATION_ZIP."` WHERE `zip`='?' AND `country_id`='?'"
									, $profile_info['zip'], $profile_info['country_id'] ) )->fetch_assoc();	
				
//				$query_add_dis = SK_MySQL::placeholder( "
//									 AND `zip` IN ( SELECT `zip` FROM `".TBL_LOCATION_ZIP."` WHERE ( $_search_distance_unit*acos( sin( ".( $_zip_info['lat']/57.29577951 ).") * sin(`latitude`/57.29577951) + cos(".( $_zip_info['lat']/57.29577951 ).") * cos(`latitude`/57.29577951) * cos(`longitude`/57.29577951 -".$_zip_info['lon']/57.29577951.") ) ) <= ?  )"
//														, $distance);
														
				$query_add_dis = SK_MySQL::placeholder( " INNER JOIN `".TBL_LOCATION_ZIP."` AS `zt`
									ON( `profile`.`zip` = `zt`.`zip` AND ( $_search_distance_unit*acos( sin( ".( $_zip_info['lat']/57.29577951 ).") * sin(`latitude`/57.29577951) + cos(".( $_zip_info['lat']/57.29577951 ).") * cos(`latitude`/57.29577951) * cos(`longitude`/57.29577951 -".$_zip_info['lon']/57.29577951.") ) ) <= '?' )"
														, $distance);
			}
		}
		else 
		{
			$query_add_dis = '';	
		}
        
        $sex_condition = "";
        if ( $config_section->display_only_looking_for  && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues($profile_id, 'match_sex');
            if (!empty($match_sex))
            {
                $sex_condition = " AND `profile`.`sex` & ".$match_sex." ";
            }
        }         

        $gender_exclusion = '';

        if ( $config_section->gender_exclusion && SK_HttpUser::is_authenticated() )
        {
            $gender_exclusion = ' AND `profile`.`sex` != ' . app_Profile::getFieldValues( $profile_id, 'sex' );
        }
		$query = SK_MySQL::placeholder( "SELECT COUNT(DISTINCT `profile`.`profile_id`) FROM `".TBL_PROFILE."` AS `profile`
			LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `online` USING( `profile_id` )
			".( $query_add_dis ? $query_add_dis : '')."
			WHERE ".app_Profile::SqlActiveString( 'profile' ).($query_add_dis ? '' : $query_add)." AND `profile`.`profile_id` !=? $sex_condition  $gender_exclusion",$profile_id);
		
		$result['total'] = SK_MySQL::query( $query )->fetch_cell();	
			
		if( $only_count )
			return $result['total'];
		
//-----
		$_query_parts['projection'] = "DISTINCT `profile`.*, `online`.`hash` AS `online`";
		$_query_parts['left_join'] = "LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `online` USING( `profile_id` )";		
		$_query_parts['order'] = '';
		$_query_parts['group'] = '';
		
		foreach ( explode("|",SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val)
		{
			if(in_array($val, array('','none')) )
				continue;
			
			app_ProfileList::_configureOrder($_query_parts, $val);				
		}			
//-----			
		$result_per_page = $config_section->result_per_page;
		$query = SK_MySQL::placeholder( "SELECT {$_query_parts['projection']} FROM `".TBL_PROFILE."` AS `profile` 
			{$_query_parts['left_join']}".($query_add_dis ? $query_add_dis : '')."
			WHERE ".app_Profile::SqlActiveString( 'profile' ).($query_add_dis ? '' : $query_add)." AND `profile`.`profile_id` !=? $sex_condition $gender_exclusion".
			((strlen($_query_parts['group']))?" GROUP BY {$_query_parts['group']}":'').
			((strlen($_query_parts['order']))?" ORDER BY {$_query_parts['order']}":'')
			." LIMIT ".$result_per_page*( $page-1 ).", ".$result_per_page ,$profile_id );
		
		$query_result = SK_MySQL::query( $query );
		while ( $row = $query_result->fetch_assoc())
			$result['profiles'][] = $row;
		
		return $result;
	}
	
}