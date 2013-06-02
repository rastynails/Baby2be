<?php

class fieldType_textarea extends fieldType_text
{
	
	/**
	 * The maximum length of value.
	 *
	 * @var integer
	 */
	public $maxlength = 2000;
	
	/**
	 * Render a textarea.
	 *
	 * @param string $name
	 * @param string $class an html class name
	 * @return string html
	 */
	public function render( array $params = null, SK_Form $form = null )
	{
		$output = '<textarea id="' . $form->getTagAutoId($this->getName()) . '" name="'.$this->getName().'"';
		
		if ( isset($params['class']) ) {
			$output .= ' class="'.$params['class'].'"';
		}
		
		$output .= ">";
		
		$value = $this->getValue();
		if ( $value ) {
			$output .= SK_Language::htmlspecialchars($value);
		}
		
		$output .=  '</textarea>';
		
	    if ( !empty($params['hasInvitation']) )
        {
            $labelEmbed = 'SK_Language.text(' . json_encode('$forms.' . $form->getName() . '.fields.' . $this->getName()) . ')';
            $output .= '<script language="javascript">SK_SetFieldInvitation(' . json_encode($form->getTagAutoId($this->getName())) . ', ' . $labelEmbed . ' );</script>';
        }
		
		return $output;
	}
	
}
