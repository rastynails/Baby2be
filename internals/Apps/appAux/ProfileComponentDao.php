<?php
require_once DIR_APPS.'appAux/ProfileComponent.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 10, 2008
 * 
 * Desc: data access object Class for profile_view_component Table
 */

final class ProfileComponentDao extends SK_BaseDao
{
	private function __construct (){}
	
	protected function getDtoClassName (){return "ProfileComponent";}
	
	protected function getTableName (){return '`'.TBL_PROFILE_VIEW_COMPONENT.'`';}
	
	private static $classInstance;
	
	/**
	 * Returns the only object of TagDao class
	 *
	 * @return TagDao
	 */
	public static function newInstance ()
	{
		if ( self::$classInstance === null )
			self::$classInstance = new self;
			
		return self::$classInstance;
	}
	
	/* !!! AUXILARY PART DEVIDER !!! */
	
	
	/**
	 * Returns profile view CMPs
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public function findProfileViewCMP( $profile_id )
	{
		$query = SK_MySQL::placeholder( "SELECT `pc`.*, `c`.`class_name`, `c`.`id` as `cid` FROM ". $this->getTableName(). " AS `pc`
			LEFT JOIN `". TBL_PROFILE_COMPONENT. "` AS `c` ON( `c`.`id` = `pc`.`component_id` )
			LEFT JOIN `".TBL_FEATURE."` AS `f` ON( `f`.`feature_id` = `c`.`feature_id` )
			WHERE `pc`.`profile_id` = ? AND `f`.`active` = 'yes' ORDER BY `pc`.`position`", $profile_id );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	public function incrementPosition( $profile_id, $array )
	{
		$query = SK_MySQL::placeholder( "UPDATE ". $this->getTableName(). " SET `position` = ( `position` + 1 ) 
			WHERE `profile_id` = ? AND `id` IN ( ?@ )", $profile_id, $array );
		
		SK_MySQL::query( $query );
	}
	
	public function decrementPosition( $profile_id, $array )
	{
		$query = SK_MySQL::placeholder( "UPDATE ". $this->getTableName(). " SET `position` = ( `position` - 1 ) 
			WHERE `profile_id` = ? AND `id` IN ( ?@ )", $profile_id, $array );
		
		SK_MySQL::query( $query );
	}
	
	public function findSectionCmps( $profile_id, $section )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE `section` = ? AND `profile_id` = ?", $section, $profile_id );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	public function findProfileCMPByProfileIdAndCMPId( $profile_id, $cmp_id )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE `profile_id` = ? AND `component_id` = ?", $profile_id, $cmp_id );
		
		return SK_MySQL::query( $query )->mapObject( $this->getDtoClassName() );
	}
	
	public function deleteProfileEntries( $profile_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `profile_id` = ?", $profile_id );
		
		SK_MySQL::query( $query );
	}
	
//	public function findCmpsToUpdate( $profile_id, $section, $position_start, $position_end )
//	{
//		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " 
//			WHERE `profile_id` = ? AND `section` = ? AND `position` > ? AND `position` < ?", $profile_id, $section, $position_start, $position_end );
//		
//		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
//	}
//	
//	public function findCmpsForAnotherSection( $profile_id, $section, $position, $id )
//	{
//		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " 
//			WHERE `profile_id` = ? AND `section` = ? AND `position` >= ? AND `id` != ?", $profile_id, $section, $position, $id );
//
//		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
//	}
//	
//	public function findCmpsForOldSection( $profile_id, $section, $position )
//	{
//		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " 
//			WHERE `profile_id` = ? AND `section` = ? AND `position` > ?", $profile_id, $section, $position );
//		
//		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
//	}
}