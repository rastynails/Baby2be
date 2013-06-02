<?php

class SK_ComponentFrontendHandler
{
	/**
	 * Handler constructor name.
	 *
	 * @var string
	 */
	public $constructor;
	
	/**
	 * Javascript operations queue.
	 *
	 * @var array
	 */
	protected $operations = array();
	
	/**
	 * Constructor.
	 *
	 * @param string $constructor name of a javascript handler class
	 */
	public function __construct( $constructor ) {
		$this->constructor = $constructor;
	}
	
	/**
	 * Add a property set action.
	 *
	 * @param string $property
	 * @param mixed $value
	 */
	public function __set( $property, $value ) {
		$this->operations[] = "$property = ".json_encode($value);
	}
	
	/**
	 * Add a handler method call.
	 *
	 * @param string $method
	 * @param array $args
	 */
	public function __call( $method, array $args ) {
		$json_args = array_map('json_encode', $args);
		$this->operations[] = "$method(".implode(', ', $json_args).")";
	}
	
	/**
	 * Returns the javascript code of called operations.
	 *
	 * @param string $var_name
	 * @return string
	 */
	public function compile_js( $var_name )
	{
		$js_code = '';
		foreach ( $this->operations as $operation ) {
			$js_code .= "$var_name.$operation;\n";
		}
		
		$this->auto_var = $var_name;
		
		return $js_code;
	}
	
	
	private $auto_var;
	
	/**
	 * The getter for $this->auto_var
	 *
	 * @return string
	 */
	public function auto_var() {
		return $this->auto_var;
	}
	
	/**
	 * Show a MACOS-like error message.
	 *
	 * @param string $msg_text
	 */
	public function error( $msg_text ) {
		$this->__call('error', array($msg_text));
	}
	
	/**
	 * Show a MACOS-like message.
	 *
	 * @param string $msg_text
	 */
	public function message( $msg_text ) {
		$this->__call('message', array($msg_text));
	}
	
	/**
	 * Debug a variable value.
	 *
	 * @param mixed $var
	 */
	public function debug( $var ) {
		$this->__call('debug', array($var));
	}
}
