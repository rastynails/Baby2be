<?php
require_once DIR_APPS.'appAux/ClassifiedsItem.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 08, 2009
 * 
 * Desc: DAO Class for classifieds_item table entries
 */

final class ClassifiedsItemDao extends SK_BaseDao 
{	
	/**
	 * Class constructor
	 *
	 */
	public function __construct (){}
	
	protected function getDtoClassName (){ return "ClassifiedsItem"; }
	protected function getTableName (){return '`'.TBL_CLASSIFIEDS_ITEM.'`';}
	
	/**
	 * Returns latest items list 
	 *
	 * @param string $entity
	 * @param int $count
	 * @return array
	 */
	public function findLatestItems ( $entity, $count )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." WHERE `entity`='?' AND `is_approved`=?
			ORDER BY `create_stamp` DESC LIMIT ?", $entity, 1, $count );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}

    public function findItems ( $keyword, $first, $count )
    {
        $query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." WHERE `title` like '?' AND `is_approved`=?
			ORDER BY `create_stamp` DESC LIMIT ?, ?", '%'.$keyword.'%', 1, $first, $count );

        return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
    }
	
	/**
	 * Returns ending soon items list 
	 *
	 * @param string $entity
	 * @param int $count
	 * @return array
	 */	
	public function findEndingSoonItems ( $entity, $count )
	{
        $time = time();
        
		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." WHERE `entity`='?' AND `is_approved`=? AND `end_stamp` > {$time}
			ORDER BY `end_stamp` ASC LIMIT ?", $entity, 1, $count );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	/**
	 * Returns new item's order
	 *	
	 * @return int
	 */	
	public function findNewItemOrder ()
	{		
		return SK_MySQL::query( "SELECT (MAX(`order`)+1) FROM ".$this->getTableName()." " )->fetch_cell();
	}	
	
	/**
	 * Returns Latest Category Items
	 *
	 * @param int $category_id
	 */
	public function findCategoryLatestItems ( $category_id, $first, $count )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." WHERE `group_id`=? AND `is_approved`=?
			ORDER BY `create_stamp` DESC LIMIT ?, ?", $category_id, 1, $first, $count );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}
	
	/**
	 * Returns Ending Soon Category Items
	 *
	 * @param int $category_id
	 */
	public function findCategoryEndingSoonItems ( $category_id, $first, $count )
	{
        $time = time();
        
		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." WHERE `group_id`=? AND `is_approved`=? AND `end_stamp` > {$time}
			ORDER BY `end_stamp` ASC LIMIT ?, ?", $category_id, 1, $first, $count );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}	
	
	/**
	 * Returns Item's Last Bid
	 *
	 * @param int $item_id
	 * @return string
	 */
	public function findItemLastBid ( $item_id )
	{
		$query = SK_MySQL::placeholder( "SELECT `bid`.`bid` FROM `".TBL_CLASSIFIEDS_BID."` AS `bid`
			LEFT JOIN `".TBL_CLASSIFIEDS_COMMENT."` AS `comm` ON `bid`.`entity_id`=`comm`.`id`
			WHERE `comm`.`entity_id`=? ORDER BY `bid`.`create_stamp` DESC LIMIT 1", $item_id );
		
		return SK_MySQL::query( $query )->fetch_cell();
		
	}
	
	/**
	 * Returns Category Items Count
	 *
	 * @param int $category_id
	 * @return int
	 */
	public function findCategoryItemsCount ( $category_id, $type )
	{
        $optional_condition = '';

        if ( $type=='ending_soon' )
        {
            $time = time();
            $optional_condition = " AND `end_stamp` > {$time} ";
        }

		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM ".$this->getTableName()." WHERE `group_id`=? AND `is_approved`=? {$optional_condition} ", $category_id, 1);

		return SK_MySQL::query( $query )->fetch_cell();
	}
	
	/**
	 * Returns Latest Items List
	 *
	 * @param string $entity
	 * @return array
	 */
	public function findLatestItemList ( $entity, $first, $count )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." WHERE `entity`='?' AND `is_approved`=?
			ORDER BY `create_stamp` DESC LIMIT ?, ?", $entity, 1, $first, $count);
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}	
	
	/**
	 * Returns Ending Soon Items List
	 *
	 * @param string $entity
	 * @return array
	 */
	public function findEndingSoonItemList ( $entity, $first, $count )
	{
        $time = time();
        
		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." WHERE `entity`='?' AND `is_approved`=? AND `end_stamp` > {$time}
			ORDER BY `end_stamp` ASC LIMIT ?, ?", $entity, 1, $first, $count);
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}

	/**
	 * Returns List of items to approve
	 *
	 * @param string $entity
	 * @return array
	 */
	public function findItemsToApprove ( $first, $count )
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM ".$this->getTableName()." as `c`
			LEFT JOIN `". TBL_PROFILE. "` AS `p` ON ( `c`.`profile_id` = `p`.`profile_id` )
			WHERE `c`.`is_approved`=?
			ORDER BY `title` LIMIT ?, ?", 0, $first, $count );
		
		return SK_MySQL::query( $query )->mapObjectArray( $this->getDtoClassName() );
	}

	/**
	 * Returns numver of items to approve
	 *	 
	 * @return array
	 */
	public function findItemsToApproveCount()
	{
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM ".$this->getTableName()." WHERE `is_approved`=? ", 0);		
		return SK_MySQL::query( $query )->fetch_cell();		
	}
	
	/**
	 * Returns Items Count
	 *
	 * @param string $entity
	 * @return int
	 */	
	public function findItemsCount ( $entity, $type )
	{
        $optional_condition = '';

        if ( $type=='ending_soon' )
        {
            $time = time();
            $optional_condition = " AND `end_stamp` > {$time} ";
        }

		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM ".$this->getTableName()." WHERE `entity`='?' AND `is_approved`=? {$optional_condition} ", $entity, 1);

		return SK_MySQL::query( $query )->fetch_cell();
	}

    public function findSearchResultCount( $keyword )
    {
        $query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM ".$this->getTableName()."
            WHERE `title` like '?' AND `is_approved`=?", '%'.$keyword.'%', 1);

        return SK_MySQL::query( $query )->fetch_cell();
    }
}