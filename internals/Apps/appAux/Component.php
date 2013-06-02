<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 10, 2008
 * 
 * Desc: DTO Class for component Table entries
 */

final class Component extends SK_Entity 
{	
	/**
	 * @var string
	 */
	private $label;
	
	/**
	 * @var string
	 */
	private $class_name;
	
	/**
	 * @var integer
	 */
	private $is_available;
	
	/**
	 * @var integer
	 */
	private $sorder;
	
	/**
	 * @var integer
	 */
	private $feature_id;
	
	/**
	 * @var integer
	 */
	private $multiple;
	
	/**
	 * @return integer
	 */
	public function getMultiple() {
		return $this->multiple;
	}
	
	/**
	 * @param integer $multiple
	 */
	public function setMultiple($multiple) {
		$this->multiple = $multiple;
	}
	/**
	 * Class constructor
	 *
	 * @param string $label
	 */
	public function __construct( $label = null, $class_name = null, $is_available = null, $feature_id = null )
	{
		$this->label = $label;
		$this->class_name = $class_name;
		$this->is_available = $is_available;
		$this->feature_id = $feature_id;
	}
	
	/**
	 * @return string
	 */
	public function getClass_name ()
	{
		return $this->class_name;
	}
	
	/**
	 * @param string $class_name
	 */
	public function setClass_name ( $class_name )
	{
		$this->class_name = $class_name;
	}
	
	/**
	 * @return integer
	 */
	public function getIs_available ()
	{
		return $this->is_available;
	}
	
	/**
	 * @param integer $is_available
	 */
	public function setIs_available ( $is_available )
	{
		$this->is_available = ((int)$is_available === 1) ? 1 : 0 ;
	}
	
	/**
	 * @return string
	 */
	public function getLabel ()
	{
		return $this->label;
	}
	
	/**
	 * @param string $label
	 */
	public function setLabel ( $label )
	{
		$this->label = $label;
	}
	/**
	 * @return integer
	 */
	public function getSorder ()
	{
		return $this->sorder;
	}
	/**
	 * @param integer $sorder
	 */
	public function setSorder ( $sorder )
	{
		$this->sorder = (int)$sorder;
	}
	/**
	 * @return integer
	 */
	public function getFeature_id ()
	{
		return $this->feature_id;
	}
	/**
	 * @param integer $feature_id
	 */
	public function setFeature_id ( $feature_id )
	{
		$this->feature_id = (int)$feature_id;
	}

}