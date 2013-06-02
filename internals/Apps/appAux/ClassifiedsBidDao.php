<?php
require_once DIR_APPS.'appAux/ClassifiedsBid.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 09, 2009
 * 
 * Desc: DAO Class for classifieds_bid table entries
 */

final class ClassifiedsBidDao extends SK_BaseDao 
{	
	/**
	 * Class constructor
	 *
	 */
	public function __construct (){}
	
	protected function getDtoClassName (){ return "ClassifiedsBid"; }
	protected function getTableName (){return '`'.TBL_CLASSIFIEDS_BID.'`';}
	
	public function getItemBidsCount ( $item_id )
	{		
		$query = SK_MySQL::placeholder( "SELECT COUNT(`bid`.`id`) FROM ".$this->getTableName()." AS `bid` 
			LEFT JOIN `".TBL_CLASSIFIEDS_COMMENT."` AS `comm` ON `bid`.`entity_id`=`comm`.`id`
			WHERE `comm`.`entity_id`=? GROUP BY `comm`.`entity_id`", $item_id );
		
		return SK_MySQL::query( $query )->fetch_cell();
	}
	
	public function getItemLowestBidInfo ( $item_id )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." AS `bid`
			LEFT JOIN `".TBL_CLASSIFIEDS_COMMENT."` AS `comm` ON `bid`.`entity_id`=`comm`.`id`
			WHERE `comm`.`entity_id`=? ORDER BY `bid`.`bid` LIMIT 1", $item_id );
		
		return SK_MySQL::query( $query )->fetch_assoc();
	}
	
	public function getItemHighestBidInfo ( $item_id )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." AS `bid`
			LEFT JOIN `".TBL_CLASSIFIEDS_COMMENT."` AS `comm` ON `bid`.`entity_id`=`comm`.`id`
			WHERE `comm`.`entity_id`=? ORDER BY `bid`.`bid` DESC LIMIT 1", $item_id );
		
		return SK_MySQL::query( $query )->fetch_assoc();
	}
	
	public function deleteEntityBid ( $entity_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ".$this->getTableName()." WHERE `entity_id`=?", $entity_id );

		SK_MySQL::query( $query );
	}

}