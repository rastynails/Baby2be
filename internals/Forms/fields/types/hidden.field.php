<?php

class fieldType_hidden extends SK_FormField
{
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name ) {
		parent::__construct($name);
	}
	
	/**
	 * There are no validation for a fields type of hidden.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function validate( $value ) {
		return $value;
	}
	
	/**
	 * Render a hidden input.
	 *
	 * @return string html
	 */
	
	public function render( array $params = null, SK_Form $form = null )
	{
		$id = isset($form) ? 'id="' . $form->getTagAutoId($this->getName()) . '"' : '';
		$output = '<input ' . $id . ' name="'.$this->getName().'" type="hidden"';
		
		$value = $this->getValue();
		if ( $value ) {
			$output .= ' value="'.SK_Language::htmlspecialchars($value).'"';
		}
		
		$output .= ' />';
		
		return $output;
	}
	
}
