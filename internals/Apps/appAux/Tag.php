<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 * 
 * Desc: DTO Class for tag Table entries
 */

final class Tag extends SK_Entity 
{
	/**
	 * @var string
	 */
	private $label;
	
	
	/**
	 * Class constructor
	 *
	 * @param string $label
	 */
	public function __construct( $label = null )
	{
		$this->label = $label;	
	}
	
	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}
	
	/**
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
	}
}