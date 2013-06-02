<?php defined('SYSPATH') OR die('No direct access allowed.');

class Members_Model extends Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_members( $list, $page, $on_page = null, $profile_id = null )
	{
		$page = isset($page) ? $page : 1;
		$on_page = isset($on_page) ? $on_page : SK_Config::Section('site.additional.profile_list')->result_per_page;
		$pr = array(); 
		switch ($list) {
			case 'featured':
				$pr = $this->get_featured($page, $on_page); 
				break;
			
			case 'new':
				$pr = $this->get_new($page, $on_page);
				break;
				
			case 'online':
				$pr = $this->get_online($page, $on_page);
				break;
				
			case 'matches':
				$pr = $this->get_matches($page, $on_page, $profile_id);
				break;
				
			case 'bookmarks':
				$pr = $this->get_bookmarks($page, $on_page, $profile_id);
				break;	
				
			default:
				$pr = $this->get_featured($page, $on_page);
		}
		return $pr;
		
	}
	
	private function activity_str($alias_name = null)
	{
		$alias = strlen(trim($alias_name)) ? "`$alias_name`." : '';
 
		$acc_conf = SK_Config::section("site")
			->Section("additional")
			->Section("profile")
			->not_reviewed_profile_access;
			 
		return !$acc_conf ? "( $alias`status`='active' AND $alias`reviewed`='y' )" : "( $alias`status`='active' )";
	}
	
	private function get_featured( $page, $on_page )
	{		
		$qp = array(
			'fields'	=> "`p`.*, `o`.`hash` AS `online`",
			'join'		=> "LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `o` ON(`p`.`profile_id` = `o`.`profile_id`)",
			'condition' => "`p`.`featured` = 'y' AND " . $this->activity_str('p'),
			'order'		=> "",
			'limit'		=> $on_page * ($page - 1) . ", " . $on_page
		);
		
		$order_conf = SK_Config::Section('site.additional.profile_list')->order;
		
		$order_conf = explode("|", $order_conf);
		 
		foreach ( $order_conf as $val)
		{
			if (in_array($val, array('','none')) )
				continue;

			$this->order_str($qp, $val);
		}

		$query = "SELECT {$qp['fields']} FROM `" . TBL_PROFILE . "` AS `p`
			{$qp['join']}
			WHERE {$qp['condition']} " . 
			(strlen(@$qp['group']) ? " GROUP BY {$qp['group']}" : "") .
			(strlen($qp['order']) ? " ORDER BY {$qp['order']}" : "") .
			" LIMIT {$qp['limit']}";

		$result = SK_MySQL::query($query);
		
		$list = array();
		while ($item = $result->fetch_object()) {
			$item->sex = SKM_Language::text('%profile_fields.value.sex_'.$item->sex);
			$item->location = app_Profile::getFieldValues( $item->profile_id, array( 'country', 'state', 'city', 'zip' ) );
			$item->age = app_Profile::getAge($item->birthdate);
			
			if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $item->profile_id)) {
				$item->activity_info['item'] = false;
			} else {
				$item->activity_info = app_Profile::ActivityInfo( $item->activity_stamp, $item->online );
				$item->activity_info['item_label'] = isset($item->activity_info['item']) ? SKM_Language::section('profile.labels')->text('activity_'.$item->activity_info['item']) : false;
			}
			
			$list[] = $item;
		}

		$query = "SELECT COUNT(`p`.`profile_id`) FROM `" . TBL_PROFILE . "` AS `p`
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `o` ON(`o`.`profile_id` = `p`.`profile_id`)
			WHERE `p`.`featured` = 'y' AND " . $this->activity_str('p');

		$total = SK_MySQL::query($query)->fetch_cell();
		
		return array('list' => $list, 'total' => $total);
	}
	
	private function get_new( $page, $on_page )
	{
	    $config_section = SK_Config::Section('site')->Section('additional')->Section('profile_list');
        $period = time() - (intval($config_section->new_members_period) * 24 * 60 * 60);
		
		$viewer_cond = SKM_User::is_authenticated() ? SK_MySQL::placeholder("`p`.`profile_id`!= ?", SKM_User::profile_id()) : '1';
		
        $sex_condition = "";
        if ( SK_Config::Section('site')->Section('additional')->Section('profile_list')->display_only_looking_for && SKM_User::is_authenticated() )
        {
            $match_sex = app_Profile::getFieldValues(SKM_User::profile_id(), 'match_sex');
            if ( !empty($match_sex) )
            {
                $sex_condition = " AND `p`.`sex` & " . $match_sex . " ";
            }
        }
		
		$qp = array(
			'fields'	=> "`p`.*, `o`.`hash` AS `online`",
			'join'		=> "LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `o` ON(`p`.`profile_id` = `o`.`profile_id`)",
			'condition' => "`p`.`join_stamp` BETWEEN  $period AND " . time() . " AND " . $this->activity_str('p') . 'AND '. $viewer_cond . $sex_condition,
			'order'		=> " `p`.`join_stamp` DESC ",
			'limit'		=> $on_page * ($page - 1) . ", " . $on_page
		);
		
		/*$order_conf = SK_Config::Section('site.additional.profile_list')->order;
		
		$order_conf = explode("|", $order_conf);
		 
		foreach ( $order_conf as $val)
		{
			if (in_array($val, array('','none')) )
				continue;

			self::order_str($qp, $val);
		}*/

		$query = "SELECT {$qp['fields']} FROM `" . TBL_PROFILE . "` AS `p`
			{$qp['join']}
			WHERE {$qp['condition']} " . 
			(strlen(@$qp['group']) ? " GROUP BY {$qp['group']}" : "") .
			(strlen($qp['order']) ? " ORDER BY {$qp['order']}" : "") .
			" LIMIT {$qp['limit']}";

		$result = SK_MySQL::query($query);
		
		$list = array();
		while ($item = $result->fetch_object()) {
			$item->sex = SKM_Language::text('%profile_fields.value.sex_'.$item->sex);
			$item->location = app_Profile::getFieldValues( $item->profile_id, array( 'country', 'state', 'city', 'zip' ) );
			$item->age = app_Profile::getAge($item->birthdate);
			
			if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $item->profile_id)) {
				$item->activity_info['item'] = false;
			} else {
				$item->activity_info = app_Profile::ActivityInfo( $item->activity_stamp, $item->online );
				$item->activity_info['item_label'] = isset($item->activity_info['item']) ? SKM_Language::section('profile.labels')->text('activity_'.$item->activity_info['item']) : false;
			}
			
			$list[] = $item;
		}

		$query = "SELECT COUNT(`p`.`profile_id`) FROM `" . TBL_PROFILE . "` AS `p`
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `o` ON(`o`.`profile_id` = `p`.`profile_id`)
			WHERE `p`.`join_stamp` BETWEEN $period AND ".time()." AND " . $this->activity_str('p') . ' AND '. $viewer_cond . $sex_condition;

		$total = SK_MySQL::query($query)->fetch_cell();
		
		if (!$total)
			return array('list' => array(), 'total' => 0);
		
		return array('list' => $list, 'total' => $total);
	}
	
	private function get_online( $page, $on_page, $sex = '' )
	{
		$avaliable_values = SK_ProfileFields::get('sex')->values;
		$sex_cond = in_array($sex, $avaliable_values) ? ' `profile`.`sex`='.$sex.' AND ':'';
		
		$qp = array(
			'fields'	=> "`p`.*, `o`.`hash` AS `online`",
			'join'		=> "LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `o` ON(`p`.`profile_id` = `o`.`profile_id`)",
			'condition' => $sex_cond . "`o`.`hash` IS NOT NULL AND " . $this->activity_str('p'),
			'order'		=> "",
			'limit'		=> $on_page * ($page - 1) . ", " . $on_page
		);
		
		$order_conf = SK_Config::Section('site.additional.profile_list')->order;
		
		$order_conf = explode("|", $order_conf);
		 
		foreach ( $order_conf as $val)
		{
			if (in_array($val, array('','none')) )
				continue;

			self::order_str($qp, $val);
		}

		$query = "SELECT {$qp['fields']} FROM `" . TBL_PROFILE . "` AS `p`
			{$qp['join']}
			WHERE {$qp['condition']} " . 
			(strlen(@$qp['group']) ? " GROUP BY {$qp['group']}" : "") .
			(strlen($qp['order']) ? " ORDER BY {$qp['order']}" : "") .
			" LIMIT {$qp['limit']}";

		$result = SK_MySQL::query($query);
		
		$list = array();
		while ($item = $result->fetch_object()) {
			$item->sex = SKM_Language::text('%profile_fields.value.sex_'.$item->sex);
			$item->location = app_Profile::getFieldValues( $item->profile_id, array( 'country', 'state', 'city', 'zip' ) );
			$item->age = app_Profile::getAge($item->birthdate);
			
			if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $item->profile_id)) {
				$item->activity_info['item'] = false;
			} else {
				$item->activity_info = app_Profile::ActivityInfo( $item->activity_stamp, $item->online );
				$item->activity_info['item_label'] = isset($item->activity_info['item']) ? SKM_Language::section('profile.labels')->text('activity_'.$item->activity_info['item']) : false;
			}
			
			$list[] = $item;
		}

		$query = "SELECT COUNT(`p`.`profile_id`) FROM `" . TBL_PROFILE . "` AS `p`
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `o` ON(`o`.`profile_id` = `p`.`profile_id`)
			WHERE {$qp['condition']} AND " . $this->activity_str('p');

		$total = SK_MySQL::query($query)->fetch_cell();
		
		return array('list' => $list, 'total' => $total);
	}
	
	private function get_matches( $page, $on_page, $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return array();

		// detect matching fields
		$query = "SELECT `match`.`match_type`, `field`.`name` AS `field_name`, `match_field`.`name` AS `match_field_name`
			FROM `".TBL_PROF_FIELD_MATCH_LINK."` AS `match`
			LEFT JOIN `".TBL_PROF_FIELD."` AS `field` USING ( `profile_field_id` )
			LEFT JOIN `".TBL_PROF_FIELD."` AS `match_field` ON `match`.`match_profile_field_id`=`match_field`.`profile_field_id`";

		$res = SK_MySQL::query($query);
		$fields_arr = array();
		
		while ( $row = $res->fetch_assoc())
			$fields_arr[] = $row;

		$country_id = app_Profile::getFieldValues($profile_id, 'country_id');

		if ( !strlen($country_id) )
			return array();
			
		$qp = array(
			'fields'	=> "`p`.*, `o`.`hash` AS `online`",
			'join'		=> "LEFT JOIN `".TBL_PROFILE_EXTEND."` AS `ex` ON(`ex`.`profile_id` = `p`.`profile_id`) 
							LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `o` ON(`p`.`profile_id` = `o`.`profile_id`)",
			'condition' => "",
			'order'		=> "",
			'limit'		=> $on_page * ($page - 1) . ", " . $on_page
		);	

		$order_conf = SK_Config::Section('site.additional.profile_list')->order;
		
		$order_conf = explode("|", $order_conf);
		 
		foreach ( $order_conf as $val)
		{
			if (in_array($val, array('','none')) )
				continue;

			$this->order_str($qp, $val);
		}
		
		$by_country_sql_cond = app_ProfilePreferences::get('matches', 'in_my_country_only_matches', $profile_id ) ? 
			SK_MySQL::placeholder("`country_id` = '?'", $country_id) : '1';
			
		$main_query = SK_MySQL::placeholder( "SELECT {$qp['fields']} FROM `".TBL_PROFILE."` AS `p`
			{$qp['join']}
			WHERE $by_country_sql_cond AND `p`.`profile_id` NOT IN ( SELECT `blocked_id` FROM `".TBL_PROFILE_BLOCK_LIST."`
			WHERE `profile_id` =? ) ", $profile_id );

		$query_cond = '';

		foreach ( $fields_arr as $key => $match_info )
		{
			$profile_info = app_Profile::getFieldValues( $profile_id, array( $match_info['field_name'], $match_info['match_field_name'] ) );

			switch ( $match_info['match_type'] )
			{
				case 'exact':
					$field_value = ( $profile_info[$match_info['field_name']] ) ? $profile_info[$match_info['field_name']] : MAX_SQL_BIGINT;
					$match_field_value = ( $profile_info[$match_info['match_field_name']] ) ? $profile_info[$match_info['match_field_name']] : MAX_SQL_BIGINT;

					$query_cond .= "AND ( $field_value&IF( ISNULL(`".$match_info['match_field_name']."`) OR `".$match_info['match_field_name']."`=0, ".MAX_SQL_BIGINT.", `".$match_info['match_field_name']."` )
						AND $match_field_value&IF( ISNULL(`".$match_info['field_name']."`) OR `".$match_info['field_name']."`=0, ".MAX_SQL_BIGINT.", `".$match_info['field_name']."` ) ) ";
					break;

				case 'range':
					if ( !$profile_info[$match_info['match_field_name']] || !$profile_info[$match_info['field_name']] )
						continue 2;

					$age_info = explode( '-', $profile_info[$match_info['match_field_name']] );

					$query_cond .= SK_MySQL::placeholder( "AND YEAR('".date("Y-m-d H:i:s")."')-YEAR(`{$match_info['field_name']}`)- IF( DAYOFYEAR(`{$match_info['field_name']}`) > DAYOFYEAR('".date("Y-m-d H:i:s")."'),1,0) >= ?
						AND YEAR('".date("Y-m-d H:i:s")."')-YEAR(`{$match_info['field_name']}`)-IF( DAYOFYEAR(`{$match_info['field_name']}`) > DAYOFYEAR('".date("Y-m-d H:i:s")."'),1,0) <=?"
						, $age_info[0], $age_info[1] );

					$age = app_Profile::getAge( $profile_info[$match_info['field_name']] );
					$query_cond .= SK_MySQL::placeholder(
						" AND SUBSTRING_INDEX( `{$match_info['match_field_name']}`, '-', 1 ) <= ?
					     AND SUBSTRING_INDEX( `{$match_info['match_field_name']}`, '-', -1 ) >= ? ", $age, $age
					);
					break;
			}
		}

		$photo_only = app_ProfilePreferences::get('matches', 'with_photo_only_matches', $profile_id ) ? true : false;
		$photo_only_sql_cond = $photo_only ? 'AND `p`.`has_photo` = "y"':'';

		$main_query .= SK_MySQL::placeholder( $query_cond." AND ".$this->activity_str('p')." AND `p`.`profile_id` != ? $photo_only_sql_cond", $profile_id );

		//$limit = self::getLimit('match');
		
		$main_query = $main_query.
		((isset($qp['order']) && strlen($qp['group'])) ? " GROUP BY {$qp['group']}" : "" ).
		((isset($qp['order']) && strlen($qp['order'])) ? " ORDER BY {$qp['order']}" : "").
		" LIMIT {$qp['limit']}";
		
		$result = SK_MySQL::query($main_query);
		
		$list = array();
		while ( $item = $result->fetch_object() ) {
			$item->sex = SKM_Language::text('%profile_fields.value.sex_'.$item->sex);
			$item->location = app_Profile::getFieldValues( $item->profile_id, array( 'country', 'state', 'city', 'zip' ) );
			$item->age = app_Profile::getAge($item->birthdate);
			
			if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $item->profile_id)) {
				$item->activity_info['item'] = false;
			} else {
				$item->activity_info = app_Profile::ActivityInfo( $item->activity_stamp, $item->online );
				$item->activity_info['item_label'] = isset($item->activity_info['item']) ? SKM_Language::section('profile.labels')->text('activity_'.$item->activity_info['item']) : false;
			}
			
			$list[] = $item;
		}
		
		return array('list' => $list, 'total' => $this->get_matches_count($profile_id) );
	}
	
	private function get_matches_count( $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return 0;

		$query_cond = '';
		
		// detect matching fields
		$query = "SELECT `match`.`match_type`, `field`.`name` AS `field_name`, `match_field`.`name` AS `match_field_name`
			FROM `".TBL_PROF_FIELD_MATCH_LINK."` AS `match`
			LEFT JOIN `".TBL_PROF_FIELD."` AS `field` USING ( `profile_field_id` )
			LEFT JOIN `".TBL_PROF_FIELD."` AS `match_field` ON `match`.`match_profile_field_id`=`match_field`.`profile_field_id`";

		$res = SK_MySQL::query($query);
		$fields_arr = array();
		
		while ( $row = $res->fetch_assoc())
			$fields_arr[] = $row;

		$country_id = app_Profile::getFieldValues($profile_id, 'country_id');

		if ( !strlen($country_id) )
			return array();
			
		$by_country_sql_cond = app_ProfilePreferences::get('matches', 'in_my_country_only_matches', $profile_id ) ? 
			SK_MySQL::placeholder("`country_id` = '?'", $country_id) : '1';

		$count_main_query = SK_MySQL::placeholder( "SELECT COUNT( `p`.`profile_id` ) FROM `".TBL_PROFILE."` AS `p`
			LEFT JOIN `".TBL_PROFILE_EXTEND."` AS `ex` ON( `p`.`profile_id`=`ex`.`profile_id` )
			LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `o` ON( `p`.`profile_id`=`o`.`profile_id` )
			WHERE $by_country_sql_cond
			AND `p`.`profile_id` NOT IN ( SELECT `blocked_id` FROM `".TBL_PROFILE_BLOCK_LIST."`
			WHERE `profile_id` =? ) ", $profile_id );

		foreach ( $fields_arr as $key => $match_info )
		{
			$profile_info = app_Profile::getFieldValues( $profile_id, array( $match_info['field_name'], $match_info['match_field_name'] ) );

			switch ( $match_info['match_type'] )
			{
				case 'exact':

					$field_value = ( $profile_info[$match_info['field_name']] ) ? $profile_info[$match_info['field_name']] : MAX_SQL_BIGINT;
					$match_field_value = ( $profile_info[$match_info['match_field_name']] ) ? $profile_info[$match_info['match_field_name']] : MAX_SQL_BIGINT;

					$query_cond .= " AND ( $field_value&IF( ISNULL(`".$match_info['match_field_name']."`) OR `".$match_info['match_field_name']."`=0, ".MAX_SQL_BIGINT.", `".$match_info['match_field_name']."` )
						AND $match_field_value&IF( ISNULL(`".$match_info['field_name']."`) OR `".$match_info['field_name']."`=0, ".MAX_SQL_BIGINT.", `".$match_info['field_name']."` ) ) ";
					break;

				case 'range':
					if ( $profile_info[$match_info['match_field_name']] )
					{
						$age_info = explode( '-', $profile_info[$match_info['match_field_name']] );

						$query_cond .= SK_MySQL::placeholder( "AND YEAR('".date("Y-m-d H:i:s")."')-YEAR(`{$match_info['field_name']}`)- IF( DAYOFYEAR(`{$match_info['field_name']}`) >= DAYOFYEAR('".date("Y-m-d H:i:s")."'),1,0) >= ?
							AND YEAR('".date("Y-m-d H:i:s")."')-YEAR(`{$match_info['field_name']}`)-IF( DAYOFYEAR(`{$match_info['field_name']}`) >= DAYOFYEAR('".date("Y-m-d H:i:s")."'),1,0) <=?"
							, $age_info[0], $age_info[1] );
					}

					if ( $profile_info[$match_info['field_name']] )
					{
						$age = app_Profile::getAge($profile_info[$match_info['field_name']]);
						$query_cond .= SK_MySQL::placeholder(
							" AND SUBSTRING_INDEX( `{$match_info['match_field_name']}`, '-', 1 ) <= ?
					     	AND SUBSTRING_INDEX( `{$match_info['match_field_name']}`, '-', -1 ) >= ? ", $age, $age);
					}

					break;
			}
		}

		$photo_only = app_ProfilePreferences::get('matches', 'with_photo_only_matches', $profile_id ) ? true : false;
		$photo_only_sql_cond = $photo_only ? 'AND `p`.`has_photo` = "y"' : '';

		$count_main_query .= SK_MySQL::placeholder( $query_cond." AND ".$this->activity_str('p')." AND `p`.`profile_id` != ? $photo_only_sql_cond", $profile_id );

		return SK_MySQL::query($count_main_query)->fetch_cell();
	}
	
	private function get_bookmarks( $page, $on_page, $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return array();

		$qp = array(
			'fields'	=> "`b`.*, `p`.*, `o`.`hash` AS `online`",
			'join'		=> "INNER JOIN `".TBL_PROFILE."` AS `p` ON(`b`.`bookmarked_id` = `p`.`profile_id`)
							INNER JOIN `".TBL_PROFILE_EXTEND."` AS `ex` ON( `b`.`bookmarked_id`=`ex`.`profile_id`) 
							LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `o` ON( `b`.`bookmarked_id`=`o`.`profile_id`)",
			'condition' => SK_MySQL::placeholder("`b`.`profile_id`=? AND ".$this->activity_str('p'), $profile_id),
			'order'		=> "",
			'limit'		=> $on_page * ($page - 1) . ", " . $on_page
		);		
		
		$order_conf = SK_Config::Section('site.additional.profile_list')->order;
		
		$order_conf = explode("|", $order_conf);
		 
		foreach ( $order_conf as $val)
		{
			if (in_array($val, array('','none')) )
				continue;

			$this->order_str($qp, $val);
		}
			
		$main_query = SK_MySQL::placeholder( "SELECT {$qp['fields']} FROM `".TBL_PROFILE_BOOKMARK_LIST."` AS `b`
			{$qp['join']}
			WHERE {$qp['condition']}".
			(strlen(@$qp['group']) ? " GROUP BY {$qp['group']}" : "") .
			(strlen($qp['order']) ? " ORDER BY {$qp['order']}" : "") .
			" LIMIT {$qp['limit']}", $profile_id );

		$result = SK_MySQL::query($main_query);
		
		$list = array();
		while ($item = $result->fetch_object()) {
			$item->sex = SKM_Language::text('%profile_fields.value.sex_'.$item->sex);
			$item->location = app_Profile::getFieldValues( $item->profile_id, array( 'country', 'state', 'city', 'zip' ) );
			$item->age = app_Profile::getAge($item->birthdate);
			
			if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $item->profile_id)) {
				$item->activity_info['item'] = false;
			} else {
				$item->activity_info = app_Profile::ActivityInfo( $item->activity_stamp, $item->online );
				$item->activity_info['item_label'] = isset($item->activity_info['item']) ? SKM_Language::section('profile.labels')->text('activity_'.$item->activity_info['item']) : false;
			}
			
			$list[] = $item;
		}

		$query = SK_MySQL::placeholder(	"SELECT COUNT(`b`.`bookmarked_id`) 
			FROM `".TBL_PROFILE_BOOKMARK_LIST."` AS `b`
			INNER JOIN `".TBL_PROFILE."` AS `p` ON( `b`.`bookmarked_id`=`p`.`profile_id` )
			WHERE `b`.`profile_id`=? AND ".$this->activity_str( 'p' ), $profile_id );

		$total = SK_MySQL::query($query)->fetch_cell();
		
		return array('list' => $list, 'total' => $total);
	}
	
	private function order_str(&$qp, $type, $alias = 'p')
	{
		$qp['join'] = isset($qp['join']) ? $qp['join'] : '';
		$qp['fields'] = isset($qp['fields']) ? $qp['fields'] : '';
		$qp['group'] = isset($qp['group']) ? $qp['group'] : '';
		$qp['order'] = isset($qp['order']) ? $qp['order'] : '';

		switch($type)
		{
			case 'paid members':
				$qp['fields'] .= (strlen($qp['fields']) ? "," : "") . 
					" SUM(`fin`.`amount`) AS `total_amount`";
				
				$qp['join'] .= " LEFT JOIN `" . TBL_FIN_SALE . "` AS `fin` ON(`$alias`.`profile_id` = `fin`.`profile_id`)";
				$qp['group'] = "`$alias`.`profile_id`";
				$qp['order'] .= (strlen($qp['order']) ? "," : "") . "`total_amount` DESC";
				break;

			case 'last activity':
				$qp['order'] .= (strlen($qp['order']) ? "," : "") .
					" IF( `o`.`profile_id` <> NULL, 0 , 1 ), `activity_stamp` DESC";
				break;

			case 'with photo':
				$qp['order'] .= (strlen($qp['order']) ? "," : "") . "`$alias`.`has_photo` DESC";
				break;

			case 'join date':
				$qp['order'] .= (strlen($qp['order']) ? "," : "") . "`$alias`.`join_stamp` DESC";
				break;
		}
	}
	
	public function quickSearch( $fields, $page, $on_page )
	{
		$query_cond = '';
		
		// sex
		if (isset($fields['sex']))
			$query_cond .= $fields['sex'] ? intval($fields['sex'])."&`main`.`match_sex` AND " : '';

		// match sex
		if (isset($fields['match_sex']))
			$query_cond .= $fields['match_sex'] ? SK_MySQL::placeholder("`main`.`sex`&? AND ", @array_sum($fields['match_sex'])) : '';

		// birthdate
		$alias = SK_ProfileFields::get('birthdate')->base_field ? 'main' : 'extend';	
		$query_cond .= SK_MySQL::placeholder(
			" YEAR(NOW())-YEAR(`$alias`.`birthdate`)- IF( DAYOFYEAR(`$alias`.`birthdate`) > DAYOFYEAR(NOW()),1,0) >= ? 
			AND YEAR(NOW())-YEAR(`$alias`.`birthdate`)-IF( DAYOFYEAR(`$alias`.`birthdate`) > DAYOFYEAR(NOW()),1,0) <=? AND ", 
			$fields['birthdate'][0], $fields['birthdate'][1] 
		); 
		
		// location
		$loc_cond = '';
                $loc_join = '';
		if (isset($fields['location']['zip']) && strlen(trim($fields['location']['zip'])))
		{
			$config = SK_Config::section('site')->Section('additional')->Section('profile_list');
			$unit = $config->search_distance_unit == 'mile' ? '3963.0' : '6378.8';
			
			if (isset($fields['location']['radius']) && intval( $fields['location']['radius']))
			{
				$zip_query = SK_MySQL::placeholder("SELECT `latitude` AS `lat`, `longitude` AS `lon` 
					FROM `".TBL_LOCATION_ZIP."` WHERE `zip`=?", $fields['location']['zip']);
	
				$zip_info = SK_MySQL::query($zip_query)->fetch_assoc();

				$loc_join = SK_MySQL::placeholder( " INNER JOIN ( SELECT `zip` FROM `".TBL_LOCATION_ZIP."` WHERE ( $unit*acos( sin( ".( $zip_info['lat']/57.29577951 ).") * sin(`latitude`/57.29577951) + cos(".( $zip_info['lat']/57.29577951 ).") * cos(`latitude`/57.29577951) * cos(`longitude`/57.29577951 -".$zip_info['lon']/57.29577951.") ) ) <= '?'  ) AS `zip_first` ON ( `zip_first`.`zip` = `main`.`zip` ) ", $fields['location']['radius'] );
			}
			else 
			{
				$loc_cond = SK_MySQL::placeholder("`main`.`zip`=? AND", @$fields['location']['zip']);
			}
		}

		// online only
		if (isset($fields['search_online_only']))
			$query_cond .= $fields['search_online_only'] ? "(`o`.`hash` IS NOT NULL) AND " : "";

		// with photo only
		if (isset($fields['search_with_photo_only']))
			$query_cond .= $fields['search_with_photo_only'] ? "(`has_photo`='y') AND " : "";
			
		// exclude viewer
		$viewer_cond = SKM_User::is_authenticated() ? SK_MySQL::placeholder(" `main`.`profile_id`!=?", SKM_User::profile_id()) : '';
		
		$qp = array(
			'fields'	=> "`main`.*, `o`.`hash` AS `online`",
			'join'		=> "LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `o` ON(`main`.`profile_id`=`o`.`profile_id`)
							LEFT JOIN `".TBL_PROFILE_EXTEND."` AS `extend` ON(`main`.`profile_id`=`extend`.`profile_id`) " . $loc_join,
			'condition' => $query_cond . $loc_cond . $viewer_cond . " AND " . $this->activity_str('main'),
			'order'		=> "",
			'limit'		=> $on_page * ($page - 1) . ", " . $on_page
		);
		
		$order_conf = SK_Config::Section('site.additional.profile_list')->order;
		
		$order_conf = explode("|", $order_conf);
 
		foreach ( $order_conf as $val)
		{
			if (in_array($val, array('','none')) )
				continue;

			$this->order_str($qp, $val, 'main');
		}

		$query = "SELECT {$qp['fields']} FROM `" . TBL_PROFILE . "` AS `main`
			{$qp['join']}
			WHERE {$qp['condition']} " . 
			(strlen(@$qp['group']) ? " GROUP BY {$qp['group']}" : "") .
			(strlen($qp['order']) ? " ORDER BY {$qp['order']}" : "") .
			" LIMIT {$qp['limit']}";

		$result = SK_MySQL::query($query);
		
		$list = array();
		while ($item = $result->fetch_object()) {
			$item->sex = SKM_Language::text('%profile_fields.value.sex_'.$item->sex);
			$item->location = app_Profile::getFieldValues( $item->profile_id, array( 'country', 'state', 'city', 'zip' ) );
			$item->age = app_Profile::getAge($item->birthdate);
			
			if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $item->profile_id)) {
				$item->activity_info['item'] = false;
			} else {
				$item->activity_info = app_Profile::ActivityInfo( $item->activity_stamp, $item->online );
				$item->activity_info['item_label'] = isset($item->activity_info['item']) ? SKM_Language::section('profile.labels')->text('activity_'.$item->activity_info['item']) : false;
			}
			
			$list[] = $item;
		}
		
		// count total
		$query = "SELECT COUNT(`main`.`profile_id`) FROM `" . TBL_PROFILE . "` AS `main`
			{$qp['join']}
			WHERE {$qp['condition']} ";
		
		return array('list' => $list, 'total' => SK_MySQL::query($query)->fetch_cell());
	}
	
	
	public function get_saved_list( $list_id, $page )
	{
		$criterion = app_SearchCriterion::getCriterionById($list_id);
		$list = app_ProfileSearch::generateResultList( $criterion['search_type'], $criterion );
		
		$list_id = app_TempProfileList::getListSessionInfo( 'search', 'list_id' );
		
		if (!$list_id) {
			return array();
		}
		$config = SK_Config::section('site')->Section('additional')->Section('profile_list');	
				
		$query = SK_MySQL::placeholder("SELECT `prof_extend`.*, `prof`.*, `tmp_list_link`.`result_number`, `prof_online`.`hash` AS `online` FROM `".TBL_LINK_PR_LIST_PR."` AS `tmp_list_link` 
			LEFT JOIN `".TBL_PROFILE."` AS `prof` USING( `profile_id` ) 
			LEFT JOIN `".TBL_PROFILE_EXTEND."` AS `prof_extend` USING( `profile_id` ) 
			LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `prof_online` USING( `profile_id` )
			WHERE `tmp_list_link`.`profile_list_id`=? 
			ORDER BY `tmp_list_link`.`result_number` 
			LIMIT ".($config->result_per_page * ( $page-1 )).", ". $config->result_per_page,
			$list_id);
			
		$result = SK_MySQL::query($query);
		
		$list = array();
		while ($item = $result->fetch_object()) {
			$item->sex = SKM_Language::text('%profile_fields.value.sex_'.$item->sex);
			$item->location = app_Profile::getFieldValues( $item->profile_id, array( 'country', 'state', 'city', 'zip' ) );
			$item->age = app_Profile::getAge($item->birthdate);
			
			if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $item->profile_id)) {
				$item->activity_info['item'] = false;
			} else {
				$item->activity_info = app_Profile::ActivityInfo( $item->activity_stamp, $item->online );
				$item->activity_info['item_label'] = isset($item->activity_info['item']) ? SKM_Language::section('profile.labels')->text('activity_'.$item->activity_info['item']) : false;
			}
			
			$list[] = $item;
		}
				
		return array('list' => $list, 'total' => app_TempProfileList::getListSessionInfo( 'search', 'pr_total' ));
	}
}