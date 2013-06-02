<?php
require_once DIR_APPS.'appAux/Badword.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 06, 2008
 *
 * Desc: DAO Class for event table entries
 */

final class BadwordDao extends SK_BaseDao
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct (){}

	protected function getDtoClassName (){ return "Badword"; }
	protected function getTableName (){return '`'.TBL_BADWORD.'`';}


	/**
	 * Returns paged badword list
	 *
	 * @param integer $first
	 * @param integer $count
	 * @return array
	 */
	public function findBadwordList( $first, $count, $type )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE type = '?' ORDER BY `label` LIMIT ?,?", $type, $first, $count );

		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}


	/**
	 * Returns badword dto
	 *
	 * @param string $label
	 * @return Badword||null
	 */
	public function findBadwordByLabel( $label, $type )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE type = '?' AND `label` = '?'", $type, $label );

		return SK_MySQL::query( $query )->mapObject( $this->getDtoClassName() );
	}


	/**
	 * Returns badwords count
	 *
	 * @return integer
	 */
	public function getBadwordsCount($type)
	{
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM ". $this->getTableName() . " WHERE type = '?' ", $type);

		return (int)SK_MySQL::query( $query )->fetch_cell();
	}
}