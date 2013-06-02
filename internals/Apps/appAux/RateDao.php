<?php
require_once DIR_APPS.'appAux/Rate.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 06, 2008
 * 
 * Desc: DAO Class for event table entries
 */

final class RateDao extends SK_BaseDao 
{	
	private $table_name;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct ( $table_name )
	{
		$this->table_name = '`'. $table_name. '`';
	}
	
	protected function getDtoClassName (){ return "Rate"; }
	protected function getTableName(){ return $this->table_name; }
	
	
	/**
	 * Returns Rate for entity_id and profile_id
	 *
	 * @param integer $entity_id
	 * @param integer $profile_id
	 * @return Rate
	 */
	public function getRateByEntityIdAndProfileId( $entity_id, $profile_id )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE `entity_id` = ? AND `profile_id` = ?", $entity_id, $profile_id );
		
		return SK_MySQL::query( $query )->mapObject( $this->getDtoClassName() );
	}
	
	
	/**
	 * Returns mapped array for entities with rates count and avg rate
	 *
	 * @param integer $first
	 * @param integer $count
	 * @return array
	 */
	public function findMostRatedEntityIds()
	{	
		$query = SK_MySQL::placeholder( "SELECT *, COUNT(*) as `items_count`, AVG(`score`) as `avg_score` 
			FROM ". $this->getTableName(). " GROUP BY `entity_id` ORDER BY `avg_score`");
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	
	/**
	 * Returns rate info for entity item
	 *
	 * @param integer $entity_id
	 * @return array
	 */
	public function findEntityItemRateInfo( $entity_id )
	{
		$query = SK_MySQL::placeholder( "SELECT *, COUNT(*) as `items_count`, AVG(`score`) as `avg_score` 
			FROM ". $this->getTableName(). " WHERE `entity_id` = ? GROUP BY `entity_id`", $entity_id );

		return SK_MySQL::query( $query )->mapObject( $this->getDtoClassName() );
	}
	
	
	/**
	 * Deletes all rate entries for entity
	 *
	 * @param integer $entity_id
	 */
	public function deleteEntityItemScores( $entity_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `entity_id` = ?", $entity_id );
		
		SK_MySQL::query( $query );
	}
	
	
	/**
	 * Returnes rate info for entityIds
	 *
	 * @param array $entityIds
	 * @return array
	 */
	public function findRatesForEntityIds( array $entityIds )
	{
		$query = SK_MySQL::placeholder( "SELECT *, COUNT(*) as `items_count`, AVG(`score`) as `avg_score` 
			FROM ". $this->getTableName(). " WHERE `entity_id` IN ( ?@ ) GROUP BY `entity_id`", $entityIds);
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	
	/**
	 * Deletes profile rates
	 *
	 * @param integer $profile_id
	 */
	public function deleteProfileRates( $profile_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `profile_id` = ?", $profile_id );
		
		SK_MySQL::query( $query );
	}
}