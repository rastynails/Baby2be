<?php

class SK_HttpRequestException extends Exception
{
	const AUTH_REQUIRED		= 1;
	const MISSING_ARGUMENT	= 2;
	const INVALID_ARGUMENT	= 3;
	
	/**
	 * Constructor.
	 *
	 * @param integer $code
	 * @param string $arg_name optional parameter name
	 */
	function __construct( $code, $arg_name = null )
	{
		switch ( $code ) {
			case self::AUTH_REQUIRED:
				$message = 'authentication required';
				break;
			case self::MISSING_ARGUMENT:
				$message = 'missing parameter "'.$arg_name.'"';
				break;
			case self::INVALID_ARGUMENT:
				$message = 'invalid argument "'.$arg_name.'"';
				break;
			default:
				$message = 'unknown error code '.$code;
				break;
		}
		
		parent::__construct($message, $code);
	}
	
	
}