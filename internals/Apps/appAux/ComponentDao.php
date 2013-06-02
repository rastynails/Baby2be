<?php
require_once DIR_APPS.'appAux/Component.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 10, 2008
 * 
 * Desc: data access object Class for component Table
 */

final class ComponentDao extends SK_BaseDao
{
	private function __construct (){}
	
	protected function getDtoClassName (){return "Component";}
	
	protected function getTableName (){return '`'.TBL_PROFILE_COMPONENT.'`';}
	
	private static $classInstance;
	
	/**
	 * Returns the only object of ComponentDao class
	 *
	 * @return ComponentDao
	 */
	public static function newInstance ()
	{
		if ( self::$classInstance === null )
			self::$classInstance = new self;
			
		return self::$classInstance;
	}
	
	/* !!! AUXILARY PART DEVIDER !!! */
	
	
	/**
	 * Returns components not in profile view list
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public function findNotInViewListCMP( $profile_id )
	{
		$query = SK_MySQL::placeholder( "SELECT `c`.* FROM ". $this->getTableName(). " AS `c` 
			LEFT JOIN ( SELECT * FROM `".TBL_PROFILE_VIEW_COMPONENT."` WHERE `profile_id` = ? ) AS `pc` ON ( `c`.`id` = `pc`.`component_id` )
			LEFT JOIN `". TBL_FEATURE ."` AS `f` ON ( `c`.`feature_id` = `f`.`feature_id` )
			WHERE ( `f`.`active` = 'yes' OR `c`.`feature_id` = 0) AND ( `pc`.`component_id` IS NULL OR `c`.`multiple` = 1 ) GROUP BY `c`.`class_name` ORDER BY `c`.`sorder`", $profile_id );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
}