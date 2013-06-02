<?php

class fieldType_checkbox extends SK_FormField
{
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name ) {
		parent::__construct($name);
	}
	
	
	public function validate( $value ) {
		return (bool)$value;
	}
	
	/**
	 * Render a checkbox.
	 *
	 * @param string $class html class for the item
	 * @param string $label
	 * @return string html
	 */
	public function render( array $params = null, SK_Form $form = null )
	{
		$output = '<input id="' . $form->getTagAutoId($this->getName()) . '" name="'.$this->getName().'" type="checkbox" ';
		
		if (isset($params['tabindex']) && $params['tabindex'] = intval($params['tabindex'])) {
			$output .= ' tabindex="'.$params['tabindex'].'"';
		}
		
		if ( isset($params['class']) ) {
			$output .= 'class="'.$params['class'].'" ';
		}
		
		if ( $this->getValue() ) {
			$output .= 'checked="checked" ';
		}
		
		$output .= 'value="1" />';
		
		if ( isset($params['label']) ) {
			$output = '<label>' . $output . $params['label'] . '</label>';
		}
		
		return $output;
	}
	
}
