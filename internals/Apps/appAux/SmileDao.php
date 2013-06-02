<?php
require_once DIR_APPS.'appAux/Smile.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 23, 2008
 * 
 * Desc: DAO Class for smile table entries
 */

final class SmileDao extends SK_BaseDao 
{	
	/**
	 * Class constructor
	 *
	 */
	public function __construct (){}
	
	protected function getDtoClassName (){ return "Smile"; }
	protected function getTableName (){return '`'.TBL_SMILE.'`';}
}