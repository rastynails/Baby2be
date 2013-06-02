<?php
require_once DIR_APPS.'appAux/CustomHtml.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 06, 2008
 * 
 * Desc: DAO Class for event table entries
 */

class CustomHtmlDao extends SK_BaseDao
{
	/**
	 * Class constructor
	 *
	 */	
	public function __construct (){}
	
	protected function getDtoClassName (){return "CustomHtml";}
	
	protected function getTableName (){return '`'.TBL_CUSTOM_HTML.'`';}
	
	public function findByCmpId( $cmp_id )
	{
		$query = SK_MySQL::placeholder("SELECT * FROM ".$this->getTableName()." WHERE `cmp_id`=?",$cmp_id);

		return SK_MySQL::query($query)->mapObject($this->getDtoClassName());
	}
	
}