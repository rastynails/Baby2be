<?php
require_once DIR_APPS.'appAux/EventProfile.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 23, 2008
 * 
 * Desc: DAO Class for event_profile table entries
 */

class EventProfileDao extends SK_BaseDao
{
	/**
	 * Class constructor
	 *
	 */	
	public function __construct (){}
	
	protected function getDtoClassName (){return "EventProfile";}
	
	protected function getTableName (){return '`'.TBL_EVENT_PROFILE.'`';}
	
	
	/**
	 * Deletes all entries for event
	 *
	 * @param integer $event_id
	 */
	public function deleteEventProfileByEventId( $event_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `event_id` = ?", $event_id );
		
		SK_MySQL::query( $query );
	}
	
	/**
	 * Deletes EventProfile entry by event_id and profiel_id
	 *
	 * @param integer $event_id
	 * @param integer $profile_id
	 */
	public function deleteEventProfileByEventIdAndProfileId( $event_id, $profile_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `event_id` = ? AND `profile_id` = ?", $event_id, $profile_id );
		
		SK_MySQL::query( $query );
	}
	
	/**
	 * Returns mapped arrray of EventProfile entries for event 
	 *
	 * @param integer $event_id
	 * @param integer $status
	 * 
	 * @return array
	 */
	public function findProfileIdsForEvent( $event_id, $status, $limit )
	{
		if( $limit )
		{
			$query = SK_MySQL::placeholder( "SELECT `e`.*, `p`.`username` FROM ". $this->getTableName(). " AS `e` 
				LEFT JOIN `". TBL_PROFILE ."` AS `p` ON ( `e`.`profile_id` = `p`.`profile_id` )
				WHERE `e`.`event_id` = ? AND `e`.`status` = ? ORDER BY `e`.`join_date` LIMIT ?", $event_id, $status, $limit );
		}
		else
		{ 
			$query = SK_MySQL::placeholder( "SELECT `e`.*, `p`.`username` FROM ". $this->getTableName(). " AS `e`
				LEFT JOIN `". TBL_PROFILE ."` AS `p` ON ( `e`.`profile_id` = `p`.`profile_id` )
				WHERE `e`.`event_id` = ? AND `e`.`status` = ? ORDER BY `e`.`join_date`", $event_id, $status );
		}
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	
	/**
	 * Returns count of EventProfile entries for event 
	 *
	 * @param integer $event_id
	 * @param integer $status
	 * @return integer
	 */
	public function findProfilesCountForEvent( $event_id, $status )
	{
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM ". $this->getTableName(). " WHERE `event_id` = ? AND `status` = ?", $event_id, $status );
		
		return (int)SK_MySQL::query( $query )->fetch_cell();
	}
	
	/**
	 * Returns entry by event and profile Ids
	 *
	 * @param integer $event_id
	 * @param integer $profile_id
	 * @return EventProfile
	 */
	public function findEventProfileByEventIdAndProfileId( $event_id, $profile_id )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE `event_id` = ? AND `profile_id` = ?", $event_id, $profile_id );
		
		return SK_MySQL::query( $query )->mapObject( $this->getDtoClassName() );
	}
	
	
	/**
	 * Deletes event profile entries
	 *
	 * @param integer $event_id
	 */
	public function deleteEventEntries( $event_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `event_id` = ?", $event_id );

		SK_MySQL::query( $query );
	}

    /**
	 * Deletes all entries for event
	 *
	 * @param integer $event_id
	 */
	public function deleteByProfileId( $profile_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `profile_id` = ?", $profile_id );
		SK_MySQL::query( $query );
	}
	
}