<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Dec 19, 2008
 *
 * Desc: DTO Class for Badword Table entries
 */

final class Badword extends SK_Entity
{
	/**
	 * @var string
	 */
	private $label;

    private $type;


	/**
	 * Class constructor
	 *
	 * @param string $label
	 */
	public function __construct( $label = null, $type = null )
	{
		$this->label = $label;
        $this->type = $type;
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
	 * @return string
	 */
	public function getType ()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType ( $type )
	{
		$this->type = $type;
	}
}