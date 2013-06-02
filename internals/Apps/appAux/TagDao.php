<?php
require_once DIR_APPS.'appAux/Tag.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 * 
 * Desc: data access object Class for tag Table
 */

final class TagDao extends SK_BaseDao
{
	private function __construct (){}
	
	protected function getDtoClassName (){return "Tag";}
	
	protected function getTableName (){return '`'.TBL_TAG.'`';}
	
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
	 * Returns mapped tag array
	 *
	 * @param array $tags_array
	 * @return array
	 */
	public function findTagsByLabel( array $tags_array )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE `label` IN ('?@')", $tags_array );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	
	/**
	 * Returns Tag item by label
	 *
	 * @param string $label
	 * @return Tag
	 */
	public function findTagByLabel( $label )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE `label` = '?'", $label );
		
		return SK_MySQL::query( $query )->mapObject( $this->getDtoClassName() );
	}
}