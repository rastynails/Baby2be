<?php
require_once DIR_APPS.'appAux/ClassifiedsFile.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 09, 2009
 * 
 * Desc: DAO Class for classifieds_file table entries
 */

final class ClassifiedsFileDao extends SK_BaseDao 
{
    private $cache = array();
	/**
	 * Class constructor
	 *
	 */
	public function __construct (){}
	
	protected function getDtoClassName (){ return "ClassifiedsFile"; }
	protected function getTableName (){return '`'.TBL_CLASSIFIEDS_FILE.'`';}
	
	
	/**
	 * Returns item files
	 *
	 * @param int $entity_id
	 * @return array
	 */
	public function findByEntityId ( $entity_id )
	{
        if ( isset($this->cache[$entity_id]) )
        {
            return $this->cache[$entity_id];
        }

		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." WHERE `entity_id`=?", $entity_id);
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}

    public function findByEntityIdList ( $entity_id_list )
	{
        $objectList = array();
        
        if ( empty($entity_id_list) )
        {
            foreach( $entity_id_list as $id )
            {
                $this->cache[$id] = empty($objectListempty) || ($objectList[$value->getEntity_id()])? array() : $objectList[$value->getEntity_id()];
            }

            return;
        }

		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." WHERE `entity_id` IN ( ?@ )", $entity_id_list);

		$list =  SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );

        

        foreach( $list as $value )
        {
            $objectList[$value->getEntity_id()][] = $value;
        }

        foreach( $entity_id_list as $id )
        {
            $this->cache[$id] = empty($objectListempty) || ($objectList[$value->getEntity_id()])? array() : $objectList[$value->getEntity_id()];
        }

        return $objectList;
	}

}