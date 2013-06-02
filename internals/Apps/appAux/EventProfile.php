<?php
/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 23, 2008
 * 
 * Desc: DTO Class for event_profile table entries
 */

final class EventProfile extends SK_Entity
{
	/**
	 * @var integer
	 */
	private $event_id;
	
	/**
	 * @var integer
	 */
	private $profile_id;
	
	/**
	 * @var integer
	 */
	private $status;
	
	/**
	 * @var integer
	 */
	private $join_date;
	
	/**
	 * Class constructor
	 *
	 * @param integer $event_id
	 * @param integer $profile_id
	 * @param integer $status
	 */
	public function __construct( $event_id = null, $profile_id = null, $status = 1 )
	{
		$this->event_id = (int)$event_id;
		$this->profile_id = (int)$profile_id;
		$this->status = (int)$status;
		$this->join_date = time();
		
	}
	
	/**
	 * @return integer
	 */
	public function getEvent_id() {
		return $this->event_id;
	}
	
	/**
	 * @param integer $event_id
	 */
	public function setEvent_id($event_id) {
		$this->event_id = (int)$event_id;
	}
	
	/**
	 * @return integer
	 */
	public function getProfile_id() {
		return $this->profile_id;
	}
	
	/**
	 * @param integer $profile_id
	 */
	public function setProfile_id($profile_id) {
		$this->profile_id = (int)$profile_id;
	}
	
	/**
	 * @return integer
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * @param integer $status
	 */
	public function setStatus($status) {
		$this->status = (int)$status;
	}
	/**
	 * @return integer
	 */
	public function getJoin_date ()
	{
		return $this->join_date;
	}
	/**
	 * @param integer $join_date
	 */
	public function setJoin_date ( $join_date )
	{
		$this->join_date = $join_date;
	}


}