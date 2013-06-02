<?php

final class TagEntity extends SK_Entity 
{
	/**
	 * @var integer
	 */
	private $tag_id;
	
	/**
	 * @var integer
	 */
	private $entity_id;
	
	
	/**
	 * Class constructor
	 *
	 * @param integer $tag_id
	 * @param integer $entity_id
	 */
	public function __construct( $tag_id = null, $entity_id = null )
	{
		$this->tag_id = (int)$tag_id;
		$this->entity_id = (int)$entity_id;
	}
	
	/**
	 * @return integer
	 */
	public function getEntity_id() {
		return $this->entity_id;
	}
	
	/**
	 * @param integer $entity_id
	 */
	public function setEntity_id($entity_id) {
		$this->entity_id = (int)$entity_id;
	}
	
	/**
	 * @return integer
	 */
	public function getTag_id() {
		return $this->tag_id;
	}
	
	/**
	 * @param integer $tag_id
	 */
	public function setTag_id($tag_id) {
		$this->tag_id = (int)$tag_id;
	}

}