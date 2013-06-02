<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 * 
 * Desc: Base class for Data Access Objects
 */

abstract class SK_BaseDao
{	
	protected abstract function getTableName();
	protected abstract function getDtoClassName();

    private static $postCache = array();

	/**
	 * Finds and returns mapped entity item
	 *
	 * @param integer $id
	 * @return BlogPost
	 */
	public function findById( $id )
	{
        $id = (int)$id;

        if( $id < 1 )
        {
            return null;
        }

        if( !array_key_exists($id, self::$postCache) )
        {
            self::$postCache[$id] = SK_MySQL::query(SK_MySQL::placeholder("SELECT * FROM ". $this->getTableName(). " WHERE `id` = ?", $id))->mapObject($this->getDtoClassName());
        }

        return self::$postCache[$id];
	}
	
	
	/**
	 * Finds and returns mapped entity list
	 *
	 * @param array $idList
	 * @return array
	 */
	public function findGroupById( array $id_list )
	{
		if(empty($id_list))
        {
			return array();
        }

        $id_list = array_unique($id_list);

		$query = SK_MySQL::placeholder("SELECT * FROM ". $this->getTableName(). " WHERE `id` IN (?@)", $id_list);
		$result = SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());

        foreach ( $result as $item )
        {
            self::$postCache[$item->getId()] = $item;
        }

        $returnArray = array();

        foreach ( $id_list as $id )
        {
            if( isset(self::$postCache[$id]) )
            {
                $returnArray[$id] = self::$postCache[$id];
            }
            else
            {
                self::$postCache[$id] = $returnArray[$id] = null;
            }
        }

        return $returnArray;
	}
	
	
	/**
	 * Returns all mapped entries of table
	 *
	 * @return array
	 */
	public function findAll()
	{
		$query = "SELECT * FROM ". $this->getTableName();
		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());	
	}
	
	
	/**
	 * Deletes entity item by id
	 *
	 * @param integer $id
	 */
	public function deleteById( $id )
	{
		SK_MySQL::query(SK_MySQL::placeholder("DELETE FROM ". $this->getTableName(). " WHERE `id` = ?", $id));	
	}
	
	
	/**
	 * Deletes list of entities by id list
	 *
	 * @param array $idList
	 */
	public function deleteGroupById( array $id_list )
	{
		if(empty($id_list))
			return;
			
		SK_MySQL::query(SK_MySQL::placeholder("DELETE FROM ". $this->getTableName(). " WHERE `id` IN (?@)", $id_list));
	}
	
	
	/**
	 * Saves and updates Entity item
	 *
	 * @param Entity $entity
	 */
	public function saveOrUpdate($entity)
	{
		$r_class = new ReflectionClass($this->getDtoClassName());
		
		$prop_array = $r_class->getDefaultProperties();
		
		unset($prop_array['id']);
				
		if( $entity->getId() === null )
		{
			$field_string = '';
			$value_string = '';
			
			foreach ( $prop_array as $key => $value )
			{
				$r_getter_method = $r_class->getMethod("get".ucfirst($key));
						
				if( $r_getter_method === false || $r_getter_method === null )
					continue;
					
				$getter_value = $r_getter_method->invoke($entity);

                $field_string .= '`'.$key.'`,';
				
				$value_string .= $getter_value === null ? "null, " : ( is_int( $getter_value ) ? 
					SK_MySQL::realEscapeString( $getter_value ). "," : 
					"'". SK_MySQL::realEscapeString( $getter_value ) ."'," ) ; 
			}
			
			$query = "INSERT INTO ". $this->getTableName(). " (".substr( $field_string, 0, -1 ).") VALUES (".substr( trim($value_string), 0, -1 ). ")";

			SK_MySQL::query($query); 
			
			$entity->setId(SK_MySQL::insert_id());
		}
		else 
		{
			$field_value_pairs = '';

			foreach ( $prop_array as $key => $value )
			{
				$r_getter_method = $r_class->getMethod("get". ucfirst($key));
						
				if( $r_getter_method === false || $r_getter_method === null )
					continue;
				
				$getter_value = $r_getter_method->invoke($entity);
					
				$field_value_pairs .= "`". $key. "`=". ( $getter_value === null ? "null" : ( is_int( $getter_value ) ? 
					SK_MySQL::realEscapeString( $getter_value ) : 
					"'".SK_MySQL::realEscapeString( $getter_value ). "'" )). ",";	
			}
			
			$query = "UPDATE ". $this->getTableName(). " SET ". substr( $field_value_pairs, 0, -1 ). " WHERE `id` = ". SK_MySQL::realEscapeString( $entity->getId() );
			
			SK_MySQL::query( $query );
		}
	}
	
	/**
	 * Convertes array to in clause param
	 *
	 * @param array $ids
	 * @return string
	 */
	protected function mergeInClause( array $ids )
	{
		$return_string = '';
		
		foreach ( $ids as $value )
		{
			$return_string .= $value.',';
		}
		
		return substr( $return_string, 0, -1 );
	}
}
