<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 * 
 * Desc: Base class for DTO classes
 */

abstract class SK_Entity
{
	/**
	 * @var integer
	 */
	protected  $id;
	
	/**
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param integer $id
	 */
	public function setId($id) {
		$this->id = (int)$id;
	}

}
