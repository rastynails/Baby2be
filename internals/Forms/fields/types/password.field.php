<?php

class fieldType_password extends fieldType_text 
{

	public function __construct( $name ) {
		parent::__construct($name);
	}
	
	/**
	 * Render a text input.
	 *
	 * @param string $name
	 * @param string $class an html class name
	 * @return string html
	 */
	public function render( array $params = null, SK_Form $form = null )
	{
		$output = '<input id="' . $form->getTagAutoId($this->getName()) . '" name="'.$this->getName().'" type="password"';
		
		if (isset($params['tabindex']) && $params['tabindex'] = intval($params['tabindex'])) {
			$output .= ' tabindex="'.$params['tabindex'].'"';
		}
		
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		
		$value = $this->getValue();
		if ( $value ) {
			$output .= ' value="'.SK_Language::htmlspecialchars($value).'"';
		}
		
		$output .=  ' />';
		
	    if ( !empty($params['hasInvitation']) )
        {
            $labelEmbed = 'SK_Language.text(' . json_encode('$forms.' . $form->getName() . '.fields.' . $this->getName()) . ')';
            $output .= '<script language="javascript">SK_SetFieldInvitation(' . json_encode($form->getTagAutoId($this->getName())) . ', ' . $labelEmbed . ' );</script>';
        }
		
		return $output;
	}
	
}
