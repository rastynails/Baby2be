<?php
require_once DIR_APPS.'appAux/TagEntity.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 06, 2008
 * 
 * Desc: DAO Class for event_entity table entries
 */

final class TagEntityDao extends SK_BaseDao 
{	
	private $table_name;
	
	private $feature;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct ( $table_name, $feature )
	{
		$this->table_name = '`'. $table_name. '`';
		$this->feature = $feature;
	}
	
	protected function getDtoClassName (){ return "TagEntity"; }
	protected function getTableName(){ return $this->table_name; }
	
	
	/**
	 * Returns entity tags with label
	 *
	 * @param integer $entity_id
	 * @return array
	 */
	public function findEntityTags( $entity_id )
	{
		$query = SK_MySQL::placeholder( "SELECT `te`.*, `t`.`label` FROM ". $this->getTableName(). " AS `te`". 
			"LEFT JOIN `". TBL_TAG. "` AS `t` ON ( `te`.`tag_id` = `t`.`id` ) WHERE `te`.`entity_id` = ? ORDER BY `t`.`label`", $entity_id );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	
	/**
	 * Deletes all entries for entity item
	 *
	 * @param integer $entity_id
	 */
	public function deleteTagEntriesForEntity( $entity_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `entity_id` = ?", $entity_id );
		
		SK_MySQL::query( $query );
	}
	
	
	/**
	 * Deletes entry by tag_id and entity_id
	 *
	 * @param integer $entity_id
	 * @param integer $tag_id
	 */
	public function deleteTagEntryByEntityIdAndTagId( $entity_id, $tag_id )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `entity_id` = ? AND `tag_id` = ?", $entity_id, $tag_id );
		
		SK_MySQL::query( $query ); 
	}
	
	
	/**
	 * Returns mapped array
	 *
	 * @param integer $tag_id
	 * @return array
	 */
	public function findEntriesByTagId( $tag_id )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ". $this->getTableName(). " WHERE `tag_id` = ?", $tag_id );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	
	/**
	 * Returns most popular tags with count
	 *
	 * @param integer $limit
	 * @return array
	 */
	public function findMostPopularTags( $limit )
	{
		if( $this->feature == 'blog' || $this->feature == 'news' )
		{
			$query = SK_MySQL::placeholder( " SELECT `t`.* FROM ( SELECT `te`.*, COUNT(*) AS `items_count`, `t`.`label` AS `tag_label` FROM ". $this->getTableName(). " AS `te` 
				LEFT JOIN `". TBL_TAG ."` AS `t` ON ( `te`.`tag_id` = `t`.`id` )
				LEFT JOIN `". TBL_BLOG_POST."` AS `b` ON ( `te`.`entity_id` = `b`.`id` ) WHERE `b`.`is_news` = ?
				GROUP BY `tag_id` ORDER BY `items_count` DESC LIMIT ? ) AS `t` ORDER BY `t`.`tag_label`", ( $this->feature == 'news' ? 1 : 0 ), $limit );
		}
		else
		{
			$query = SK_MySQL::placeholder( " SELECT `t`.* FROM ( SELECT `te`.*, COUNT(*) AS `items_count`, `t`.`label` AS `tag_label` FROM ". $this->getTableName(). " AS `te` 
				LEFT JOIN `". TBL_TAG ."` AS `t` ON ( `te`.`tag_id` = `t`.`id` )
				GROUP BY `tag_id` ORDER BY `items_count` DESC LIMIT ? ) AS `t` ORDER BY `t`.`tag_label`", $limit );
		}
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
}