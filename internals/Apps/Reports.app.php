<?php

/**
 * Universal class for making user reports 
 * on profiles and other member's content 
 *
 * @package SkaDate
 * @link http://www.skadate.com
 * @version 7.0
 */
class app_Reports
{
/**
	 * Add user report on specified entity
	 *
	 * @param integer $reporter_id
	 * @param integer $entity_id
	 * @param string $type
	 * @param string $reason
	 *
	 * @return integer
	 *
	 */ 
	public static function addReport( $reporter_id, $entity_id, $type, $reason )
	{
		if ( !intval( $reporter_id ) || !intval( $entity_id ) || !( $type = trim( $type ) ) )
			return -2;
		
		$reason = trim( $reason );
		
		// check if profile already reported
		$query = SK_MySQL::placeholder( "SELECT `report_id` FROM `".TBL_REPORT."` 
			WHERE `reporter_id`=? AND `entity_id`=?", $reporter_id, $entity_id );
		
		$check_result = SK_MySQL::query( $query );
		
		if ( $check_result->num_rows() !== 0 )
			return -3;
			
		$query = SK_MySQL::placeholder( "INSERT INTO`".TBL_REPORT."` (`reporter_id`, `entity_id`, `type`, `reason`, `time_stamp`) 
				VALUES (?,?,'?','?', ?)", $reporter_id, $entity_id, $type, $reason, time() );
		SK_MySQL::query($query);
		
		if ( SK_MySQL::insert_id() )
			return 1;
			
		return -1;
	}
	
}