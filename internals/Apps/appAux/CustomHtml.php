<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Feb 09, 2009
 * 
 * Desc: DTO Class for CustomHtml Table entries
 */

final class CustomHtml extends SK_Entity 
{
	/**
	 * @var integer
	 */
	private $cmp_id;
	
	/**
	 * @var string
	 */
	private $html_code;

	/**
	 * @var string
	 */
	private $cap_label;
	
	/**
	 * Class constructor
	 *
	 * @param string $label
	 */
	public function __construct( $cmp_id = null, $html_code = null )
	{
		$this->cmp_id = $cmp_id;
		$this->html_code = $html_code;	
	}
	
	/**
	 * @return integer
	 */
	public function getCmp_id() 
	{
		return $this->cmp_id;
	}
	
	/**
	 * @return string
	 */
	public function getHtml_code() 
	{
		return $this->html_code;
	}
	
	/**
	 * @param integer $cmp_id
	 */
	public function setCmp_id($cmp_id) 
	{
		$this->cmp_id = (int)$cmp_id;
	}
	
	/**
	 * @param string $html_code
	 */
	public function setHtml_code($html_code) 
	{
		$this->html_code = $html_code;
	}
	
	/**
	 * @return string
	 */
	public function getCap_label() 
	{
		return $this->cap_label;
	}
	
	/**
	 * @param string $cap_label
	 */
	public function setCap_label($cap_label) 
	{
		$this->cap_label = $cap_label;
	}

}