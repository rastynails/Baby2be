<?php
require_once DIR_APPS.'appAux/ClassifiedsBidDao.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 13, 2009
 * 
 * Desc: Classifieds Bid Service Class
 */

final class app_ClassifiedsBidService
{
	/**
	 * @var ClassifiedsBidDao
	 */
	private $classifiedsBidDao;
		
	/**
	 * @var array
	 */
	private static $classInstance;
	
	/**
	 * Class constructor
	 */
	private function __construct()
	{
		$this->classifiedsBidDao = new ClassifiedsBidDao();
	}
	
	/**
	 * Returns the only instance of the class
	 *
	 * @return app_ClassifiedsBidService
	 */
	public static function newInstance ()
	{
		if ( self::$classInstance === null )
			self::$classInstance = new self();
		return self::$classInstance;
	}
	
	/**
	 * Returns item bids count
	 *
	 * @param int $item_id
	 * @return int
	 */
	public function getItemBidsCount ( $item_id )
	{
		return $this->classifiedsBidDao->getItemBidsCount( $item_id );		
	}
	
	/**
	 * Returns lowest or highest(depending on item type) item bind info
	 *
	 * @param string $entity
	 * @param int $item_id
	 * @return array
	 */
	public function getItemBid ( $entity, $item_id )
	{
		$bid_info = array();
		if ( $entity == 'wanted' ) {
			$bid_info = $this->classifiedsBidDao->getItemLowestBidInfo( $item_id );
		}
		else {
			$bid_info = $this->classifiedsBidDao->getItemHighestBidInfo( $item_id );
		}

		return $bid_info;
	}
	
	/**
	 * Saves Or Updates entity bid
	 *
	 * @param ClassifiedsBid $bid
	 */
	public function saveOrUpdate ( ClassifiedsBid $bid )
	{
		$this->classifiedsBidDao->saveOrUpdate( $bid );
	}
	
	/**
	 * Deletes Entity Bid
	 *
	 * @param int $entity_id
	 */
	public function deleteEntityBid ( $entity_id )
	{
		$this->classifiedsBidDao->deleteEntityBid( $entity_id );
	}
	
	/** ---------------------------- static interface -------------------------------------- **/
	
	/**
	 * Returns item bids count
	 *
	 * @param int $item_id
	 * @return int
	 */
	public static function stGetItemBidsCount( $item_id )
	{
		$service = self::newInstance();
		
		return $service->getItemBidsCount( $item_id );		
	}
	
	/**
	 * Returns lowest or highest(depending on item type) item bind info
	 *
	 * @param string $entity
	 * @param int $item_id
	 * @return array
	 */
	public function stGetItemBid ( $entity, $item_id )
	{
		$service = self::newInstance();
		
		return $service->getItemBid( $entity, $item_id );		
	}
	
	/**
	 * Deletes entity bid
	 *
	 * @param int $entity_id
	 */
	public function stDeleteEntityBid ( $entity_id )
	{
		$service = self::newInstance();
		
		return $service->deleteEntityBid( $entity_id );		
	}	
	
}