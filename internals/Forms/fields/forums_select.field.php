<?php

class field_forums_select extends fieldType_select
{
	/**
	 * Possible values list.
	 *
	 * @var array
	 */
	protected $values = array();
	
	protected $multiple;
	
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name )
	{
		parent::__construct($name);
	}
	
	/**
	 * Validator for field type of select.
	 * Note: validation works only if $this->values is sets up
	 * during $this->setup() and compiled with the form state as well.
	 *
	 * @param string $value
	 * @return string
	 */
	public function validate( $value )
	{	
		if ( $this->values && !in_array($value, $this->values) ) {
			throw new SK_FormFieldValidationException('illegal_value');
		}
		
		return $value;
	}
	
	
	public function setValues($values = array())
	{
		$this->values = $values;
	}
	
	/**
	 * @return unknown
	 */
	public function getMultiple () 
	{ 
		return $this->multiple; 
	}

	/**
	 * Sets select multiple
	 * 
	 */
	public function setMultiple () 
	{ 
		$this->multiple = '[]'; 
	}

	/**
	 * Render a field.
	 *
	 * @param string $name
	 * @param string $type 'select'|'radio' default 'select'
	 * @param string $class an html class name
	 * @return string html
	 */
	public function render( array $params = null, SK_Form $form = null )
	{	
		$output = '<select id="' . $form->getTagAutoId($this->getName()) . '" name="'.$this->getName().$this->getMultiple().'"';
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		if( $this->getMultiple() ) {
			$output .= ' multiple="multiple"';
		}
		if( isset($params['size']) ) {
			$output .= ' size="'.$params['size'].'"';
		}		
		$output .= '>';
		
		$selected_value = $this->getValue();

		foreach ( $this->values as $label=>$value )
		{
			$output .= '<option value="'.$value.'"';
			if ( $value == $selected_value && $selected_value ) {
				$output .= ' selected="selected"';
			}
			if ( !$value ) {
				$output .= ' disabled=true style="color: #9F9F9F;"';
			}
			$output .= '>'.$label.'</option>';
		}
		
		$output .= '</select>';
		
		return $output;
	}
	
}
