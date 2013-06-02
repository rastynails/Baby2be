<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 23, 2008
 * 
 * Desc: DTO Class for Smile Table entries
 */

final class Smile extends SK_Entity 
{
	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var string
	 */
	private $url;
	
	/**
	 * Class constructor
	 *
	 * @param string $code
	 * @param string $url
	 * @param string $description
	 */
	public function __construct( $code = null, $url = null )
	{
		$this->code = $code;
		$this->url = $url;
	}
	
	/**
	 * @return string
	 */
	public function getCode ()
	{
		return $this->code;
	}
	
	/**
	 * @param string $code
	 */
	public function setCode ( $code )
	{
		$this->code = $code;
	}
	
	/**
	 * @return string
	 */
	public function getUrl ()
	{
		return $this->url;
	}
	
	/**
	 * @param string $url
	 */
	public function setUrl ( $url )
	{
		$this->url = $url;
	}
}