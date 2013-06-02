<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 08, 2009
 * 
 * Desc: DTO Class for classifieds_group Table entries
 */

final class ClassifiedsGroup extends SK_Entity 
{
	/**
	 * @var string
	 */
	private $entity;	
	
	/**
	 * @var int
	 */
	private $parent_id;

	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var int
	 */
	private $order;
		
	/**
	 * Class constructor
	 *
	 * @param string $entity
	 * @param int $parent_id
	 * @param string $name
	 * @param int $order
	 */
	public function __construct( $entity = null, $parent_id = null, $name = null, $order = null )
	{
		$this->entity = $entity;
		$this->parent_id = $parent_id;
		$this->name = $name;
		$this->order = $order;
	}
	
	/**
	 * @return string
	 */
	public function getEntity()
	{
		return $this->entity;
	}
	
	/**
	 * @return int
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * @return int
	 */
	public function getParent_id()
	{
		return $this->parent_id;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param string $entity
	 */
	public function setEntity($entity)
	{
		$this->entity = $entity;
	}
	
	/**
	 * @param int $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param int $parent_id
	 */
	public function setParent_id($parent_id)
	{
		$this->parent_id = $parent_id;
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

}