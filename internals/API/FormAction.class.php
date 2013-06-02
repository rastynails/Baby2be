<?php

abstract class SK_FormAction
{
	/**
	 * The name of an action.
	 *
	 * @var string
	 */
	private $name;
	
	/**
	 * The name of an action extension class.
	 *
	 * @var string
	 */
	private $class;
	
	/**
	 * The list of $field_key=>$required pairs for the this action.
	 *
	 * @var array
	 */
	private $fields = array();
	
	/**
	 * The list of fields which are acts in this action.
	 *
	 * @var array
	 */
	protected $process_fields = array();
	
	/**
	 * The list of fields which are required for this action.
	 *
	 * @var array
	 */
	protected $required_fields = array();
	
	/**
	 * Confirmation message language address.
	 *
	 * @var string
	 */
	private $confirm_msg_lang_addr;
	
	/**
	 * Constructor.
	 *
	 * @param string $action_name
	 */
	protected function __construct( $action_name )
	{
		$this->name = $action_name;
		$this->class = get_class($this);
	}
	
	/**
	 * Set a confirmation message for the action.
	 *
	 * @param string $lang_addr
	 */
	protected function setConfirmation( $lang_addr ) {
		$this->confirm_msg_lang_addr = $lang_addr;
	}
	
	/**
	 * Get a confirmation message of the action.
	 *
	 * @return string
	 */
	public function getConfirmation() {
		return $this->confirm_msg_lang_addr;
	}
	
	/**
	 * Returns the name of an action.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * The getter for {@link $this->fields}.
	 *
	 * @return array
	 */
	public function getProcessFields() {
		return $this->fields;
	}
	
	
	public function setup( SK_Form $form )
	{
		if ( empty($this->process_fields) ) {
			$this->process_fields = array_keys($form->fields);
		}
		
		foreach ( $this->process_fields as $field_key ) {
			$this->fields[$field_key] = in_array($field_key, $this->required_fields);
		}
		
	}
	
	
	final public function js_presentation()
	{
		$js_prop = "fields: ".json_encode($this->fields)."\n";
		
		if ( $this->confirm_msg_lang_addr ) {
			$js_prop .= ", confirm_msg: '".$this->confirm_msg_lang_addr."'\n";
		}
		
		return "{\n$js_prop}";
		
	}
	
	
	public static function __set_state( array $params )
	{
		$_this = new $params['class']($params['name']);
		
		unset($params['class'], $params['name']);
		
		foreach ( $params as $prop => $value )
			$_this->$prop = $value;
		
		return $_this;
	}
	
	/**
	 * The getter for $this->required_fields.
	 *
	 * @return array
	 */
	public function required_fields() {
		return $this->required_fields;
	}
	
	/**
	 * An optional abstraction method for action entry data validation.
	 *
	 * @param array $data
	 * @param SK_FormResponse $response
	 * @param SK_Form $form
	 */
	public function checkData( array $data, SK_FormResponse $response, SK_Form $form ) {}
	
	
	abstract public function process( array $data, SK_FormResponse $response, SK_Form $form );
	
}
