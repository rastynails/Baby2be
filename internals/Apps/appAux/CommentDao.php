<?php
require_once DIR_APPS.'appAux/Comment.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 * 
 * Desc: Abstract Dao for all comment tables
 */

final class CommentDao extends SK_BaseDao
{
	//private static $classInstance;
	
	/**
	 * @var string
	 */
	private $table_name; 
		
	/**
	 * Class constructor
	 *
	 * @param string $table_name
	 */
	public function __construct( $table_name )
	{
		$this->table_name = '`'.$table_name.'`';
	}
	
//	/**
//	 * Returns the only object of BlogDao class
//	 * 
//	 * @param string $table_name
//	 * @return BlogDao
//	 */
//	public static function newInstance ( $table_name )
//	{
//		if ( self::$classInstance === null )
//			self::$classInstance = new self( $table_name );
//			
//		return self::$classInstance;
//	}
	
	/**
	 * @see BaseDao::getDtoClassName()
	 *
	 */
	protected function getDtoClassName(){return 'Comment';}
	
	/**
	 * @see SK_BaseDao::getTableName()
	 *
	 */
	protected function getTableName(){return $this->table_name;}

	
	/**
	 * Gets comment list for entity
	 *
	 * @param integer $entityId
	 * @param integer $first
	 * @param integer $count
	 * @return array
	 */
	public function getCommentList( $entity_id, $first, $count, $entityType = '' )
	{
		$query = SK_MySQL::placeholder("SELECT `c`.*, `p`.`username` FROM ". $this->getTableName(). " AS `c`
			LEFT JOIN `". TBL_PROFILE. "` AS `p` ON (`c`.`author_id` = `p`.`profile_id`) WHERE `c`.`entity_id`=? AND `entityType`='?' ORDER BY `c`.`create_time_stamp` DESC LIMIT ?, ?", $entity_id, $entityType, $first, $count);

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	
	/**
	 * Gets comment list for entity item without listing
	 *
	 * @param integer $entityId
	 * @return array
	 */
	public function getCommentListWL( $entity_id, $entityType = '' )
	{
		$query = SK_MySQL::placeholder("SELECT `c`.*, `p`.`username` FROM ". $this->getTableName(). " AS `c`
			LEFT JOIN `". TBL_PROFILE. "` AS `p` ON (`c`.`author_id` = `p`.`profile_id`) WHERE `c`.`entity_id`=? AND `entityType`='?' ORDER BY `c`.`create_time_stamp` ASC", $entity_id , $entityType);

		return SK_MySQL::query($query)->mapObjectArray($this->getDtoClassName());
	}
	
	
	/**
	 * Gets comments count for entity
	 *
	 * @param integer $entityId
	 * @return integer
	 */
	public function getCommentsCount( $entity_id, $entityType = '' )
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(*) AS `items_count` FROM ". $this->getTableName(). " WHERE `entity_id`=? AND `entityType`='?' ", $entity_id, $entityType);
		
		return (int)SK_MySQL::query($query)->fetch_cell();
	}
	
	
	/**
	 * Deletes all entity comments
	 *
	 * @param integer $entity_id
	 */
	public function deleteEntityComments( $entity_id, $entityType = '' )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `entity_id` = ? AND `entityType`='?'", $entity_id, $entityType);

		SK_MySQL::query( $query );
	}
	
	
	/**
	 * Returns most commented entities with comment count
	 *
	 * @return array
	 */
	public function findMostCommentedEntities($entityType)
	{
		$query = SK_MySQL::placeholder( "SELECT *, COUNT(*) AS `items_count` FROM ". $this->getTableName(). " WHERE `entityType`='?' GROUP BY `entity_id` ORDER BY `items_count`", $entityType );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	
	/**
	 * Returns comments count for entity group
	 *
	 * @param array $entityIds
	 * @return array
	 */
	public function findCommentsCountForEntityIds( array $entityIds, $entityType = '' )
	{
		$query = SK_MySQL::placeholder("SELECT *, COUNT(*) AS `items_count` FROM ". $this->getTableName(). " WHERE `entityType`='?' AND `entity_id` IN ( ?@ ) GROUP BY `entity_id`", $entityType, $entityIds ); 
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	
	/**
	 * Deletes profile comments
	 *
	 * @param integer $profile_id
	 */
	public function deleteProfileComments( $profile_id, $entityType = '' )
	{
		$query = SK_MySQL::placeholder( "DELETE FROM ". $this->getTableName(). " WHERE `author_id` = ? AND `entityType`='?'", $profile_id, $entityType );
		
		SK_MySQL::query( $query );
	}
    
    /**
	 * Gets comment list for entity
	 *
	 * @param int $entity_id
	 * @return 
	 */
	public function findClassifiedsComments ( $entity_id )
	{
		$query = SK_MySQL::placeholder( "SELECT `comm`.*, `bid`.`bid` FROM ".$this->getTableName()." AS `comm` 
			LEFT JOIN `".TBL_CLASSIFIEDS_BID."` AS `bid` ON `comm`.`id`=`bid`.`entity_id` WHERE `comm`.`entity_id`=? ORDER BY `comm`.`create_time_stamp`", $entity_id );	

		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}    
	
}