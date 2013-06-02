<?php

class field_category_select extends fieldType_select
{
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
	 * Render a field.
	 *
	 * @param string $name
	 * @param string $type 'select'|'radio' default 'select'
	 * @param string $class an html class name
	 * @return string html
	 */
	public function render( array $params = null, SK_Form $form = null )
	{	
		$output = '<select id="' . $form->getTagAutoId($this->getName()) . '" name="'.$this->getName().'"';
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		if( isset($params['size']) ) {
			$output .= ' size="'.$params['size'].'"';
		}		
		$output .= '>';
		
		$selected_value = $this->getValue();

		foreach ( $this->values as $value=>$option )
		{
			$output .= '<option value="'.$value.'"';
			if ( $value == $selected_value && $selected_value ) {
				$output .= ' selected="selected"';
			}
			if ( $option['class'] ) {
				$output .= ' class="'.$option['class'].'"';
			}			
			if ( $value !== intval($value) ) {
				$output .= ' disabled=true style="color: #9F9F9F;"';
			}
			$output .= '>'.$option['label'].'</option>';
		}
		
		$output .= '</select>';
		
		return $output;
	}
	
	/**
	 * @see fieldType_select::validate()
	 *
	 * @param string $value
	 * @return string
	 */
	public function validate( $value )
	{		
		if ( $this->values && !in_array($value, array_keys($this->values) ) ) {
			throw new SK_FormFieldValidationException('illegal_value');
		}
		
		return $value;
	}

	
}
