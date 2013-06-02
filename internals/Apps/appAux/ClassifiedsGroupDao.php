<?php
require_once DIR_APPS.'appAux/ClassifiedsGroup.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 08, 2009
 * 
 * Desc: DAO Class for classifieds_group table entries
 */

final class ClassifiedsGroupDao extends SK_BaseDao 
{	
	/**
	 * Class constructor
	 *
	 */
	public function __construct (){}
	
	protected function getDtoClassName (){ return "ClassifiedsGroup"; }
	protected function getTableName (){return '`'.TBL_CLASSIFIEDS_GROUP.'`';}
	
	
	/**
	 * Returns all groups of this entity
	 *
	 * @param string $entity
	 * @return array
	 */
	public function findItemGroups ( $entity, $is_approved = 1 )
	{
		$query = SK_MySQL::placeholder( "SELECT `g`.*, (SELECT COUNT(*) FROM (SELECT * FROM `".TBL_CLASSIFIEDS_ITEM."` WHERE `is_approved`=1) AS `cc` WHERE `cc`.`group_id`=`g`.`id` ) AS `items_count`  
            FROM ".$this->getTableName()." AS `g` LEFT JOIN `".TBL_CLASSIFIEDS_ITEM."` AS `i` 
            ON( `g`.`id` = `i`.`group_id` AND `g`.`entity` = `i`.`entity` ) 
            WHERE `g`.`entity`='?' ", $entity);
        if ($is_approved)
            $query .= " AND (`i`.`is_approved`=$is_approved)";
            
		$query .= "	GROUP BY `g`.`id` ORDER BY `g`.`order`";

		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	/**
	 * Returns all groups
	 *
	 * @return array
	 */
	public function findAllGroups ()
	{
		return SK_MySQL::query( "SELECT * FROM ".$this->getTableName()." WHERE 1 ORDER BY `order`" )
			->mapObjectArray( $this->getDtoClassName() );
	}	
	
	/**
	 * Returns group's entity
	 *
	 * @param int $group_id
	 * @return string
	 */
	public function findGroupEntity ( $group_id )
	{
		$query = SK_MySQL::placeholder( "SELECT `entity` FROM ".$this->getTableName()." WHERE `id`=?", $group_id );
		
		return SK_MySQL::query( $query )->fetch_cell();
	}
	
}