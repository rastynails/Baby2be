<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 26, 2008
 * 
 * Desc: DTO Class for Rate Table entries
 */

final class Rate extends SK_Entity 
{
	/**
	 * @var integer
	 */
	private $entity_id;
	
	/**
	 * @var integer
	 */
	private $profile_id;
	
	/**
	 * @var integer
	 */
	private $score;
	
	/**
	 * @var integer
	 */
	private $date;
	
	/**
	 * Class constructor
	 *
	 * @param string $label
	 */
	public function __construct( $entity_id = null, $profile_id = null, $score = null, $date = null )
	{
		$this->entity_id = (int)$entity_id;
		$this->profile_id = (int)$profile_id;
		$this->score = (int)$score;
		$this->date = (int)$date;	
	}
	
	/**
	 * @return integer
	 */
	public function getDate ()
	{
		return $this->date;
	}
	
	/**
	 * @param integer $date
	 */
	public function setDate ( $date )
	{
		$this->date = (int)$date;
	}
	
	/**
	 * @return integer
	 */
	public function getEntity_id ()
	{
		return $this->entity_id;
	}
	
	/**
	 * @param integer $entity_id
	 */
	public function setEntity_id ( $entity_id )
	{
		$this->entity_id = (int)$entity_id;
	}
	
	/**
	 * @return integer
	 */
	public function getProfile_id ()
	{
		return $this->profile_id;
	}
	
	/**
	 * @param integer $profile_id
	 */
	public function setProfile_id ( $profile_id )
	{
		$this->profile_id = (int)$profile_id;
	}
	
	/**
	 * @return integer
	 */
	public function getScore ()
	{
		return $this->score;
	}
	
	/**
	 * @param integer $score
	 */
	public function setScore ( $score )
	{
		$this->score = (int)$score;
	}

}