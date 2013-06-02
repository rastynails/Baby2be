<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 09, 2009
 * 
 * Desc: DTO Class for classifieds_file Table entries
 */

final class ClassifiedsFile extends SK_Entity 
{
	/**
	 * @var int
	 */
	private $entity_id;

	/**
	 * @var string
	 */
	private $extension;		
	
	/**
	 * Class constructor
	 *
	 * @param int $entity_id
	 * @param string $extension
	 */
	public function __construct( $entity_id = null, $extension = null )
	{
		$this->entity_id = $entity_id;
		$this->extension = $extension;
	}
	
	/**
	 * @return int
	 */
	public function getEntity_id()
	{
		return $this->entity_id;
	}
	
	/**
	 * @return string
	 */
	public function getExtension()
	{
		return $this->extension;
	}
		
	/**
	 * @param int $entity_id
	 */
	public function setEntity_id($entity_id)
	{
		$this->entity_id = $entity_id;
	}
	
	/**
	 * @param string $extension
	 */
	public function setExtension($extension)
	{
		$this->extension = $extension;
	}

}