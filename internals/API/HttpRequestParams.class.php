<?php

class SK_HttpRequestParams
{
	/**
	 * Params associative list.
	 *
	 * @var array
	 */
	public $params = array();
	
	/**
	 * Is params validated.
	 *
	 * @var boolean
	 */
	private $validated = false;
	
	/**
	 * Construct.
	 *
	 * @param array|object $params
	 */
	public function __construct( $params )
	{
		if ( is_array($params) ) {
			$this->params = $params;
		}
		elseif ( is_object($params) ) {
			foreach ( $params as $prop => $value ) {
				$this->params[$prop] = $value;
			}
		}
	}
	
	/**
	 * Validate http request params.
	 *
	 * @param array $rules
	 * @param boolean $authentication_required
	 * @throws SK_HttpRequestException
	 */
	public function validate( array $rules, $authentication_required = false )
	{
		// checking authentication if need
		if ( $authentication_required && !SK_HttpUser::is_authenticated() ) {
			throw new SK_HttpRequestException(SK_HttpRequestException::AUTH_REQUIRED);
		}
		
		// checking params
		$_params = $this->params;
		$this->params = array();
		foreach ( $rules as $prop => $type )
		{
			if ( !key_exists($prop, $_params) ) {
				throw new SK_HttpRequestException(SK_HttpRequestException::MISSING_ARGUMENT, $prop);
			}
			
			$value = &$_params[$prop];
			
			switch ( $type )
			{
				case 'int': case 'integer':
					$this->params[$prop] = (integer)$value;
					break;
				
				case 'id': case 'autoid':
					if ( !$value || !is_numeric($value) || $value < 1 ) {
						throw new SK_HttpRequestException(SK_HttpRequestException::INVALID_ARGUMENT, $prop);
					}
					$this->params[$prop] = $value;
					break;
				
				case 'str': case 'string':
					$this->params[$prop] = (string)$value;
					break;
				
				case 'array':
					$this->params[$prop] = (array)$value;
					break;
				
				case 'float': case 'double':
					$this->params[$prop] = (float)$value;
					break;
				
				default:
					throw new Exception('unknown validation type "'.$type.'"');
					break;
			}
		}
		
		$this->validated = true;
	}
	
	/**
	 * Properties getter.
	 *
	 * @param string $prop
	 */
	public function __get( $prop ) {
		if ( $this->validated && !key_exists($prop, $this->params) ) {
			throw new SK_HttpRequestException(SK_HttpRequestException::MISSING_ARGUMENT, $prop);
		}
		return $this->params[$prop];
	}
	
	
	public function __set( $prop, $value ) {
		throw new Exception('overloading params is unallowable', 0);
	}
	
	public function has($prop_name) {
		return array_key_exists($prop_name, $this->params);
	}
	
}
