<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 08, 2009
 * 
 * Desc: DTO Class for classifieds_bid Table entries
 */

final class ClassifiedsBid extends SK_Entity 
{
	/**
	 * @var int
	 */
	private $profile_id;

	/**
	 * @var int
	 */
	private $entity_id;

	/**
	 * @var string
	 */
	private $bid;		

	/**
	 * @var int
	 */
	private $create_stamp;		

	/**
	 * Class constructor
	 *
	 * @param int $profile_id
	 * @param int $entity_id
	 * @param string $bid
	 * @param int $create_stamp
	 */
	public function __construct( $profile_id = null, $entity_id = null, $bid = null, $create_stamp )
	{
		$this->profile_id = $profile_id;
		$this->entity_id = $entity_id;
		$this->bid = $bid;
		$this->create_stamp = $create_stamp;
	}
	
	/**
	 * @return string
	 */
	public function getBid()
	{
		return $this->bid;
	}
	
	/**
	 * @return int
	 */
	public function getCreate_stamp()
	{
		return $this->create_stamp;
	}
	
	/**
	 * @return int
	 */
	public function getEntity_id()
	{
		return $this->entity_id;
	}
	
	/**
	 * @return int
	 */
	public function getProfile_id()
	{
		return $this->profile_id;
	}
	
	/**
	 * @param string $bid
	 */
	public function setBid($bid)
	{
		$this->bid = $bid;
	}
	
	/**
	 * @param int $create_stamp
	 */
	public function setCreate_stamp($create_stamp)
	{
		$this->create_stamp = $create_stamp;
	}
	
	/**
	 * @param int $entity_id
	 */
	public function setEntity_id($entity_id)
	{
		$this->entity_id = $entity_id;
	}
	
	/**
	 * @param int $profile_id
	 */
	public function setProfile_id($profile_id)
	{
		$this->profile_id = $profile_id;
	}


}