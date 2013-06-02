<?php

class app_ProfileViewHistory
{
	public static function track($profile_id, $reviewed_id)
	{
		$query = SK_MySQL::placeholder(" REPLACE `" . TBL_PROFILE_VIEW_HISTORY . "` (`id`, `profile_id`, `viewed_id`, `time_stamp`) VALUES(null, ?, ?,?)",
			$profile_id, $reviewed_id, time());
		
		SK_MySQL::query($query);
		
		return (bool)SK_MySQL::affected_rows();
	}
	
	public static function dropViews($start_stamp, $end_stamp)
	{
		$query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_VIEW_HISTORY . "` WHERE `time_stamp` BETWEEN ? AND ?",
		$start_stamp, $end_stamp);
		
		SK_MySQL::query($query);
		
		return (bool)SK_MySQL::affected_rows();
	}
	
	
	public static function getList($profile_id, $start_stamp, $end_stamp, $criterias=null)
	{
		$criterias = (strlen(trim($criterias)))?$criterias:'1';
		$page = app_ProfileList::getPage();
		
		$config_section = SK_Config::Section('site')->Section('additional')->Section('profile_list');
		
		$first = (int) $config_section->result_per_page * ( $page-1 );
		$count = (int) $config_section->result_per_page;
		
		$_query_parts['projection'] = '`p`.*, `pvh`.`view_count`, `pvh`.`view_time`, `online`.`hash` AS `online`';
		$_query_parts['right_join'] = SK_MySQL::placeholder(
		  "RIGHT JOIN (SELECT `profile_id`, COUNT(`profile_id`) AS `view_count`, MAX(time_stamp) view_time FROM `" . TBL_PROFILE_VIEW_HISTORY . "` 
		          WHERE `viewed_id`=? AND `time_stamp` BETWEEN ? AND ? GROUP BY `profile_id`) AS `pvh` USING(`profile_id`)"
		, $profile_id, $start_stamp, $end_stamp);
		
		$_query_parts['left_join'] = "LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` ON (`p`.`profile_id` = `online`.`profile_id`)";
		$_query_parts['condition'] = "`p`.`profile_id` IS NOT NULL AND " . app_Profile::SqlActiveString('p') . " AND $criterias";
		$_query_parts['limit'] = "$first, $count";
		
        foreach ( explode("|",$config_section->order) as $val)
        {
            if(in_array($val, array('','none')) )
                continue;
        
            app_ProfileList::_configureOrder($_query_parts, $val, 'p');
        }

            $sex_condition = '';

            if ( $config_section->display_only_looking_for )
            {
                $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');

                $sex_condition = !empty( $match_sex ) ? " AND `p`.`sex` & " . $match_sex . " " : '';
            }

            if ( $config_section->gender_exclusion )
            {
                $gender_exclusion = ' AND `p`.`sex` != ' . app_Profile::getFieldValues( $profile_id, 'sex' );
            }

		$query = "SELECT {$_query_parts['projection']} FROM `".TBL_PROFILE."` AS `p`
		    {$_query_parts['right_join']}
            {$_query_parts['left_join']}
            WHERE {$_query_parts['condition']} $gender_exclusion $sex_condition ".
            ( isset($_query_parts['group']) && (strlen($_query_parts['group']))?" GROUP BY {$_query_parts['group']}":"" ).
            ( (strlen($_query_parts['order']))?" ORDER BY {$_query_parts['order']}":"" ).
            " LIMIT {$_query_parts['limit']}";
		
		$result = SK_MySQL::query($query);
		
		$list = array();
		while ($item = $result->fetch_assoc()) {
			$list[] = $item;
		}
				
		$result = array();
		
		$result['profiles'] = $list;

		$query = SK_MySQL::placeholder("
		SELECT COUNT(*) 
		FROM
		(
			SELECT	`p`.`profile_id`
			FROM `" . TBL_PROFILE . "` AS `p`
			RIGHT JOIN (SELECT `profile_id`, COUNT(`profile_id`) AS `view_count` FROM `" . TBL_PROFILE_VIEW_HISTORY . "` WHERE `viewed_id`=? AND `time_stamp` BETWEEN ? AND ? GROUP BY `profile_id`) AS `pvh`
				USING(`profile_id`)
			WHERE `p`.`profile_id` IS NOT NULL AND " . app_Profile::SqlActiveString('p') . " AND $criterias $gender_exclusion $sex_condition
		) AS `q` ",
			$profile_id, $start_stamp, $end_stamp);
			
		$result['total'] = SK_MySQL::query($query)->fetch_cell();
		
		return $result;
	}
	
	private static function date_list($profile_id, $viewed_id)
	{
		$query = SK_MySQL::placeholder("SELECT `time_stamp` FROM `" . TBL_PROFILE_VIEW_HISTORY . "` WHERE `profile_id`=? AND `viewed_id`=? ORDER BY `time_stamp` DESC",
			$profile_id, $viewed_id);
		$result = SK_MySQL::query($query);
		
		if ($result->num_rows()) {
			return array();
		}
		
		$list = array();
		while ($info = $result->fetch_assoc())
			$list[] = $info['time_stamp'];
		return $list;
	}
	
	public static function getViewerProfilesCount($profile_id, $start_stamp, $end_stamp)
	{
            $gender_exclusion = '';

            $config = SK_Config::section( 'site.additional.profile_list' );

            $sex_condition = '';

            if ( $config->display_only_looking_for )
            {
                $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');

                $sex_condition = !empty( $match_sex ) ? " AND `p`.`sex` & " . $match_sex . " " : '';
            }

            if ( $config->gender_exclusion )
            {
                $gender_exclusion = ' AND `p`.`sex` != ' . app_Profile::getFieldValues( $profile_id, 'sex' );
            }
		$query = SK_MySQL::placeholder("
		SELECT COUNT(*) 
		FROM
		(
			SELECT	`p`.`profile_id`
			FROM `" . TBL_PROFILE . "` AS `p`
			RIGHT JOIN (SELECT `profile_id`, COUNT(`profile_id`) AS `view_count` FROM `" . TBL_PROFILE_VIEW_HISTORY . "` WHERE `viewed_id`=? AND `time_stamp` BETWEEN ? AND ? GROUP BY `profile_id`) AS `pvh`
				USING(`profile_id`)
			WHERE `p`.`profile_id` IS NOT NULL AND " . app_Profile::SqlActiveString('p') . " $sex_condition $gender_exclusion
		) AS `q` ",
			$profile_id, $start_stamp, $end_stamp);
		
		return intval(SK_MySQL::query($query)->fetch_cell());
	}
}