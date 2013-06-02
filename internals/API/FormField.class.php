<?php

abstract class SK_FormField
{
	/**
	 * The name of a field.
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * The class name of a field.
	 *
	 * @var string
	 */
	private $class;
	
	/**
	 * Field value.
	 *
	 * @var mixed
	 */
	private $value;
	
	/**
	 * Constructor.
	 *
	 * @param string $field_name
	 */
	protected function __construct( $field_name )
	{
		$this->name = $field_name;
		$this->class = get_class($this);
	}
	
	/**
	 * Get a field name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Setup a field state before it exports into a static declaration file.
	 *
	 * @param SK_Form $form
	 */
	public function setup( SK_Form $form ) {}
	
	/**
	 * Field javascript prototype.
	 *
	 * @var array
	 */
	protected $js_presentation =
		array(
			/**
			 * Field on DOM ready constructor.
			 *
			 * @param jQuery $input a set of input elements.
			 */
			'construct' =>
				'function($input, form_handler) {}',
			
			/**
			 * Frontend value validator function.
			 *
			 * @param mixed value
			 * @throws SK_FormFieldValidationException
			 */
			'validate'	=>
				'function(value, required) {}',
			
			/**
			 * Field focus event handler.
			 */
			'focus' =>
				'function() {}'
		);
	
	/**
	 * Returns the javascript presentation of a field.
	 *
	 * @return unknown
	 */
	public function js_presentation()
	{
		$prop_list = array();
		foreach ( $this->js_presentation as $prop => $val ) {
			$prop_list[] = "$prop: $val";
		}
		return "{\n\t" . implode(",\n\t", $prop_list) . "\n}";
	}
	
	/**
	 * Field state setter.
	 *
	 * @param array $params
	 * @return SK_FormField
	 */
	public static function __set_state( array $params )
	{
		$field = new $params['class']($params['name']);
		unset($params['class'], $params['name']);
		
		foreach ( $params as $prop => $value )
			$field->$prop = $value;
		
		return $field;
	}
	
	/**
	 * Set value for a field validating through {@link $this->validate()} method.
	 *
	 * @param mixed $value
	 */
	public function setValue( $value ) {
		$this->value = $this->validate($value);
	}
	
	/**
	 * Get a field value.
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Validate a field value.
	 *
	 * @param mixed $value
	 * @throws SK_FormFieldValidationException
	 * @return mixed valid value for a field
	 */
	abstract public function validate( $value );
	
	/**
	 * Return a field html presentation.
	 *
	 * @param array $params
	 * @return string html
	 */
	abstract public function render( array $params = null, SK_Form $form = null );
	
}

