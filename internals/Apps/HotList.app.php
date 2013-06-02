<?php

/**
 * Class for managing "hot" members list, 
 * who bought this service by SMS
 *
 */
class app_HotList
{
	/**
	 * Returns list of profiles in hot list
	 *
	 * @return array
	 */
	public static function getHotList()
	{
		$limit = (int) SK_Config::section('site.additional.profile_list')->hot_list_limit;
		
		$limit = $limit ? $limit : 6;
		
        $sex_condition = "";
        if ( SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_looking_for_hotlist && SK_HttpUser::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');
            if (!empty($match_sex))
            {
                $sex_condition = " AND `profile`.`sex` & ".$match_sex." ";
            }            
        }              
        
		$query = "SELECT `h`.*, `profile`.*
			FROM `".TBL_HOTLIST."` AS `h`
			INNER JOIN `".TBL_PROFILE."` AS `profile` ON (`h`.`profile_id`=`profile`.`profile_id`)
			LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `o` ON (`o`.`profile_id`=`profile`.`profile_id`)
			WHERE ".app_Profile::SqlActiveString( 'profile' ).$sex_condition."
			ORDER BY `h`.`timestamp` DESC LIMIT $limit";
		
		$res = SK_MySQL::query($query);
		
		$list = array();
		while ($row = $res->fetch_assoc())
			$list[] = $row;

		return $list;
	}
	
	/**
	 * Adds profile to hot list
	 *
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function addProfile($profile_id)
	{
		if (!$profile_id)
			return false;
		
		$in_list = app_HotList::isHot($profile_id);
		
		if ( $in_list )
		{
            $query = SK_MySQL::placeholder("UPDATE `" . TBL_HOTLIST ."` SET `timestamp`=? 
                WHERE `profile_id`=?", time(), $profile_id);
		}
		else
		{
    		$query = SK_MySQL::placeholder("INSERT INTO `" . TBL_HOTLIST ."` (`profile_id`, `timestamp`) 
    			VALUES (?,?)", $profile_id, time());
		}
		SK_MySQL::query($query);
		
		$aff_rows = SK_MySQL::affected_rows();
		 
		if ( $aff_rows )
			self::unsetExceedProfiles();
		
		return $aff_rows ? true : false;
	}
	
	/**
	 * Deletes profiles from hot list if limit was exceeded
	 *
	 * @return boolean
	 */
	private function unsetExceedProfiles()
	{
		$limit = (int) SK_Config::section('site.additional.profile_list')->hot_list_limit;

		if ($limit) {
			$query = "SELECT `profile_id` FROM `".TBL_HOTLIST."`
				ORDER BY `timestamp` DESC
				LIMIT $limit";
				
			$res = SK_MySQL::query($query);
			$ids = '';
			while($cell = $res->fetch_cell())
				$ids .= $cell . ', ';
			
			$ids = substr($ids, 0, -2);
			
			$query = "DELETE FROM `".TBL_HOTLIST."` WHERE `profile_id` NOT IN($ids)";

			SK_MySQL::query($query);
			
			return SK_MySQL::affected_rows() ? true : false;
		}
		return false;
	}
	
	/**
	 * Check if profile is already in the list
	 *
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function isHot($profile_id)
	{
		if (!$profile_id)
			return false;
			
		$query = SK_MySQL::placeholder("SELECT `profile_id` 
			FROM `".TBL_HOTLIST."` WHERE `profile_id`=?", $profile_id);

		$in_list = SK_MySQL::query($query);
		
		return $in_list->num_rows() ? true : false;
	}
        
        /**
         * Remove profile from Hot list
         * 
         * @param integer $profile_id
         * @return boolean 
         */
        public static function removeProfile( $profile_id )
        {
            if ( empty($profile_id) )
            {
                return false;
            }
            
            if ( self::isHot($profile_id) )
            {
                $qeruy = SK_MySQL::placeholder('DELETE FROM `'.TBL_HOTLIST.'`
                    WHERE `profile_id`=?', $profile_id );
                SK_MySQL::query( $qeruy );
                
                return SK_MySQL::affected_rows() ? true : false;
            }
            else
            {
                return false;
            }
        }        
	
}
