<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 10, 2008
 * 
 * Desc: DTO Class for profile_view_component Table entries
 */

final class ProfileComponent extends SK_Entity 
{	
	/**
	 * @var integer
	 */
	private $component_id;
	
	/**
	 * @var integer
	 */
	private $profile_id;
	
	/**
	 * @var integer
	 */
	private $section;
	
	/**
	 * @var integer
	 */
	private $position;
	
	/**
	 * Class constructor
	 *
	 * @param string $label
	 */
	public function __construct( $component_id = null, $profile_id = null, $section = null, $position = null )
	{
		$this->component_id = $component_id;
		$this->profile_id = $profile_id;
		$this->section = $section;
		$this->position = $position;
	}
	
	/**
	 * @return integer
	 */
	public function getComponent_id ()
	{
		return $this->component_id;
	}
	
	/**
	 * @param integer $component_id
	 */
	public function setComponent_id ( $component_id )
	{
		$this->component_id = (int)$component_id;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getPosition ()
	{
		return $this->position;
	}
	
	/**
	 * @param integer $position
	 */
	public function setPosition ( $position )
	{
		$this->position = (int)$position;
		return $this;
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
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getSection ()
	{
		return $this->section;
	}
	
	/**
	 * @param integer $section
	 */
	public function setSection ( $section )
	{
		$this->section = (int)$section;
		return $this;
	}

}