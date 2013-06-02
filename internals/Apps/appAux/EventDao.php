<?php
require_once DIR_APPS.'appAux/Event.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 06, 2008
 *
 * Desc: DAO Class for event table entries
 */

class EventDao extends SK_BaseDao
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct (){}

	protected function getDtoClassName (){return "Event";}

	protected function getTableName (){return '`'.TBL_EVENT.'`';}


	/**
	 * Returns mapped Event objects with profile and location info for period of time
	 *
	 * @param integer $start_ts
	 * @param integer $end_ts
	 * @param integer $first
	 * @param integer $count
	 * @return array
	 */
	public function findPeriodEvents( $start_ts, $end_ts, $first, $count, $is_speed_dating = 0 )
	{
		$query = SK_MySQL::placeholder( "SELECT `e`.*, `ep`.`items_count`, `p`.`username`,
			`lco`.`Country_str_name` AS `country_label`, `ls`.`Admin1_str_name` AS `state_label`, `lci`.`Feature_str_name` AS `city_label`
			FROM ".$this->getTableName(). " AS `e`
			LEFT JOIN ( SELECT `event_id`, COUNT(*) AS `items_count` FROM `". TBL_EVENT_PROFILE. "` WHERE `status` = 1 GROUP BY `event_id` ) AS `ep` ON ( `e`.`id` = `ep`.`event_id` )
			LEFT JOIN `". TBL_PROFILE."` AS `p` ON ( `e`.`profile_id` = `p`.`profile_id` )
			LEFT JOIN `". TBL_LOCATION_COUNTRY ."` AS `lco` ON ( `e`.`country_id` = `lco`.`Country_str_code` )
			LEFT JOIN `". TBL_LOCATION_STATE ."` AS `ls` ON ( `e`.`state_id` = `ls`.`Admin1_str_code` )
			LEFT JOIN `". TBL_LOCATION_CITY ."` AS `lci` ON ( `e`.`city_id` = `lci`.`Feature_int_id` )
			WHERE `e`.`is_speed_dating`=? AND `e`.`admin_status` = 1 AND ( ( `e`.`start_date` BETWEEN ? AND ? OR `e`.`end_date` BETWEEN ? AND ? ) OR ( `e`.`start_date` < ? AND `e`.`end_date` > ? ) ) ORDER BY `e`.`start_date` LIMIT ?, ?", $is_speed_dating, $start_ts, $end_ts, $start_ts, $end_ts, $start_ts, $end_ts, $first, $count );

		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}

    public function findUserPendingApprovalEvents( $userId )
	{
		$query = SK_MySQL::placeholder( "SELECT `e`.*, `ep`.`items_count`, `p`.`username`,
			`lco`.`Country_str_name` AS `country_label`, `ls`.`Admin1_str_name` AS `state_label`, `lci`.`Feature_str_name` AS `city_label`
			FROM ".$this->getTableName(). " AS `e`
			LEFT JOIN ( SELECT `event_id`, COUNT(*) AS `items_count` FROM `". TBL_EVENT_PROFILE. "` WHERE `status` = 1 GROUP BY `event_id` ) AS `ep` ON ( `e`.`id` = `ep`.`event_id` )
			LEFT JOIN `". TBL_PROFILE."` AS `p` ON ( `e`.`profile_id` = `p`.`profile_id` )
			LEFT JOIN `". TBL_LOCATION_COUNTRY ."` AS `lco` ON ( `e`.`country_id` = `lco`.`Country_str_code` )
			LEFT JOIN `". TBL_LOCATION_STATE ."` AS `ls` ON ( `e`.`state_id` = `ls`.`Admin1_str_code` )
			LEFT JOIN `". TBL_LOCATION_CITY ."` AS `lci` ON ( `e`.`city_id` = `lci`.`Feature_int_id` )
			WHERE `e`.`is_speed_dating`= 0 AND `e`.`admin_status` = 0 AND `e`.`profile_id` = ? ORDER BY `e`.`start_date` LIMIT 20", $userId );

		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}


	/**
	 * Returns Event start date ts for month
	 *
	 * @param integer $year
	 * @param integer $month
	 * @return array
	 */
	public function findMonthEventsStartDates( $year, $month )
	{
            $start_date = mktime(0, 0, 0, $month, 1, $year);
            $end_date = mktime(23, 59, 59, $month, date('t', mktime(0, 0, 0, $month, 1, $year)), $year);
            $query = SK_MySQL::placeholder( "SELECT * FROM " .$this->getTableName(). " WHERE `admin_status` = 1 AND (`start_date` BETWEEN ? AND ? OR `end_date` BETWEEN ? AND ?)",
                    $start_date, $end_date, $start_date, $end_date);				
            return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}


	/**
	 * Enter description here...
	 *
	 * @param integer $event_id
	 * @return array
	 */
	public function findEventFullInfo( $event_id )
	{
		$query = SK_MySQL::placeholder( "SELECT `e`.*, `ep`.`items_count`, `p`.`username`,
			`lco`.`Country_str_name` AS `country_label`, `ls`.`Admin1_str_name` AS `state_label`, `lci`.`Feature_str_name` AS `city_label`
			FROM ".$this->getTableName(). " AS `e`
			LEFT JOIN ( SELECT `event_id`, COUNT(*) AS `items_count` FROM `". TBL_EVENT_PROFILE. "` WHERE `status` = 1 GROUP BY `event_id` ) AS `ep` ON ( `e`.`id` = `ep`.`event_id` )
			LEFT JOIN `". TBL_PROFILE."` AS `p` ON ( `e`.`profile_id` = `p`.`profile_id` )
			LEFT JOIN `". TBL_LOCATION_COUNTRY ."` AS `lco` ON ( `e`.`country_id` = `lco`.`Country_str_code` )
			LEFT JOIN `". TBL_LOCATION_STATE ."` AS `ls` ON ( `e`.`state_id` = `ls`.`Admin1_str_code` )
			LEFT JOIN `". TBL_LOCATION_CITY ."` AS `lci` ON ( `e`.`city_id` = `lci`.`Feature_int_id` )
			WHERE `e`.`id` = ?", $event_id );

		return SK_MySQL::query( $query )->mapObject( $this->getDtoClassName() );
	}


	/**
	 * Returns event list profile profile joined
	 *
	 * @param integer $profile_id
	 * @param integer $count
	 * @return array
	 */
	public function findProfileEvents( $profile_id, $count )
	{
		$query = SK_MySQL::placeholder( "SELECT `e`.* FROM ". $this->getTableName(). " as `e`
			INNER JOIN `". TBL_EVENT_PROFILE. "` AS `ep` ON ( `e`.`id` = `ep`.`event_id` )
						WHERE (  `e`.`admin_status` = 1 AND `ep`.`profile_id` = ?  ) AND `ep`.`status` = 1
            GROUP BY `e`.`id`
            ORDER BY `e`.`create_date` DESC LIMIT ?", $profile_id, $profile_id,  $count );

		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}


	/**
	 * Returns events count profile joined
	 *
	 * @param integer $profile_id
	 * @return integer
	 */
	public function findProfileEventsCount( $profile_id )
	{
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) as `items_count` FROM ". $this->getTableName(). " as `e`
			LEFT JOIN `". TBL_EVENT_PROFILE. "` AS `ep` ON ( `e`.`id` = `ep`.`event_id` )
			WHERE `e`.`admin_status` = 1 AND `ep`.`profile_id` = ? ", $profile_id );

		return (int)SK_MySQL::query( $query )->fetch_cell();
	}

	public function findEventsToModerate( $first, $count )
	{
		$query = SK_MySQL::placeholder( "SELECT `e`.*, `p`.`username` FROM ". $this->getTableName(). " as `e`
			LEFT JOIN `". TBL_PROFILE. "` AS `p` ON ( `e`.`profile_id` = `p`.`profile_id` )
			WHERE `e`.`admin_status` = 0 ORDER BY `e`.`start_date` DESC LIMIT ?, ?", $first, $count);

		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}

	public function findEventsToModerateCount()
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(*) as `items_count` FROM ". $this->getTableName(). " WHERE `admin_status` = 0");

		return (int)SK_MySQL::query( $query )->fetch_cell();
	}

	public function findAllActiveEvents()
	{
		$query = SK_MySQL::placeholder( "SELECT `e`.*, `ep`.`items_count`, `p`.`username`,
			`lco`.`Country_str_name` AS `country_label`, `ls`.`Admin1_str_name` AS `state_label`, `lci`.`Feature_str_name` AS `city_label`
			FROM ".$this->getTableName(). " AS `e`
			LEFT JOIN ( SELECT `event_id`, COUNT(*) AS `items_count` FROM `". TBL_EVENT_PROFILE. "` WHERE `status` = 1 GROUP BY `event_id` ) AS `ep` ON ( `e`.`id` = `ep`.`event_id` )
			LEFT JOIN `". TBL_PROFILE."` AS `p` ON ( `e`.`profile_id` = `p`.`profile_id` )
			LEFT JOIN `". TBL_LOCATION_COUNTRY ."` AS `lco` ON ( `e`.`country_id` = `lco`.`Country_str_code` )
			LEFT JOIN `". TBL_LOCATION_STATE ."` AS `ls` ON ( `e`.`state_id` = `ls`.`Admin1_str_code` )
			LEFT JOIN `". TBL_LOCATION_CITY ."` AS `lci` ON ( `e`.`city_id` = `lci`.`Feature_int_id` )
			WHERE `e`.`admin_status` = 1 ORDER BY `e`.`start_date`" );

		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}

	public function findSpeedDatingEventForProfile( $current_timestamp, $profile_id )
	{

		$query = SK_MySQL::placeholder( "SELECT `ep`.`event_id`, `e`.`title`, `e`.`description`, `e`.`image`, `e`.`start_date`, `e`.`end_date` FROM `". TBL_EVENT ."` as `e`
										LEFT JOIN `". TBL_EVENT_PROFILE ."` as `ep` ON (`e`.`id` = `ep`.`event_id`)
										WHERE `ep`.`status`=1 AND `is_speed_dating` = 1 AND ? BETWEEN `start_date` AND `end_date` AND `ep`.`profile_id`=?",
										$current_timestamp , $profile_id);
		//printArr($query);
		//return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
		//return SK_MySQL::query( $query )->fetch_array();
		return MySQL::fetchRow( $query );
	}

	public function findSpeedDatingEventInvitation( $event_id, $profile_id, $drawn_opponents )
	{
		$query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_EVENT_SPEED_DATING_PROFILE."`
			WHERE `event_id`=? AND `opponent_profile_id`=? ".
			( !empty($drawn_opponents) ?  "AND `profile_id` NOT IN(".implode(', ', array_keys($drawn_opponents) ).") " : "" ).
			" ", $event_id, $profile_id);

		return MySQL::fetchField( $query );
	}



	public function findSpeedDatingEventProfileMatches( $event_id, $profile_id, array  $drawn_opponents, $search_by_location )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return array();

		// detect matching fields
		$_query = "SELECT `match`.`match_type`, `field`.`name` AS `field_name`, `match_field`.`name` AS `match_field_name`
			FROM `".TBL_PROF_FIELD_MATCH_LINK."` AS `match`
			LEFT JOIN `".TBL_PROF_FIELD."` AS `field` USING ( `profile_field_id` )
			LEFT JOIN `".TBL_PROF_FIELD."` AS `match_field` ON `match`.`match_profile_field_id`=`match_field`.`profile_field_id`";

		$_fields_arr = MySQL::fetchArray( $_query );

		$_profile_info = app_Profile::getFieldValues( $profile_id, array( 'country_id' ) );

		if ( !strlen( $_profile_info['country_id'] ) )
			return array();

//-----
		$_query_parts['projection'] = "`profile`.`profile_id` ";
		$_query_parts['left_join'] = "
			LEFT JOIN `".TBL_PROFILE_EXTEND."` AS `extend` USING( `profile_id` )
			LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `online` USING( `profile_id` )
			LEFT JOIN `". TBL_EVENT_PROFILE ."` AS `event` ON ( `profile`.`profile_id` = `event`.`profile_id` )
			LEFT JOIN `". TBL_EVENT_SPEED_DATING ."` AS `speed_dating` ON ( `profile`.`profile_id` = `speed_dating`.`profile_id` )
			";

		$_by_country_sql_cond = (!empty($search_by_location)) ? sql_placeholder('`country_id` = ? AND ', $_profile_info['country_id']) : "";

		$_main_query = sql_placeholder( "SELECT {$_query_parts['projection']} FROM `".TBL_PROFILE."` AS `profile`
			{$_query_parts['left_join']}
			WHERE $_by_country_sql_cond `profile`.`profile_id` NOT IN ( SELECT `blocked_id` FROM `".TBL_PROFILE_BLOCK_LIST."`
																				WHERE `profile_id` =? ) ", $profile_id )."
				AND `event`.`event_id` = $event_id
                AND `speed_dating`.`event_id` = $event_id
				AND `event`.`profile_id` <> $profile_id
				AND `event`.`status` = 1
				AND `online`.`profile_id` = `event`.`profile_id`
				AND `speed_dating`.`is_free` = 1
                AND (SELECT `is_free` FROM `". TBL_EVENT_SPEED_DATING ."` WHERE `event_id`=$event_id AND `profile_id`=$profile_id )
                ".(count($drawn_opponents) ? ' AND `event`.`profile_id` NOT IN('.implode(', ', array_keys($drawn_opponents) ).')' : '');

		$_query_cond='';

		foreach ( $_fields_arr as $_key => $_match_info )
		{
			$_profile_info = app_Profile::getFieldValues( $profile_id, array( $_match_info['field_name'], $_match_info['match_field_name'] ) );


			switch ( $_match_info['match_type'] )
			{
				case 'exact':
					$_field_value = ( $_profile_info[$_match_info['field_name']] ) ? $_profile_info[$_match_info['field_name']] : MAX_SQL_BIGINT;
					$_match_field_value = ( $_profile_info[$_match_info['match_field_name']] ) ? $_profile_info[$_match_info['match_field_name']] : MAX_SQL_BIGINT;

					$_query_cond .= "AND ( $_field_value&IF( ISNULL(`".$_match_info['match_field_name']."`) OR `".$_match_info['match_field_name']."`=0, ".MAX_SQL_BIGINT.", `".$_match_info['match_field_name']."` )
						AND $_match_field_value&IF( ISNULL(`".$_match_info['field_name']."`) OR `".$_match_info['field_name']."`=0, ".MAX_SQL_BIGINT.", `".$_match_info['field_name']."` ) ) ";
					break;

				case 'range':

					if ( !$_profile_info[$_match_info['match_field_name']] || !$_profile_info[$_match_info['field_name']] )
						continue 2;

					$_age_info = explode( '-', $_profile_info[$_match_info['match_field_name']] );

					$_query_cond .= sql_placeholder( "AND YEAR('".date("Y-m-d H:i:s")."')-YEAR(`{$_match_info['field_name']}`)- IF( DAYOFYEAR(`{$_match_info['field_name']}`) > DAYOFYEAR('".date("Y-m-d H:i:s")."'),1,0) >= ?
									AND YEAR('".date("Y-m-d H:i:s")."')-YEAR(`{$_match_info['field_name']}`)-IF( DAYOFYEAR(`{$_match_info['field_name']}`) > DAYOFYEAR('".date("Y-m-d H:i:s")."'),1,0) <=?"
								, $_age_info[0], $_age_info[1] );


					$_query_cond .= sql_placeholder(
						" AND SUBSTRING_INDEX( `{$_match_info['match_field_name']}`, '-', 1 ) <= ?profile_age
					     AND SUBSTRING_INDEX( `{$_match_info['match_field_name']}`, '-', -1 ) >= ?profile_age ",
							array( 'profile_age' => app_Profile::getAge( $_profile_info[$_match_info['field_name']] ) ) );

					break;
			}
		}


		$_main_query .= sql_placeholder( $_query_cond." AND ".app_Profile::SqlActiveString( 'profile' )." AND `profile`.`profile_id`!= ? LIMIT 1 ", $profile_id );

		return MySQL::fetchField( $_main_query );
	}

    public function speedDatingSearchByLocation( $event_id )
    {
        $query = SK_MySQL::placeholder( "SELECT `search_by_location` FROM `".TBL_EVENT."` WHERE `id`=? AND `is_speed_dating`=1 ", $event_id);
        $result = MySQL::fetchField($query);

        if (!empty($result))
            return true;

        return false;
    }

	public function addDatingSession( $event_id, $profile_id )
	{
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_EVENT_SPEED_DATING."` (`event_id`, `profile_id`, `is_free` )
										 VALUES (?, ?, 1) ", $event_id, $profile_id);
		SK_MySQL::query( $query );
	}

	public function truncateSpeedDatingEvent( $profile_id = null )
	{
        $condition = !empty($profile_id) ? SK_MySQL::placeholder(' WHERE `profile_id`=? ', $profile_id) : '';

        $query = "DELETE FROM `".TBL_EVENT_SPEED_DATING."` ".$condition;
        SK_MySQL::query( $query );

        $query = "DELETE FROM `".TBL_EVENT_SPEED_DATING_PROFILE."` ".$condition;
        SK_MySQL::query( $query );

	}

	public function updateDatingSession($event_id, $profile_id, $is_free=1 )
	{
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_EVENT_SPEED_DATING."` SET `is_free`=?
			WHERE `event_id`=? AND `profile_id`=? ", $is_free, $event_id, $profile_id);

		return MySQL::affectedRows( $query );
	}

	public function addDatingSessionOpponent($event_id, $profile_id, $opponent_id, $start_time, $end_time )
	{
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_EVENT_SPEED_DATING_PROFILE."` (`event_id`, `profile_id`, `opponent_profile_id`, `start_time`, `end_time` )
										 VALUES (?, ?, ?, ?, ?) ", $event_id, $profile_id, $opponent_id, $start_time, $end_time );
		return MySQL::affectedRows( $query );
	}

	public function getEventSpeedDatingSessionEndTime( $event_id, $profile_id, $opponent_id )
	{
		$query = SK_MySQL::placeholder( "SELECT `end_time` FROM `".TBL_EVENT_SPEED_DATING_PROFILE."` WHERE `event_id`=? AND `profile_id`=? AND `opponent_profile_id`=? ", $event_id, $profile_id, $opponent_id );
		return MySQL::fetchField( $query );
	}

	public function stopEventSpeedDatingSession( $event_id, $profile_id, $opponent_id )
	{
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_EVENT_SPEED_DATING_PROFILE."` SET `end_time`=? WHERE `event_id`=? AND `profile_id`=? AND `opponent_profile_id`=? ", time(), $event_id, $profile_id, $opponent_id );
		return MySQL::affectedRows( $query );
	}

//	/**
//	 * Deletes all profile events
//	 *
//	 * @param integer $profile_id
//	 */
//	public function deleteProfileEvents( $profile_id )
//	{
//		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `profile_id` = ?", $profile_id );
//
//		SK_MySQL::query( $query );
//	}
}